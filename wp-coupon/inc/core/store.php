<?php

/**
 * Get stores
 *
 * @param array $args
 * @return array
 */
function wpcoupon_get_stores( $args = array() ){
    // get store id if in the loop and not set

    $default = array(
        'orderby'                => 'count',
        'taxonomy'               => 'coupon_store',
        'order'                  => 'DESC',
        'hide_empty'             => false,
        'include'                => array(),
        'exclude'                => array(),
        'exclude_tree'           => array(),
        'number'                 => '10',
        'hierarchical'           => false,
        'pad_counts'             => false,
        'child_of'               => 0,
        'childless'              => false,
        'cache_domain'           => 'core',
        'update_term_meta_cache' => true,
    );

    $args = wp_parse_args( $args, $default );

    return get_terms( $args );
}





/**
 * Get featured stores
 *
 * @since 1.0.0
 * @param null $store_id
 * @return object|null
 */
function wpcoupon_get_featured_stores( $number = 8 ){
    // get store id if in the loop and not set

    $args = array(
        'orderby'                => 'count',
        'order'                  => 'DESC',
        'hide_empty'             => false,
        'include'                => array(),
        'exclude'                => array(),
        'exclude_tree'           => array(),
        'number'                 => $number,
        'hierarchical'           => false,
        'pad_counts'             => false,
        'child_of'               => 0,
        'childless'              => false,
        'cache_domain'           => 'core',
        'update_term_meta_cache' => true,
        'meta_query'             =>  array(
            'relation' => 'AND',
            array(
                'key'     => '_wpc_is_featured',
                'value'   => 'on',
                'compare' => '=',
            ),
        )
    );

    return get_terms( 'coupon_store', $args );
}




/**
 * Class WPCoupon_Store
 *
 * @see WP_Term
 */
class WPCoupon_Store {


    /**
     * Term ID.
     *
     * @since 4.4.0
     * @access public
     * @var int
     */
    public $term_id;

    /**
     * The term's name.
     *
     * @since 4.4.0
     * @access public
     * @var string
     */
    public $name = '';

    /**
     * The term's slug.
     *
     * @since 4.4.0
     * @access public
     * @var string
     */
    public $slug = '';

    /**
     * The term's term_group.
     *
     * @since 4.4.0
     * @access public
     * @var string
     */
    public $term_group = '';

    /**
     * Term Taxonomy ID.
     *
     * @since 4.4.0
     * @access public
     * @var int
     */
    public $term_taxonomy_id = 0;

    /**
     * The term's taxonomy name.
     *
     * @since 4.4.0
     * @access public
     * @var string
     */
    public $taxonomy = '';

    /**
     * The term's description.
     *
     * @since 4.4.0
     * @access public
     * @var string
     */
    public $description = '';

    /**
     * ID of a term's parent term.
     *
     * @since 4.4.0
     * @access public
     * @var int
     */
    public $parent = 0;

    /**
     * Cached object count for this term.
     *
     * @since 4.4.0
     * @access public
     * @var int
     */
    public $count = 0;


    //public $term;
    public $_wpc_store_url;
    public $_wpc_store_aff_url;
    public $_wpc_store_name;
    public $_wpc_store_heading;
    public $_wpc_is_featured;
    public $_wpc_store_image_id;
    public $_wpc_store_image;
    public $_wpc_extra_info;
    public $_wpc_coupon_code;
    public $_wpc_coupon_print;
    public $_wpc_coupon_sale;


    /**
     *  Construct function
     *
     * @param mixed $p
     * @param string $current_url
     */
    function __construct( $term = null ) {

        if ( ! is_object( $term ) || ! ( $term instanceof WP_Term ) ) {
            $term = get_term( $term , 'coupon_store' );
        }

        if ( $term ) {
            foreach( $term as $k => $v ){
                $this->$k =  $v;
            }
        }
        // default meta keys
        $default_meta = array(
            '_wpc_store_url'       => '',
            '_wpc_store_aff_url'   => '',
            '_wpc_store_name'      => '',
            '_wpc_store_heading'   => '',
            '_wpc_is_featured'     => '',
            '_wpc_count_posts'     => 0,
            '_wpc_extra_info'      => '',
            '_wpc_store_image_id'  => '',
            '_wpc_store_image'     => '',
            '_wpc_coupon_code'     => 0,
            '_wpc_coupon_print'    => 0,
            '_wpc_coupon_sale'     => 0,
        );

        // Setup meta key as property
        foreach ( $default_meta as $key => $v ) {
            $this->{$key} =  get_term_meta( $this->term_id, $key, true );
        }

        /**
         * This wp embed if have not any posts
         */
        global $post;
        if ( ! is_object( $post ) ) {
            $post = ( object ) array(
                'ID' => 999999
            );
        }
    }

