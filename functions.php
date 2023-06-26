<?php
/**
 * Timber starter-theme
 * https://github.com/timber/starter-theme
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.1
 */

/**
 * If you are installing Timber as a Composer dependency in your theme, you'll need this block
 * to load your dependencies and initialize Timber. If you are using Timber via the WordPress.org
 * plug-in, you can safely delete this block.
 */
$composer_autoload = __DIR__ . '/vendor/autoload.php';
if ( file_exists( $composer_autoload ) ) {
	require_once $composer_autoload;
	$timber = new Timber\Timber();
}

require get_template_directory() . "/includes/enqueue.php";
require get_template_directory() . "/includes/extends.php";

/**
 * This ensures that Timber is loaded and available as a PHP class.
 * If not, it gives an error message to help direct developers on where to activate
 */
if ( ! class_exists( 'Timber' ) ) {

	add_action(
		'admin_notices',
		function() {
			echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#timber' ) ) . '">' . esc_url( admin_url( 'plugins.php' ) ) . '</a></p></div>';
		}
	);

	add_filter(
		'template_include',
		function( $template ) {
			return get_stylesheet_directory() . '/static/no-timber.html';
		}
	);
	return;
}

/**
 * Check for ACF
 */
if ( class_exists('acf_pro') || class_exists('acf') )
{
    // Define path and URL to the ACF plugin.
    define( 'KING_ACF_PATH', get_stylesheet_directory() . '/includes/acf/' );
    define( 'KING_ACF_URL', get_stylesheet_directory_uri() . '/includes/acf/' );

    // Include the ACF plugin.
    include_once( KING_ACF_PATH . 'acf.php' );

} else {
    add_action(
		'admin_notices',
		function() {
			echo '<div class="error"><p>ACF not activated. Make sure you activate the plugin in <a href="' . esc_url( admin_url( 'plugins.php#acf' ) ) . '">' . esc_url( admin_url( 'plugins.php' ) ) . '</a></p></div>';
		}
	);
}

/**
 * Sets the directories (inside your theme) to find .twig files
 */
Timber::$dirname = array( 'templates', 'views' );

/**
 * By default, Timber does NOT autoescape values. Want to enable Twig's autoescape?
 * No prob! Just set this value to true
 */
Timber::$autoescape = false;


/**
 * We're going to configure our theme inside of a subclass of Timber\Site
 * You can move this to its own file and include here via php's include("MySite.php")
 */
class KingdomGaming extends Timber\Site {
	/** Add timber support. */
	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'theme_supports' ) );
		add_filter( 'timber/context', array( $this, 'add_to_context' ) );
		add_filter( 'timber/twig', array( $this, 'add_to_twig' ) );
		add_action( 'after_setup_theme', array( $this, 'register_menus' ) );
		parent::__construct();
	}

	public function register_menus() {
        register_nav_menus( array(
	    	'primary_menu' => __( 'Primary Menu', 'kingdom-gaming' ),
	    	'secondary_menu' => __( 'Secondary Menu', 'kingdom-gaming' ),
	    	'utility_menu' => __( 'Utitlity Menu', 'kingdom-gaming' ),
	    	'footer_menu'  => __( 'Footer Menu', 'kingdom-gaming' ),
		) );
	}

	/** This is where you add some context
	 *
	 * @param string $context context['this'] Being the Twig's {{ this }}.
	 */
	public function add_to_context( $context ) {
		$context['foo']   = 'bar';
		$context['stuff'] = 'I am a value set in your functions.php file';
		$context['notes'] = 'These values are available everytime you call Timber::context();';
		$context['menu']  = new Timber\Menu();
		$context['site']  = $this;
		return $context;
	}

	public function theme_supports() {
		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
			)
		);

		/*
		 * Enable support for Post Formats.
		 *
		 * See: https://codex.wordpress.org/Post_Formats
		 */
		add_theme_support(
			'post-formats',
			array(
				'aside',
				'image',
				'video',
				'quote',
				'link',
				'gallery',
				'audio',
			)
		);

		add_theme_support( 'menus' );
        add_theme_support( 'editor-styles' );
        add_theme_support( 'responsive-embeds' );
        add_theme_support( 'align-wide' );
        add_theme_support( 'custom-logo' );
	}

    public function get_breadcrumbs() {
        $breadcrumb = '<li><a href="'.home_url().'" rel="nofollow">Home</a></li>';
        $spacer = "<span class='breadcrumb-divider'>»</span>";

        if (is_category() || is_single()) {
            global $post;
            if($post->post_parent){
                $ancestors = get_post_ancestors($post->ID);
                foreach ( $ancestors as $ancestor ) {
                    $breadcrumb .= $spacer;
                    $breadcrumb .= '<li><a href="'.get_permalink($ancestor).'" rel="nofollow">'.get_the_title($ancestor).'</a></li>';
                }
            }
            $breadcrumb .= $spacer;
            $categories = get_the_category();
            if(!empty($categories)){
                foreach($categories as $category) {
                    $breadcrumb .= '<li><a href="'.get_category_link($category->term_id).'" rel="nofollow">'.$category->name.'</a></li>' . $spacer;
                }
            }
            $breadcrumb .= "<li>" . get_the_title() . "</li>";
        } elseif (is_search()) {
            $breadcrumb .= $spacer . "Search Results for... ";
            $breadcrumb .= '"<em>';
            $breadcrumb .= get_search_query();
            $breadcrumb .= '</em>"';
        }

        return "<ul class='breadcrumbs'>{$breadcrumb}</ul>";
    }

	/** This is where you can add your own functions to twig.
	 *
	 * @param string $twig get extension.
	 */
	public function add_to_twig( $twig ) {
		$twig->addExtension( new Twig\Extension\StringLoaderExtension() );
        $twig->addFunction( new \Twig\TwigFunction( 'get_breadcrumbs', array( $this, 'get_breadcrumbs' ) ) );
		return $twig;
	}

}

new KingdomGaming();
