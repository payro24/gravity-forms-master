<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if (!self::is_gravityforms_supported()) {
	die(sprintf(__("درگاه payro24 نیاز به گرویتی فرم نسخه %s دارد. برای بروز رسانی هسته گرویتی فرم به %sسایت گرویتی فرم فارسی%s مراجعه نمایید .", "gravityformspayro24"), self::$min_gravityforms_version, "<a href='http://gravityforms.ir/11378' target='_blank'>", "</a>"));
}

if (rgpost('action') == "delete") {
	check_admin_referer("list_action", "gf_payro24_list");
	$id = absint(rgpost("action_argument"));
	payro24_DB::delete_feed($id);
	?>
	<div class="updated fade" style="padding:6px"><?php _e("فید حذف شد", "gravityformspayro24") ?></div><?php
} else if (!empty($_POST["bulk_action"])) {

	check_admin_referer("list_action", "gf_payro24_list");
	$selected_feeds = rgpost("feed");
	if (is_array($selected_feeds)) {
		foreach ($selected_feeds as $feed_id) {
			payro24_DB::delete_feed($feed_id);
		}
	}

	?>
	<div class="updated fade" style="padding:6px"><?php _e("فید ها حذف شدند", "gravityformspayro24") ?></div>
	<?php
}
?>
<div class="wrap">

	<?php if ($arg != 'per-form') { ?>

		<h2>
			<?php _e("فرم های payro24", "gravityformspayro24");
			if (get_option("gf_payro24_configured")) { ?>
				<a class="add-new-h2"
				   href="admin.php?page=gf_payro24&view=edit"><?php _e("افزودن جدید", "gravityformspayro24") ?></a>
				<?php
			} ?>
		</h2>

	<?php } ?>

	<form id="confirmation_list_form" method="post">
		<?php wp_nonce_field('list_action', 'gf_payro24_list') ?>
		<input type="hidden" id="action" name="action"/>
		<input type="hidden" id="action_argument" name="action_argument"/>
		<div class="tablenav">
			<div class="alignleft actions" style="padding:8px 0 7px 0;">
				<label class="hidden"
					   for="bulk_action"><?php _e("اقدام دسته جمعی", "gravityformspayro24") ?></label>
				<select name="bulk_action" id="bulk_action">
					<option value=''> <?php _e("اقدامات دسته جمعی", "gravityformspayro24") ?> </option>
					<option value='delete'><?php _e("حذف", "gravityformspayro24") ?></option>
				</select>
				<?php
				echo '<input type="submit" class="button" value="' . __("اعمال", "gravityformspayro24") . '" onclick="if( jQuery(\'#bulk_action\').val() == \'delete\' && !confirm(\'' . __("فید حذف شود ؟ ", "gravityformspayro24") . __("\'Cancel\' برای منصرف شدن, \'OK\' برای حذف کردن", "gravityformspayro24") . '\')) { return false; } return true;"/>';
				?>
				<a class="button button-primary" style="text-align: center;display: inline-block;margin: 0;"
				   href="admin.php?page=gf_settings&subview=gf_payro24"><?php _e('تنظیمات payro24', 'gravityformspayro24') ?></a>
			</div>
		</div>
		<table class="wp-list-table widefat fixed striped toplevel_page_gf_edit_forms" cellspacing="0">
			<thead>
			<tr>
				<th scope="col" id="cb" class="manage-column column-cb check-column"
					style="padding:13px 3px;width:30px"><input type="checkbox"/></th>
				<th scope="col" id="active" class="manage-column"
					style="width:<?php echo $arg != 'per-form' ? '50px' : '20px' ?>"><?php echo $arg != 'per-form' ? __('وضعیت', 'gravityformspayro24') : '' ?></th>
				<th scope="col" class="manage-column"
					style="width:<?php echo $arg != 'per-form' ? '65px' : '30%' ?>"><?php _e(" آیدی فید", "gravityformspayro24") ?></th>
				<?php if ($arg != 'per-form') { ?>
					<th scope="col"
						class="manage-column"><?php _e("فرم متصل به درگاه", "gravityformspayro24") ?></th>
				<?php } ?>
				<th scope="col" class="manage-column"><?php _e("نوع تراکنش", "gravityformspayro24") ?></th>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<th scope="col" id="cb" class="manage-column column-cb check-column" style="padding:13px 3px;">
					<input type="checkbox"/></th>
				<th scope="col" id="active"
					class="manage-column"><?php echo $arg != 'per-form' ? __('وضعیت', 'gravityformspayro24') : '' ?></th>
				<th scope="col" class="manage-column"><?php _e("آیدی فید", "gravityformspayro24") ?></th>
				<?php if ($arg != 'per-form') { ?>
					<th scope="col"
						class="manage-column"><?php _e("فرم متصل به درگاه", "gravityformspayro24") ?></th>
				<?php } ?>
				<th scope="col" class="manage-column"><?php _e("نوع تراکنش", "gravityformspayro24") ?></th>
			</tr>
			</tfoot>
			<tbody class="list:user user-list">
			<?php
			if ($arg != 'per-form') {
				$settings = payro24_DB::get_feeds();
			} else {
				$settings = payro24_DB::get_feed_by_form(rgget('id'), false);
			}

			if (!get_option("gf_payro24_configured")) {
				?>
				<tr>
					<td colspan="5" style="padding:20px;">
						<?php echo sprintf(__("برای شروع باید درگاه را فعال نمایید . به %sتنظیمات payro24%s بروید . ", "gravityformspayro24"), '<a href="admin.php?page=gf_settings&subview=gf_payro24">', "</a>"); ?>
					</td>
				</tr>
				<?php
			} else if (is_array($settings) && sizeof($settings) > 0) {
				foreach ($settings as $setting) {
					?>
					<tr class='author-self status-inherit' valign="top">

						<th scope="row" class="check-column"><input type="checkbox" name="feed[]"
																	value="<?php echo $setting["id"] ?>"/></th>

						<td><img style="cursor:pointer;width:25px"
								 src="<?php echo esc_url(GFCommon::get_base_url()) ?>/images/active<?php echo intval($setting["is_active"]) ?>.png"
								 alt="<?php echo $setting["is_active"] ? __("درگاه فعال است", "gravityformspayro24") : __("درگاه غیر فعال است", "gravityformspayro24"); ?>"
								 title="<?php echo $setting["is_active"] ? __("درگاه فعال است", "gravityformspayro24") : __("درگاه غیر فعال است", "gravityformspayro24"); ?>"
								 onclick="ToggleActive(this, <?php echo $setting['id'] ?>); "/></td>

						<td><?php echo $setting["id"] ?>
							<?php if ($arg == 'per-form') { ?>
								<div class="row-actions">
                                                <span class="edit">
                                                    <a title="<?php _e("ویرایش فید", "gravityformspayro24") ?>"
													   href="admin.php?page=gf_payro24&view=edit&id=<?php echo $setting["id"] ?>"><?php _e("ویرایش فید", "gravityformspayro24") ?></a>
                                                    |
                                                </span>
									<span class="trash">
                                                    <a title="<?php _e("حذف", "gravityformspayro24") ?>"
													   href="javascript: if(confirm('<?php _e("فید حذف شود؟ ", "gravityformspayro24") ?> <?php _e("\'Cancel\' برای انصراف, \'OK\' برای حذف کردن.", "gravityformspayro24") ?>')){ DeleteSetting(<?php echo $setting["id"] ?>);}"><?php _e("حذف", "gravityformspayro24") ?></a>
                                                </span>
								</div>
							<?php } ?>
						</td>

						<?php if ($arg != 'per-form') { ?>
							<td class="column-title">
								<strong><a class="row-title"
										   href="admin.php?page=gf_payro24&view=edit&id=<?php echo $setting["id"] ?>"
										   title="<?php _e("تنظیم مجدد درگاه", "gravityformspayro24") ?>"><?php echo $setting["form_title"] ?></a></strong>

								<div class="row-actions">
                                            <span class="edit">
                                                <a title="<?php _e("ویرایش فید", "gravityformspayro24") ?>"
												   href="admin.php?page=gf_payro24&view=edit&id=<?php echo $setting["id"] ?>"><?php _e("ویرایش فید", "gravityformspayro24") ?></a>
                                                |
                                            </span>
									<span class="trash">
                                                <a title="<?php _e("حذف فید", "gravityformspayro24") ?>"
												   href="javascript: if(confirm('<?php _e("فید حذف شود؟ ", "gravityformspayro24") ?> <?php _e("\'Cancel\' برای انصراف, \'OK\' برای حذف کردن.", "gravityformspayro24") ?>')){ DeleteSetting(<?php echo $setting["id"] ?>);}"><?php _e("حذف", "gravityformspayro24") ?></a>
                                                |
                                            </span>
									<span class="view">
                                                <a title="<?php _e("ویرایش فرم", "gravityformspayro24") ?>"
												   href="admin.php?page=gf_edit_forms&id=<?php echo $setting["form_id"] ?>"><?php _e("ویرایش فرم", "gravityformspayro24") ?></a>
                                                |
                                            </span>
									<span class="view">
                                                <a title="<?php _e("مشاهده صندوق ورودی", "gravityformspayro24") ?>"
												   href="admin.php?page=gf_entries&view=entries&id=<?php echo $setting["form_id"] ?>"><?php _e("صندوق ورودی", "gravityformspayro24") ?></a>
                                                |
                                            </span>
									<span class="view">
                                                <a title="<?php _e("نمودارهای فرم", "gravityformspayro24") ?>"
												   href="admin.php?page=gf_payro24&view=stats&id=<?php echo $setting["form_id"] ?>"><?php _e("نمودارهای فرم", "gravityformspayro24") ?></a>
                                            </span>
								</div>
							</td>
						<?php } ?>


						<td class="column-date">
							<?php
							if (isset($setting["meta"]["type"]) && $setting["meta"]["type"] == 'subscription') {
								_e("عضویت", "gravityformspayro24");
							} else {
								_e("محصول معمولی یا فرم ارسال پست", "gravityformspayro24");
							}
							?>
						</td>
					</tr>
					<?php
				}
			} else {
				?>
				<tr>
					<td colspan="5" style="padding:20px;">
						<?php
						if ($arg == 'per-form') {
							echo sprintf(__("شما هیچ فید payro24ی ندارید . %sیکی بسازید%s .", "gravityformspayro24"), '<a href="admin.php?page=gf_payro24&view=edit&fid=' . absint(rgget("id")) . '">', "</a>");
						} else {
							echo sprintf(__("شما هیچ فید payro24ی ندارید . %sیکی بسازید%s .", "gravityformspayro24"), '<a href="admin.php?page=gf_payro24&view=edit">', "</a>");
						}
						?>
					</td>
				</tr>
				<?php
			}
			?>
			</tbody>
		</table>
	</form>
