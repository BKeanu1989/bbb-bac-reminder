<?php
require_once(BBB_BAC_REMINDER_PATH . 'class-bac-reminder-resend-emails.php');
require_once(BBB_BAC_REMINDER_PATH . 'class-bac-reminder-update-status.php');

trait Time_Helpers
{
    public function modify_date(string $modifcation = '', string $start = '')
    {
        if (empty($start)) {
            $start = date("Y-m-d");
        }
        $date = new DateTime($start);
        if (!empty($modifcation)) {
            $date->modify($modifcation);
        }
        $date = $date->format("Y-m-d");
        return $date;
    }
}

class BacReminder {
    use Time_Helpers;
    public function __construct() 
    {
        $this->days_to_send_reminder = get_option('bminder_days_to_send_reminder', 7);
        $this->days_to_update_status = get_option('bminder_days_to_update_status', 14);
        $this->today = $this->modify_date();
        $this->date_to_send_reminder = $this->modify_date("-{$this->days_to_send_reminder} days");
        $this->date_to_update_status = $this->modify_date("-{$this->days_to_update_status} days");
    }

    public function init() 
    {
        $this->handle_orders_to_update();
        $this->handle_orders_to_resend_email();
    }
    
    public function handle_orders_to_update()
    {
        $orders_to_update = new BacReminderUpdatestatus();
        var_dump($orders_to_update);
    }
    
    public function handle_orders_to_resend_email()
    {
        $orders_to_resend_emails = new BacReminderResendEmails();
        var_dump($orders_to_resend_emails);
        global $wpdb;
        $this->orders_to_resend_email = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}posts WHERE post_status = 'wc-on-hold' AND DATE(post_date) < %s", $this->date_to_send_reminder));

    }

    // resend email

    // order_comment_if_resend

    
}

