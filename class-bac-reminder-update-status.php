<?php

class BacReminderUpdateStatus {
    /**
     * sets an array of orders_to_update
     * @param string $date_to_update_status | formatted string date
     */
    public function __construct($date_to_update_status)
    {
        global $wpdb;

        $this->date_to_update_status = $date_to_update_status;
        $this->orders_to_update = $wpdb->get_results($wpdb->prepare("SELECT ID FROM {$wpdb->prefix}posts WHERE post_status = 'wc-on-hold' AND DATE(post_date) < %s", $this->date_to_update_status), ARRAY_A);
        $this->orders_to_update = array_column($this->orders_to_update, 'ID');
    }

    /**
     * inits the main handler
     * foreach order_id in orders_to_update array -> call handle_main
     * @uses handle_main
     */
    public function init() 
    {
        foreach ($this->orders_to_update as $order_id) {
            // $this->handle_main($order_id)
        }
    }

    /**
     * handles necessary function calls per $order_id
     * @param int $order_id
     */
    public function handle_main($order_id)
    {
        $order_id = (int) $order_id;

        $_order = new WC_Order($order_id);
        $success = $this->update_status($_order);
    }
    /**
     * updates an order with a comment
     * @param object $_order | order object of wc
     */
    public function update_status($_order)
    {
        $note = __('Status via reminder plugin automatisch geändert', 'bbb-bac-reminder');

        return $_order->update_status('wc-cancelled', $note);
    }
}