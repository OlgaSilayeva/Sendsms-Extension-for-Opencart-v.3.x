<?php
class ModelExtensionSendsmsOrders extends Model
{
    public function getCounties()
    {
        $sql = 'SELECT DISTINCT payment_zone FROM ' . DB_PREFIX . 'order ORDER BY payment_zone ASC';
        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function getPhoneNumbers($data = array())
    {
        $sql = "SELECT DISTINCT o.telephone FROM " . DB_PREFIX . "order AS o, " . DB_PREFIX . "order_product AS op WHERE op.order_id = o.order_id";

        if (isset($data['filter_date_start']) && !empty($data['filter_date_start'])) {
            $sql .= " AND o.date_added >= '" . $this->db->escape($data['filter_date_start']) . " 00:00:00'";
        }
        if (isset($data['filter_date_end']) && !empty($data['filter_date_end'])) {
            $sql .= " AND o.date_added <= '" . $this->db->escape($data['filter_date_end']) . " 23:59:59'";
        }
        if (isset($data['filter_sum']) && !empty($data['filter_sum'])) {
            $sql .= " AND o.total*o.currency_value >= '" . $this->db->escape($data['filter_sum']) . "'";
        }
        if (isset($data['filter_county']) && !empty($data['filter_county'])) {
            $in = '';
            foreach (explode('|', $data['filter_county']) as $country) {
                $in .= '\'' . $this->db->escape($country) . '\', ';
            }
            if (!empty($in)) {
                $in = substr($in, 0, -2);
            }
            $sql .= " AND o.payment_zone IN (" . $in . ")";
        }
        if (isset($data['filter_product']) && !empty($data['filter_product'])) {
            $in = '';
            foreach (explode('|', $data['filter_product']) as $product) {
                $in .= '\'' . $this->db->escape($product) . '\', ';
            }
            if (!empty($in)) {
                $in = substr($in, 0, -2);
            }
            $sql .= " AND op.product_id IN (" . $in . ")";
        }

        $query = $this->db->query($sql);

        $this->load->model('extension/sendsms/send');
        $rows = $query->rows;
        foreach ($rows as &$row) {
            $row['telephone'] = $this->model_extension_sendsms_send->validatePhone($row['telephone']);
        }
        return $rows;
    }
}
