<?php
/**
* Plugin Name: WP Client Logo Carousel List
* Description: WP Client Logo Carousel List is a WordPress plugin which create custom post type to add client's logo's / icons to display anywhere of your site using shortcode. WP Client Logo Carousel List is for Add Logos with this Shortcode '[logocarousel]'.
* Version: 1.0.0
* Author: umakant_dataman
* Author URI: https://profiles.wordpress.org/umakant_dataman
* License: GPL2
*/

function wpclcl_logo_list() {
  $labels = array(
    'name'               => _x( 'Logos', 'post type general name' ),
    'singular_name'      => _x( 'Logo', 'post type singular name' ),
    'add_new'            => _x( 'Add New', 'Logo' ),
    'add_new_item'       => __( 'Add New Logo' ),
    'edit_item'          => __( 'Edit Logo' ),
    'new_item'           => __( 'New Logo' ),
    'all_items'          => __( 'All Logo' ),
    'view_item'          => __( 'View Logo' ),
    'search_items'       => __( 'Search Logo' ),
    'not_found'          => __( 'No Logos found' ),
    'not_found_in_trash' => __( 'No Logos found in the Trash' ), 
    'menu_name'          => 'Logos'
  );
  $args = array(
    'labels'        => $labels,
    'description'   => 'Holds our Logos and Logo specific data',
    'public'        => true,
    'menu_position' => 5,
	'menu_icon' 	=> 'dashicons-images-alt2',
    'supports'      => array( 'title', 'thumbnail' ),
    'has_archive'   => true,
  );
  register_post_type( 'logocarousel', $args ); 
}
add_action( 'init', 'wpclcl_logo_list' );
add_image_size( 'logo_thumb', 240, 240, true);

// GET FEATURED IMAGE
function wpclcl_logo_featured_image($post_ID) {
    $post_thumbnail_logo_id = get_post_thumbnail_id($post_ID);
    if ($post_thumbnail_logo_id) {
        $post_thumbnail_logo_img = wp_get_attachment_image_src($post_thumbnail_logo_id, 'logo_thumb');
        return $post_thumbnail_logo_img[0];
    }
}

// ADD NEW COLUMN
function logo_columns_head($defaults) {
    $defaults['featured_image'] = 'Logos';
    return $defaults;
}
 
// SHOW THE FEATURED IMAGE
function wpclcl_logo_columns_content($column_name, $post_ID) {
    if ($column_name == 'featured_image') {
        $post_featured_logo_image = wpclcl_logo_featured_image($post_ID);
        if ($post_featured_logo_image) {
            echo '<img src="' . $post_featured_logo_image . '" />';
        }
    }
}

add_filter('manage_posts_columns', 'logo_columns_head');
add_action('manage_posts_custom_column', 'wpclcl_logo_columns_content', 10, 3);


// create shortcode to list all Testimonials which come in blue
add_shortcode( 'logocarousel', 'wpclcl_logo_query_list' );
function wpclcl_logo_query_list( $atts ) {
    ob_start();
    $query = new WP_Query( array(
        'post_type' => 'logocarousel',
        'posts_per_page' => -1,
        'order' => 'ASC',
        'orderby' => 'title',
    ) );
    if ( $query->have_posts() ) { ?>
        <div id="owl-logo" class="owl-carousel">
            <?php while ( $query->have_posts() ) : $query->the_post(); ?>
            <div class="item dataman_logo"  id="post-<?php the_ID(); ?>">
            	<div class="logo_reviews">
					<div class="logo_img">
						<?php if ( has_post_thumbnail() ) {
							the_post_thumbnail();
						} else { ?>
							<img src="<?php echo plugins_url('images/demo.jpg', __FILE__); ?>">
						<?php } ?>
					</div>
				</div>
            </div>
			
            <?php endwhile;
            wp_reset_postdata(); ?>
        </div>
    <?php $myvariable_logo = ob_get_clean();
    return $myvariable_logo;
    }
}

add_action('wp_footer', 'wpclcl_wplogo_register_scripts');
function wpclcl_wplogo_register_scripts() {
    if (!is_admin()) {
        // register
        wp_register_script('wp_logos_script', plugins_url('js/owl.carousel.min.js', __FILE__));
		// enqueue
        wp_enqueue_script('wp_logos_script');
        wp_enqueue_script( 'jquery' );
    }
}

add_action('wp_footer', 'wpclcl_wp_logo_register_scripts');
function wpclcl_wp_logo_register_scripts() {
    if (!is_admin()) { ?>
       	<!-- Frontpage Demo -->
       <style>
		.inner-col1 .owl-buttons div {
			  top: 54%;
			}
			.owl-buttons .owl-prev {
			  left: -53px;
			}
			.owl-buttons .owl-next {
				right: -53px;
				background-position: -82px !important;
			}
			.owl-buttons div {
				background: url("<?php echo esc_url( plugins_url( 'images/arrow.png', __FILE__ ) ); ?>") no-repeat;
			    height: 68px;
				margin-top: -43px;
				outline: 0 none;
				position: absolute;
				text-indent: -9999px;
				top: 50%;
				width: 45px;
				background-position: 7px;
				z-index: 9;
			}
			.owl-buttons .owl-next {
			  right: -53px;
			  background-position: -69px 0;
			}
			.logo_img {
				border: 2px solid #000;
				margin-right: 8px;
			}
       </style>
    <script>
    jQuery(document).ready(function($) {
       jQuery("#owl-logo").owlCarousel({
        autoPlay: 3000, //Set AutoPlay to 3 seconds
		items : 4,
        navigation: false,
		navigationText : ["prev","next"],
		stopOnHover: true,
        slideSpeed: 300,
        paginationSpeed: 400,
        pagination: true,
        items: 1,
        itemsCustom: [
            [0, 1],
            [450, 2],
            [600, 3],
            [700, 3],
            [768, 3],
            [1000, 4],
            [1200, 4],
            [1400, 4],
            [1600, 4]
        ]
    });
    jQuery("#owl-logo .owl-controls .owl-prev").click(function() {
        var owl = jQuery("#owl-demo-product");
        owl.trigger('owl.prev');
    });
    jQuery("#owl-logo .owl-controls .owl-next").click(function() {
        var owl = jQuery("#owl-demo-product");
        owl.trigger('owl.next');
    });
    });
    </script>
    <?php
    }
}

add_action('wp_footer', 'wpclcl_wp_logo_register_styles');
function wpclcl_wp_logo_register_styles() {
	// register
    wp_register_style('wpclcl_wp_logos_styles', plugins_url('css/owl.carousel.css', __FILE__));
    wp_register_style('wpclcl_wp_logos_styles_theme', plugins_url('css/owl.theme.css', __FILE__));
    // enqueue
    wp_enqueue_style('wpclcl_wp_logos_styles');
    wp_enqueue_style('wpclcl_wp_logos_styles_theme');
    }
?>
