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
        // echo '<pre>';

        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            $meta = get_post_meta($post_id);
            //     print_r($meta);

            $logo = wp_get_attachment_url($meta['logo'][0]);
            $link = $meta['link'][0];
            $banner_image = wp_get_attachment_url($meta['banner_image'][0]);
            $subtitle = $meta['subtitle'][0];
            ?>
            <!--            <div class="item">
                            <a href="<?php echo $link; ?>">
                                <img src="<?php echo $logo; ?>" class="img-responsive" alt="...">
                            </a>
                        </div>-->

            <a href="<?php echo $link; ?>" class="ui fluid card">
                <div class="image">
                    <img src="<?php echo $banner_image; ?>">
                </div>
                <div class="content">
                    <span class="header">
                        <img src="<?php echo $logo; ?>" class="ui middle aligned tiny image"> 
                        <div class="right">
                            <span class="header"><?php the_title(); ?></span>
                            <span class="subtitle"><?php echo '<span class="sub">' . $subtitle . '</span>'; ?> </span>
                        </div>
                </div>
            </a>
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
                    <div class="bst-column column">

                        <div class="ui segment title coupon-button-type">
                            <?php if ($has_thumb) { ?>
                                <div class="image"> <?php echo wpcoupon_coupon()->get_thumb('large'); ?></div>
                            <?php } ?>
                            <h5><?php echo esc_html(get_the_title()); ?></h5>
                            <?php
                            switch (wpcoupon_coupon()->get_type()) {

                                case 'sale':
                                    ?>
                                                                                                                                                                                                                        <a rel="nofollow" data-type="<?php echo wpcoupon_coupon()->get_type(); ?>" data-coupon-id="<?php echo wpcoupon_coupon()->ID; ?>" data-aff-url="<?php echo esc_attr(wpcoupon_coupon()->get_go_out_url()); ?>" class="coupon-deal coupon-button" href="<?php echo esc_attr(wpcoupon_coupon()->get_href()); ?>"><?php esc_html_e('Get This Deal', 'wp-coupon'); ?> <!--<i class="shop icon"></i>--></a>
                                    <?php
                                    break;
                                case 'print':
                                    ?>
                                    <a rel="nofollow" data-type="<?php echo wpcoupon_coupon()->get_type(); ?>" data-coupon-id="<?php echo wpcoupon_coupon()->ID; ?>" data-aff-url="<?php echo esc_attr(wpcoupon_coupon()->get_go_out_url()); ?>" class="coupon-print coupon-button" href="<?php echo esc_attr(wpcoupon_coupon()->get_href()); ?>"><?php esc_html_e('Print Coupon', 'wp-coupon'); ?> <i class="print icon"></i></a>
                                    <?php
                                    break;
                                default:
                                    ?>
                                    <a rel="nofollow" data-type="<?php echo wpcoupon_coupon()->get_type(); ?>"
                                       data-coupon-id="<?php echo wpcoupon_coupon()->ID; ?>"
                                       href="<?php echo esc_attr(wpcoupon_coupon()->get_href()); ?>"
                                       class="coupon-button coupon-code"
                                       data-tooltip="<?php echo esc_attr_e('Click to copy & open site', 'wp-coupon'); ?>"
                                       data-position="top center"
                                       data-inverted=""
                                       data-code="<?php echo esc_attr(wpcoupon_coupon()->get_code()); ?>"
                                       data-aff-url="<?php echo esc_attr(wpcoupon_coupon()->get_go_out_url()); ?>">
                                        <span class="code-text" rel="nofollow"><?php echo esc_html(wpcoupon_coupon()->get_code(8)); ?></span>
                                        <span class="get-code"><?php esc_html_e('Get Code', 'wp-coupon'); ?></span>
                                    </a>
                            <?php }
                            ?>
        <!--                            <a class="coupon-deal coupon-button" href="<?php // echo esc_attr(wpcoupon_coupon()->get_store_url());                              ?>">
        GET THIS DEAL
        </a>-->
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
                    'terms' => array(41),
                    'operator' => 'IN',
                ),
            ),
                //'meta_value' => '',41
                //'orderby' => 'meta_value_num',
        );

        $coupons = wpcoupon_get_coupons($args, $paged, $max_pages);

        if ($coupons) {
            ?>    <div class="owl-carousel owl-theme bank-carousel"> <?php
            foreach ($coupons as $post) {
                wpcoupon_setup_coupon($post, $current_link);
                $has_thumb = wpcoupon_maybe_show_coupon_thumb();
                ?>
                    <div class="item">
                        <div class="ui segment">
                            <div class="image"> <?php echo wpcoupon_coupon()->get_thumb('large'); ?></div>
                            <h5> <?php echo esc_html(get_the_title()); ?></h5>
                            <?php
                            switch (wpcoupon_coupon()->get_type()) {

                                case 'sale':
                                    ?>
                                                                                                                                                                                                                      <a rel="nofollow" data-type="<?php echo wpcoupon_coupon()->get_type(); ?>" data-coupon-id="<?php echo wpcoupon_coupon()->ID; ?>" data-aff-url="<?php echo esc_attr(wpcoupon_coupon()->get_go_out_url()); ?>" class="coupon-deal coupon-button" href="<?php echo esc_attr(wpcoupon_coupon()->get_href()); ?>"><?php esc_html_e('Get This Deal', 'wp-coupon'); ?> <!--<i class="shop icon"></i>--></a>
                                    <?php
                                    break;
                                case 'print':
                                    ?>
                                    <a rel="nofollow" data-type="<?php echo wpcoupon_coupon()->get_type(); ?>" data-coupon-id="<?php echo wpcoupon_coupon()->ID; ?>" data-aff-url="<?php echo esc_attr(wpcoupon_coupon()->get_go_out_url()); ?>" class="coupon-print coupon-button" href="<?php echo esc_attr(wpcoupon_coupon()->get_href()); ?>"><?php esc_html_e('Print Coupon', 'wp-coupon'); ?> <i class="print icon"></i></a>
                                    <?php
                                    break;
                                default:
                                    ?>
                                    <a rel="nofollow" data-type="<?php echo wpcoupon_coupon()->get_type(); ?>"
                                       data-coupon-id="<?php echo wpcoupon_coupon()->ID; ?>"
                                       href="<?php echo esc_attr(wpcoupon_coupon()->get_href()); ?>"
                                       class="coupon-button coupon-code"
                                       data-tooltip="<?php echo esc_attr_e('Click to copy & open site', 'wp-coupon'); ?>"
                                       data-position="top center"
                                       data-inverted=""
                                       data-code="<?php echo esc_attr(wpcoupon_coupon()->get_code()); ?>"
                                       data-aff-url="<?php echo esc_attr(wpcoupon_coupon()->get_go_out_url()); ?>">
                                        <span class="code-text" rel="nofollow"><?php echo esc_html(wpcoupon_coupon()->get_code(8)); ?></span>
                                        <span class="get-code"><?php esc_html_e('Get Code', 'wp-coupon'); ?></span>
                                    </a>
                            <?php }
                            ?>

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

    <!-- Top Category Offer Start   -->
    <style>

    </style>


    <?php
    $wcatTerms = get_terms('coupon_category', array('hide_empty' => 0, 'parent' => 0, 'number' => 4, 'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'is_featured',
            ),
    )));
    ?> 
    <div class="top-category">
        <h2 class="widget-title">Top category offer</h2>
        <?php
        echo ' <ul class="list-inline go-gc">';
        foreach ($wcatTerms as $wcatTerm) :
            $catImage = get_field('image', $wcatTerm);
            ?> 

            <li>
                <div class="go-gcCard shown"> 
                    <div class="go-gcFront go-smooth">
                        <img class="lazy" src="<?php echo $catImage; ?>"  alt="<?php echo $wcatTerm->name; ?>" style="display: inline;">
                        <span class="go-smooth"><?php echo $wcatTerm->name; ?></span> 
                    </div> 
                    <div class="go-gcBack go-smooth"> <b><?php echo $wcatTerm->name; ?></b> 


                        <?php /* <a href="<?php echo get_term_link($wcatTerm->slug, $wcatTerm->taxonomy); ?>"><?php echo $wcatTerm->name; ?></a> */ ?>
                        <?PHP
                        $catArray = get_posts(
                                array(
                                    'posts_per_page' => 3,
                                    'post_type' => 'coupon',
                                    'tax_query' => array(
                                        array(
                                            'taxonomy' => 'coupon_category',
                                            'field' => 'term_id',
                                            'terms' => $wcatTerm->term_id,
                                        )
                                    )
                                )
                        );
                        if ($catArray) {
                            echo ' <ul class="list-unstyled">';
                            foreach ($catArray as $post) :
                                wpcoupon_setup_coupon($post, $current_link);
                                ?>

                                <li class="go-cpn-show" >
                                    <span> <?php echo wpcoupon_coupon()->get_thumb('thumbnail'); ?></span>
                                    <p><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
                                </li>



                                <?php
                            endforeach;
                            wp_reset_postdata();
                            echo ' </ul>';
                        }
                        ?>
                    </div> 
                </div> 
            </li> 
            <?php
        endforeach;
        echo '</ul></div>';
        ?>

        <!-- Top Category Offer END   -->
        <?php
        $cooki = 'recent_posts';
        $ft_posts = isset($_COOKIE[$cooki]) ? json_decode($_COOKIE[$cooki], true) : null;


        if ($ft_posts) {
            ?>
            <div class="best-offer">
                <h2 class="widget-title">Recently Used Offer</h2>
                <div class="ui four column doubling grid best-offer-column">

                    <?php
                    foreach ($ft_posts as $post) {
                        wpcoupon_setup_coupon($post, $current_link);
                        $has_thumb = wpcoupon_maybe_show_coupon_thumb();
                        ?>
                        <div class="bst-column column">

                            <div class="ui segment title">
                                <?php if ($has_thumb) { ?>
                                    <div class="image"> <?php echo wpcoupon_coupon()->get_thumb('large'); ?></div>
                                <?php } ?>
                                <h5><?php echo esc_html(get_the_title()); ?></h5>
                                <?php
                                switch (wpcoupon_coupon()->get_type()) {

                                    case 'sale':
                                        ?>
                                                                                                                                                                                                                        <a rel="nofollow" data-type="<?php echo wpcoupon_coupon()->get_type(); ?>" data-coupon-id="<?php echo wpcoupon_coupon()->ID; ?>" data-aff-url="<?php echo esc_attr(wpcoupon_coupon()->get_go_out_url()); ?>" class="coupon-deal coupon-button" href="<?php echo esc_attr(wpcoupon_coupon()->get_href()); ?>"><?php esc_html_e('Get This Deal', 'wp-coupon'); ?> <!--<i class="shop icon"></i>--></a>
                                        <?php
                                        break;
                                    case 'print':
                                        ?>
                                        <a rel="nofollow" data-type="<?php echo wpcoupon_coupon()->get_type(); ?>" data-coupon-id="<?php echo wpcoupon_coupon()->ID; ?>" data-aff-url="<?php echo esc_attr(wpcoupon_coupon()->get_go_out_url()); ?>" class="coupon-print coupon-button" href="<?php echo esc_attr(wpcoupon_coupon()->get_href()); ?>"><?php esc_html_e('Print Coupon', 'wp-coupon'); ?> <i class="print icon"></i></a>
                                        <?php
                                        break;
                                    default:
                                        ?>
                                        <a rel="nofollow" data-type="<?php echo wpcoupon_coupon()->get_type(); ?>"
                                           data-coupon-id="<?php echo wpcoupon_coupon()->ID; ?>"
                                           href="<?php echo esc_attr(wpcoupon_coupon()->get_href()); ?>"
                                           class="coupon-button coupon-code"
                                           data-tooltip="<?php echo esc_attr_e('Click to copy & open site', 'wp-coupon'); ?>"
                                           data-position="top center"
                                           data-inverted=""
                                           data-code="<?php echo esc_attr(wpcoupon_coupon()->get_code()); ?>"
                                           data-aff-url="<?php echo esc_attr(wpcoupon_coupon()->get_go_out_url()); ?>">
                                            <span class="code-text" rel="nofollow"><?php echo esc_html(wpcoupon_coupon()->get_code(8)); ?></span>
                                            <span class="get-code"><?php esc_html_e('Get Code', 'wp-coupon'); ?></span>
                                        </a>
                                <?php }
                                ?>
        <!--                            <a class="coupon-deal coupon-button" href="<?php // echo esc_attr(wpcoupon_coupon()->get_store_url());                              ?>">
        GET THIS DEAL
        </a>-->
                            </div>
                        </div>
                    <?php }
                    ?>

                </div>
            </div>
        <?php }
        ?>

        <!--    <div class="home-accordian ui accordion">
                <div class="active title">
                    How to use coupon? <i class="dropdown icon"></i>
                </div>
                <div class="active content">
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque sed nunc mi. Ut aliquet justo at sagittis interdum. Maecenas et diam odio. Curabitur tristique est risus.</p>
                </div>
                <div class="title">
        
                    Something about barakatalan - branding text for us  <i class="dropdown icon"></i>
                </div>
                <div class="content">
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque sed nunc mi. Ut aliquet justo at sagittis interdum. Maecenas et diam odio. Curabitur tristique est risus.</p>
                </div>
                <div class="title">
        
                    To place promotional banner for special events like Saudi National day.  <i class="dropdown icon"></i>
                </div>
                <div class="content">
                    <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque sed nunc mi. Ut aliquet justo at sagittis interdum. Maecenas et diam odio. Curabitur tristique est risus.</p>
                </div>
            </div>-->
        <div class="best-offer">
            <h2 class="widget-title">Looking for information on saving events around the year</h2>
            <img class="ui fluid image" src="http://s3-ap-south-1.amazonaws.com/barakatalan/fsd/2018/11/25105418/Screenshot-2018-11-25-at-10.51.23-AM.png"
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
            jQuery('.ui.accordion').accordion();
            jQuery(".baner-carousel").owlCarousel({
                items: 3,
                dots: true,
                autoplay: 1000,
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
