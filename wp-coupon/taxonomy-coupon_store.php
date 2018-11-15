<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WP Coupon
 */
get_header();

$term = get_queried_object();
wpcoupon_setup_store($term);
$current_link = get_permalink($term);
$store_name = wpcoupon_store()->name;
$layout = wpcoupon_get_option('store_layout', 'left-sidebar');
?>
<section class="custom-page-header single-store-header">
    <div class="container">
        <?php
        /**
         * Hooked
         * @see wpcoupon_breadcrumb() - 15
         *
         * @since 1.0.0
         */
        do_action('wpcoupon_before_container');
        ?>
        <div class="inner shadow-box">
            <div class="inner-content clearfix">
                <div class="header-thumb">
                    <div class="header-store-thumb">
                        <a rel="nofollow" target="_blank" title="<?php
                        esc_html_e('Shop ', 'wp-coupon');
                        echo wpcoupon_store()->get_display_name();
                        ?>" href="<?php echo wpcoupon_store()->get_go_store_url(); ?>">
                               <?php
                               echo wpcoupon_store()->get_thumbnail();
                               ?>
                        </a>
                    </div>
                    <a class="add-favorite" data-id="<?php echo wpcoupon_store()->term_id; ?>" href="#"><i class="empty heart icon"></i><span><?php esc_html_e('Favorite This Store', 'wp-coupon'); ?></span></a>
                </div>
                <div class="header-content">

                    <h1><?php echo wpcoupon_store()->get_single_store_name(); ?></h1>
                    <?php
                    wpcoupon_store()->get_content(true, true);
                    ?>
                    <?php
                    /**
                     * Hooked
                     *
                     * @see wpcoupon_store_share() - 15
                     *
                     * @since 1.0.0
                     */
                    do_action('wpcoupon_store_content');
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>

