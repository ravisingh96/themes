<?php
/**
 * Template Name: Home Page
 *
 * @package WP-Coupon
 * @since 1.0.
 */


get_header();

/**
 * Hooks wpcoupon_after_header
 *
 * @see wpcoupon_page_header();
 *
 */
do_action( 'wpcoupon_after_header' );
$layout = wpcoupon_get_site_layout();
if ( ! is_active_sidebar( 'frontpage-sidebar' ) ) {
    $layout = 'no-sidebar';
}

?>
<div id="content-wrap" class="frontpage-container container <?php echo esc_attr( $layout ); ?>">

   hello
</div> <!-- /#content-wrap -->


<?php get_footer(); ?>