</div>
<script type="text/javascript">
    function DeleteSetting(id) {
        jQuery("#action_argument").val(id);
        jQuery("#action").val("delete");
        jQuery("#confirmation_list_form")[0].submit();
    }

    function ToggleActive(img, feed_id) {
        var is_active = img.src.indexOf("active1.png") >= 0;
        if (is_active) {
            img.src = img.src.replace("active1.png", "active0.png");
            jQuery(img).attr('title', '<?php _e("درگاه غیر فعال است", "gravityformspayro24") ?>').attr('alt', '<?php _e("درگاه غیر فعال است", "gravityformspayro24") ?>');
        } else {
            img.src = img.src.replace("active0.png", "active1.png");
            jQuery(img).attr('title', '<?php _e("درگاه فعال است", "gravityformspayro24") ?>').attr('alt', '<?php _e("درگاه فعال است", "gravityformspayro24") ?>');
        }
        var mysack = new sack(ajaxurl);
        mysack.execute = 1;
        mysack.method = 'POST';
        mysack.setVar("action", "gf_payro24_update_feed_active");
        mysack.setVar("gf_payro24_update_feed_active", "<?php echo wp_create_nonce("gf_payro24_update_feed_active") ?>");
        mysack.setVar("feed_id", feed_id);
        mysack.setVar("is_active", is_active ? 0 : 1);
        mysack.onError = function () {
            alert('<?php _e("خطای Ajax رخ داده است", "gravityformspayro24") ?>')
        };
        mysack.runAJAX();
        return true;
    }
</script>