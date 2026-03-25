<?php

/**
 * Plugin Name: WooCommerce WhatsApp Order Button
 * Description: Adds a WhatsApp order button on WooCommerce single product page with variation support.
 * Version: 1.0
 * Author: Smit Jadav
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_init', 'wow_register_settings');

function wow_register_settings() {
    register_setting('wow_settings_group', 'wow_whatsapp_number', 'sanitize_text_field');
    register_setting('wow_settings_group', 'wow_country_code', 'sanitize_text_field');
    register_setting('wow_settings_group', 'wow_show_shop', 'absint');
    register_setting('wow_settings_group', 'wow_show_single', 'absint');
    register_setting('wow_settings_group', 'wow_show_cart', 'absint');
}

function wow_settings_page() {

    $number = get_option('wow_whatsapp_number', '');
    $country_code = get_option('wow_country_code', '91'); // Default to 91
    $show_shop = get_option('wow_show_shop', 0);
    $show_single = get_option('wow_show_single', 0);
    $show_cart = get_option('wow_show_cart', 0);

    ?>
    <div class="wrap">
        <h1>WhatsApp Order Settings</h1>

        <form method="post" action="options.php">
            <?php
                settings_fields('wow_settings_group');
                do_settings_sections('wow_settings_group');
            ?>

            <table class="form-table">
                <tr>
                    <th>Country Code</th>
                    <td>
                        <input type="text" name="wow_country_code" value="<?php echo esc_attr($country_code); ?>" placeholder="91" />
                        <p class="description">Country code without + (e.g., 91 for India)</p>
                    </td>
                </tr>
                <tr>
                    <th>WhatsApp Number</th>
                    <td>
                        <input type="text" name="wow_whatsapp_number" value="<?php echo esc_attr($number); ?>" placeholder="9999999999" />
                        <p class="description">Number without country code</p>
                    </td>
                </tr>

                <tr>
                    <th>Show on Shop Page</th>
                    <td>
                        <input type="hidden" name="wow_show_shop" value="0" />
                        <input type="checkbox" name="wow_show_shop" value="1" <?php checked(1, $show_shop); ?> />
                    </td>
                </tr>

                <tr>
                    <th>Show on Single Product</th>
                    <td>
                        <input type="hidden" name="wow_show_single" value="0" />
                        <input type="checkbox" name="wow_show_single" value="1" <?php checked(1, $show_single); ?> />
                    </td>
                </tr>

                <tr>
                    <th>Show on Cart Page</th>
                    <td>
                        <input type="hidden" name="wow_show_cart" value="0" />
                        <input type="checkbox" name="wow_show_cart" value="1" <?php checked(1, $show_cart); ?> />
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}


/* Enqueue JS */
function wow_enqueue_scripts() {

    if ( is_product() || is_shop() || is_product_category() || is_product_tag() || is_cart() ) {

        wp_enqueue_script(
            'wow-whatsapp-js',
            plugin_dir_url(__FILE__) . 'js/whatsapp-order.js',
            array('jquery'),
            '1.0',
            true
        );
    }

    wp_enqueue_style(
        'wow-style',
        plugin_dir_url(__FILE__) . 'css/style.css'
    );
}

add_action('wp_enqueue_scripts', 'wow_enqueue_scripts');


/* Add Button to Product Page */
function wow_add_whatsapp_button()
{

    $country_code = get_option('wow_country_code', '91');
    $number = get_option('wow_whatsapp_number');
    $phone = $country_code . $number;
    echo '<button class="whatsapp-order-btn" data-phone="' . $phone . '">
    Order on WhatsApp
    </button>';
}


add_action('admin_menu', 'wow_add_admin_menu');

function wow_add_admin_menu() {
    add_menu_page(
        'WhatsApp Orders',   // Page title
        'WhatsApp Orders',   // Menu title
        'manage_options',    // Capability
        'wow-settings',      // Slug
        'wow_settings_page', // Callback
        'dashicons-whatsapp',// Icon
        25
    );
}


add_action('wp', 'wow_conditionally_add_button');

function wow_conditionally_add_button() {
    $show_shop = absint(get_option('wow_show_shop', 0));
    $show_single = absint(get_option('wow_show_single', 0));
    $show_cart = absint(get_option('wow_show_cart', 0));

    if ( is_product() && $show_single ) {
        add_action('woocommerce_single_product_summary', 'wow_add_whatsapp_button', 35);
    }

    if ( (is_shop() || is_product_category() || is_product_tag()) && $show_shop ) {
        add_action('woocommerce_after_shop_loop_item', 'wow_add_whatsapp_button', 20);
    }

    if ( is_cart() && $show_cart ) {
        add_action('woocommerce_cart_totals_after_order_total', 'wow_add_whatsapp_button', 10);
    }
}