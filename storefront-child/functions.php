<?php
/**
 * Создаем поле для счетчика просмотров
 * $view название колонки в базе данных
 * 
 */
function product_views() {
    global $post, $wpdb;
    $view = 'views';
    $wpdb->query($wpdb->prepare("UPDATE $wpdb->postmeta SET meta_value = (meta_value+1) WHERE post_id = %d AND meta_key = %s", $post->ID, $view));
    add_post_meta($post->ID, $view, 1, true);
}
// Добавляем нашу функцию к хуку wp_head
add_action('wp_head', 'product_views');

/**
 * Получаем IDs заказов для товара по ID.
 * $product_id (обязательный) - ID товара.
 * $order_status (дополнительный фильтр) по дефолту ставим 'wc-completed'
 *
 * получаем массив заказов
 */
function get_orders_ids_by_product_id( $product_id, $order_status = array( 'wc-completed' ) ) {
    global $wpdb;

    $results = $wpdb->get_col("
        SELECT order_items.order_id
        FROM {$wpdb->prefix}woocommerce_order_items as order_items
        LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id
        LEFT JOIN {$wpdb->posts} AS posts ON order_items.order_id = posts.ID
        WHERE posts.post_type = 'shop_order'
        AND posts.post_status IN ( '" . implode( "','", $order_status ) . "' )
        AND order_items.order_item_type = 'line_item'
        AND order_item_meta.meta_key = '_product_id'
        AND order_item_meta.meta_value = '$product_id'
    ");
    
    return $results;
}