<div id="content-wrap" class="container <?php echo esc_attr($layout); ?>">

    <div id="primary" class="content-area">
        <main id="main" class="site-main coupon-store-main" role="main">
            <?php
            /**
             * Hooked
             *
             * @see: wpcoupon_store_coupons_filter -  15
             * @see wpcoupon_store_coupons_filter
             * @since 1.0.0
             */
            do_action('wpcoupon_before_coupon_listings');

            rewind_posts();

            /**
             * get coupons of this store
             */
            global $wp_query;
            $coupons = $wp_query->posts;
            $term_id = get_queried_object_id();

            $number_active = absint(wpcoupon_get_option('store_number_active', 15));
            $coupons = wpcoupon_get_store_coupons($term_id, $number_active, 1, 'active');
            $coupon_max_pages = $wp_query->max_num_pages;
            $number_unpopular = absint(wpcoupon_get_option('store_number_unpopular', 10));
            $unpopular_coupons = wpcoupon_get_store_coupons($term_id, $number_unpopular, 1, 'unpopular');
            $unpopular_max_pages = $wp_query->max_num_pages;
            $number_expired = absint(wpcoupon_get_option('store_number_expires', 5));
            $expired_coupons = wpcoupon_get_store_coupons($term_id, $number_expired, 1, 'expires');
            $expired_max_pages = $wp_query->max_num_pages;
            // loop template
            $loop_tpl = wpcoupon_get_option('store_loop_tpl', 'full');


            // Premium Coupon

            $premiumArgs = array(
                'post_type' => 'coupon',
                'post_status' => 'publish',
                'posts_per_page' => 1,
                'tax_query' => array(
                    'relation' => 'AND',
                    array(
                        'taxonomy' => 'coupon_store',
                        'field' => 'term_id',
                        'terms' => array($term_id),
                        'operator' => 'IN',
                    ),
                ),
                'orderby' => 'menu_order date',
                'order' => 'desc'
            );
            $premiumArgs['meta_query'] = array(
                'relation' => 'AND',
                array(
                    'relation' => 'AND',
                    array(
                        'key' => 'is_premium',
                        'value' => '1',
                        'compare' => '=',
                    ),
                )
            );
            $wp_premiumQuery = new WP_Query($premiumArgs);
            $wp_premium = $wp_premiumQuery->get_posts();


            // Sponsored Coupon

            $sponsoredArgs = array(
                'post_type' => 'coupon',
                'post_status' => 'publish',
                'posts_per_page' => 5,
                'tax_query' => array(
                    'relation' => 'AND',
                    array(
                        'taxonomy' => 'coupon_store',
                        'field' => 'term_id',
                        'terms' => array($term_id),
                        'operator' => 'IN',
                    ),
                ),
                'orderby' => 'menu_order date',
                'order' => 'desc'
            );
            $sponsoredArgs['meta_query'] = array(
                'relation' => 'AND',
                array(
                    'relation' => 'AND',
                    array(
                        'key' => 'is_sponsored',
                        'value' => '1',
                        'compare' => '=',
                    ),
                )
            );
            $wp_sponsorQuery = new WP_Query($sponsoredArgs);
            $wp_sponsor = $wp_sponsorQuery->get_posts();



            if ($coupons || $unpopular_coupons || $expired_coupons) {
                ?>
                <section id="coupon-listings-store" class=" wpb_content_element">
                    <div class="ajax-coupons">
                        <!--Premium Coupon  -->
                        <?php if ($wp_premium) { ?>
                            <div class="store-listings st-list-coupons premium_coupon">
                                <?php
                                foreach ($wp_premium as $coupon) {
                                    wpcoupon_setup_coupon($coupon);
                                    $post = $coupon->post;
                                    setup_postdata($post);
                                    get_template_part('loop/loop-coupon', $loop_tpl);
                                }
                                ?>
                            </div>
                        <?php } ?>
                        <!--Premium Coupon  -->

                        <!--Sponsor Coupon  -->
                        <?php if ($wp_sponsor) { ?>
                            <div class="store-listings st-list-coupons sponsor_coupon">
                                <?php
                                foreach ($wp_sponsor as $coupon) {
                                    wpcoupon_setup_coupon($coupon);
                                    $post = $coupon->post;
                                    setup_postdata($post);
                                    get_template_part('loop/loop-coupon', $loop_tpl);
                                }
                                ?>
                            </div>
                        <?php } ?>
                        <!--Sponsor Coupon  -->

                        <div class="store-listings st-list-coupons">
                            <?php
                            foreach ($coupons as $coupon) {
                                wpcoupon_setup_coupon($coupon);
                                $post = $coupon->post;
                                setup_postdata($post);
                                get_template_part('loop/loop-coupon', $loop_tpl);
                            }
                            ?>
                        </div>
                        <!-- END .store-listings -->
                        <?php
                        $args = array(
                            'type' => 'active',
                            'number' => $number_active,
                            'store_id' => $term_id
                        );

                        if ($coupon_max_pages > 1) {
                            ?>
                            <div class="load-more wpb_content_element">
                                <a href="#" class="ui button btn btn_primary btn_large" data-doing="load_store_coupons" data-next-page="2"
                                   data-args="<?php echo esc_attr(json_encode($args)); ?>"
                                   data-loading-text="<?php esc_attr_e('Loading...', 'wp-coupon'); ?>"><?php esc_html_e('Load More Coupons', 'wp-coupon'); ?> <i class="arrow circle outline down icon"></i></a>
                            </div>
                        <?php }
                        ?>
                    </div><!-- /.ajax-coupons -->
                    <?php
                    if ($unpopular_coupons) {
                        $heading = wpcoupon_get_option('store_unpopular_coupon');
                        $heading = str_replace('%store_name%', $store_name, $heading);
                        ?>
                        <h2 class="section-heading coupon-status-heading unpopular-heading"><?php echo wp_kses_post($heading); ?></h2>
                        <?php ?>
                        <div class="ajax-coupons unpopular-coupons">
                            <div class="store-listings st-list-coupons">
                                <?php
                                foreach ($unpopular_coupons as $coupon) {
                                    wpcoupon_setup_coupon($coupon);
                                    $post = $coupon->post;
                                    setup_postdata($post);
                                    get_template_part('loop/loop-coupon', $loop_tpl);
                                }
                                ?>
                            </div>

                            <?php
                            $args = array(
                                'type' => 'unpopular',
                                'number' => $number_unpopular,
                                'store_id' => $term_id
                            );

                            if ($unpopular_max_pages > 1) {
                                ?>
                                <div class="load-more wpb_content_element">
                                    <a href="#" class="ui button btn btn_primary btn_large" data-doing="load_store_coupons" data-next-page="2"
                                       data-args="<?php echo esc_attr(json_encode($args)); ?>"
                                       data-loading-text="<?php esc_attr_e('Loading...', 'wp-coupon'); ?>"><?php esc_html_e('Load More Coupons', 'wp-coupon'); ?> <i class="arrow circle outline down icon"></i></a>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <?php
                    }

                    if ($expired_coupons) {
                        $heading = wpcoupon_get_option('store_expired_coupon');
                        $heading = str_replace('%store_name%', $store_name, $heading);
                        ?>
                        <h2 class="section-heading coupon-status-heading"><?php echo wp_kses_post($heading); ?></h2>
                        <?php ?>
                        <div class="ajax-coupons expired-coupons">
                            <div class="store-listings st-list-coupons">
                                <?php
                                foreach ($expired_coupons as $coupon) {
                                    wpcoupon_setup_coupon($coupon);
                                    $post = $coupon->post;
                                    setup_postdata($post);
                                    get_template_part('loop/loop-coupon', $loop_tpl);
                                }
                                ?>
                            </div>
                            <?php
                            $args = array(
                                'type' => 'expires',
                                'number' => $number_expired,
                                'store_id' => $term_id
                            );
                            if ($expired_max_pages > 1) {
                                ?>
                                <div class="load-more wpb_content_element">
                                    <a href="#" class="ui button btn btn_primary btn_large" data-doing="load_store_coupons" data-next-page="2"
                                       data-args="<?php echo esc_attr(json_encode($args)); ?>"
                                       data-loading-text="<?php esc_attr_e('Loading...', 'wp-coupon'); ?>"><?php esc_html_e('Load More Coupons', 'wp-coupon'); ?> <i class="arrow circle outline down icon"></i></a>
                                </div>
                            <?php }
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                </section>
                <?php
            } else { // no coupons found
                ?>
                <div class="ui warning message">
                    <i class="close icon"></i>
                    <div class="header">
                        <?php esc_html_e('Oops! No coupons found', 'wp-coupon'); ?>
                    </div>
                    <p><?php esc_html_e('There are no coupons for this store, please come back later.', 'wp-coupon'); ?></p>
                </div>
                <?php
            } // if ( $coupons )

            do_action('st_after_coupon_listings');
            echo wpcoupon_store()->get_extra_info();

            wp_reset_postdata();
            ?>
        </main><!-- #main -->
    </div><!-- #primary -->

    <?php get_sidebar('store'); ?>

</div> <!-- /#content-wrap -->

<?php get_footer(); ?>
