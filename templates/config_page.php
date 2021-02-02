<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
wp_register_style('gform_admin_payro24', GFCommon::get_base_url() . '/css/admin.css');
wp_print_styles(array('jquery-ui-styles', 'gform_admin_payro24', 'wp-pointer'));

?>


<?php if (is_rtl()) { ?>
<style type="text/css">
    table.gforms_form_settings th {
        text-align: right !important
    }
</style>
<?php } ?>
    <div class="wrap gforms_edit_form gf_browser_gecko"></div>
    <?php
    $id = !rgempty("payro24_setting_id") ? rgpost("payro24_setting_id") : absint(rgget("id"));
    $config = empty($id) ? array(
        "meta" => array(),
        "is_active" => true
    ) : payro24_DB::get_feed($id);
    $get_feeds = payro24_DB::get_feeds();
    $form_name = '';


    $_get_form_id = rgget('fid') ? rgget('fid') : (!empty($config["form_id"]) ? $config["form_id"] : '');

    foreach ((array)$get_feeds as $get_feed) {
        if ($get_feed['id'] == $id) {
            $form_name = $get_feed['form_title'];
        }
    }
    ?>
    <h2 class="gf_admin_page_title"><?php _e("پیکربندی درگاه payro24", "gravityformspayro24") ?>
        <?php if (!empty($_get_form_id)) { ?>
            <span class="gf_admin_page_subtitle">
            <span
                    class="gf_admin_page_formid"><?php echo sprintf(__("فید: %s", "gravityformspayro24"), $id) ?></span>
            <span
                    class="gf_admin_page_formname"><?php echo sprintf(__("فرم: %s", "gravityformspayro24"), $form_name) ?></span>
        </span>
        <?php } ?>
    </h2>
    <a class="button add-new-h2" href="admin.php?page=gf_settings&subview=gf_payro24"
       style="margin:8px 9px;"><?php _e("تنظیمات payro24", "gravityformspayro24") ?></a>
    <?php
    if (!rgempty("gf_payro24_submit")) {
        check_admin_referer("update", "gf_payro24_feed");
        $config["form_id"] = absint(rgpost("gf_payro24_form"));
        $config["meta"]["type"] = rgpost("gf_payro24_type");
        $config["meta"]["addon"] = rgpost("gf_payro24_addon");
        $config["meta"]["update_post_action1"] = rgpost('gf_payro24_update_action1');
        $config["meta"]["update_post_action2"] = rgpost('gf_payro24_update_action2');
        $config["meta"]["payro24_conditional_enabled"] = rgpost('gf_payro24_conditional_enabled');
        $config["meta"]["payro24_conditional_field_id"] = rgpost('gf_payro24_conditional_field_id');
        $config["meta"]["payro24_conditional_operator"] = rgpost('gf_payro24_conditional_operator');
        $config["meta"]["payro24_conditional_value"] = rgpost('gf_payro24_conditional_value');
        $config["meta"]["payro24_conditional_type"] = rgpost('gf_payro24_conditional_type');
        $config["meta"]["desc_pm"] = rgpost("gf_payro24_desc_pm");
        $config["meta"]["customer_fields_desc"] = rgpost("payro24_customer_field_desc");
        $config["meta"]["customer_fields_email"] = rgpost("payro24_customer_field_email");
        $config["meta"]["customer_fields_mobile"] = rgpost("payro24_customer_field_mobile");
        $config["meta"]["customer_fields_name"] = rgpost("payro24_customer_field_name");
        $safe_data = array();
        foreach ($config["meta"] as $key => $val) {
            if (!is_array($val)) {
                $safe_data[$key] = sanitize_text_field($val);
            } else {
                $safe_data[$key] = array_map('sanitize_text_field', $val);
            }
        }
        $config["meta"] = $safe_data;

        $config = apply_filters(self::$author . '_gform_gateway_save_config', $config);
        $config = apply_filters(self::$author . '_gform_payro24_save_config', $config);

        $id = payro24_DB::update_feed($id, $config["form_id"], $config["is_active"], $config["meta"]);
        if (!headers_sent()) {
            wp_redirect(admin_url('admin.php?page=gf_payro24&view=edit&id=' . $id . '&updated=true'));
            exit;
        } else {
            echo "<script type='text/javascript'>window.onload = function () { top.location.href = '" . admin_url('admin.php?page=gf_payro24&view=edit&id=' . $id . '&updated=true') . "'; };</script>";
            exit;
        }
        ?>
        <div class="updated fade"
             style="padding:6px"><?php echo sprintf(__("فید به روز شد . %sبازگشت به لیست%s.", "gravityformspayro24"), "<a href='?page=gf_payro24'>", "</a>") ?></div>
        <?php
    }
    $_get_form_id = rgget('fid') ? rgget('fid') : (!empty($config["form_id"]) ? $config["form_id"] : '');
    $form = array();
    if (!empty($_get_form_id)) {
        $form = RGFormsModel::get_form_meta($_get_form_id);
    }
    if (rgget('updated') == 'true') {
        $id = empty($id) && isset($_GET['id']) ? rgget('id') : $id;
        $id = absint($id); ?>
        <div class="updated fade"
             style="padding:6px"><?php echo sprintf(__("فید به روز شد . %sبازگشت به لیست%s . ", "gravityformspayro24"), "<a href='?page=gf_payro24'>", "</a>") ?></div>

        <?php
    }
    if (!empty($_get_form_id)) { ?>
        <div id="gf_form_toolbar">
            <ul id="gf_form_toolbar_links">
                <?php
                $menu_items = apply_filters('gform_toolbar_menu', GFForms::get_toolbar_menu_items($_get_form_id), $_get_form_id);
                echo GFForms::format_toolbar_menu_items($menu_items); ?>

                <li class="gf_form_switcher">
                    <label for="export_form"><?php _e('یک فید انتخاب کنید', 'gravityformspayro24') ?></label>
                    <?php
                    $feeds = payro24_DB::get_feeds();
                    if (RG_CURRENT_VIEW != 'entry') { ?>
                        <select name="form_switcher" id="form_switcher"
                                onchange="GF_SwitchForm(jQuery(this).val());">
                            <option value=""><?php _e('تغییر فید payro24', 'gravityformspayro24') ?></option>
                            <?php foreach ($feeds as $feed) {
                                $selected = $feed["id"] == $id ? "selected='selected'" : ""; ?>
                                <option
                                        value="<?php echo $feed["id"] ?>" <?php echo $selected ?> ><?php echo sprintf(__('فرم: %s (فید: %s)', 'gravityformspayro24'), $feed["form_title"], $feed["id"]) ?></option>
                            <?php } ?>
                        </select>
                        <?php
                    }
                    ?>
                </li>
            </ul>
        </div>
    <?php } ?>
    <?php
    $condition_field_ids = array('1' => '');
    $condition_values = array('1' => '');
    $condition_operators = array('1' => 'is');
    ?>
    <div id="gform_tab_group" class="gform_tab_group vertical_tabs">
        <?php if (!empty($_get_form_id)) { ?>
            <ul id="gform_tabs" class="gform_tabs">
                <?php
                $title = '';
                $get_form = GFFormsModel::get_form_meta($_get_form_id);
                $current_tab = rgempty('subview', $_GET) ? 'settings' : rgget('subview');
                $current_tab = !empty($current_tab) ? $current_tab : ' ';
                $setting_tabs = GFFormSettings::get_tabs($get_form['id']);
                if (!$title) {
                    foreach ($setting_tabs as $tab) {
                        $query = array(
                            'page' => 'gf_edit_forms',
                            'view' => 'settings',
                            'subview' => $tab['name'],
                            'id' => $get_form['id']
                        );
                        $url = add_query_arg($query, admin_url('admin.php'));
                        echo $tab['name'] == 'payro24' ? '<li class="active">' : '<li>';
                        ?>
                        <a href="<?php echo esc_url($url); ?>"><?php echo esc_html($tab['label']) ?></a>
                        <span></span>
                        </li>
                        <?php
                    }
                }
                ?>
            </ul>
        <?php }
        $has_product = false;
        if (isset($form["fields"])) {
            foreach ($form["fields"] as $field) {
                $shipping_field = GFAPI::get_fields_by_type($form, array('shipping'));
                if ($field["type"] == "product" || !empty($shipping_field)) {
                    $has_product = true;
                    break;
                }
            }
        } else if (empty($_get_form_id)) {
            $has_product = true;
        }
        ?>
        <div id="gform_tab_container_<?php echo $_get_form_id ? $_get_form_id : 1 ?>"
             class="gform_tab_container">
            <div class="gform_tab_content" id="tab_<?php echo !empty($current_tab) ? $current_tab : '' ?>">
                <div id="form_settings" class="gform_panel gform_panel_form_settings">
                    <h3>
                        <span>
                            <i class="fa fa-credit-card"></i>
                            <?php _e("پیکربندی درگاه payro24", "gravityformspayro24"); ?>
                        </span>
                    </h3>
                    <form method="post" action="" id="gform_form_settings">
                        <?php wp_nonce_field("update", "gf_payro24_feed") ?>
                        <input type="hidden" name="payro24_setting_id" value="<?php echo $id ?>"/>
                        <table class="form-table gforms_form_settings" cellspacing="0" cellpadding="0">
                            <tbody>
                            <tr style="<?php echo rgget('id') || rgget('fid') ? 'display:none !important' : ''; ?>">
                                <th>
                                    <?php _e("انتخاب فرم", "gravityformspayro24"); ?>
                                </th>
                                <td>
                                    <select id="gf_payro24_form" name="gf_payro24_form" onchange="GF_SwitchFid(jQuery(this).val());">
                                        <option value=""><?php _e("یک فرم انتخاب نمایید", "gravityformspayro24"); ?> </option>
                                        <?php
                                        $available_forms = payro24_DB::get_available_forms();
                                        foreach ($available_forms as $current_form) {
                                            $selected = absint($current_form->id) == $_get_form_id ? 'selected="selected"' : ''; ?>
                                            <option value="<?php echo absint($current_form->id) ?>" <?php echo $selected; ?>><?php echo esc_html($current_form->title) ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                    <img src="<?php echo esc_url(GFCommon::get_base_url()) ?>/images/spinner.gif" id="payro24_wait" style="display: none;"/>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <?php if (empty($has_product) || !$has_product) { ?>
                            <div id="gf_payro24_invalid_product_form" class="gf_payro24_invalid_form"
                                 style="background-color:#FFDFDF; margin-top:4px; margin-bottom:6px;padding:18px; border:1px dotted #C89797;">
                                <?php _e("فرم انتخاب شده هیچ گونه فیلد قیمت گذاری ندارد، لطفا پس از افزودن این فیلدها مجددا اقدام نمایید.", "gravityformspayro24") ?>
                            </div>
                        <?php } else { ?>
                            <table class="form-table gforms_form_settings"
                                   id="payro24_field_group" <?php echo empty($_get_form_id) ? "style='display:none;'" : "" ?>
                                   cellspacing="0" cellpadding="0">
                                <tbody>
                                <tr>
                                    <th>
                                        <?php _e("فرم ثبت نام", "gravityformspayro24"); ?>
                                    </th>
                                    <td>
                                        <input type="checkbox" name="gf_payro24_type"
                                               id="gf_payro24_type_subscription"
                                               value="subscription" <?php echo rgar($config['meta'], 'type') == "subscription" ? "checked='checked'" : "" ?>/>
                                        <label for="gf_payro24_type"></label>
                                        <span
                                                class="description"><?php _e('در صورتی که تیک بزنید عملیات ثبت نام که توسط افزونه User Registration انجام خواهد شد تنها برای پرداخت های موفق عمل خواهد کرد'); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        <?php _e("توضیحات پرداخت", "gravityformspayro24"); ?>
                                    </th>
                                    <td>
                                        <input type="text" name="gf_payro24_desc_pm" id="gf_payro24_desc_pm"
                                               class="fieldwidth-1"
                                               value="<?php echo rgar($config["meta"], "desc_pm") ?>"/>
                                        <span
                                                class="description"><?php _e("شورت کد ها : {form_id} , {form_title} , {entry_id}", "gravityformspayro24"); ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        <?php _e("نام پرداخت کننده", "gravityformspayro24"); ?>
                                    </th>
                                    <td class="payro24_customer_fields_name">
                                        <?php
                                        if (!empty($form)) {
                                            $form_fields = self::get_form_fields($form);
                                            $selected_field = !empty($config["meta"]["customer_fields_name"]) ? $config["meta"]["customer_fields_name"] : '';
                                            echo self::get_mapped_field_list('payro24_customer_field_name', $selected_field, $form_fields);
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        <?php _e("ایمیل پرداخت کننده", "gravityformspayro24"); ?>
                                    </th>
                                    <td class="payro24_customer_fields_email">
                                        <?php
                                        if (!empty($form)) {
                                            $form_fields = self::get_form_fields($form);
                                            $selected_field = !empty($config["meta"]["customer_fields_email"]) ? $config["meta"]["customer_fields_email"] : '';
                                            echo self::get_mapped_field_list('payro24_customer_field_email', $selected_field, $form_fields);
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        <?php _e("توضیح تکمیلی", "gravityformspayro24"); ?>
                                    </th>
                                    <td class="payro24_customer_fields_desc">
                                        <?php
                                        if (!empty($form)) {
                                            $form_fields = self::get_form_fields($form);
                                            $selected_field = !empty($config["meta"]["customer_fields_desc"]) ? $config["meta"]["customer_fields_desc"] : '';
                                            echo self::get_mapped_field_list('payro24_customer_field_desc', $selected_field, $form_fields);
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        <?php _e("تلفن همراه پرداخت کننده", "gravityformspayro24"); ?>
                                    </th>
                                    <td class="payro24_customer_fields_mobile">
                                        <?php
                                        if (!empty($form)) {
                                            $form_fields = self::get_form_fields($form);
                                            $selected_field = !empty($config["meta"]["customer_fields_mobile"]) ? $config["meta"]["customer_fields_mobile"] : '';
                                            echo self::get_mapped_field_list('payro24_customer_field_mobile', $selected_field, $form_fields);
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php $display_post_fields = !empty($form) ? GFCommon::has_post_field($form["fields"]) : false; ?>
                                <tr <?php echo $display_post_fields ? "" : "style='display:none;'" ?>>
                                    <th>
                                        <?php _e("نوشته بعد از پرداخت موفق", "gravityformspayro24"); ?>
                                    </th>
                                    <td>
                                        <select id="gf_payro24_update_action1"
                                                name="gf_payro24_update_action1">
                                            <option
                                                    value="default" <?php echo rgar($config["meta"], "update_post_action1") == "default" ? "selected='selected'" : "" ?>><?php _e("وضعیت پیشفرض فرم", "gravityformspayro24") ?></option>
                                            <option
                                                    value="publish" <?php echo rgar($config["meta"], "update_post_action1") == "publish" ? "selected='selected'" : "" ?>><?php _e("منتشر شده", "gravityformspayro24") ?></option>
                                            <option
                                                    value="draft" <?php echo rgar($config["meta"], "update_post_action1") == "draft" ? "selected='selected'" : "" ?>><?php _e("پیشنویس", "gravityformspayro24") ?></option>
                                            <option
                                                    value="pending" <?php echo rgar($config["meta"], "update_post_action1") == "pending" ? "selected='selected'" : "" ?>><?php _e("در انتظار بررسی", "gravityformspayro24") ?></option>
                                            <option
                                                    value="private" <?php echo rgar($config["meta"], "update_post_action1") == "private" ? "selected='selected'" : "" ?>><?php _e("خصوصی", "gravityformspayro24") ?></option>
                                        </select>
                                    </td>
                                </tr>

                                <tr <?php echo $display_post_fields ? "" : "style='display:none;'" ?>>
                                    <th>
                                        <?php _e("نوشته قبل از پرداخت موفق", "gravityformspayro24"); ?>
                                    </th>
                                    <td>
                                        <select id="gf_payro24_update_action2"
                                                name="gf_payro24_update_action2">
                                            <option
                                                    value="dont" <?php echo rgar($config["meta"], "update_post_action2") == "dont" ? "selected='selected'" : "" ?>><?php _e("عدم ایجاد پست", "gravityformspayro24") ?></option>
                                            <option
                                                    value="default" <?php echo rgar($config["meta"], "update_post_action2") == "default" ? "selected='selected'" : "" ?>><?php _e("وضعیت پیشفرض فرم", "gravityformspayro24") ?></option>
                                            <option
                                                    value="publish" <?php echo rgar($config["meta"], "update_post_action2") == "publish" ? "selected='selected'" : "" ?>><?php _e("منتشر شده", "gravityformspayro24") ?></option>
                                            <option
                                                    value="draft" <?php echo rgar($config["meta"], "update_post_action2") == "draft" ? "selected='selected'" : "" ?>><?php _e("پیشنویس", "gravityformspayro24") ?></option>
                                            <option
                                                    value="pending" <?php echo rgar($config["meta"], "update_post_action2") == "pending" ? "selected='selected'" : "" ?>><?php _e("در انتظار بررسی", "gravityformspayro24") ?></option>
                                            <option
                                                    value="private" <?php echo rgar($config["meta"], "update_post_action2") == "private" ? "selected='selected'" : "" ?>><?php _e("خصوصی", "gravityformspayro24") ?></option>
                                        </select>
                                    </td>
                                </tr>

                                <tr>
                                    <th>
                                        <?php echo __("سازگاری با افزودنی ها", "gravityformspayro24"); ?>
                                    </th>
                                    <td>
                                        <input type="checkbox" name="gf_payro24_addon"
                                               id="gf_payro24_addon_true"
                                               value="true" <?php echo rgar($config['meta'], 'addon') == "true" ? "checked='checked'" : "" ?>/>
                                        <label for="gf_payro24_addon"></label>
                                        <span
                                                class="description"><?php _e('برخی افزودنی های گرویتی فرم دارای متد add_delayed_payment_support هستند. در صورتی که میخواهید این افزودنی ها تنها در صورت تراکنش موفق عمل کنند این گزینه را تیک بزنید.', 'gravityformspayro24'); ?></span>
                                    </td>
                                </tr>

                                <?php
                                do_action(self::$author . '_gform_gateway_config', $config, $form);
                                do_action(self::$author . '_gform_payro24_config', $config, $form);
                                ?>

                                <tr id="gf_payro24_conditional_option">
                                    <th>
                                        <?php _e("منطق شرطی", "gravityformspayro24"); ?>
                                    </th>
                                    <td>
                                        <input type="checkbox" id="gf_payro24_conditional_enabled"
                                               name="gf_payro24_conditional_enabled" value="1"
                                               onclick="if(this.checked){jQuery('#gf_payro24_conditional_container').fadeIn('fast');} else{ jQuery('#gf_payro24_conditional_container').fadeOut('fast'); }" <?php echo rgar($config['meta'], 'payro24_conditional_enabled') ? "checked='checked'" : "" ?>/>
                                        <label for="gf_payro24_conditional_enabled"><?php _e("فعالسازی منطق شرطی", "gravityformspayro24"); ?></label><br/>
                                        <br>
                                        <table cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td>
                                                    <div id="gf_payro24_conditional_container" <?php echo !rgar($config['meta'], 'payro24_conditional_enabled') ? "style='display:none'" : "" ?>>

                                                        <span><?php _e("این درگاه را فعال کن اگر ", "gravityformspayro24") ?></span>

                                                        <select name="gf_payro24_conditional_type">
                                                            <option value="all" <?php echo rgar($config['meta'], 'payro24_conditional_type') == 'all' ? "selected='selected'" : "" ?>><?php _e("همه", "gravityformspayro24") ?></option>
                                                            <option value="any" <?php echo rgar($config['meta'], 'payro24_conditional_type') == 'any' ? "selected='selected'" : "" ?>><?php _e("حداقل یکی", "gravityformspayro24") ?></option>
                                                        </select>
                                                        <span><?php _e("مطابق گزینه های زیر باشند:", "gravityformspayro24") ?></span>

                                                        <?php
                                                        if (!empty($config["meta"]["payro24_conditional_field_id"])) {
                                                            $condition_field_ids = $config["meta"]["payro24_conditional_field_id"];
                                                            if (!is_array($condition_field_ids)) {
                                                                $condition_field_ids = array('1' => $condition_field_ids);
                                                            }
                                                        }

                                                        if (!empty($config["meta"]["payro24_conditional_value"])) {
                                                            $condition_values = $config["meta"]["payro24_conditional_value"];
                                                            if (!is_array($condition_values)) {
                                                                $condition_values = array('1' => $condition_values);
                                                            }
                                                        }

                                                        if (!empty($config["meta"]["payro24_conditional_operator"])) {
                                                            $condition_operators = $config["meta"]["payro24_conditional_operator"];
                                                            if (!is_array($condition_operators)) {
                                                                $condition_operators = array('1' => $condition_operators);
                                                            }
                                                        }

                                                        ksort($condition_field_ids);
                                                        foreach ($condition_field_ids as $i => $value):?>

                                                            <div class="gf_payro24_conditional_div"
                                                                 id="gf_payro24_<?php echo $i; ?>__conditional_div">

                                                                <select class="gf_payro24_conditional_field_id"
                                                                        id="gf_payro24_<?php echo $i; ?>__conditional_field_id"
                                                                        name="gf_payro24_conditional_field_id[<?php echo $i; ?>]"
                                                                        title="">
                                                                </select>

                                                                <select class="gf_payro24_conditional_operator"
                                                                        id="gf_payro24_<?php echo $i; ?>__conditional_operator"
                                                                        name="gf_payro24_conditional_operator[<?php echo $i; ?>]"
                                                                        style="font-family:tahoma,serif !important"
                                                                        title="">
                                                                    <option value="is"><?php _e("هست", "gravityformspayro24") ?></option>
                                                                    <option value="isnot"><?php _e("نیست", "gravityformspayro24") ?></option>
                                                                    <option value=">"><?php _e("بیشتر یا بزرگتر از", "gravityformspayro24") ?></option>
                                                                    <option value="<"><?php _e("کمتر یا کوچکتر از", "gravityformspayro24") ?></option>
                                                                    <option value="contains"><?php _e("شامل میشود", "gravityformspayro24") ?></option>
                                                                    <option value="starts_with"><?php _e("شروع می شود با", "gravityformspayro24") ?></option>
                                                                    <option value="ends_with"><?php _e("تمام میشود با", "gravityformspayro24") ?></option>
                                                                </select>

                                                                <div id="gf_payro24_<?php echo $i; ?>__conditional_value_container"
                                                                     style="display:inline;">
                                                                </div>

                                                                <a class="add_new_condition gficon_link"
                                                                   href="#">
                                                                    <i class="gficon-add"></i>
                                                                </a>

                                                                <a class="delete_this_condition gficon_link"
                                                                   href="#">
                                                                    <i class="gficon-subtract"></i>
                                                                </a>
                                                            </div>
                                                        <?php endforeach; ?>

                                                        <input type="hidden"
                                                               value="<?php echo key(array_slice($condition_field_ids, -1, 1, true)); ?>"
                                                               id="gf_payro24_conditional_counter">

                                                        <div id="gf_no_conditional_message"
                                                             style="display:none;background-color:#FFDFDF; margin-top:4px; margin-bottom:6px; padding-top:6px; padding:18px; border:1px dotted #C89797;">
                                                            <?php _e("برای قرار دادن منطق شرطی، باید فیلدهای فرم شما هم قابلیت منطق شرطی را داشته باشند.", "gravityformspayro24") ?>
                                                        </div>

                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <input type="submit" class="button-primary gfbutton"
                                               name="gf_payro24_submit"
                                               value="<?php _e("ذخیره", "gravityformspayro24"); ?>"/>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        <?php } ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<style type="text/css">.gforms_form_settings select {
        width: 180px !important
    }

    .add_new_condition, .delete_this_condition {
        text-decoration: none !important;
        color: #000;
        outline: 0 !important
    }

    #gf_payro24_conditional_container *, .add_new_condition *, .delete_this_condition * {
        outline: 0 !important
    }

    .condition_field_value {
        width: 150px !important
    }

    table.gforms_form_settings th {
        font-weight: 600;
        line-height: 1.3;
        font-size: 14px
    }

    .gf_payro24_conditional_div {
        margin: 3px
    }
</style>
<script type="text/javascript">
    function GF_SwitchFid(fid) {
        jQuery("#payro24_wait").show();
        document.location = "?page=gf_payro24&view=edit&fid=" + fid;
    }

    function GF_SwitchForm(id) {
        if (id.length > 0) {
            document.location = "?page=gf_payro24&view=edit&id=" + id;
        }
    }

    var form = [];
    form = <?php echo !empty($form) ? GFCommon::json_encode($form) : GFCommon::json_encode(array()) ?>;

    jQuery(document).ready(function ($) {

        var delete_link, selectedField, selectedValue, selectedOperator;

        delete_link = $('.delete_this_condition');
        if (delete_link.length === 1)
            delete_link.hide();

        $(document.body).on('change', '.gf_payro24_conditional_field_id', function () {
            var id = $(this).attr('id');
            id = id.replace('gf_payro24_', '').replace('__conditional_field_id', '');
            var selectedOperator = $('#gf_payro24_' + id + '__conditional_operator').val();
            $('#gf_payro24_' + id + '__conditional_value_container').html(GetConditionalFieldValues("gf_payro24_" + id + "__conditional", jQuery(this).val(), selectedOperator, "", 20, id));
        }).on('change', '.gf_payro24_conditional_operator', function () {
            var id = $(this).attr('id');
            id = id.replace('gf_payro24_', '').replace('__conditional_operator', '');
            var selectedOperator = $(this).val();
            var field_id = $('#gf_payro24_' + id + '__conditional_field_id').val();
            $('#gf_payro24_' + id + '__conditional_value_container').html(GetConditionalFieldValues("gf_payro24_" + id + "__conditional", field_id, selectedOperator, "", 20, id));
        }).on('click', '.add_new_condition', function () {
            var parent_div = $(this).parent('.gf_payro24_conditional_div');
            var counter = $('#gf_payro24_conditional_counter');
            var new_id = parseInt(counter.val()) + 1;
            var content = parent_div[0].outerHTML
                .replace(new RegExp('gf_payro24_\\d+__', 'g'), ('gf_payro24_' + new_id + '__'))
                .replace(new RegExp('\\[\\d+\\]', 'g'), ('[' + new_id + ']'));
            counter.val(new_id);
            counter.before(content);
            RefreshConditionRow("gf_payro24_" + new_id + "__conditional", "", "is", "", new_id);
            $('.delete_this_condition').show();
            return false;
        }).on('click', '.delete_this_condition', function () {
            $(this).parent('.gf_payro24_conditional_div').remove();
            var delete_link = $('.delete_this_condition');
            if (delete_link.length === 1)
                delete_link.hide();
            return false;
        });

        <?php foreach ( $condition_field_ids as $i => $field_id ) : ?>
        selectedField = "<?php echo str_replace('"', '\"', $field_id)?>";
        selectedValue = "<?php echo str_replace('"', '\"', $condition_values['' . $i . ''])?>";
        selectedOperator = "<?php echo str_replace('"', '\"', $condition_operators['' . $i . ''])?>";
        RefreshConditionRow("gf_payro24_<?php echo $i;?>__conditional", selectedField, selectedOperator, selectedValue, <?php echo $i;?>);
        <?php endforeach;?>
    });

    function RefreshConditionRow(input, selectedField, selectedOperator, selectedValue, index) {
        var field_id = jQuery("#" + input + "_field_id");
        field_id.html(GetSelectableFields(selectedField, 20));
        var optinConditionField = field_id.val();
        var checked = jQuery("#" + input + "_enabled").attr('checked');
        if (optinConditionField) {
            jQuery("#gf_no_conditional_message").hide();
            jQuery("#" + input + "_div").show();
            jQuery("#" + input + "_value_container").html(GetConditionalFieldValues("" + input + "", optinConditionField, selectedOperator, selectedValue, 20, index));
            jQuery("#" + input + "_value").val(selectedValue);
            jQuery("#" + input + "_operator").val(selectedOperator);
        } else {
            jQuery("#gf_no_conditional_message").show();
            jQuery("#" + input + "_div").hide();
        }
        if (!checked) jQuery("#" + input + "_container").hide();
    }

    function GetConditionalFieldValues(input, fieldId, selectedOperator, selectedValue, labelMaxCharacters, index) {
        if (!fieldId)
            return "";
        var str = "";
        var name = (input.replace(new RegExp('_\\d+__', 'g'), '_')) + "_value[" + index + "]";
        var field = GetFieldById(fieldId);
        if (!field)
            return "";

        var is_text = false;

        if (selectedOperator == '' || selectedOperator == 'is' || selectedOperator == 'isnot') {
            if (field["type"] == "post_category" && field["displayAllCategories"]) {
                str += '<?php $dd = wp_dropdown_categories(array(
                    "class" => "condition_field_value",
                    "orderby" => "name",
                    "id" => "gf_dropdown_cat_id",
                    "name" => "gf_dropdown_cat_name",
                    "hierarchical" => true,
                    "hide_empty" => 0,
                    "echo" => false
                )); echo str_replace("\n", "", str_replace("'", "\\'", $dd)); ?>';
                str = str.replace("gf_dropdown_cat_id", "" + input + "_value").replace("gf_dropdown_cat_name", name);
            } else if (field.choices) {
                var isAnySelected = false;
                str += "<select class='condition_field_value' id='" + input + "_value' name='" + name + "'>";
                for (var i = 0; i < field.choices.length; i++) {
                    var fieldValue = field.choices[i].value ? field.choices[i].value : field.choices[i].text;
                    var isSelected = fieldValue == selectedValue;
                    var selected = isSelected ? "selected='selected'" : "";
                    if (isSelected)
                        isAnySelected = true;
                    str += "<option value='" + fieldValue.replace(/'/g, "&#039;") + "' " + selected + ">" + TruncateMiddle(field.choices[i].text, labelMaxCharacters) + "</option>";
                }
                if (!isAnySelected && selectedValue) {
                    str += "<option value='" + selectedValue.replace(/'/g, "&#039;") + "' selected='selected'>" + TruncateMiddle(selectedValue, labelMaxCharacters) + "</option>";
                }
                str += "</select>";
            } else {
                is_text = true;
            }
        } else {
            is_text = true;
        }

        if (is_text) {
            selectedValue = selectedValue ? selectedValue.replace(/'/g, "&#039;") : "";
            str += "<input type='text' class='condition_field_value' style='padding:3px' placeholder='<?php _e("یک مقدار وارد نمایید", "gravityformspayro24"); ?>' id='" + input + "_value' name='" + name + "' value='" + selectedValue + "'>";
        }
        return str;
    }

    function GetSelectableFields(selectedFieldId, labelMaxCharacters) {
        var str = "";
        if (typeof form.fields !== "undefined") {
            var inputType;
            var fieldLabel;
            for (var i = 0; i < form.fields.length; i++) {
                fieldLabel = form.fields[i].adminLabel ? form.fields[i].adminLabel : form.fields[i].label;
                inputType = form.fields[i].inputType ? form.fields[i].inputType : form.fields[i].type;
                if (IsConditionalLogicField(form.fields[i])) {
                    var selected = form.fields[i].id == selectedFieldId ? "selected='selected'" : "";
                    str += "<option value='" + form.fields[i].id + "' " + selected + ">" + TruncateMiddle(fieldLabel, labelMaxCharacters) + "</option>";
                }
            }
        }
        return str;
    }

    function TruncateMiddle(text, maxCharacters) {
        if (!text)
            return "";
        if (text.length <= maxCharacters)
            return text;
        var middle = parseInt(maxCharacters / 2);
        return text.substr(0, middle) + "..." + text.substr(text.length - middle, middle);
    }

    function GetFieldById(fieldId) {
        for (var i = 0; i < form.fields.length; i++) {
            if (form.fields[i].id == fieldId)
                return form.fields[i];
        }
        return null;
    }

    function IsConditionalLogicField(field) {
        var inputType = field.inputType ? field.inputType : field.type;
        var supported_fields = ["checkbox", "radio", "select", "text", "website", "textarea", "email", "hidden", "number", "phone", "multiselect", "post_title",
            "post_tags", "post_custom_field", "post_content", "post_excerpt"];
        var index = jQuery.inArray(inputType, supported_fields);
        return index >= 0;
    }
</script>
