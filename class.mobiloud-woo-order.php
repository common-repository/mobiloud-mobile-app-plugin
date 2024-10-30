<?php

class MobiloudWooOrder
{
    public static function init()
    {
        if (file_exists(ABSPATH . 'wp-admin/includes/plugin.php')) {
            include_once(ABSPATH . 'wp-admin/includes/plugin.php');

            if (is_plugin_active('mobiloud-mobile-app-plugin/mobiloud.php') && !is_plugin_active('mobile-app/canvas.php')) {
                add_action('woocommerce_thankyou', array(__CLASS__, 'order_placed'), 10, 1);

                if (MobiloudWooOrder::is_hops_enabled()) {
                    add_filter('manage_woocommerce_page_wc-orders_columns', array(__CLASS__, 'add_new_column_order_tag'));
                    add_action('manage_woocommerce_page_wc-orders_custom_column', array(__CLASS__, 'get_order_tag_value'), 25, 2);

                    // These hooks are used for filtering order tags - HOPS enabled
                    add_action('woocommerce_order_query_args', array(__CLASS__, 'order_tag_query_hops'));
                    add_action('woocommerce_order_list_table_restrict_manage_orders', array(__CLASS__, 'filter_orders_by_order_tag'));
                } else {
                    add_action('manage_edit-shop_order_columns', array(__CLASS__, 'add_new_column_woo_list_order_tag'));
                    add_action('manage_shop_order_posts_custom_column', array(__CLASS__, 'get_order_tag_value_in_woo_list'), 25, 2);

                    // These hooks are used for filtering order tags.
                    add_filter('restrict_manage_posts', array(__CLASS__, 'filter_orders_by_order_tag'));
                    add_filter('request', array(__CLASS__, 'filter_orders_by_order_tag_query'));
                }

                // export csv - woo order list
                add_action('export_filters', array(__CLASS__, 'add_order_tag_export_filter'));
                add_action('export_wp', array(__CLASS__, 'custom_export_woocommerce_orders'));
            }
        }
    }

    /**
     * Add tag on order placed
     *
     * @param mixed $order_id
     * @return void
     */
    public static function order_placed($order_id)
    {
        if (!$order_id || get_post_meta($order_id, '_thankyou_action_done', true)) {
            return;
        }

        // Check the user agent
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';

        if (strpos(strtolower($user_agent), 'canvas') !== false) {

            $order = wc_get_order($order_id);

            $order->update_meta_data('_order_tag', 'MobiLoud');
            $order->update_meta_data('_thankyou_action_done', true);
            $order->save();
        }
    }

    /**
     * Add new col to order list
     *
     * @return array
     */
    public static function add_new_column_order_tag($columns)
    {
        $columns['order_tag'] = 'Tag';
        return $columns;
    }

    /**
     * Show tag value in order list
     *
     * @param mixed $column_name
     * @param mixed $order
     * @return void
     */
    public static function get_order_tag_value($column_name, $order)
    {
        if ('order_tag' === $column_name) {
            $custom_data = $order->get_meta('_order_tag');
            echo esc_html($custom_data);
        }
    }

