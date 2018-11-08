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

    <!-- Slider -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.carousel.min.js"></script>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/owl-carousel/1.3.3/owl.theme.min.css' type='text/css' media='all' />
    <center>
        <h1 class="frontpage-heading">Find Latest Coupons, Deals &amp; Offers For Today</h1>
        <h4 class="frontpage-subheading">Save More Using Coupon Codes For Top Stores</h4>
        <br>
    </center>
    <div class="owl-carousel">
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

            <a href="<?php echo $link; ?>">
                <img src="<?php echo $logo; ?>" class="img-responsive" alt="...">
            </a>

            <?php
        }
        ?>
    </div>
    <script>
        jQuery.noConflict();
        jQuery(".owl-carousel").owlCarousel({
            items: 3,
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
                    items: 3,
                    dots: true,
                    nav: false,
                    mouseDrag: true,
                    touchDrag: true
                }
            }
        });

    </script>

    <?php
    $get_args = array();
    $number = 8;

    $get_args['posts_per_page'] = $number;
    $posts = wpcoupon_get_coupons($get_args, $paged, $_max_page);
    $current_link = get_permalink();

    if ($posts) {
        ?>
        <div class="store-listings st-list-coupons">


        </div>
        <div class="ui four column grid">
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
                        <?php
                        echo esc_html(get_the_title());
                        ?>

                        <a href="<?php echo esc_attr(wpcoupon_coupon()->get_store_url()); ?>">
                            GET THIS DEAL
                        </a>
                    </div>
                </div>
            <?php }
            ?>


        </div>
    <?php }
    ?>



</div> <!-- /#content-wrap -->


<?php get_footer(); ?>
