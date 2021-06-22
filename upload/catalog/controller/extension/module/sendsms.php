<?php
class ControllerExtensionModuleSendsms extends Controller
{
    public function status_change($route, $data)
    {
        $orderStatusId = $data[1];
        $orderId = $data[0];

        $this->load->model('checkout/order');
        $order = $this->model_checkout_order->getOrder($orderId);
        $oldOrderStatusId = $order['order_status_id'];

        if ($oldOrderStatusId != $orderStatusId) {
            # get text for event
            $this->load->model('setting/setting');
            $message = $this->config->get('module_sendsms_message_' . $orderStatusId);
            $short = $this->config->get('module_sendsms_message_short_' . $orderStatusId);
            $gdpr = $this->config->get('module_sendsms_message_gdpr_' . $orderStatusId);
            if (!empty($message) && !empty($order['telephone'])) {
                # replace variables in text
                $replace = array(
                    '{billing_first_name}' => $order['payment_firstname'],
                    '{billing_last_name}' => $order['payment_lastname'],
                    '{shipping_first_name}' => $order['shipping_firstname'],
                    '{shipping_last_name}' => $order['shipping_lastname'],
                    '{order_number}' => $order['order_id'],
                    '{order_date}' => $order['date_added'],
                    '{order_total}' => round($order['total'] * $order['currency_value'], 2) . ' ' . $order['currency_code']
                );
                foreach ($replace as $key => $value) {
                    $message = str_replace($key, $value, $message);
                }

                # send sms
                $this->send_sms($order['telephone'], $message, $short, $gdpr);
            }
        }
    }

    public function send_sms($phone, $message, $short, $gdpr, $type = 'order')
    {
        $this->load->model('setting/setting');
        $username = $this->config->get('module_sendsms_username');
        $password = $this->config->get('module_sendsms_password');
        $from = $this->config->get('module_sendsms_label');
        $simulation = $this->config->get('module_sendsms_simulation');
        if ($simulation) {
            $phone = $this->config->get('module_sendsms_simulation_phone');
        }
        $phone = $this->validatePhone($phone);
        $message = $this->cleanDiacritice($message);

        if (!empty($phone) && !empty($username) && !empty($password) && !empty($message)) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_URL, 'https://hub.sendsms.ro/json?action=message_send' . ($gdpr ? "_gdpr" : "") . '&username=' . urlencode($username) . '&password=' . urlencode($password) . '&from=' . urlencode($from) . '&to=' . urlencode($phone) . '&text=' . urlencode($message) . '&short=' . ($short ? 'true' : 'false'));
            curl_setopt($curl, CURLOPT_HTTPHEADER, array("Connection: keep-alive"));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

            $status = curl_exec($curl);
            $status = json_decode($status, true);

            # add to history
            $this->load->model('extension/sendsms/history');
            $this->model_extension_sendsms_history->addHistory(
                isset($status['status']) ? $status['status'] : '',
                isset($status['message']) ? $status['message'] : '',
                isset($status['details']) ? $status['details'] : '',
                $message,
                $type,
                $phone
            );
        }
    }

    public function validatePhone($phone_number)
    {
        if (empty($phone_number)) return '';
        require_once DIR_SYSTEM . 'library/sendsms.php';
        $lib = new SendSMS();
        $country_codes = $lib->country_codes;
        $phone_number = $this->clearPhone($phone_number);
        //Strip out leading zeros:
        //this will check the country code and apply it if needed
        $cc = $this->config->get('module_sendsms_country_code');
        if (!in_array($cc, $country_codes)) {
            $cc = 'INT';
        }
        if ($cc === "INT") {
            return $phone_number;
        }
        $phone_number = ltrim($phone_number, '0');
        $country_code = $country_codes[$cc];

        if (!preg_match('/^' . $country_code . '/', $phone_number)) {
            $phone_number = $country_code . $phone_number;
        }

        return $phone_number;
    }

    public function clearPhone($phone_number)
    {
        $phone_number = str_replace(['+', '-'], '', filter_var($phone_number, FILTER_SANITIZE_NUMBER_INT));
        //Strip spaces and non-numeric characters:
        $phone_number = preg_replace("/[^0-9]/", "", $phone_number);
        return $phone_number;
    }

    /**
     * @param $string
     * @return string
     */
    public function cleanDiacritice($string)
    {
        $bad = array(
            "\xC4\x82",
            "\xC4\x83",
            "\xC3\x82",
            "\xC3\xA2",
            "\xC3\x8E",
            "\xC3\xAE",
            "\xC8\x98",
            "\xC8\x99",
            "\xC8\x9A",
            "\xC8\x9B",
            "\xC5\x9E",
            "\xC5\x9F",
            "\xC5\xA2",
            "\xC5\xA3",
            "\xC3\xA3",
            "\xC2\xAD",
            "\xe2\x80\x93"
        );
        $cleanLetters = array("A", "a", "A", "a", "I", "i", "S", "s", "T", "t", "S", "s", "T", "t", "a", " ", "-");
        return str_replace($bad, $cleanLetters, $string);
    }
}
