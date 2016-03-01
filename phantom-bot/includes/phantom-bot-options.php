<?php

function phantom_bot_admin_add_page()
{
    add_options_page('Phantom Bot', 'Phantom Bot', 'manage_options', 'phantom_bot', 'phantom_bot_options_page');
}

add_action('admin_menu', 'phantom_bot_admin_add_page');

function phantom_bot_options_page()
{

    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    $phantom_bot_ip = 'phantom_bot_ip';
    $phantom_bot_ip_data = 'phantom_bot_ip';

    $phantom_bot_oauth = 'phantom_bot_oauth';
    $phantom_bot_oauth_data = 'phantom_bot_oauth';

    $phantom_bot_banned = 'phantom_bot_banned';
    $phantom_bot_banned_data = 'phantom_bot_banned';

    $hidden_field_name = 'mt_submit_hidden';

    $opt_ip = get_option($phantom_bot_ip);
    $opt_oauth = get_option($phantom_bot_oauth);
    $opt_banned = get_option($phantom_bot_banned);


    if (isset($_POST[$hidden_field_name]) && $_POST[$hidden_field_name] == 'Y') {
        $opt_ip = $_POST[$phantom_bot_ip_data];
        $opt_oauth = $_POST[$phantom_bot_oauth_data];
        $opt_banned = $_POST[$phantom_bot_banned];
        update_option($phantom_bot_ip, $opt_ip);
        update_option($phantom_bot_oauth, $opt_oauth);
        update_option($phantom_bot_banned, $opt_banned);
        ?>
        <div class="updated"><p><strong><?php _e('settings saved.', 'phantom_bot'); ?></strong></p></div>
    <?php
    }

    echo '<div class="wrap">';
    echo "<h2>" . __('Phantom Bot Plugin Settings', 'phantom_bot') . "</h2>";

    ?>

<form name="form1" method="post" action="">
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

<p><?php _e("Phantom Bot IP: ", 'phantom_bot'); ?>
<input type="text" name="<?php echo $phantom_bot_ip_data; ?>" value="<?php echo $opt_ip; ?>" size="20">
</p>
<p><?php _e("Phantom Bot oauth: ", 'phantom_bot'); ?>
<input type="text" name="<?php echo $phantom_bot_oauth_data; ?>" value="<?php echo $opt_oauth; ?>" size="50">
</p>
<p><?php _e("Banned Users (user1,user2): ", 'phantom_bot'); ?>
<input type="text" name="<?php echo $phantom_bot_banned_data; ?>" value="<?php echo $opt_banned; ?>" size="50">
</p><hr />

<p class="submit">
<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
</p>

</form>
</div>

<?php

}