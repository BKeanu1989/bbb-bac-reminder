<?php

class BacReminderUpdatestatus {
    public function __construct($date_to_update_status)
    {
        global $wpdb;

        $this->date_to_update_status = $date_to_update_status;
        $this->orders_to_update = $wpdb->get_results($wpdb->prepare("SELECT ID FROM {$wpdb->prefix}posts WHERE post_status = 'wc-on-hold' AND DATE(post_date) < %s", $this->date_to_update_status), ARRAY_A);
        $this->orders_to_update = array_column($this->orders_to_update, 'ID');
    }

    public function init() 
    {
        var_dump($this->orders_to_update);
    }

    public function handle_main($order_id)
    {
        $order_id = (int) $order_id;

        $_order = new WC_Order($order_id);
        $success = $this->update_status($_order);
    }

    public function update_status($_order)
    {
        $note = __('Status automatisch geändert');

        return $_order->update_status('wc-cancelled', $note);
    }
}