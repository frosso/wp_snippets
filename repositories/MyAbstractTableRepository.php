<?php

/**
 * This is probably an overkill, but I use it for some simple custom tables
 *
 * Class MyAbstractTableRepository
 */
abstract class MyAbstractTableRepository {
    protected $query_args = array();

    protected $select = array( '*' );

    protected $where_clauses = array(
        0 => '1=1',
    );

    protected $limit_clause = array();

    protected $table;

    /**
     * For the moment, let's provide a table name
     *
     * @param array $args
     */
    protected function __construct( array $args = array() ) {
        $this->table = $args['table_name'];
    }

    /**
     * @return $this
     */
    public static function query() {
        $class = get_called_class();

        return new $class;
    }

    /**
     * Sets a WHERE clause
     *
     * @param $column
     * @param string $operator
     * @param null $filter
     * @param null $type
     *
     * @return $this
     */
    public function setWhere( $column, $operator = '=', $filter = null, $type = null ) {
        if ( is_null( $filter ) ) {
            unset( $this->where_clauses[$column] );
        } else {
            $this->where_clauses[$column] = $column . $operator . $filter;
            if ( $type == 'string' ) {
                $this->where_clauses[$column] = $column . $operator . "'" . $filter . "'";
            }
        }

        return $this;
    }

    /**
     * @param int|null $count
     *
     * @return $this
     */
    public function limit( $count = null ) {
        if ( is_null( $count ) ) {
            $this->limit_clause = null;
        } else {
            $this->limit_clause = array( 0, $count );
        }

        return $this;
    }

    /**
     * Returns the number of items in the specified query
     *
     * @return int
     */
    public function count() {
        $this->select = array( 'COUNT(*)' );
        /** @var $wpdb wpdb */
        global $wpdb;

        $query = $this->buildQuery();

        return $wpdb->get_var( $query );
    }

    /**
     * @return array
     */
    public function result() {
        /** @var $wpdb wpdb */
        global $wpdb;

        $query = $this->buildQuery();

        return $wpdb->get_results( $query, ARRAY_A );
    }

    protected function buildQuery() {
        /** @var $wpdb wpdb */
        global $wpdb;

        $select = implode( ', ', $this->select );
        $where = implode( ' AND ', $this->where_clauses );
        $limit = !empty( $this->limit_clause ) ? 'LIMIT ' . implode( ', ', $this->limit_clause ) : '';

        return implode( ' ', array(
            'SELECT ' . $select,
            'FROM ' . $wpdb->prefix . $this->table,
            'WHERE ' . $where,
            $limit,
        ) );
    }
} 