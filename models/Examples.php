<?php

class TableExample extends TableModel {
    protected $fields = array( 'creation_date', 'value' );
    public $table = 'table_example';

    // I like to keep the activation function that creates the table into the model class
    public static function onActivation() {
        global $wpdb;
        $full_table_name = $wpdb->prefix . 'table_example';

        $sql = "CREATE TABLE $full_table_name ("
            . "  ID INT NOT NULL AUTO_INCREMENT,"
            . "  creation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,"
            . "  value VARCHAR(10) DEFAULT '' NOT NULL,"
            . "  UNIQUE KEY ID (ID)"
            . ");";
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

    public static function setDefaultValues( $values ) {
        // did we set the creation date?
        if ( empty( $values['creation_date'] ) ) {
            $values['creation_date'] = date( 'Y-m-d H:i:s', time() );
        }

        return $values;
    }

    protected static function boot() {
        parent::boot();

        // you could do the same if you had an 'updated_date'
        // only that you should hook into "frosso_plugin/model/table_example/pre_save"
        add_filter( "frosso_plugin/model/table_example/pre_insert", array( 'TableExample', 'setDefaultValues' ) );

    }
}

register_activation_hook( MY_PLUGIN, array( 'TableExample', 'onActivation' ) );