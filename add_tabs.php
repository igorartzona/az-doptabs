<?php
/* ====== Добавление вкладок в админку ===== */
if ( ! defined( 'ABSPATH' ) ) exit;

/* Добавление вкладок */
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

        <div class="options_group">

	<h2><strong>Дополнительная документация</strong></h2>

            <?php
		wp_editor(get_post_meta( $post->ID, '_az_dop_docs', true ), 'azdopdocs', array(
                    'wpautop'       => 1,
                    'media_buttons' => 1,
                    'textarea_name' => 'azdopdocs',
                    'textarea_rows' => 20,
                    'tabindex'      => null,
                    'editor_css'    => '<style>.quicktags-toolbar, .wp-editor-tools, .wp-editor-wrap, .wp-switch-editor {padding: 5px 10px;} </style>',
                    'editor_class'  => 'form-field',
                    'teeny'         => 0,
                    'dfw'           => 0,
                    'tinymce'       => 1,
                    'quicktags'     => 1,
                    'drag_drop_upload' => false
		) );
            ?>

        <h2><strong>Кнопка запроса чертежа</strong></h2>

            <?php
		woocommerce_wp_checkbox( array(
		   'id'            => '_az_drawing_checkbox',
		   'wrapper_class' => '',
		   'label'         => 'Показать',
		   'description'   => '',
		) );
            ?>

	</div>

        <div class="options_group">

            <h2><strong>Видео</strong></h2>

            <?php
		wp_editor(get_post_meta( $post->ID, '_az_video', true ), 'azvideo', array(
                    'wpautop'       => 1,
                    'media_buttons' => 1,
                    'textarea_name' => 'azvideo',
                    'textarea_rows' => 20,
                    'tabindex'      => null,
                    'editor_css'    => '<style>#wp-azvideo-wrap {padding:10px;} #wp-azvideo-editor-container .wp-editor-area{height:200px;width:100%;border: 1px solid #eee;margin-bottom: 10px;}</style>',
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

	<?php
}

/* Обновление полей в БД */
add_action( 'woocommerce_process_product_meta', 'az_doptabs_field_save', 10 );
function az_doptabs_field_save( $post_id ) {

    $post_azcomplectation = isset( $_POST['azcomplectation'] ) ? $_POST['azcomplectation'] : '';
    update_post_meta( $post_id, '_az_complectation', $post_azcomplectation );

    $post_deprecatedcheckbox = isset( $_POST['_az_deprecated_checkbox'] ) ? 'yes' : 'no';
    update_post_meta( $post_id, '_az_deprecated_checkbox', $post_deprecatedcheckbox );

    if (  isset( $_POST['az_altproduct_type'] ) && !empty($_POST['az_altproduct_type'] ) ) {
        update_post_meta( $post_id, '_az_altproduct_type_ids',  array_map( 'absint', (array) $_POST['az_altproduct_type'] ));
    } else {
        delete_post_meta( $post_id, '_az_altproduct_type_ids');
    }

    $post_azdopdocs = isset( $_POST['azdopdocs'] ) ? $_POST['azdopdocs'] : '';
    update_post_meta( $post_id, '_az_dop_docs', $post_azdopdocs );

    $post_azdrawingcheckbox = isset( $_POST['_az_drawing_checkbox'] ) ? 'yes' : 'no';
    update_post_meta( $post_id, '_az_drawing_checkbox', $post_azdrawingcheckbox );

    $post_azvideo = isset( $_POST['azvideo'] ) ? $_POST['azvideo'] : '';
    update_post_meta( $post_id, '_az_video', $post_azvideo );

    $post_az3dmodel = isset( $_POST['az3dmodel'] ) ? $_POST['az3dmodel'] : '';
    update_post_meta( $post_id, '_az_3dmodel', $post_az3dmodel );
}

/*Добавление кнопок в редактор*/
//https://wp-kama.ru/id_3520/dopolnitelnyie-knopki-v-html-redaktore-wordpress-quicktags-api.html
add_action( 'admin_print_footer_scripts', 'az_add_quicktags' );
function az_add_quicktags() {
    if ( ! wp_script_is('quicktags') )
	return;

    ?>
	<script type="text/javascript">
	QTags.addButton( 'az_datasheet_tag', 'Даташит', '<h4>Datasheet ( Даташит ):</h4>', '', 'h', 'Даташит', 1 );
	QTags.addButton( 'az_passport_tag', 'Технический паспорт', '<h4>Технический паспорт :</h4>', '', 'h', 'Паспорт', 1 );
	QTags.addButton( 'az_schema_tag', 'Схема', '<h4>Схема подключения :</h4>', '', 'h', 'Схема', 1 );
	</script>
    <?php
}