<?php

/**
 * Provides a better WPML integration
 *
 * User: Francesco Rosso
 */
class MyWPMLFilterGetPageId {
    public function __construct() {
        add_filter( 'frosso_plugin/get_post_id', array( &$this, 'filter_post_id' ) );
    }

    public function filter_post_id( $page_id ) {
        if ( !function_exists( 'icl_object_id' ) ) {
            return $page_id;
        }

        // WPML treats any other object != than 'page' as a 'post'
        $post_type = get_post_type( $page_id );
        if ( $post_type != 'page' ) {
            $post_type = 'post';
        }

        // returns the object ID of for the displayed language
        // if missing returns the original
        return $page_id = icl_object_id( $page_id, $post_type, true );
    }
}

new MyWPMLFilterGetPageId();