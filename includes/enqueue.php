<?php

/**
 * Proper way to enqueue scripts and styles
 */
function enqueue_fonts()
{
    wp_enqueue_style( 'google-fonts', 'https://fonts.googleapis.com/css2?family=Titillium+Web&display=swap', false );
    wp_enqueue_style( 'adobe-fonts', 'https://use.typekit.net/dko6epv.css', false );
}
// add fonts to editor
add_action('enqueue_block_editor_assets', 'enqueue_fonts');
add_action('wp_enqueue_scripts', 'enqueue_fonts');

/**
 * Enqueue scripts and styles.
 *
 * @return void
 * @throws Exception
 */
function load_scripts()
{
    load_css_files();
    load_js_scripts();
}
add_action('wp_enqueue_scripts', 'load_scripts');

/**
 * Enqueue admin scripts and styles.
 * @return void
 * @throws Exception
 */
function admin_style()
{
}
add_action('admin_enqueue_scripts', 'admin_style');

/**
 * Enqueue all css files.
 * @return void
 * @throws Exception
 */
function load_css_files()
{
    enqueue("css", "kingdom-gaming-styles", "/assets/css/styles.css");
}


function load_js_scripts()
{
}

/**
 * Shorthand for enqueueing styles/scripts
 * @param string $fileType (CSS or JS)
 * @param string $label (unquie name for the file)
 * @param string $path (relative path to the file)
 * @return void
 * @throws Exception
 */
function enqueue(string $fileType, string $label, string $path)
{
    if ($fileType == 'css') {
        wp_enqueue_style($label, get_stylesheet_directory_uri() . $path, false, filemtime(get_stylesheet_directory() . $path));
    } else if ($fileType == 'js') {
        wp_enqueue_script($label, get_stylesheet_directory_uri() . $path, false, filemtime(get_stylesheet_directory() . $path));
    }
}