    /**
     * Check if this post is store
     *
     * @return bool
     */
    public function is_store(){
        return $this->term_id > 0 ?  true : false;
    }

    /**
     * If is featured store
     *
     * @return bool
     */
    public function is_featured( ) {
        return strtolower( $this->_wpc_is_featured ) == 'on' ?  true : false;
    }

    /**
     * Get term url
     *
     * @return bool|string|WP_Error
     */
    function get_url(){
        if ( ! $this->is_store() ){
            return false;
        }
        $url =  false;
        if ( ! $url && taxonomy_exists( 'coupon_store' ) ) {
            $url = get_term_link( $this );
        }
        return $url;
    }

    /**
     * Get store home URL
     *
     * @since 1.0.0
     * @param string $default
     * @return string
     */
    public function get_home_url( $default_store =  true ) {
        if ( ! $this->is_store() ) {
            return false;
        }

        $url = ( string ) $this->_wpc_store_url;
        if ( ! $url && $default_store  ) {
            $url = $this->get_url();
        }
        if ( $url ) {
            return $url;
        } else {
            return false;
        }

    }

    /**
     * Get store aff url / website URL
     *
     * @since 1.0.0
     * @param string $default
     * @return string
     */
    public function get_website_url( $default_store =  true, $aff_first = true ) {
        if ( ! $this->is_store() ) {
            return false;
        }

        if ( $aff_first ) {
            if ( $this->_wpc_store_aff_url != '' ) {
                return (string)$this->_wpc_store_aff_url;
            }
        }

        $url = ( string ) $this->_wpc_store_url;
        if ( ! $url && $default_store  ) {
            $url = $this->get_url();
        }
        if ( $url ) {
            return $url;
        } else {
            return false;
        }

    }

    function get_extra_info(){
        $content = '';
        if ( $this->_wpc_extra_info != '' ){
            $content =  apply_filters( 'the_content', $this->_wpc_extra_info );
        }
        return $content;
    }

    function get_content( $echo = false, $read_more = false ){

        if ( $read_more ) {
           $content = wpcoupon_toggle_content_more( $this->description );
        } else {
            $content = apply_filters( 'the_content', $this->description );
        }

        $content = apply_filters( 'wpcoupon_get_store_content', $content, $read_more, $this );

        if ( $echo ){
            echo  $content;
        } else {
            return $content;
        }
    }

    function has_thumbnail( ){
        return ( intval( $this->_wpc_store_image_id ) > 0 ) ? $this->_wpc_store_image_id : false;
    }

    /**
     * Get store thumbnail
     *
     * @param string $size
     * @param array $atts array of html atts
     * @return mixed|string|void
     */
    function get_thumbnail( $size = 'wpcoupon_small_thumb', $url_only = false  ){
	    $image_alt = get_post_meta(  $this->_wpc_store_image_id, '_wp_attachment_image_alt', true);
        if ( $this->has_thumbnail( ) ) {
            if ( $url_only ) {
                $image = wp_get_attachment_image_src( $this->_wpc_store_image_id, $size );
                if ( $image ) {
                    return $image[ 0 ];
                } else {
                    return false;
                }
            } else {

	            $image_title = get_the_title($this->_wpc_store_image_id);
               return wp_get_attachment_image( $this->_wpc_store_image_id, $size, false, array('alt' => $image_alt, 'title' => $image_title) );
            }

        } else if ( $this->_wpc_store_url != '' ) {
            global $_wp_additional_image_sizes;
            if( ! is_array( $size ) && isset ( $_wp_additional_image_sizes[ $size ] )  ) {
                $_size =  $_wp_additional_image_sizes[ $size ];
            }else {
                $_size = false;
            }
            $_size = wp_parse_args( $_size, array(
                'width' => 200,
                'height'=> 115
            ) );

            $url = 'http://s.wordpress.com/mshots/v1/'.urlencode( $this->_wpc_store_url ).'?w='.$_size['width'].'&h='.$_size['height'];
            $url = apply_filters( 'wpcoupon_webshoot_url', $url, $this->_wpc_store_url, $_size );

            if ( $url_only ) {
                return $url;
            }
            return '<img src="'.$url.'" alt="'. $image_alt .'">';
        } else {
            $image = get_template_directory_uri() . '/assets/images/store.png';
            if ( $url_only ) {
                return $image;
            }
            return '<img alt="" src="'.apply_filters( 'wpcoupon_store_placeholder', $image ).'" >';
        }
    }

