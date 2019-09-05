<?php
/* ====== Отображение вкладок на фронтенде ===== */
if ( ! defined( 'ABSPATH' ) ) exit;

/* Добавление вкладок */
add_filter('woocommerce_product_tabs','az_add_tabs');
function az_add_tabs($tabs){

    global $post;

    $az_complectation = get_post_meta( $post->ID, '_az_complectation', true );
    $az_dopdocs = get_post_meta( $post->ID, '_az_dop_docs', true );
    $az_video = get_post_meta( $post->ID, '_az_video', true );
    $az_3dmodel = get_post_meta( $post->ID, '_az_3dmodel', true );


    if ( !empty($az_complectation) ) {

	$tabs['new_tab'] = array(
            'title'    => 'Комплектация',
            'priority' => 40,
            'callback' => 'az_complectation_tab'
	);

    }

    if ( !empty($az_dopdocs) ) {

	$tabs['new_tab4'] = array(
            'title'    => 'Документация',
            'priority' => 45,
            'callback' => 'az_dopdocs_tab'
	);

    }


    if ( !empty($az_video) ) {

	$tabs['new_tab2'] = array(
            'title'    => 'Видео',
            'priority' => 50,
            'callback' => 'az_video_tab'
	);

    }

    if ( !empty($az_3dmodel) ) {

	$tabs['new_tab3'] = array(
            'title'    => '3D модель',
            'priority' => 55,
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

function az_dopdocs_tab(){

	global $post;

	$az_dopdocs = get_post_meta( $post->ID, '_az_dop_docs', true );

	if ( !empty($az_dopdocs) ) echo $az_dopdocs;

        $az_draw = get_post_meta($post->ID, '_az_drawing_checkbox', true);

        if ($az_draw == 'yes') : ?>

            <div class="docs-div">

                <button>Запросить CAD-файл</button>

            </div>

        <?php   endif;

}


function az_video_tab(){

	global $post;

	$az_video = get_post_meta( $post->ID, '_az_video', true );

	if ( !empty($az_video) && (stripos($az_video, 'youtu') > 0) ) {

            ?>

            <iframe src="<?php echo $az_video; ?>" width="560" height="315" frameborder="0" allowfullscreen="allowfullscreen"></iframe>

            <?php

	}

}

function az_3dmodel_tab(){

	global $post;

	$az_3dmodel = get_post_meta( $post->ID, '_az_3dmodel', true );

	if ( !empty($az_3dmodel) ) echo $az_3dmodel;

}



/* Вывод альтернативных продуктов */
add_action( 'woocommerce_single_product_summary', 'az_altproduct_view', 5 );
function az_altproduct_view() {

	global $post;

	$az_deprecated = get_post_meta( $post->ID, '_az_deprecated_checkbox', true );

	if ( $az_deprecated == 'yes' ) {
		//echo '<div style="border:1px solid;padding:1em;margin-bottom:1em;background: bisque;">Данный товар был выведен из ассортимента</div>';
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );

	$product_field_ids = get_post_meta( $post->ID, '_az_altproduct_type_ids', true );

	if ( !empty( $product_field_ids ) ) {

		$altproduct_ids = implode(",", $product_field_ids);

		$altproduct_columns = count( $product_field_ids );

                $col = ( $altproduct_columns > 2 ) ? 2 : $altproduct_columns;

                ?>

                <h3><?php echo _e('Рекомендуемый товар на замену:');?></h3>

                <div class="az-altproduct">

                    <?php echo do_shortcode( '[products class="related" limit="'.$altproduct_columns.'" columns="'.$col.'" ids="'.$altproduct_ids.'"]'); ?>

                </div>

                <hr />

                <?php
	}

        }

}

/* Надпись вместо цены у товара, выведенного из ассортимента */
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