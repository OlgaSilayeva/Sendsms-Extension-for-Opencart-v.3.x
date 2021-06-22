<?php
class ModelExtensionSendSMSUser extends Model
{
    public function get_balance()
    {
        $this->load->model('setting/setting');
        $username = urlencode($this->config->get('module_sendsms_username'));
        $password = urlencode($this->config->get('module_sendsms_password'));
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_HEADER, 1);
        curl_setopt($curl, CURLOPT_URL, "https://api.sendsms.ro/json?action=user_get_balance&username=$username&password=$password");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Connection: keep-alive"));

        $result = curl_exec($curl);

        $size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);

        $result = json_decode(substr($result, $size));

        if ($result->status >= 0)
            return $result;
        return false;
    }
}
