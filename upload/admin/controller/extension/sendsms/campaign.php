<?php
class ControllerExtensionSendSMSCampaign extends Controller
{

    private $error = array();

    public function index()
    {
        $this->load->language('extension/module/sendsms');
        $this->document->setTitle($this->language->get('heading_campaign'));

        $this->load->model('design/layout');
        $data['heading_title'] = $this->language->get('heading_campaign');
        $data['user_token'] = $this->session->data['user_token'];

        # post
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $url = '';
            if (isset($this->request->post['filter_date_start'])) {
                $url .= '&filter_date_start=' . urlencode(html_entity_decode($this->request->post['filter_date_start'], ENT_QUOTES, 'UTF-8'));
            }
            if (isset($this->request->post['filter_date_end'])) {
                $url .= '&filter_date_end=' . urlencode(html_entity_decode($this->request->post['filter_date_end'], ENT_QUOTES, 'UTF-8'));
            }
            if (isset($this->request->post['filter_sum'])) {
                $url .= '&filter_sum=' . urlencode(html_entity_decode($this->request->post['filter_sum'], ENT_QUOTES, 'UTF-8'));
            }
            if (isset($this->request->post['filter_product'])) {
                $forUrlPrds = '';
                foreach ($this->request->post['filter_product'] as $product) {
                    $forUrlPrds .= preg_replace('/\D/', '', $product) . "|";
                }
                if (!empty($forUrlPrds)) {
                    $forUrlPrds = substr($forUrlPrds, 0, -1);
                }
                $url .= '&filter_product=' . urlencode($forUrlPrds);
            }
            if (isset($this->request->post['filter_county'])) {
                $forUrlCtry = '';
                foreach($this->request->post['filter_county'] as $country) {
                    $forUrlCtry .= html_entity_decode($country, ENT_QUOTES, 'UTF-8') . "|";
                }
                if(!empty($forUrlCtry)) {
                    $forUrlCtry = substr($forUrlCtry, 0, -1);
                }
                $url .= '&filter_county=' . urlencode($forUrlCtry);
            }
            $this->response->redirect($this->url->link('extension/sendsms/campaign/filter', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }

        # breadcrumbs
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/sendsms', 'user_token=' . $this->session->data['user_token'], true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_campaign'),
            'href' => $this->url->link('extension/sendsms/campaign', 'user_token=' . $this->session->data['user_token'], true)
        );

        # page links
        $data['history_link'] = $this->url->link('extension/sendsms/history', 'user_token=' . $this->session->data['user_token'], true);
        $data['history_text'] = $this->language->get('text_history');
        $data['about_link'] = $this->url->link('extension/sendsms/about', 'user_token=' . $this->session->data['user_token'], true);
        $data['about_text'] = $this->language->get('text_about');
        $data['campaign_link'] = $this->url->link('extension/sendsms/campaign', 'user_token=' . $this->session->data['user_token'], true);
        $data['campaign_text'] = $this->language->get('text_campaign');
        $data['test_link'] = $this->url->link('extension/sendsms/test', 'user_token=' . $this->session->data['user_token'], true);
        $data['test_text'] = $this->language->get('text_test');

        # texts
        $data['button_check_price'] = $this->language->get('button_check_price');
        $data['button_save'] = $this->language->get('button_filter');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['action'] = $this->url->link('extension/sendsms/campaign', 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel'] = $this->url->link('extension/module/sendsms', 'user_token=' . $this->session->data['user_token'], true);
        $data['heading_filter'] = $this->language->get('heading_filter');
        $data['campaign_date'] = $this->language->get('campaign_date');
        $data['campaign_start_date'] = $this->language->get('campaign_start_date');
        $data['campaign_end_date'] = $this->language->get('campaign_end_date');
        $data['campaign_sum'] = $this->language->get('campaign_sum');
        $data['campaign_product'] = $this->language->get('campaign_product');
        $data['campaign_billing_county'] = $this->language->get('campaign_billing_county');
        $data['campaign_send_all'] = $this->language->get('campaign_send_all');

        # list of products
        $this->load->model('catalog/product');
        $data['products'] = $this->model_catalog_product->getProducts();

        # list of billing counties
        $this->load->model('extension/sendsms/orders');
        $data['counties'] = $this->model_extension_sendsms_orders->getCounties();

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        # common template
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/sendsms/campaign', $data));
    }

