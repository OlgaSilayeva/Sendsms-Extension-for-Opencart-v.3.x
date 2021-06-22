<?php
class ModelExtensionSendsmsSend extends Model
{
    public function send_sms($phone, $message, $short, $gdpr, $type = 'order')
    {
        $this->load->model('setting/setting');
        $username = $this->config->get('module_sendsms_username');
        $password = $this->config->get('module_sendsms_password');
        $from = $this->config->get('module_sendsms_label');
        $simulation = $this->config->get('module_sendsms_simulation');
        if ($simulation === "1") {
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

            if ($this->config->get('module_sendsms_price_per_phone') == 0|| $this->config->get('module_sendsms_price_per_phone_date') < date('Y-m-d H:i:s')) {
                curl_setopt($curl, CURLOPT_URL, 'https://hub.sendsms.ro/json?action=route_check_price&username=' . urlencode($username) . '&password=' . urlencode($password) . '&to=' . urlencode($phone));
                $status = curl_exec($curl);
                $status = json_decode($status, true);
                if ($status['details']['status'] === 64) {
                    $this->model_setting_setting->editSettingValue('module_sendsms', 'module_sendsms_price_per_phone', $status['details']['cost']);
                    $this->model_setting_setting->editSettingValue('module_sendsms', 'module_sendsms_price_per_phone_date', date('Y-m-d H:i:s', strtotime('+1 day')));
                }
            }
            curl_close($curl);
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
        if (!array_key_exists($cc, $country_codes)) {
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
