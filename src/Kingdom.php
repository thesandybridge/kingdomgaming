<?php

namespace App;

use Timber\Site;
use Timber\Timber;

/**
 * Class Kingdom
 */
class Kingdom extends Site
{
	public function __construct()
	{
		add_action('after_setup_theme', array($this, 'theme_supports'));
		add_action('init', array($this, 'register_post_types'));
		add_action('init', array($this, 'register_taxonomies'));
		add_action( 'after_setup_theme', array( $this, 'register_menus' ) );

		add_filter('timber/context', array($this, 'add_to_context'));
		add_filter('timber/twig', array($this, 'add_to_twig'));
		add_filter('timber/twig/environment/options', [$this, 'update_twig_environment_options']);

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

	/**
	 * This is where you can register custom post types.
	 */
	public function register_post_types()
	{
	}

	/**
	 * This is where you can register custom taxonomies.
	 */
	public function register_taxonomies()
	{
	}

	/**
	 * This is where you add some context
	 *
	 * @param string $context context['this'] Being the Twig's {{ this }}.
	 */
	public function add_to_context($context)
	{
		$context['menu']  = Timber::get_menu();
		$context['site']  = $this;

		return $context;
	}

	public function theme_supports()
	{
		// Add default posts and comments RSS feed links to head.
		add_theme_support('automatic-feed-links');

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support('title-tag');

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support('post-thumbnails');

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

	/**
	 * This is where you can add your own functions to twig.
	 *
	 * @param Twig\Environment $twig get extension.
	 */
	public function add_to_twig($twig)
	{
		/**
		 * Required when you want to use Twig’s template_from_string.
		 * @link https://twig.symfony.com/doc/3.x/functions/template_from_string.html
		 */
		//$twig->addExtension( new Twig\Extension\StringLoaderExtension() );
        $twig->addFunction( new \Twig\TwigFunction( 'get_breadcrumbs', array( $this, 'get_breadcrumbs' ) ) );

		return $twig;
	}

	/**
	 * Updates Twig environment options.
	 *
	 * @link https://twig.symfony.com/doc/2.x/api.html#environment-options
	 *
	 * @param array $options An array of environment options.
	 *
	 * @return array
	 */
	function update_twig_environment_options($options)
	{
		// $options['autoescape'] = true;

		return $options;
	}
}
