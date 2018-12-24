<?php
/*
 * This is the child theme for WP Coupon theme, generated with Generate Child Theme plugin by catchthemes.
 *
 * (Please see https://developer.wordpress.org/themes/advanced-topics/child-themes/#how-to-create-a-child-theme)
 */
add_action( 'wp_enqueue_scripts', 'wp_coupon_child_enqueue_styles' );
function wp_coupon_child_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array('parent-style')
    );
}
/*
 * Your code goes below
 */


function homepageBanner($atts) {
    ?> 
    <center>
        <h1 class="frontpage-heading"><?php echo $atts['h1'] ?></h1>
        <h4 class="frontpage-subheading"><?php echo $atts['h4'] ?></h4>
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
            //     print_r($meta);

            $logo = wp_get_attachment_url($meta['logo'][0]);
            $link = $meta['link'][0];
            $banner_image = wp_get_attachment_url($meta['banner_image'][0]);
            $subtitle = $meta['subtitle'][0];
            ?>


            <a href="<?php echo $link; ?>" class="ui fluid card">
                <div class="image">
                    <img alt="<?php the_title(); ?>" title="<?php the_title(); ?>" src="<?php echo $banner_image; ?>">
                </div>
                <div class="content">
                    <span class="header">
                        <img src="<?php echo $logo; ?>" alt="<?php the_title(); ?>" title="<?php the_title(); ?>" class="ui middle aligned tiny image"> 
                        <div class="right">
                            <span class="header"><?php the_title(); ?></span>
                            <span class="subtitle"><?php echo '<span class="sub">' . $subtitle . '</span>'; ?> </span>
                        </div>
                </div>
            </a>
        <?php } ?>

    </div> <?php
}

add_shortcode('homepageBanner', 'homepageBanner');

function topStores($atts) {

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
        <h2 class="widget-title"><?php echo $atts['h2'] ?></h2>
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
                                <a onclick="setFsdCookie(<?php echo wpcoupon_coupon()->ID; ?>)" href="<?php echo wpcoupon_store()->get_url(); ?>"><?php echo wpcoupon_store()->name; ?></a>
                            </div>

                        </div>

                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php
}

add_shortcode('topStores', 'topStores');

function bestOffers($atts) {

    $get_args = array();
    $number = 20;

    $get_args['posts_per_page'] = $number;
    $posts = wpcoupon_get_coupons($get_args, $paged, $_max_page);


    if ($posts) {
        ?>
        <div class="best-offer">
            <h2 class="widget-title"><?php echo $atts['title'] ?></h2>
            <div class="ui four column doubling grid best-offer-column">

                <?php
                foreach ($posts as $post) {
                    wpcoupon_setup_coupon($post, $current_link);
                    $has_thumb = wpcoupon_maybe_show_coupon_thumb();
                    ?>
                    <div onclick="setFsdCookie(<?php echo wpcoupon_coupon()->ID; ?>)" class="bst-column column">

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
                        </div>
                    </div>
                <?php }
                ?>

            </div>
        </div>
        <?php
    }
}

add_shortcode('bestOffers', 'bestOffers');

function catOffer($atts) {

    $paged = wpcoupon_get_paged();
    $args = array(
        'tax_query' => array(
            'relation' => 'AND',
            array(
                'taxonomy' => 'coupon_category',
                'field' => 'term_id',
                'terms' => array($atts['title']),
                'operator' => 'IN',
            ),
        ),
    );

    $coupons = wpcoupon_get_coupons($args, $paged, $max_pages);

    if ($coupons) {
        ?>   
        <div class="best-offer-column bank-offer">
            <h2 class="widget-title"><?php echo $atts['title']; ?></h2>
            <div class="owl-carousel owl-theme bank-carousel"> <?php
                $c = 0;
                $max = $atts['max'] ? $atts['max'] : 8;
                foreach ($coupons as $post) {
                    if ($c == $max) {
                        break;
                    }
                    wpcoupon_setup_coupon($post, $current_link);
                    $has_thumb = wpcoupon_maybe_show_coupon_thumb();
                    ?>
                    <div onclick="setFsdCookie(<?php echo wpcoupon_coupon()->ID; ?>)" class="item">
                        <div class="ui segment coupon-button-type">
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
                    $c++;
                }
                ?>
            </div>
        </div>

        <?php
    }
}

add_shortcode('catOffer', 'catOffer');

function topCatOffer($atts) {

    $wcatTerms = get_terms('coupon_category', array('hide_empty' => 0, 'parent' => 0, 'number' => 4, 'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'is_featured',
                'value' => 1
            ),
    )));
    ?> 
    <div class="top-category">
        <h2 class="widget-title"><?php echo $atts['title']; ?></h2>
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
                                    <p><a onclick="setFsdCookie(<?php echo wpcoupon_coupon()->ID; ?>)" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></p>
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
    }

    add_shortcode('topCatOffer', 'topCatOffer');

    function recentlyUsedCoupons($atts) {
        $cooki = 'recent_posts';
        $ft_posts = isset($_COOKIE[$cooki]) ? json_decode($_COOKIE[$cooki], true) : null;


        if ($ft_posts) {
            ?>
            <div class="best-offer">
                <h2 class="widget-title"><?php echo $atts['title']; ?></h2>
                <div class="ui four column doubling grid best-offer-column">

                    <?php
                    foreach ($ft_posts as $post) {
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

                            </div>
                        </div>
                    <?php }
                    ?>

                </div>
            </div>
            <?php
        }
    }

    add_shortcode('recentlyUsedCoupons', 'recentlyUsedCoupons');
    