<?php

class PercentComplete {

    protected static $instance = null;

    /**
     * @var array
     */
    protected $items = array();

    private function __construct() {
    }

    /**
     * Singleton implementation
     * @return PercentComplete
     */
    public static function getInstance() {
        if ( self::$instance == null ) {
            self::$instance = new PercentComplete();
        }

        return self::$instance;
    }

    /**
     * Registers an Item
     *
     * @param ITodoItem $item
     *
     * @return $this
     */
    public function register( ITodoItem $item ) {
        // completed items go to the beginning
        $function = 'array_push';
        if ( $item->isComplete() ) {
            $function = 'array_unshift';
        }
        $function( $this->items, $item );

        return $this;
    }

    /**
     * @return ITodoItem[]
     */
    public function getItems() {
        return $this->items;
    }

    /**
     * Returns the % number of completed elements. Ex.: 0.8
     * @return float
     */
    public function getPercentComplete() {
        $completed = 0;
        foreach ( $this->getItems() as $item ) {
            /* @var $item ITodoItem */
            if ( $item->isComplete() ) {
                $completed = $completed + 1;
            }
        }

        return $completed / count( $this->getItems() );
    }

}