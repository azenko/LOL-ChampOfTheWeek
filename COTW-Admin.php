<?php
// create custom plugin settings menu
add_action('admin_menu', 'COTW_create_menu');

function COTW_create_menu() {

    //create new top-level menu
    add_menu_page('LOL Champions Of The Weeks Plugin Settings', 'LOL-COTW Settings', 'administrator', __FILE__, 'COTW_Admin_Page',plugins_url('/images/icon.png', __FILE__));

    //call register settings function
    add_action( 'admin_init', 'COTW_register_settings' );
}


function COTW_register_settings() {
    //register our settings
    register_setting( 'cotw-settings-group', 'cotw_api_key' );
    register_setting( 'cotw-settings-group', 'cotw_srv_region' );
    register_setting( 'cotw-settings-group', 'cotw_api_url' );
    register_setting( 'cotw-settings-group', 'cotw_cdn_version' );
}

function COTW_Admin_Page() {
?>
<div class="wrap">
<h2>LOL Champions Of The Weeks</h2>

<form method="post" action="options.php">
    <?php settings_fields( 'cotw-settings-group' ); ?>
    <?php do_settings_sections( 'cotw-settings-group' ); ?>
    <table class="form-table">
        <tr valign="top">
        <th scope="row">Your API Key</th>
        <td><input type="text" name="cotw_api_key" value="<?php echo get_option('cotw_api_key'); ?>" /></td>
        </tr>
        <tr valign="top">
        <th scope="row">Server Region</th>
            <td>
                <select name="cotw_srv_region">
                    <option value="br" <?php if(get_option('cotw_srv_region') == "br"){echo "selected";} ?>>Brazil</option>
                    <option value="eune" <?php if(get_option('cotw_srv_region') == "eune"){echo "selected";} ?>>EU Nordic & East</option>
                    <option value="euw" <?php if(get_option('cotw_srv_region') == "euw"){echo "selected";} ?>>EU West</option>
                    <option value="lan" <?php if(get_option('cotw_srv_region') == "lan"){echo "selected";} ?>>Latin America North</option>
                    <option value="las" <?php if(get_option('cotw_srv_region') == "las"){echo "selected";} ?>>Latin America South</option>
                    <option value="na" <?php if(get_option('cotw_srv_region') == "na"){echo "selected";} ?>>North America</option>
                    <option value="oce" <?php if(get_option('cotw_srv_region') == "oce"){echo "selected";} ?>>Oceania</option>
                </select>
            </td>
        </tr>
        <tr valign="top">
        <th scope="row">API URL</th>
        <td><input type="text" name="cotw_api_url" value="<?php echo get_option('cotw_api_url'); ?>"/> By Default : http://prod.api.pvp.net/</td>
        </tr>
        
        <tr valign="top">
        <th scope="row">DDragon CDN Version</th>
        <td><input type="text" name="cotw_cdn_version" value="<?php echo get_option('cotw_cdn_version'); ?>" /> Found It On : http://ddragon.leagueoflegends.com/tool/ - Number after dragontail in the head bar. Like 4.4.3</td>
        </tr>
    </table>
    
    <?php submit_button(); ?>

</form>
</div>
<?php } ?>