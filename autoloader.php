<?php

class MyAutoloader {
    /**
     * Static loader method
     *
     * @param string $class
     */
    public static function load( $class ) {
        // your classes should be namespaced
        // an example could be the class CustomPrefix\controllers\MyController.php
        // that should be available in the path: dirname( __FILE__ ) . '/controllers/MyController.php'
        $prefix = 'CustomPrefix';
        if ( strpos( $class, $prefix ) !== false ) {
            $class = str_replace( $prefix, '', $class );
            $class = str_replace( '\\', DIRECTORY_SEPARATOR, $class );
            require_once dirname( __FILE__ ) . '/' . $class . '.php';
        }
    }
}

/**
 * The autoload-stack could be inactive so the function will return false
 */
if ( in_array( '__autoload', (array)spl_autoload_functions() ) )
    spl_autoload_register( '__autoload' );
spl_autoload_register( array( 'MyAutoloader', 'load' ) );
