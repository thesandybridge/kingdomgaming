<?php

add_filter('acf/settings/save_json', 'king_acf_json_save_point');
function king_acf_json_save_point( $path ) {

    // update path
    $path = get_stylesheet_directory() . '/acf';


    // return
    return $path;

}

?>
