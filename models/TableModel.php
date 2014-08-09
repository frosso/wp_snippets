<?php

/**
 * Another overkill. There are a lot of ORMs around, but
 * my company sometimes uses some outdated PHP version that is not compatible with them.
 *
 * Class TableModel
 */
abstract class TableModel {
    /**
     * Is the model present in the DB?
     * @var bool
     */
    protected $saved = false;

    /**
     * Did we set some property?
     * @var bool
     */
    protected $dirty = false;

    /**
     * The ID in the db that identified the field
     * @var int
     */
    public $ID;

    /**
     * Required. The table in the DB. You need to set this field
     * @var string
     */
    public $table;

    /**
     * Required. The table fields we can load
     * @var array
     */
    protected $fields = array();

    /**
     * The values of the fields
     * @var array|mixed
     */
    protected $values = array();

    private static $booted = false;

    /**
     * If we specify an id, we try to load the row from the db
     *
     * @param int $id
     */
    public function __construct( $id = null ) {
        if ( !self::isBooted() ) {
            // we need PHP >= 5.3.0 for this
            static::boot();
        }
        // if we specify an id, we load the fields from the DB
        if ( !empty( $id ) ) {
            $this->loadFromDb( $id );
        }
    }

    /**
     * Loads the $fields from the DB
     *
     * @param int $id
     */
    protected function loadFromDb( $id ) {
        /**
         * @var $wpdb wpdb
         */
        global $wpdb;

        $result = $wpdb->get_row( 'SELECT ' . implode( ', ', $this->fields ) . ' FROM ' . $wpdb->prefix . $this->table . ' WHERE ID=' . $id, ARRAY_A );

        // TODO: should we throw an exception or something?
        if ( $result != null ) {
            $this->ID = $id;
            // copio i campi dal risultato
            $this->values = $result;

            $this->saved = true;
        }
    }

    /**
     * Se la password è già stata salvata (ed è stata caricata dal db), posso aggiornare solamente il campo 'used'
     */
    public function save() {
        /**
         * @var $wpdb \wpdb
         */
        global $wpdb;

        $values = $this->values;

        // maybe we want to do something with this
        $values = apply_filters( "frosso_plugin/model/{$this->table}/pre_save", $values, $this );

        // If the values are not present in the DB, we insert them
        if ( !$this->isSaved() ) {
            // insert

            // maybe we want to set some default values
            $values = apply_filters( "frosso_plugin/model/{$this->table}/pre_insert", $values, $this );
            $wpdb->insert(
                $wpdb->prefix . $this->table,
                $values
            ); // TODO: if we fail, what should we do? Exception?
            $this->ID = $wpdb->insert_id;
            $this->saved = true;
        } else {
            // update

            // maybe we want to do something with this
            $values = apply_filters( "frosso_plugin/model/{$this->table}/pre_update", $values, $this );
            $wpdb->update(
                $wpdb->prefix . $this->table,
                $values,
                array( 'ID' => $this->ID )
            );
        }

        $this->values = $values;
        $this->dirty = false;

        return $this;

    }

    public function __get( $field ) {
        if ( !in_array( $field, $this->fields ) ) {
            throw new Exception( 'Field not found. Field required: ' . $field, 1 );
        }

        if ( !isset( $this->values[$field] ) ) {
            return null;
        }

        return $this->values[$field];

    }

    public function __set( $field, $value ) {
        if ( !in_array( $field, $this->fields ) ) {
            throw new Exception( 'Field not found. Field required: ' . $field, 1 );
        }
        // we set something. This thing is dirty now!
        // we're not doing anything with this field, yet. But who cares!
        $this->dirty = true;
        $this->values[$field] = $value;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSaved() {
        return $this->saved == true;
    }

    /**
     * @return bool
     */
    public function isDirty() {
        return $this->dirty == true;
    }

    private static function isBooted() {
        return self::$booted == true;
    }

    /**
     * If you override this method, remember also to call parent::boot();
     * It can be useful to load function and register hooks in this special place.
     * It's similar to Eloquent, I don't even know if it's the same
     * but I thought it was cool. And I decided to use this.
     */
    protected static function boot() {
        self::$booted = true;
    }
}