    protected function validate()
    {
        if (!$this->user->hasPermission('access', 'sendsms/campaign')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    public function filter()
    {
        $this->load->language('extension/module/sendsms');
        $this->document->setTitle($this->language->get('heading_campaign'));

        $this->load->model('design/layout');
        $data['heading_title'] = $this->language->get('heading_campaign');
        $data['user_token'] = $this->session->data['user_token'];

        # breadcrumbs
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/sendsms', 'user_token=' . $this->session->data['user_token'], true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_campaign'),
            'href' => $this->url->link('extension/sendsms/campaign', 'user_token=' . $this->session->data['user_token'], true)
        );

        # page links
        $data['history_link'] = $this->url->link('extension/sendsms/history', 'user_token=' . $this->session->data['user_token'], true);
        $data['history_text'] = $this->language->get('text_history');
        $data['about_link'] = $this->url->link('extension/sendsms/about', 'user_token=' . $this->session->data['user_token'], true);
        $data['about_text'] = $this->language->get('text_about');
        $data['campaign_link'] = $this->url->link('extension/sendsms/campaign', 'user_token=' . $this->session->data['user_token'], true);
        $data['campaign_text'] = $this->language->get('text_campaign');
        $data['test_link'] = $this->url->link('extension/sendsms/test', 'user_token=' . $this->session->data['user_token'], true);
        $data['test_text'] = $this->language->get('text_test');

        # build url
        $url = '';
        if (isset($this->request->get['filter_date_start'])) {
            $url .= '&filter_date_start=' . urlencode(html_entity_decode($this->request->get['filter_date_start'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['filter_date_end'])) {
            $url .= '&filter_date_end=' . urlencode(html_entity_decode($this->request->get['filter_date_end'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['filter_sum'])) {
            $url .= '&filter_sum=' . urlencode(html_entity_decode($this->request->get['filter_sum'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['filter_product'])) {
            $url .= '&filter_product=' . urlencode(html_entity_decode($this->request->get['filter_product'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['filter_county'])) {
            $url .= '&filter_county=' . urlencode(html_entity_decode($this->request->get['filter_county'], ENT_QUOTES, 'UTF-8'));
        }

        # texts
        $data['button_save'] = $this->language->get('button_send');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['action'] = $this->url->link('extension/sendsms/campaign/filter', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['cancel'] = $this->url->link('extension/sendsms/campaign', 'user_token=' . $this->session->data['user_token'], true);
        $data['heading_filtered'] = $this->language->get('heading_filtered');
        $data['entry_phones'] = $this->language->get('campaign_entry_phones');
        $data['entry_message'] = $this->language->get('test_entry_message');
        $data['entry_characters_left'] = $this->language->get('entry_characters_left');
        $data['campaign_estimate_price'] = $this->language->get('campaign_estimate_price');
        $data['error_send_a_message_first'] = $this->language->get('error_send_a_message_first');
        $data['error_message_required'] = $this->language->get('error_message_required');

        $this->load->model('setting/setting');
        $data['price_per_phone'] = $this->config->get('module_sendsms_price_per_phone');
        # get phone numbers
        $filters = array(
            'filter_date_start' => isset($this->request->get['filter_date_start']) ? $this->request->get['filter_date_start'] : '',
            'filter_date_end' => isset($this->request->get['filter_date_end']) ? $this->request->get['filter_date_end'] : '',
            'filter_sum' => isset($this->request->get['filter_sum']) ? $this->request->get['filter_sum'] : '',
            'filter_product' => isset($this->request->get['filter_product']) ? $this->request->get['filter_product'] : '',
            'filter_county' => isset($this->request->get['filter_county']) ? $this->request->get['filter_county'] : ''
        );
        $this->load->model('extension/sendsms/orders');
        $data['phones'] = $this->model_extension_sendsms_orders->getPhoneNumbers($filters);
        $this->session->data['filters'] = json_encode($filters);
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        # common template
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/sendsms/filtered', $data));
    }

    protected function validate_filtered()
    {
        $error = array();
        if (!$this->user->hasPermission('access', 'sendsms/campaign')) {
            $error[] = $this->language->get('error_permission');
            return $error;
        }

        if (empty($this->request->post['module_sendsms_phones']) && $this->request->post['module_sendsms_all'] === "0") {
            $error[] = $this->language->get('error_phone_required');
            return $error;
        }

        if (empty($this->request->post['module_sendsms_message'])) {
            $error[] = $this->language->get('error_message_required');
            return $error;
        }

        return $error;
    }

    private function randomNumberSequence($requiredLength = 7, $highestDigit = 8)
    {
        $sequence = '';
        for ($i = 0; $i < $requiredLength; ++$i) {
            $sequence .= mt_rand(0, $highestDigit);
        }
        return $sequence;
    }

    public function sendAjaxSMS()
    {
        $this->load->language('extension/module/sendsms');
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $error = $this->validate_filtered();
            if (empty($error)) {
                $phones = array();
                if ($this->request->post['module_sendsms_all'] === "1") {
                    $this->load->model('extension/sendsms/orders');
                    $filters = $this->session->data['filters'];
                    $phones = $this->model_extension_sendsms_orders->getPhoneNumbers($filters);
                    foreach ($phones as &$phone) { //clear the telephone key from the array
                        $phone = $phone['telephone'];
                    }
                } else {
                    $phones = explode("|", $this->request->post['module_sendsms_phones']);
                }
                $simulation = $this->config->get('module_sendsms_simulation');
                if ($simulation) {
                    foreach ($phones as &$phone) { //clear the telephone key from the array
                        $phone = $this->config->get('module_sendsms_simulation_phone');
                    }
                }
                $fileUrl = DIR_UPLOAD . 'batch.csv';
                if ($file = fopen($fileUrl, 'w')) {
                    $this->load->model('setting/setting');
                    $username = $this->config->get('module_sendsms_username');
                    $password = $this->config->get('module_sendsms_password');
                    $from = $this->config->get('module_sendsms_label');
                    if (empty($username)) $error = $this->language->get('error_username_required');
                    if (empty($password)) $error = $this->language->get('error_password_required');
                    if (empty($from)) $error = $this->language->get('error_from_required');
                    if (empty($error)) {
                        $headers = array(
                            "message",
                            "to",
                            "from"
                        );
                        fputcsv($file, $headers);
                        foreach ($phones as &$phone) {
                            fputcsv($file, array(
                                $this->request->post['module_sendsms_message'],
                                $phone,
                                $from
                            ));
                        }
                        $data = 'data=' . file_get_contents($fileUrl);
                        $name = 'OpenCart - ' . HTTP_SERVER . " - " . uniqid();
                        $start_time = '';
                        // $start_time = "2970-01-01 02:00:00";
                        $curl = curl_init();

                        curl_setopt($curl, CURLOPT_HEADER, 0);
                        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

                        curl_setopt($curl, CURLOPT_URL, 'https://api.sendsms.ro/json?action=batch_create&username=' . urlencode($username) . '&password=' . urlencode($password) . '&start_time=' . urlencode($start_time) . '&name=' . urlencode($name));
                        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Connection: keep-alive"));
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                        $result = curl_exec($curl);
                        curl_close($curl);
                        $result = json_decode($result, true);
                        $this->load->model('extension/sendsms/history');
                        $this->model_extension_sendsms_history->addHistory(
                            isset($result['status']) ? $result['status'] : '',
                            isset($result['message']) ? $result['message'] : '',
                            isset($result['details']) ? $result['details'] : '',
                            $this->language->get('campaign_batch_check') . $name,
                            'Batch Campaign',
                            $this->language->get('campaign_go_to_sendsms')
                        );
                        $error = $this->language->get('campaign_batch_created');
                        fclose($file);
                        if (!unlink($fileUrl)) {
                            $error .= ' ' . $this->language->get('error_unable_to_delete_file') . " ($fileUrl)";
                        }
                    }
                } else {
                    $error = $this->language->get('error_unable_to_create_file') . " ($fileUrl)";
                }
            }
        }
        $this->response->setOutput(json_encode($error));
    }
}
