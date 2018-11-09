<?php
get_header();
$cate_title = single_cat_title( '', false );
$cate_id =  get_queried_object_id();

$layout = wpcoupon_get_option( 'coupon_cate_layout', 'right-sidebar' );
?>
<section class="custom-page-header">
    <div class="container">
        <?php
        /**
        * Hooked
        * @see wpcoupon_breadcrumb() - 15
        *
        * @since 1.0.0
        */
        do_action( 'wpcoupon_before_container' );
        ?>

        <div class="inner shadow-box">
            <div class="inner-content clearfix">
                <?php

                $image_id = get_term_meta( $cate_id, '_wpc_cat_image_id', true );
                $thumb = false;
                if ( $image_id > 0 ) {
                    $image= wp_get_attachment_image_src( $image_id, 'medium' );
                    if ( $image ) {
                        $thumb = '<img src="'.esc_attr( $image[0] ).'" alt=" ">';
                    }
                }

                if ( ! $thumb ) {
                    $icon = get_term_meta( $cate_id, '_wpc_icon', true );
                    if ( trim( $icon ) !== '' ){
                        $thumb = '<i class="circular '.esc_attr( $icon ).'"></i>';
                    }
                }

                if ( $thumb ) {
                ?>
                <div class="header-thumb">
                    <div class="ui center aligned icon">
                        <?php
                        echo apply_filters( '_wpcoupon_cat_thumb', $thumb );
                        ?>
                    </div>
                </div>
                <?php } ?>


                <div class="header-content">
                    <?php
                    $heading =  wpcoupon_get_option('coupon_cate_heading', esc_html__('Newest %coupon_cate% Coupons', 'wp-coupon') );
                    $heading = str_replace( '%coupon_cate%', $cate_title, $heading );
                    ?>
                    <h1><?php echo wp_kses_post( $heading ); ?></h1>
                    <?php
                    $description = get_the_archive_description();
                    if ( $description ) {
                        echo '<div class="tax-desc">' .  wpcoupon_toggle_content_more( $description ). '</div>';
                    }

                    if ( wpcoupon_get_option( 'coupon_cate_socialshare' ) ) {
                    /**
                     * Hooked
                     *
                     * @see wpcoupon_store_share() - 15
                     *
                     * @since 1.0.0
                     */
                    do_action('wpcoupon_cat_content');
                    } ?>

                </div>
            </div>
        </div>
    </div>
</section>

<div id="content-wrap" class="container <?php echo esc_attr( $layout ); ?>">

    <div id="primary" class="content-area">
        <main id="main" class="site-main ajax-coupons" role="main">
            <?php
            $sub_heading =  wpcoupon_get_option( 'coupon_cate_subheading' );
            $sub_heading = str_replace( '%coupon_cate%', $cate_title, $sub_heading );
            ?>
            <section id="store-listings-wrapper" class="st-list-coupons wpb_content_element">
                <?php if ( $sub_heading ) { ?>
                <h2 class="section-heading"><?php echo wp_kses_post( $sub_heading ); ?></h2>
                <?php } ?>
                <?php
                global $wp_rewrite;
                $paged =  wpcoupon_get_paged();
                $args = array(
                    'tax_query' => array(
                        'relation' => 'AND',
                        array(
                            'taxonomy' => 'coupon_category',
                            'field'    => 'term_id',
                            'terms'    => array( $cate_id ),
                            'operator' => 'IN',
                        ),
                    ),
                    //'meta_value' => '',
                    //'orderby' => 'meta_value_num',
                );

                $coupons =  wpcoupon_get_coupons( $args, $paged , $max_pages );
                $current_link = $_SERVER['REQUEST_URI'];

                $tpl = wpcoupon_get_option( 'coupon_cate_tpl', 'cat' );

                if (  $coupons )  {
                    foreach ( $coupons as $post ) {
                        wpcoupon_setup_coupon( $post , $current_link );
                        get_template_part( 'loop/loop-coupon', $tpl );
                    }
                }

                ?>
            </section>
            <?php

            if ( 'ajax_loadmore' ==  wpcoupon_get_option( 'coupon_cate_paging', 'ajax_loadmore' ) ) {
                if ($max_pages > $paged) { ?>
                    <div class="load-more wpb_content_element">
                        <a href="<?php next_posts($max_pages); ?>" class="ui button btn btn_primary btn_large" data-next-page="<?php echo($paged + 1); ?>"
                           data-link="<?php echo esc_attr($current_link); ?>" data-cat-id="<?php echo esc_attr( $cate_id ); ?>"
                           data-loading-text="<?php esc_attr_e('Loading...', 'wp-coupon'); ?>"><?php esc_html_e('Load More Coupons', 'wp-coupon'); ?> <i class="arrow circle outline down icon"></i></a>
                    </div>
                <?php }
            } else {
                get_template_part( 'content', 'paging' );
            }

            ?>

        </main><!-- #main -->
    </div><!-- #primary -->

    <div id="secondary" class="widget-area sidebar" role="complementary">
        <?php
        dynamic_sidebar( 'sidebar-coupon-category' );
        ?>
    </div>

    <?php
    $ads = wpcoupon_get_option( 'coupon_cate_ads', '' );
    if ( $ads ) {
        echo '<div class="clear"></div>';
        echo balanceTags( $ads );
    }
    ?>

</div> <!-- /#content-wrap -->

<?php
get_footer();
?>
