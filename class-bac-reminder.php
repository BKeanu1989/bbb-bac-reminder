<?php
require_once(BBB_BAC_REMINDER_PATH . 'class-bac-reminder-resend-emails.php');
// require_once(BBB_BAC_REMINDER_PATH . 'class-bac-reminder-update-status.php');

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

    /**
     * sets options and if not set, set default values
     * 
     * sets dates for send reminder emails & orders to update date
     */
    public function __construct() 
    {
        $this->days_to_send_reminder = get_option('bminder_days_to_send_reminder', 10);
        // $this->days_to_update_status = get_option('bminder_days_to_update_status', 14);
        $this->today = $this->modify_date();
        $this->date_to_send_reminder = $this->modify_date("-{$this->days_to_send_reminder} days");
        // $this->date_to_update_status = $this->modify_date("-{$this->days_to_update_status} days");
    }
    /**
     * inits main work
     * 1. update orders
     * 2. send reminder email for remaining orders
     * 
     * @uses handle_orders_to_update, handle_orders_to_resend_email
     * @return void
     */
    public function init() 
    {
        // $this->handle_orders_to_update();
        $this->handle_orders_to_resend_email();
    }

    /**
     * updates orders
     * @uses BacReminderUpdateStatus
     */
    public function handle_orders_to_update()
    {
        // $orders_to_update = new BacReminderUpdateStatus($this->date_to_update_status);
        // $orders_to_update->init();
    }
    /**
     * sends reminder emails
     * 
     * @uses BacReminderResendEmails
     */
    public function handle_orders_to_resend_email()
    {
        $orders_to_resend_emails = new BacReminderResendEmails($this->date_to_send_reminder);
        $orders_to_resend_emails->init();
    }
}

