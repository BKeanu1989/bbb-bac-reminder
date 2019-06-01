<?php

class BacReminderResendEmails {
    /**
     * sets an array of orders_to_resend_email
     * 
     * @param string $date formatted date string
     */
    public function __construct($date)
    {
        global $wpdb;

        $this->date = $date;
        $today_string = date('Y-m-d');
        $this->todayObject = new DateTime($today_string);
        // could also be realized with get_option (activation hook)... now hardcoding
        $uploaded_date = '2019-05-09';
        // testing
        $this->orders_to_resend_email = $wpdb->get_col($wpdb->prepare("SELECT ID FROM {$wpdb->prefix}posts WHERE DATE(post_date) > %s AND post_type = 'shop_order' AND post_status = 'wc-on-hold'", '2019-04-01'));
        // $this->orders_to_resend_email = $wpdb->get_col($wpdb->prepare("SELECT ID FROM {$wpdb->prefix}posts WHERE DATE(post_date) > %s AND post_type = 'shop_order' AND post_status = 'wc-on-hold'", $uploaded_date));

        // $this->orders_to_resend_email = array_column($this->orders_to_resend_email, 'ID');
    }
    /**
     * inits the resend email handler
     * foreach order_id call handle_main(order_id)
     * 
     * 
     * @uses handle_main
     * @return void
     */
    public function init() 
    {
        foreach ($this->orders_to_resend_email as $order_id) {
            $reminder_sent = get_post_meta($order_id, 'reminder_email_sent', true);
            if (empty($reminder_sent) || $reminder_sent === 'no') {
                $this->handle_main($order_id);
            }
        }
    }
    /**
     * does all the main work for an order_id
     * @param int $order_id
     * @uses resend_email, add_comment, add_email_sent_meta
     */
    public function handle_main($order_id)
    {
        try {
            $resend_email = false;
            $order_id = (int) $order_id;
            if (!$order_id) return;
            $_order = wc_get_order($order_id);

            $order_items = $_order->get_items();

            foreach($order_items AS $item_id => $item) {
                $_product = $item->get_product();
                if ($_product->is_type('variation')) {
                    $_product = wc_get_product($_product->get_parent_id());
                }

                $festivalStart_String = get_post_meta($_product->get_id(), '_festival_start', true);
                $festivalStart_Object = new DateTime($festivalStart_String);

                $festivalStart_Object->modify('-10 days');

                if ($this->todayObject > $festivalStart_Object) {
                    $resend_email = true;
                }
            }
            
            if ($resend_email) {
                $success = $this->resend_email($_order);
                $this->add_comment($_order);
                $this->add_email_sent_meta($_order);
            } 
        } catch (Error $error) {
            error_log(print_r($error, 1));
        }
    }

    /**
     * triggers reminder email for given order
     * @param object $order
     */
    public function resend_email($order)
    {
        $wc_emails = WC()->mailer()->get_emails();
        if (empty($wc_emails)) return;

        $email_id = 'wc_customer_reminder_order';

        foreach($wc_emails AS $wc_mail) {
            if ($wc_mail->id === $email_id) {
                // correct email
                $wc_mail->trigger($order->get_id());
            }
        }
    }

    /**
     * adds an order note after sending an email
     * @param object $order
     */
    public function add_comment($order)
    {
        $note = __('Erinnerungsmail gesendet', 'bbb-bac-reminder');
        $order->add_order_note($note);
    }

    /**
     * adds 'reminder_email_sent' meta to an order
     * @param object $order
     */
    public function add_email_sent_meta($order) 
    {
        $order_id = $order->get_id();
        if (!add_post_meta($order_id, 'reminder_email_sent', 'yes', true)) {
            update_post_meta($order_id, 'reminder_email_sent', 'yes');
        }
    }
}