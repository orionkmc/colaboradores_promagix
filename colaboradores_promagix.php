<?php
/**
* Plugin Name: Addons Colaboradores promagix
* Plugin URI: http://www.tunegocioweb.info
* Description: Agregar un tab
* Version: 0.0.1
* Author: orionkmc
* Author URI: http://www.tunegocioweb.info
* License: GPL2
*/

add_filter( 'woocommerce_product_tabs', 'woo_new_product_tab' );

function woo_new_product_tab( $tabs ) {

    global $current_user;

    $id = $current_user->ID;
    $user_rol = get_userdata( $id );
    foreach ($user_rol->roles as $key) {
        if ($key == 'colaborador') {
            $tabs['test_tab'] = array(
                'title'     => "Para Colaboradores",
                'priority'  => 1,
                'callback'  => 'woo_new_product_tab_content'
            );
            return $tabs;
        }
    }

}


function woo_new_product_tab_content() {
    global $wpdb;
    global $current_user;

    $id = $current_user->ID;
    $id_product = get_the_ID();


    $affiliate_id = $wpdb->get_results("SELECT affiliate_id FROM wp_affiliate_wp_affiliates WHERE user_id = '$id'", OBJECT);
    $_posr_cost_of_good = $wpdb->get_results("SELECT meta_value FROM wp_postmeta WHERE meta_key = '_posr_cost_of_good' AND post_id = $id_product", OBJECT);
    $_price = $wpdb->get_results("SELECT meta_value FROM wp_postmeta WHERE meta_key = '_price' AND post_id = $id_product", OBJECT);

    $affwp_settings = $wpdb->get_results("SELECT option_value FROM wp_options WHERE option_name = 'affwp_settings'", OBJECT);

    $total = $_price[0]->meta_value - $_posr_cost_of_good[0]->meta_value;
?>
    <h3>Tu Ganancia por venta: <span style="color:green;"><?= $total * (unserialize( $affwp_settings[0]->option_value )['referral_rate'] / 100) ?>$</span></h3>
    <h3>Url para compartir este producto:</h3>
    <?php
    echo "http://" .$_SERVER["HTTP_HOST"] .$_SERVER["REQUEST_URI"] ."?ref=". $affiliate_id[0]->affiliate_id; ?>
<?php
}