<?php

/**
 * Check if the user has a certain role
 *
 * es.: user_is( 'administrator', 1 )
 *
 * @param string $role
 * @param int|WP_User $user_id
 *
 * @return bool
 */
function user_is( $role, $user_id = null ) {
    $user = $user_id;
    if ( get_class( $user_id ) != 'WP_User' ) {
        if ( is_numeric( $user_id ) )
            $user = get_userdata( $user_id );
        else
            $user = wp_get_current_user();
    }

    if ( empty( $user ) )
        return false;

    return in_array( $role, (array)$user->roles );
}

/**
 * Returns a meta_key (custom field) for the term in a taxonomy.
 *
 * If the desired meta_key does not exist, or no value is associated with it, FALSE will be returned.
 *
 * @param $taxonomy
 * @param $term_id
 * @param $meta_key
 * @param bool $default
 *
 * @return mixed|void
 */
function get_term_meta( $taxonomy, $term_id, $meta_key, $default = false ) {
    return get_option( 'tax_' . $taxonomy . '_' . $term_id . '_' . $meta_key, $default );
}


/**
 * Updates the value of an existing meta key (custom field) for the specified term in a taxonomy.
 *
 * @param $taxonomy
 * @param $term_id
 * @param $meta_key
 * @param $new_value
 *
 * @return bool
 */
function update_term_meta( $taxonomy, $term_id, $meta_key, $new_value ) {
    return update_option( 'tax_' . $taxonomy . '_' . $term_id . '_' . $meta_key, $new_value );
}

/**
 * Returns an array with all the parent terms AND the current term at the end
 *
 * @param string $taxonomy
 * @param int $term_id
 *
 * @return array
 */
function get_parent_terms_ids( $taxonomy, $term_id ) {
    if ( $term_id == 0 ) {
        return array();
    }

    $term = get_term( $term_id, $taxonomy );

    $parents = get_parent_terms_ids( $term->parent, $taxonomy );
    $parents[] = $term_id;

    return $parents;

}

if ( !function_exists( 'get_called_class' ) ) :
    /**
     * Fallback for PHP versions < 5.3.0
     */
    function get_called_class( ) {
        $bt = debug_backtrace( );
        return get_class( $bt[1]['object'] );
    }

endif;