    /**
     * Get store display name
     *
     * @since 1.0.0
     * @return string|void
     */
    public function get_display_name() {
        return ( $this->name != '' ) ? $this->name :  esc_html__( 'Untitled', 'wp-coupon' );
    }


    /**
     *  Get single store display name
     *
     * @return mixed|string|void
     */
    public function get_single_store_name(){
        $heading = ( $this->_wpc_store_heading ) ?  $this->_wpc_store_heading : wpcoupon_get_option( 'store_heading' );
        if ( $heading != '' ) {
            return str_replace( '%store_name%', $this->get_display_name(), $heading );
        } else {
            // return esc_html__( 'Unknown Name', 'wp-coupon' );
            return $this->get_display_name();
        }
    }


    /**
     * Count coupon in this store
     *
     * @since 1.0.0
     * @return array
     */
    function count_coupon(){

        $types_txt = wpcoupon_get_coupon_types();
        $type = array();
        // inital count types
        foreach ( $types_txt as $k => $t ){
            $type[ $k ] = 0;
        }

        $cache_key = "get_coupon_count:".$this->term_id;
        $cache = wp_cache_get( $cache_key, 'coupons_count' );
        if ( false !== $cache ) {
            return wp_parse_args( $cache, $type );
        }

        global $wpdb;

        $querystr = "
            SELECT DISTINCT $wpdb->posts.ID FROM $wpdb->posts
            LEFT JOIN $wpdb->postmeta ON($wpdb->posts.ID = $wpdb->postmeta.post_id)
            LEFT JOIN $wpdb->term_relationships ON($wpdb->posts.ID = $wpdb->term_relationships.object_id)
            LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
            LEFT JOIN $wpdb->terms ON( $wpdb->term_taxonomy.term_id = $wpdb->terms.term_id )
            WHERE $wpdb->terms.term_id = {$this->term_id}
            AND $wpdb->posts.post_status = 'publish'
            AND $wpdb->posts.post_type = 'coupon'
        ";

        $sql_count =  "
         SELECT count( $wpdb->postmeta.post_id ) as count_number, $wpdb->postmeta.meta_value
          FROM $wpdb->postmeta
          WHERE $wpdb->postmeta.post_id IN ( $querystr  )
          AND $wpdb->postmeta.meta_key = '_wpc_coupon_type'
          GROUP BY $wpdb->postmeta.meta_value
        ";

        $rows = $wpdb->get_results( $sql_count );
        if ( $rows ) {
            foreach( $rows as $r ) {
                $type[ $r->meta_value ] = $r->count_number;
            }
        }
        $type = array_map( 'absint', $type );
        wp_cache_set( $cache_key, $type, 'coupons_count' );

        return $type;
    }

    /**
     * Get coupons of this store
     *
     * @return null|object
     */
    function get_coupons(){
        // get store id if in the loop and not set
        global $wp_query;

        $args = array(
            'post_type'      => 'coupon',
            'posts_per_page' => -1,
            'tax_query' => array(
                'relation' => 'AND',
                array(
                    'taxonomy' => 'coupon_store',
                    'field'    => 'term_id',
                    'terms'    => array( $this->term_id ),
                    'operator' => 'NOT',
                ),
            ),
            'orderby'         => 'date',
            'order'           => 'desc'
        );

        $wp_query = new WP_Query( $args );
        return $wp_query->get_posts();
    }

