<?php
/*
Plugin Name: BigBoxBerlin ÜberweisungsErinnerer
Plugin URI: https://
Description: Dieses Plugin benutzt die eigen definierten Emails und sendet bei Bedarf Emails. 
Version: 0.1.0
Author: Kevin Fechner
Author URI: https://complete-webolutions.de
Text Domain: bbb-bac-reminder
Domain Path: /languages
 */

if (! defined('ABSPATH')) exit;

define('BBB_BAC_REMINDER_PATH', WP_PLUGIN_DIR . '/bbb-bac-reminder/');
require_once BBB_BAC_REMINDER_PATH . 'class-bac-reminder.php';

register_activation_hook(__FILE__, 'bminder_activation');
/**
 * installs an daily cron job upon activation
 */
function bminder_activation()
{
    if (!wp_next_scheduled('bminder_handler')) {
        wp_schedule_event(time(), 'daily', 'bminder_handler');
        error_log("next event:" . wp_next_scheduled('bminder_handler'));
    }

}
/**
 * daily cron job function
 */
add_action('bminder_handler', 'bminder_daily', 10, 0);
function bminder_daily()
{
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

register_deactivation_hook(__FILE__, 'bbb_bac_reminder_deactivation');
/**
 * uninstalls / removes cron job upon deactivation
 */
function bbb_bac_reminder_deactivation()
{
    error_log("cronjob cleared");
    wp_clear_scheduled_hook('bminder_handler');
}

// $bbbReminder = new BacReminder();
// $bbbReminder->init();
// var_dump($bbbReminder);
