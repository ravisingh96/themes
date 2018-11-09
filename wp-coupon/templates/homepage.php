<?php
/**
 * Template Name: Home Page
 *
 * @package WP-Coupon
 * @since 1.0.
 */
get_header();
the_post();
global $wp_query, $post, $paged, $wp_rewrite;
/**
 * Hooks wpcoupon_after_header
 *
 * @see wpcoupon_page_header();
 *
 */
do_action('wpcoupon_after_header');
$layout = wpcoupon_get_site_layout();
if (!is_active_sidebar('frontpage-sidebar')) {
    $layout = 'no-sidebar';
}
?>
<div id="content-wrap" class="frontpage-container container <?php echo esc_attr($layout); ?>">


    <center>
        <h1 class="frontpage-heading">Find Latest Coupons, Deals &amp; Offers For Today</h1>
        <h4 class="frontpage-subheading">Save More Using Coupon Codes For Top Stores</h4>
        <br>
    </center>
    <div class="owl-carousel owl-theme baner-carousel">
        <?php
        $query = new WP_Query(array(
            'post_type' => 'home_slider',
            'post_status' => array('publish')
        ));


        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            $meta = get_post_meta($post_id);
            $logo = wp_get_attachment_url($meta['logo'][0]);
            $link = $meta['link'][0];
            ?>
            <div class="item">
                <a href="<?php echo $link; ?>">
                    <img src="<?php echo $logo; ?>" class="img-responsive" alt="...">
                </a>
            </div>
        <?php } ?>

    </div>

    <?php
    $tax_args = array(
        'orderby' => 'count',
        'order' => 'DESC',
        'hide_empty' => false,
        'include' => '',
        'exclude' => '',
        'exclude_tree' => array(),
        'number' => 18,
        'hierarchical' => false,
        'pad_counts' => false,
        'child_of' => 0,
        'childless' => false,
        'cache_domain' => 'core',
        'taxonomy' => 'coupon_store',
        'update_term_meta_cache' => true,
    );
    $stores = get_terms($tax_args);
    ?>
    <div class="top-store">	
        <h2 class="widget-title">Top Stores</h2>
        <div class="widget-content ">
            <div class="ui six column doubling grid popular-stores stores-thumbs">
                <?php
                foreach ($stores as $store) {
                    wpcoupon_setup_store($store);
                    ?>
                    <div class="column">
                        <div class="store-thumb">
                            <a class="ui image middle aligned" href="<?php echo wpcoupon_store()->get_url(); ?>">
                                <?php echo wpcoupon_store()->get_thumbnail() ?></a>
                            <div class="store-name">	
                                <a href="<?php echo wpcoupon_store()->get_url(); ?>"><?php echo wpcoupon_store()->name; ?></a>
                            </div>

                        </div>

                    </div>
                <?php } ?>
            </div>
        </div>
    </div>






    <?php
    $get_args = array();
    $number = 20;

    $get_args['posts_per_page'] = $number;
    $posts = wpcoupon_get_coupons($get_args, $paged, $_max_page);
    $current_link = get_permalink();

    if ($posts) {
        ?>
        <div class="best-offer">
            <h2 class="widget-title">Today's Best Offer</h2>
            <div class="ui four column doubling grid best-offer-column">

                <?php
                foreach ($posts as $post) {
                    wpcoupon_setup_coupon($post, $current_link);
                    $has_thumb = wpcoupon_maybe_show_coupon_thumb();
                    ?>
                    <div class="column">

                        <div class="ui segment title">
                            <?php if ($has_thumb) { ?>
                                <div class="image"> <?php echo wpcoupon_coupon()->get_thumb('large'); ?></div>
                            <?php } ?>
                            <h5><?php echo esc_html(get_the_title()); ?></h5>

                            <a class="coupon-deal coupon-button" href="<?php echo esc_attr(wpcoupon_coupon()->get_store_url()); ?>">
                                GET THIS DEAL
                            </a>
                        </div>
                    </div>
                <?php }
                ?>

            </div>
        </div>
    <?php }
    ?>

    <div class="best-offer-column bank-offer">
        <h2 class="widget-title">Bank Offer's</h2>
        <?php
        $paged = wpcoupon_get_paged();
        $args = array(
            'tax_query' => array(
                'relation' => 'AND',
                array(
                    'taxonomy' => 'coupon_category',
                    'field' => 'term_id',
                    'terms' => array(55),
                    'operator' => 'IN',
                ),
            ),
                //'meta_value' => '',41
                //'orderby' => 'meta_value_num',
        );

        $coupons = wpcoupon_get_coupons($args, $paged, $max_pages);
        $current_link2 = get_permalink();
        if ($coupons) {
            ?>    <div class="owl-carousel owl-theme bank-carousel"> <?php
            foreach ($coupons as $post) {
                wpcoupon_setup_coupon($post, $current_link2);
                $has_thumb = wpcoupon_maybe_show_coupon_thumb();
                ?>
                    <div class="item">
                        <div class="ui segment">
                            <div class="image"> <?php echo wpcoupon_coupon()->get_thumb('large'); ?></div>
                            <h5> <?php echo esc_html(get_the_title()); ?></h5>
                            <a
                                title="<?php echo esc_attr(get_the_title(wpcoupon_coupon()->ID)) ?>"
                                <?php if (!wpcoupon_is_single_enable()) { ?>
                                    rel="nofollow"
                                <?php } ?>
                                class="coupon-deal coupon-button"
                                data-type="<?php echo wpcoupon_coupon()->get_type(); ?>"
                                data-coupon-id="<?php echo wpcoupon_coupon()->ID; ?>"
                                data-aff-url="<?php echo esc_attr(wpcoupon_coupon()->get_go_out_url()); ?>"
                                data-code="<?php echo esc_attr(wpcoupon_coupon()->get_code()); ?>"
                                href="<?php echo esc_attr(wpcoupon_coupon()->get_href()); ?>">GET THIS DEAL<?php // echo get_the_title( wpcoupon_coupon()->ID );  ?></a>

                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>

            <?php
        }
        ?>
    </div>

</div> <!-- /#content-wrap -->


<?php get_footer(); ?>
<!-- Slider -->
<script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/owl.carousel.min.js"></script>
<link rel='stylesheet' href='<?php echo get_stylesheet_directory_uri(); ?>/assets/css/owl.carousel.css' type='text/css' media='all' />
<link rel='stylesheet' href='<?php echo get_stylesheet_directory_uri(); ?>/assets/css/owl.theme.default.min.css' type='text/css' media='all' />
<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery.noConflict();
        jQuery(".baner-carousel").owlCarousel({
            items: 3,
            dots: true,
            loop: true,
            margin: 20,
            responsive: {
                0: {
                    items: 1,
                    dots: true,
                    mouseDrag: true,
                    touchDrag: true
                },
                480: {
                    items: 2,
                    dots: true,
                    mouseDrag: true,
                    touchDrag: true
                },
                750: {
                    items: 3,
                    dots: true,
                    mouseDrag: true,
                    touchDrag: true
                },
                1000: {
                    items: 3,
                    dots: true,
                    nav: false,
                    mouseDrag: true,
                    touchDrag: true
                }
            }
        });
        jQuery(".bank-carousel").owlCarousel({
            items: 4,
            dots: true,
            loop: true,
            margin: 10,
            responsive: {
                0: {
                    items: 1,
                    dots: true,
                    mouseDrag: true,
                    touchDrag: true
                },
                480: {
                    items: 2,
                    dots: true,
                    mouseDrag: true,
                    touchDrag: true
                },
                750: {
                    items: 3,
                    dots: true,
                    mouseDrag: true,
                    touchDrag: true
                },
                1000: {
                    items: 4,
                    dots: true,
                    nav: true,
                    mouseDrag: true,
                    touchDrag: true
                }
            }
        });
    });
</script>