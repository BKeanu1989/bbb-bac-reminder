<?php

class BacReminderResendEmails {
    public function __construct($date)
    {
        global $wpdb;

        $this->date = $date;
        $this->orders_to_resend_email = $wpdb->get_results($wpdb->prepare("SELECT ID FROM {$wpdb->prefix}posts WHERE DATE(post_date) < %s AND post_type = 'shop_order' AND post_status = 'wc-on-hold'", $this->date), ARRAY_A);
        $this->orders_to_resend_email = array_column($this->orders_to_resend_email, 'ID');
    }

    public function init() 
    {
        foreach ($this->orders_to_resend_email as $order_id) {
            $this->handle_main($order_id);
        }
    }

    public function handle_main($order_id)
    {
        $order_id = (int) $order_id;
        $_order = new WC_Order($order_id);
// wc_get_order($order_id);
        $success = $this->resend_email($_order);

        $this->add_comment($_order);
    }

    public function resend_email($order)
    {
        $wc_emails = WC()->mailer()->get_emails();
        if (empty($wc_emails)) return;

        $email_id = 'wc_customer_reminder_order';

        foreach($wc_emails AS $wc_mail) {
            if ($wc_mail->id === $email_id) {
                // correct email
                // $wc_mail->trigger($order->get_id());
            }
        }
    }

    public function add_comment($order)
    {
        $note = __('Erinnerungsmail gesendet');
        // $order->add_order_note($note);
    }
}