<?php

if (!defined('ABSPATH')) {
    exit;
}
$bminder_days_to_send_reminder = get_option('bminder_days_to_send_reminder', 10);

?>

<form action="<?php echo home_url() ?>/wp-admin/admin-post.php" method="POST">
    <input type="hidden" name="action" value="bbb_reminder_options">

    <div>
        <label for="days_to_send_reminder"><?php _e('Tage bis zur Erinnerungsmail:'); ?></label>
        <input type="number" name="bminder_days_to_send_reminder" id="days_to_send_reminder" value="<?php echo $bminder_days_to_send_reminder; ?>">
    </div>

    <!-- <div>
        <label for="days_to_update_status"><?php // _e('Tage bis zur automatischen Status Änderung:') ?></label>
        <input type="number" name="bminder_days_to_update_status" id="days_to_update_status" value="<?php // echo $bminder_days_to_update_status; ?>">
    </div> -->


    <?php submit_button(); ?>
</form>