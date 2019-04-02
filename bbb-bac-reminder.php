<?php
/*
Plugin Name: BigBoxBerlin ÜberweisungsErinnerer
Plugin URI: https://
Description: BigBoxBerlin ÜberweisungsErinnerer
Version: 0.1.0
Author: Kevin Fechner
Author URI: https://complete-webolutions.de
Text Domain: bbb-bac-reminder
Domain Path: /languages
 */

if (! defined('ABSPATH')) exit;

define('BBB_BAC_REMINDER_PATH', WP_PLUGIN_DIR . '/bbb-bac-reminder/');

register_activation_hook(__FILE__, 'bminder_activation');
function bminderh_activation()
{
    if (!wp_next_scheduled('bminder_handler')) {
        wp_schedule_event(time(), 'hourly', 'bminder_handler');
        error_log("next event:" . wp_next_scheduled('bminder_handler'));
    }

}

add_action('bminder_handler', 'bminder_daily', 10, 0);
function bminder_daily()
{
    require_once BBB_BAC_REMINDER_PATH . 'class-bac-reminder.php';
    $bbbReminder = new BacReminder();
    $bbbReminder->init();

}

add_action('admin_menu', 'bminder_option');
function bminder_option()
{
    add_options_page(
        'Bac Reminder Options',
        'BBB Bac Reminder',
        'manage_options',
        'bbb-bac-reminder.php',
        'bbb_bac_reminder_options'
    );
}

function bbb_bac_reminder_options()
{
    require_once(BBB_BAC_REMINDER_PATH . 'options-page.php');
}


add_action('admin_post_bbb_reminder_options', 'bbb_reminder_options');
function bbb_reminder_options()
{
    $days_to_send_reminder = isset($_POST['bminder_days_to_send_reminder']) ? (int) $_POST['bminder_days_to_send_reminder'] : 7;
    $days_to_update_status = isset($_POST['bminder_days_to_update_status']) ? (int) $_POST['bminder_days_to_update_status'] : 14;

    if (false === add_option('bminder_days_to_send_reminder', $days_to_send_reminder, '', 'no')) {
        update_option('bminder_days_to_send_reminder', $days_to_send_reminder, 'no');
    }

    if (false === add_option('bminder_days_to_update_status', $days_to_update_status, '', 'no')) {
        update_option('bminder_days_to_update_status', $days_to_update_status, 'no');
    }

    if (wp_safe_redirect($_SERVER['HTTP_REFERER'])) {
        exit;
    }
}

// add settings
// time for resending email
// time for order status update