    /**
     * Check HOPS (High-Performance Order Storage) is enabled
     *
     * @return bool
     */
    public static function is_hops_enabled()
    {
        if (class_exists('WooCommerce')) {
            $hops_enabled = get_option('woocommerce_custom_orders_table_enabled');

            if ($hops_enabled === 'yes') {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    /**
     * Add new column to the default order list
     *
     * @param mixed $column_name
     * @param mixed $order
     */
    public static function add_new_column_woo_list_order_tag($columns)
    {
        $new_columns = array();

        // Insert the new column after the 'order_total' column
        foreach ($columns as $key => $column) {
            $new_columns[$key] = $column;
            if ('order_total' === $key) {
                $new_columns['order_tag'] = 'Tag';
            }
        }

        return $new_columns;
    }

    /**
     * Populate the new column with data for the default URL
     *
     * @param string $column
     * @param int $post_id
     */
    public static function get_order_tag_value_in_woo_list($column, $post_id)
    {
        if ('order_tag' === $column) {
            $order = wc_get_order($post_id);

            $order_tag = get_post_meta($order->get_id(), '_order_tag', true);

            echo esc_html($order_tag);
        }
    }

    /**
     * Create order tag filter dropdown
     * 
     * @return void
     */
    public static function filter_orders_by_order_tag($type)
    {
        global $typenow;
        if ('shop_order' === $typenow || 'shop_order' === $type) {

            $selected_tag = isset($_GET['order_tag_filter']) ? $_GET['order_tag_filter'] : '';
            echo '<select name="order_tag_filter">';
            echo '<option value="">' . __('All Tags', 'textdomain') . '</option>';
            echo '<option value="Mobiloud" ' . selected($selected_tag, 'Mobiloud', false) . '>' . __('Mobiloud', 'textdomain') . '</option>';
            echo '</select>';
        }
    }

    /**
     * Create order tag filter dropdown
     * 
     * @param mixed $vars
     * @return mixed
     */
    public static function filter_orders_by_order_tag_query($vars)
    {
        global $typenow;
        if ('shop_order' === $typenow && isset($_GET['order_tag_filter']) && $_GET['order_tag_filter'] != '') {
            $vars['meta_query'] = array(
                array(
                    'key' => '_order_tag',
                    'value' => sanitize_text_field($_GET['order_tag_filter']),
                    'compare' => '='
                )
            );
        }

        return $vars;
    }

    /**
     * Implement a custom filter UI for orders in the export tool section
     * 
     * @return void
     */
    public static function add_order_tag_export_filter()
    {
?>
        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {
                var ordersRadioButton = document.querySelector('input[value="shop_order"]');
                var filterContainer = document.createElement('div');
                filterContainer.id = 'order-tag-filter';
                filterContainer.style.display = 'none';
                filterContainer.style.marginLeft = '22px';
                filterContainer.innerHTML = `
                <label for="order_tag">Order Tag:</label>
                <select name="order_tag" id="order_tag">
                    <option value="all">All</option>
                    <option value="MobiLoud">Mobiloud</option>
                </select>
            `;

                ordersRadioButton.parentNode.parentNode.appendChild(filterContainer);

                function toggleOrderTagFilter() {
                    if (ordersRadioButton.checked) {
                        filterContainer.style.display = 'block';
                    } else {
                        filterContainer.style.display = 'none';
                    }
                }

                var radioButtons = document.querySelectorAll('input[name="content"]');
                radioButtons.forEach(function(radioButton) {
                    radioButton.addEventListener('change', toggleOrderTagFilter);
                });

                toggleOrderTagFilter();
            });
        </script>
<?php
    }

    /**
     * Custom filter for WooCommerce orders and export them to a CSV file in the tools section.
     * 
     * @param mixed $args
     * @return void
     */
    public static function custom_export_woocommerce_orders($args)
    {
        if ($args['content'] !== 'shop_order') {
            return;
        }

        // Set the headers for CSV export
        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename=woocommerce-orders.csv');
        header('Content-Type: text/csv; charset=' . get_option('blog_charset'), true);

        $order_tag = isset($_GET['order_tag']) ? sanitize_text_field($_GET['order_tag']) : 'all';

        $query = array(
            'limit'        => -1,
            'orderby'      => 'date',
            'order'        => 'DESC',
        );

        if ($order_tag !== 'all') {
            $query['meta_key'] = '_order_tag';
            $query['meta_value'] = $order_tag;
            $query['meta_compare'] = '=';
        }

        $orders = wc_get_orders($query);

        $output = fopen('php://output', 'w');

        $headers = array(
            'Order ID', 'Order Number', 'Order Date', 'Order Status', 'Order Tag', 'Order Total', 'Order Subtotal',
            'Order Discount Total', 'Order Shipping Total', 'Order Tax Total', 'Customer Name', 'Customer Email',
            'Billing Address 1', 'Billing Address 2', 'Billing City', 'Billing State', 'Billing Postcode', 'Billing Country',
            'Shipping Address 1', 'Shipping Address 2', 'Shipping City', 'Shipping State', 'Shipping Postcode', 'Shipping Country',
            'Payment Method', 'Shipping Method', 'Line Items', 'Coupons', 'Order Notes', 'Order Currency',
            'Transaction ID', 'Customer IP Address', 'Customer User Agent', 'Customer User ID'
        );

        fputcsv($output, $headers);

        foreach ($orders as $order) {
            $line_items = array();
            foreach ($order->get_items() as $item_id => $item) {
                $product = $item->get_product();
                $line_items[] = $item->get_quantity() . ' x ' . $product->get_name();
            }

            $order_notes = wc_get_order_notes(array('order_id' => $order->get_id()));

            $coupons = $order->get_coupon_codes();

            $data = array(
                $order->get_id(),
                $order->get_order_number(),
                $order->get_date_created()->date('Y-m-d H:i:s'),
                $order->get_status(),
                get_post_meta($order->get_id(), '_order_tag', true),
                $order->get_total(),
                $order->get_subtotal(),
                $order->get_total_discount(),
                $order->get_shipping_total(),
                $order->get_total_tax(),
                $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
                $order->get_billing_email(),
                $order->get_billing_address_1(),
                $order->get_billing_address_2(),
                $order->get_billing_city(),
                $order->get_billing_state(),
                $order->get_billing_postcode(),
                $order->get_billing_country(),
                $order->get_shipping_address_1(),
                $order->get_shipping_address_2(),
                $order->get_shipping_city(),
                $order->get_shipping_state(),
                $order->get_shipping_postcode(),
                $order->get_shipping_country(),
                $order->get_payment_method_title(),
                $order->get_shipping_method(),
                implode(', ', $line_items),
                implode(', ', $coupons),
                implode(' | ', wp_list_pluck($order_notes, 'content')),
                $order->get_currency(),
                $order->get_transaction_id(),
                $order->get_customer_ip_address(),
                $order->get_customer_user_agent(),
                $order->get_user_id()
            );

            fputcsv($output, $data);
        }

        fclose($output);
        exit;
    }

    /**
     * Filter query for HOPS enabled - order tag column
     * 
     * @param mixed $query_args
     * @return mixed
     */
    public static function order_tag_query_hops($query_args)
    {
        if (!is_admin()) {
            return;
        }

        // filter
        if (isset($query_args['type']) && 'shop_order' === $query_args['type'] && isset($_GET['order_tag_filter']) && $_GET['order_tag_filter'] != '') {
            $query_args['meta_query'] = array(
                array(
                    'key' => '_order_tag',
                    'value' => sanitize_text_field($_GET['order_tag_filter']),
                    'compare' => '='
                )
            );
        }

        return $query_args;
    }
}

add_action('woocommerce_loaded', array('MobiloudWooOrder', 'init'));
