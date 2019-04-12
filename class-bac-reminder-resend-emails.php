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
        $this->orders_to_resend_email = $wpdb->get_results($wpdb->prepare("SELECT ID FROM {$wpdb->prefix}posts WHERE DATE(post_date) < %s AND post_type = 'shop_order' AND post_status = 'wc-on-hold'", $this->date), ARRAY_A);
        $this->orders_to_resend_email = array_column($this->orders_to_resend_email, 'ID');
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
        $order_id = (int) $order_id;
        $_order = new WC_Order($order_id);

        $success = $this->resend_email($_order);

        $this->add_comment($_order);
        $this->add_email_sent_meta($_order);
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