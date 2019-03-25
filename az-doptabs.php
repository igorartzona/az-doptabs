<?php
/*
 * Plugin Name: az-doptabs
 * Description: Индивидуальные дополнительные вкладки для woocommerce
 * Version: 0.4
 * Author: jvj 
 */
 
 if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_action('admin_notices', 'ad_show_admin_message');
function ad_show_admin_message(){
 
   //Check if plugin is activated
   include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
 
   //Plugin check
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
?>
<?php
add_action( 'woocommerce_product_options_advanced', 'az_add_doptabs_field' );
function az_add_doptabs_field() {
	
	global $product, $post;
	?>

	<div class="options_group">
		
		<h2><strong>Комплектация</strong></h2>
		
		<?php
			wp_editor(get_post_meta( $post->ID, '_az_complectation', true ), 'azcomplectation', array(
				'wpautop'       => 1,
				'media_buttons' => 1,
				'textarea_name' => 'azcomplectation',
				'textarea_rows' => 20,
				'tabindex'      => null,
				'editor_css'    => '<style>#wp-azcomplectation-wrap {padding:10px;} #wp-azcomplectation-editor-container .wp-editor-area{height:200px;width:100%;border: 1px solid #eee;margin-bottom: 10px;}</style>',
				'editor_class'  => 'form-field',
				'teeny'         => 0,
				'dfw'           => 0,
				'tinymce'       => 1,
				'quicktags'     => 1,
				'drag_drop_upload' => false
			) );		
		?>
		
	</div>
	
	<div class="options_group">
		
		<h2><strong>3D модель</strong></h2>		
		<?php
			wp_editor(get_post_meta( $post->ID, '_az_3dmodel', true ), '3dmodel', array(
				'wpautop'       => 1,
				'media_buttons' => 1,
				'textarea_name' => 'az3dmodel',
				'textarea_rows' => 20,
				'tabindex'      => null,
				'editor_css'    => '<style>#wp-3dmodel-wrap {padding:10px;} #wp-3dmodel-editor-container .wp-editor-area{height:200px;width:100%;border: 1px solid #eee;margin-bottom: 10px;}</style>',
				'editor_class'  => 'form-field',
				'teeny'         => 0,
				'dfw'           => 0,
				'tinymce'       => 1,
				'quicktags'     => 1,
				'drag_drop_upload' => false
			) );		
		?>
		
	</div>
	
	<div class="options_group">
		
		<h2><strong>Статус товара</strong></h2>

		<p>		
			<?php
				woocommerce_wp_checkbox( array(
				   'id'            => '_az_deprecated_checkbox',
				   'wrapper_class' => '',
				   'label'         => 'Убран из ассортимента',
				   'description'   => '',
				) );	
			?>
		</p>
		
		<p class="form-field az_altproduct_type">
			<label for="az_altproduct_type">Альтернативный товар</label> 
			<select id="az_altproduct_type"
					name="az_altproduct_type[]"
					class="wc-product-search"
					multiple="multiple"
					style="width: 50%;" 
					data-placeholder="<?php esc_attr_e( 'Search products&hellip;', 'woocommerce' ); ?>"
					data-action="woocommerce_json_search_products_and_variations" 
					data-exclude="<?php echo intval( $post->ID ); ?>"
			>
      
				<?php
					$az_altproduct_type_ids = get_post_meta( $post->ID, '_az_altproduct_type_ids', true );
					$product_ids = ! empty( $az_altproduct_type_ids ) && isset($az_altproduct_type_ids) ? array_map( 'absint',  $az_altproduct_type_ids ) : array();
					if ( $product_ids ) {
						foreach ( $product_ids as $product_id ) {
							$product      = wc_get_product( $product_id );
							$product_name = $product->get_formatted_name();
							echo '<option value="' . esc_attr( $product_id ) . '" ' . selected(true, true, false )  . '>' .
							esc_html( $product->get_formatted_name() ) . '</option>';
						 }
					  }
				?>
			</select>
			
			<span class="woocommerce-help-tip" data-tip="Выберите товары на замену"></span>
		</p>
		
	</div>
	
	<?php
}
add_action( 'woocommerce_process_product_meta', 'az_doptabs_field_save', 10 );
function az_doptabs_field_save( $post_id ) {
	
	$post_azcomplectation = isset( $_POST['azcomplectation'] ) ? $_POST['azcomplectation'] : '';
	update_post_meta( $post_id, '_az_complectation', $post_azcomplectation );
	
	$post_az3dmodel = isset( $_POST['az3dmodel'] ) ? $_POST['az3dmodel'] : '';
	update_post_meta( $post_id, '_az_3dmodel', $post_az3dmodel );
	
	$post_deprecatedcheckbox = isset( $_POST['_az_deprecated_checkbox'] ) ? 'yes' : 'no';
	update_post_meta( $post_id, '_az_deprecated_checkbox', $post_deprecatedcheckbox );
	
	
	if (  isset( $_POST['az_altproduct_type'] ) && !empty($_POST['az_altproduct_type'] ) ) {            
            update_post_meta( $post_id, '_az_altproduct_type_ids',  array_map( 'absint', (array) $_POST['az_altproduct_type'] ));          
        } else {           
            delete_post_meta( $post_id, '_az_altproduct_type_ids');            
        }	
	
}
?>
<?php
add_filter('woocommerce_product_tabs','az_add_tabs');
function az_add_tabs($tabs){
    
	global $post;
	
	$az_complectation = get_post_meta( $post->ID, '_az_complectation', true );
	$az_3dmodel = get_post_meta( $post->ID, '_az_3dmodel', true );
	
	if ( !empty($az_complectation) ) {
	
		$tabs['new_tab'] = array(
			'title'    => 'Комплектация',
			'priority' => 40,
			'callback' => 'az_complectation_tab'
		);

	}
	
	if ( !empty($az_3dmodel) ) {
	
		$tabs['new_tab2'] = array(
			'title'    => '3D модель',
			'priority' => 50,
			'callback' => 'az_3dmodel_tab'
		);

	}

    return $tabs;
}

function az_complectation_tab(){

	global $post;

	$az_complectation = get_post_meta( $post->ID, '_az_complectation', true );
	
	if ( !empty($az_complectation) ) echo $az_complectation;
			
}

function az_3dmodel_tab(){

	global $post;

	$az_3dmodel = get_post_meta( $post->ID, '_az_3dmodel', true );

	if ( !empty($az_3dmodel) ) echo $az_3dmodel;
			
}

add_action( 'woocommerce_single_product_summary', 'az_altproduct_view', 5 );
function az_altproduct_view() {
	
	global $post;	
	
	$az_deprecated = get_post_meta( $post->ID, '_az_deprecated_checkbox', true );
	
	if ( $az_deprecated == 'yes' ) {
		//echo '<div style="border:1px solid;padding:1em;margin-bottom:1em;background: bisque;">Данный товар был выведен из ассортимента</div>';
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
	}	
		
	
	$product_field_ids = get_post_meta( $post->ID, '_az_altproduct_type_ids', true );	
	
	if ( !empty( $product_field_ids ) ) {
		
		$altproduct_ids = implode(",", $product_field_ids);
		
		$altproduct_columns = count( $product_field_ids );
		
                if ( $altproduct_columns > 2 ) $col = 2; else $col = $altproduct_columns;
                
                ?>				
				
				<div class="az-front-message"><i>Товар выведен из ассортимента</i></div>
				
                <h3><?php echo _e('Рекомендуемый товар на замену:');?></h3>
                
                <div class="az-altproduct">
                
                    <?php echo do_shortcode( '[products class="related" limit="'.$altproduct_columns.'" columns="'.$col.'" ids="'.$altproduct_ids.'"]'); ?>
                
                </div>
                
                <hr />
                
                <?php		
	}
	
}

add_filter('woocommerce_get_price_html', 'az_empty_price_replace_deprecated', 2, 2);
function az_empty_price_replace_deprecated( $price ) {
		
    add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );	
	
    $az_deprecated = get_post_meta( get_the_ID(), '_az_deprecated_checkbox', true );
	
    if ( $az_deprecated == 'yes' ) { 
	
            //$price = '<div style="color:darkorange;">Товар выведен из ассортимента</div>';
			
			$price = '<i>Товар выведен из ассортимента</i>';
		
            remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );		
    	
	}
	
    return $price;
    
}

?>
<?php
//удаляем из БД созданные плагином мета-поля
register_uninstall_hook(__FILE__, 'az_doptabs_uninstall');
function az_doptabs_uninstall() {
	
	$allposts = get_posts('numberposts=-1&post_type=product&post_status=any');

	foreach( $allposts as $postinfo) {		
		delete_post_meta( $postinfo->ID, '_az_complectation');
		delete_post_meta( $postinfo->ID, '_az_3dmodel');
		delete_post_meta( $postinfo->ID, '_az_deprecated_checkbox');
		delete_post_meta( $postinfo->ID, '_az_altproduct_type_ids');		
	}
	
}
?>