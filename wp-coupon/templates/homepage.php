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
$current_link = get_permalink();
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
<?php the_content(); ?>
</div> <!-- /#content-wrap -->


<?php get_footer(); ?>
<!-- Slider -->
<script src="<?php echo get_stylesheet_directory_uri(); ?>/assets/js/owl.carousel.min.js"></script>
<link rel='stylesheet' href='<?php echo get_stylesheet_directory_uri(); ?>/assets/css/owl.carousel.css' type='text/css' media='all' />
<link rel='stylesheet' href='<?php echo get_stylesheet_directory_uri(); ?>/assets/css/owl.theme.default.min.css' type='text/css' media='all' />
<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery.noConflict();
        jQuery('.ui.accordion').accordion();
        jQuery(".baner-carousel").owlCarousel({
            items: 3,
            dots: true,
            autoplay: 1000,
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
