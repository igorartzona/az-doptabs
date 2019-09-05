<?php
/*
 * Plugin Name: az-doptabs
 * Description: Индивидуальные дополнительные вкладки для woocommerce
 * Version: 1.2.0
 * Author: jvj
 * WC requires at least: 3.0.0
 * WC tested up to: 3.6
 */

 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/* Сообщение в админке если не активен woo */
add_action('admin_notices', 'az_show_admin_message');
function az_show_admin_message(){

   include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

   if( !is_plugin_active( 'woocommerce/woocommerce.php' ) ){
       ?>

    <div id="message" class="is-dismissible notice notice-warning">
        <p>
            <?php _e( 'Плагин <a href="https://wordpress.org/plugins/woocommerce/" target=_blank >Woocommerce</a> не активен. Без него плагин <strong>az-doptabs</strong> бесполезен.' );?>
        </p>
    </div>

       <?php
   }
}

/* подключение файлов*/
define( 'CD_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'CD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );


require_once ( CD_PLUGIN_PATH . 'add_tabs.php');
require_once ( CD_PLUGIN_PATH . 'view_tabs.php');

/* удаляем из БД созданные плагином мета-поля */
register_uninstall_hook(__FILE__, 'az_doptabs_uninstall');
function az_doptabs_uninstall() {

	//$allposts = get_posts('numberposts=-1&post_type=product&post_status=any');

	foreach( $allposts as $postinfo) {
		//delete_post_meta( $postinfo->ID, '_az_complectation');
		//delete_post_meta( $postinfo->ID, '_az_3dmodel');
		//delete_post_meta( $postinfo->ID, '_az_deprecated_checkbox');
		//delete_post_meta( $postinfo->ID, '_az_altproduct_type_ids');
	}

}