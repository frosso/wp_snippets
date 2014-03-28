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
     * Sets a AND WHERE clause
     *
     * @param $column
     * @param string $operator
     * @param null $filter
     * @param null $type
     *
     * @return $this
     */
    public function andWhere( $column, $operator = '=', $filter = null, $type = null ) {
        if ( is_null( $filter ) ) {
            unset( $this->where_clauses[$column] );
        } else {
            $this->where_clauses[$column] = 'AND ' . $column . $operator . $filter;
            if ( $type == 'string' ) {
                $this->where_clauses[$column] = 'AND ' . $column . $operator . "'" . $filter . "'";
            }
        }

        return $this;
    }

    /**
     * Sets a OR WHERE clause
     *
     * @param $column
     * @param string $operator
     * @param null $filter
     * @param null $type
     *
     * @return $this
     */
    public function orWhere( $column, $operator = '=', $filter = null, $type = null ) {
        $clause_identifier = 'OR' . $column;
        if ( is_null( $filter ) ) {
            unset( $this->where_clauses[$clause_identifier] );
        } else {
            $this->where_clauses[$clause_identifier] = 'OR ' . $column . $operator . $filter;
            if ( $type == 'string' ) {
                $this->where_clauses[$clause_identifier] = 'OR ' . $column . $operator . "'" . $filter . "'";
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
     * @param string $select
     *
     * @return null|string
     */
    private function aggregate( $select ) {
        /** @var $wpdb wpdb */
        global $wpdb;

        $this->select = array( $select );

        $query = $this->buildQuery();

        return $wpdb->get_var( $query );
    }

    /**
     * Aggregate
     * Returns the number of items in the specified query
     *
     * @return int
     */
    public function count() {
        return $this->aggregate( 'COUNT(*)' );
    }

    /**
     * Aggregate
     * Returns the max value for the specified column
     *
     * @param $column
     *
     * @return null|string
     */
    public function max( $column ) {
        return $this->aggregate( "MAX({$column})" );
    }

    /**
     * Aggregate
     * Returns the min value for the specified column
     *
     * @param $column
     *
     * @return null|string
     */
    public function min( $column ) {
        return $this->aggregate( "MIN({$column})" );
    }

    /**
     * Aggregate
     * Returns the average value for the specified column
     *
     * @param $column
     *
     * @return null|string
     */
    public function avg( $column ) {
        return $this->aggregate( "AVG({$column})" );
    }

    /**
     * Aggregate
     * Returns the sum of the values for the specified column
     *
     * @param $column
     *
     * @return null|string
     */
    public function sum( $column ) {
        return $this->aggregate( "SUM({$column})" );
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
        $where = implode( ' ', $this->where_clauses );
        $limit = !empty( $this->limit_clause ) ? 'LIMIT ' . implode( ', ', $this->limit_clause ) : '';

        return implode( ' ', array(
            'SELECT ' . $select,
            'FROM ' . $wpdb->prefix . $this->table,
            'WHERE ' . $where,
            $limit,
        ) );
    }
} 