    /**
     * Count coupons of this store
     *
     * @return array
     */
    function get_coupon_count(){
        return $this->count_coupon();
    }

    /**
     * Get Go Out store url
     *
     * @example: http://site.com/go-store/{coupon_id}
     *
     * @return string
     */
    function get_go_store_url(){

        $slug =  wpcoupon_get_option( 'go_store_slug', 'go-store' );

        $home = trailingslashit( home_url( ) );
        // If permalink enable
        if ( get_option( 'permalink_structure' ) != '' ) {
            return $home . $slug.'/' . $this->term_id;
        }else {
            return $home . '?go_store_id=' . $this->term_id;
        }
    }


} // end class WPCoupon_Store


/**
 * Setup store data
 *
 * @since 1.0.0
 *
 * @param null store
 * @param bool $setup_data
 * @return null
 */
function wpcoupon_setup_store( $store  = null ) {
    if ( $store instanceof WPCoupon_Store ) {
        return $store;
    }
    $_term = get_term( $store, 'coupon_store' );
    $GLOBALS['store'] =  new WPCoupon_Store( $_term );
}

/**
 * Alias of WPCoupon_Store
 *
 * This function Must use after wpcoupon_setup_coupon
 *
 * @since 1.0.0
 * @see WPCoupon_Store
 * @see  wpcoupon_setup_coupon()
 *
 * @param null $store
 * @param bool $setup_data
 * @return null|WPCoupon_Store
 */
function wpcoupon_store( $store =  null  ) {

    // check if store is set
    if ( ! $store ) {
        $store = $GLOBALS['store'];
    }

    if ( $store instanceof WPCoupon_Store ) {
        return $store;
    }

    return new WPCoupon_Store( $store );
}


/**
 * Add Edit Store CSS
 */
function wpcoupon_store_admin_list_posts_css( ) {
    wp_enqueue_style( 'wp-coupon-admin', get_template_directory_uri().'/assets/css/admin.css' );
}
add_action( 'admin_head-edit-tags.php', 'wpcoupon_store_admin_list_posts_css' );
add_action( 'admin_print_scripts-term.php', 'wpcoupon_store_admin_list_posts_css' );


/**
 * Return Search stores data
 *
 * @param array $args
 * @param array $data
 * @return array
 */
function wpcoupon_get_stores_search( $args = array() ) {
    $stores = wpcoupon_get_stores( $args );
    $r = array();
    foreach ( $stores as $k=> $store ) {
        wpcoupon_setup_store( $store );
        $n = wpcoupon_store()->count;
        $r[] =  array(
            "id"    => wpcoupon_store()->term_id,
            "title" => wpcoupon_store()->name,
            "url"   => wpcoupon_store()->get_url(),
            "home"  => wpcoupon_store()->get_home_url(),
            "image" => wpcoupon_store()->get_thumbnail(),
            "description" => sprintf( _n( '%d Coupon',  '%d Coupons', $n, 'wp-coupon' ), $n )
        );
    }
    return $r;
}



/**
 * Custom query in single taxonomy coupon_store
 * @param $query
 */
function wpcoupon_get_all_coupons( $query ) {

    if ( $query->is_tax( 'coupon_store' ) && $query->is_main_query() ) {
        $query->set( 'posts_per_page', 0 );
        $query->set( 'post_type', 'coupon' );
    }

}
///add_action( 'pre_get_posts', 'wpcoupon_get_all_coupons' );

if ( is_admin() ) {
    require_once get_template_directory().'/inc/core/term-editor.php';
}


/**
 * Auto store thumbnail if set
 *
 * Hook cmb2_save_{object_type}_fields
 *
 */
