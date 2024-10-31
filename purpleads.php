<?php
/*
Plugin Name: PurpleAds Ads.txt Manager
Version: 1.0.1
Author: PurpleAds LTD
Author URI: https://purpleads.io
License: GPLv3 or later
Description: Effortlessly manage and maintain your website's ads.txt file with PurpleAds Adstxt Manager.
Text Domain: purpleads-adstxt-plugin
*/

if (!defined('ABSPATH')) {
    die;
}

add_action('admin_menu', 'purpleads_ads_txt_menu');
add_action('admin_enqueue_scripts', 'purpleads_txt_enqueue_admin_styles');


function purpleads_ads_txt_menu() {
	add_menu_page( 'Ads.txt Manager', 'Ads.txt Manager', 'manage_options', 'purpleads-ads-txt-manager', 'purpleads_ads_txt_display', 'dashicons-purpleads');
}


function purpleads_txt_enqueue_admin_styles()
{
    wp_enqueue_style('purpleads_admin_style', plugin_dir_url(__FILE__).'style.css');
    wp_register_style('dashicons-purpleads', plugin_dir_url(__FILE__).'/assets/css/dashicons-purpleads.css');
    wp_enqueue_style('dashicons-purpleads');
}

function purpleads_ads_txt_display() {
	$ads_txt_file = ABSPATH . 'ads.txt';
	if (!file_exists($ads_txt_file)) {
		file_put_contents($ads_txt_file, '');
	}
	$ads_txt_file_url = home_url('/ads.txt');
	$ads_txt_content = file_get_contents($ads_txt_file);
	?>
	<script src="<?php echo esc_url("https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.js"); ?>" integrity="sha512-8RnEqURPUc5aqFEN04aQEiPlSAdE0jlFS/9iGgUyNtwFnSKCXhmB6ZTNl7LnDtDWKabJIASzXrzD0K+LYexU9g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<link rel="stylesheet" href="<?php echo esc_url("https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.css"); ?>" integrity="sha512-uf06llspW44/LZpHzHT6qBOIVODjWtv4MxCricRxkzvopAlSWnTf6hpZTFxuuZcuNE9CBQhqE0Seu1CoRk84nQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<div class="wrap" style='padding:10px; background-color:white; width:50% '>
	<div style='text-align: center;'>
	<img class="top-logo"  style='width:50%' src="<?php echo plugin_dir_url(__FILE__).'assets/banner-md.png'; ?>">
	</div>
	<?php if (isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true'): ?>
        <div class="notice notice-success is-dismissible">
            <p><strong><?php _e('Adstxt File Updated', 'purpleads-adstxt-plugin'); ?></strong></p>
        </div>
        <?php endif; ?>
		<p style='font-size:15px'>
		This plugin provides an easy interface to add or remove ads.txt lines from your website, your ads.txt file can be found here: <a style='text-decoration: none' target="_blank" href="<?php echo esc_url($ads_txt_file_url); ?>"><?php echo esc_url($ads_txt_file_url); ?></a><br>
		</p>
		<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
			<?php wp_nonce_field('purpleads_ads_txt_save', 'purpleads_ads_txt_nonce'); ?>
			<textarea style='color:blue' id="purpleads_ads_txt_editor" name="purpleads_ads_txt_content"><?php echo esc_textarea($ads_txt_content); ?></textarea>
			<br>
			<input type="hidden" name="action" value="purpleads_ads_txt_save">
			<input type="submit" value="Save" name='save' style='background-color:#7434ec' class="button button-primary">
		</form>
		<p style='font-style:italic; margin-top:25px'>
		Ads.txt manager plugin is built and maintained by <a target="_blank" style='text-decoration: none' href="<?php echo esc_url('https://purpleads.io/'); ?>">PurpleAds</a>,<br> an ad network built for publishers - monetizing websites with premium demand partners and convenient payout terms.
		</p>
	</div>
	<script>
		var purpleads_ads_txt_editor = CodeMirror.fromTextArea(document.getElementById("purpleads_ads_txt_editor"), {
			mode: "apache",
			lineNumbers: true,
			lineWrapping: true,
			theme: "dark",
			viewportMargin: Infinity,
		});
		purpleads_ads_txt_editor.setSize(null, 600);
	</script>
	<?php
}

function purpleads_ads_txt_save() {
    if (isset($_POST['purpleads_ads_txt_nonce']) && wp_verify_nonce($_POST['purpleads_ads_txt_nonce'], 'purpleads_ads_txt_save')) {
        $ads_txt_file = ABSPATH . 'ads.txt';
        if (isset($_POST['purpleads_ads_txt_content'])) {
            $ads_txt_content = stripslashes($_POST['purpleads_ads_txt_content']);
            file_put_contents($ads_txt_file, $ads_txt_content);
            wp_redirect(admin_url('admin.php?page=purpleads-ads-txt-manager&settings-updated=true'));
            exit();
        }
    }
}
add_action('admin_post_purpleads_ads_txt_save', 'purpleads_ads_txt_save');

