<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if (rgpost("uninstall")) {
	check_admin_referer("uninstall", "gf_payro24_uninstall");
	self::uninstall();
	echo '<div class="updated fade" style="padding:20px;">' . __("درگاه با موفقیت غیرفعال شد و اطلاعات مربوط به آن نیز از بین رفت برای فعالسازی مجدد میتوانید از طریق افزونه های وردپرس اقدام نمایید .", "gravityformspayro24") . '</div>';

	return;
} else if (isset($_POST["gf_payro24_submit"])) {

	check_admin_referer("update", "gf_payro24_update");
	$settings = array(
		"gname" => rgpost('gf_payro24_gname'),
		"api_key" => rgpost('gf_payro24_api_key'),
		"sandbox" => rgpost('gf_payro24_sandbox'),
	);
	update_option("gf_payro24_settings", array_map('sanitize_text_field', $settings));
	if (isset($_POST["gf_payro24_configured"])) {
		update_option("gf_payro24_configured", sanitize_text_field($_POST["gf_payro24_configured"]));
	} else {
		delete_option("gf_payro24_configured");
	}
} else {
	$settings = get_option("gf_payro24_settings");
}

if (!empty($_POST)) {
	echo '<div class="updated fade" style="padding:6px">' . __("تنظیمات ذخیره شدند .", "gravityformspayro24") . '</div>';
} else if (isset($_GET['subview']) && $_GET['subview'] == 'gf_payro24' && isset($_GET['updated'])) {
	echo '<div class="updated fade" style="padding:6px">' . __("تنظیمات ذخیره شدند .", "gravityformspayro24") . '</div>';
}
?>

<form action="" method="post">
	<?php wp_nonce_field("update", "gf_payro24_update") ?>
	<h3>
		<span>
			<i class="fa fa-credit-card"></i>
			<?php _e("تنظیمات payro24", "gravityformspayro24") ?>
		</span>
	</h3>
	<table class="form-table">
		<tr>
			<th scope="row"><label
					for="gf_payro24_configured"><?php _e("فعالسازی", "gravityformspayro24"); ?></label>
			</th>
			<td>
				<input type="checkbox" name="gf_payro24_configured"
					   id="gf_payro24_configured" <?php echo get_option("gf_payro24_configured") ? "checked='checked'" : "" ?>/>
				<label class="inline"
					   for="gf_payro24_configured"><?php _e("بله", "gravityformspayro24"); ?></label>
			</td>
		</tr>
		<?php
		$gateway_title = __("payro24", "gravityformspayro24");
		if (sanitize_text_field(rgar($settings, 'gname'))) {
			$gateway_title = sanitize_text_field($settings["gname"]);
		}
		?>
		<tr>
			<th scope="row">
				<label for="gf_payro24_gname">
					<?php _e("عنوان", "gravityformspayro24"); ?>
					<?php gform_tooltip('gateway_name') ?>
				</label>
			</th>
			<td>
				<input style="width:350px;" type="text" id="gf_payro24_gname" name="gf_payro24_gname"
					   value="<?php echo $gateway_title; ?>"/>
			</td>
		</tr>
		<tr>
			<th scope="row"><label
					for="gf_payro24_api_key"><?php _e("API KEY", "gravityformspayro24"); ?></label></th>
			<td>
				<input style="width:350px;text-align:left;direction:ltr !important" type="text"
					   id="gf_payro24_api_key" name="gf_payro24_api_key"
					   value="<?php echo sanitize_text_field(rgar($settings, 'api_key')) ?>"/>
			</td>
		</tr>
		<tr>
			<th scope="row"><label
					for="gf_payro24_sandbox"><?php _e("آزمایشگاه", "gravityformspayro24"); ?></label>
			</th>
			<td>
				<input type="checkbox" name="gf_payro24_sandbox"
					   id="gf_payro24_sandbox" <?php echo rgar($settings, 'sandbox') ? "checked='checked'" : "" ?>/>
				<label class="inline"
					   for="gf_payro24_sandbox"><?php _e("بله", "gravityformspayro24"); ?></label>
			</td>
		</tr>
		<tr>
			<td colspan="2"><input style="font-family:tahoma !important;" type="submit"
								   name="gf_payro24_submit" class="button-primary"
								   value="<?php _e("ذخیره تنظیمات", "gravityformspayro24") ?>"/></td>
		</tr>
	</table>
</form>
<form action="" method="post">
	<?php
	wp_nonce_field("uninstall", "gf_payro24_uninstall");
	if (self::has_access("gravityforms_payro24_uninstall")) {
		?>
		<div class="hr-divider"></div>
		<div class="delete-alert alert_red">
			<h3>
				<i class="fa fa-exclamation-triangle gf_invalid"></i>
				<?php _e("غیر فعالسازی افزونه دروازه پرداخت payro24", "gravityformspayro24"); ?>
			</h3>
			<div
				class="gf_delete_notice"><?php _e("تذکر : بعد از غیرفعالسازی تمامی اطلاعات مربوط به payro24 حذف خواهد شد", "gravityformspayro24") ?></div>
			<?php
			$uninstall_button = '<input  style="font-family:tahoma !important;" type="submit" name="uninstall" value="' . __("غیر فعال سازی درگاه payro24", "gravityformspayro24") . '" class="button" onclick="return confirm(\'' . __("تذکر : بعد از غیرفعالسازی تمامی اطلاعات مربوط به payro24 حذف خواهد شد . آیا همچنان مایل به غیر فعالسازی میباشید؟", "gravityformspayro24") . '\');"/>';
			echo apply_filters("gform_payro24_uninstall_button", $uninstall_button);
			?>
		</div>
	<?php } ?>
</form>