function wpcoupon_store_auto_thumbnail( $object_id , $cmb_id, $update = null,  $cmb = null ){
    if ( $cmb_id == '_wpc_store_meta' ) {
        $data = wp_parse_args( $_POST, array(
            '_wpc_auto_thumbnail' => '',
            '_wpc_store_url'      => '',
            '_wpc_store_image_id' => '',
        ) );
        if ( $data['_wpc_auto_thumbnail'] == 'on' && intval( $data['_wpc_store_image_id'] ) <= 0 ) {
            $id = wpcoupon_download_webshoot( $data['_wpc_store_url'] );
            if ( $id ) {
                update_term_meta( $object_id, '_wpc_store_image', wp_get_attachment_url( $id ) );
                update_term_meta( $object_id, '_wpc_store_image_id', $id );
            }
        }
    }
}
add_action( "cmb2_save_term_fields",'wpcoupon_store_auto_thumbnail', 60, 4 );


/**
 * Get coupons in store
 *
 * @param null $store_id
 * @param int $number
 * @param string $type empty string|active|unpopular|expires
 * @return array
 */
function wpcoupon_get_store_coupons( $store_id = null, $number = 20, $paged = null, $type ='' ){
    // get store id if in the loop and not set
    global $wp_query;
    $args = array(
        'post_type'      => 'coupon',
        'post_status'    => 'publish',
        'posts_per_page' => $number,
        'paged' => $paged,
        'tax_query' => array(
            'relation' => 'AND',
            array(
                'taxonomy' => 'coupon_store',
                'field'    => 'term_id',
                'terms'    => array( $store_id ),
                'operator' => 'IN',
            ),
        ),
        'orderby'         => 'menu_order date',
        'order'           => 'desc'
    );

    $now = current_time('timestamp');
    if ( $type == 'active' ) {

        $unpopular_coupons_percent = apply_filters( 'unpopular_coupons_percent', 20 );
        $args['meta_query'] = array(
            'relation' => 'AND',
            array(
                'relation' => 'AND',
                array(
                    'key' => '_wpc_percent_success',
                    'value' => $unpopular_coupons_percent,
                    'type' => 'NUMERIC',
                    'compare' => '>=',
                ),
                array(
                    'key' => '_wpc_percent_success',
                    'value' => '',
                    'compare' => '!=',
                ),
            ),
            array(
                'relation' => 'OR',
                array(
                    'key' => '_wpc_expires',
                    'value' => $now,
                    'type' => 'NUMERIC',
                    'compare' => '>=',
                ),
                array(
                    'key' => '_wpc_expires',
                    'value' => '',
                    'compare' => '=',
                ),
            )
        );

    } elseif ( $type == 'unpopular' ) {

        $unpopular_coupons_percent = apply_filters( 'unpopular_coupons_percent', 20 );
        $args['meta_query'] = array(
            'relation' => 'AND',
            array(
                'relation' => 'AND',
                array(
                    'key' => '_wpc_percent_success',
                    'value' => floatval( $unpopular_coupons_percent ),
                    'type' => 'NUMERIC',
                    'compare' => '<',
                ),
                array(
                    'key' => '_wpc_percent_success',
                    'value' => '',
                    'compare' => '!=',
                ),
            ),
            array(
                'relation' => 'OR',
                array(
                    'key' => '_wpc_expires',
                    'value' => $now,
                    'type' => 'NUMERIC',
                    'compare' => '>=',
                ),
                array(
                    'key' => '_wpc_expires',
                    'value' => '',
                    'compare' => '=',
                ),
            )
        );

        $args['meta_key'] = '_wpc_percent_success';
        $args['orderby'] = 'meta_value';
        $args['meta_type'] = 'NUMERIC';
        $args['order']  = 'desc';

    } elseif ( $type == 'expires' ) {

        $args['meta_query'] = array(
            'relation' => 'AND',
            array(
                'key' => '_wpc_expires',
                'compare' => 'EXISTS',
            ),
            array(
                'key' => '_wpc_expires',
                'type' => 'NUMERIC',
                'value' => 0,
                'compare' => '>',
            ),
            array(
                'key' => '_wpc_expires',
                'value' => $now,
                'type' => 'NUMERIC',
                'compare' => '<=',
            ),
        );

        $args['meta_key'] = '_wpc_expires';
        $args['orderby'] = 'meta_value';
        $args['meta_type'] = 'NUMERIC';
        $args['order']  = 'desc';
    }

    // st_debug( $args );
    $wp_query = new WP_Query( $args );
    //st_debug( $wp_query->request );

    return $wp_query->get_posts();
}