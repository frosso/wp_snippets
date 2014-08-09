<?php

/**
 * Provides an abstraction to perform queries with WP_Query
 * It makes it easier to query posts with postmeta (custom fields)
 * Class MyAbstractPostRepository
 */
abstract class MyAbstractPostRepository {
    protected $query_args = array();

    protected function __construct( array $args = array() ) {
        $this->setRawQueryArgs( array(
            'posts_per_page' => -1,
            'meta_query'     => array(),
        ) );
        $this->setRawQueryArgs( $args );
    }

    /**
     * Sets some raw WP_Query args
     *
     * @param array $args
     *
     * @return $this
     */
    public function setRawQueryArgs( array $args ) {
        $this->query_args = array_merge_recursive( $this->query_args, $args );

        return $this;
    }

    /**
     * @return $this
     */
    public static function query() {
        $class = get_called_class();

        return new $class;
    }

    /**
     * @return WP_Query
     */
    public function getQuery() {
        return new WP_Query( $this->query_args );
    }

    /**
     * @return array of WP_Posts
     */
    public function result() {
        return $this->getQuery()->posts;
    }

    /**
     * @return int
     */
    public function count() {
        $result = $this->getQuery();

        return $result->found_posts;
    }

    /**
     * Set to -1 for no limit
     *
     * @param int $count
     *
     * @return $this
     */
    public function limit( $count ) {
        $this->query_args['posts_per_page'] = $count;

        return $this;
    }

    /**
     * Provides an easier way to set a meta_query
     * So you can make a method that does this
     * <code>
     * public function used(){ // if some of your posts have a 'used' postmeta
     *      return $this->setMetaQuery('used', '1');
     * }
     * </code>
     *
     * @param string $meta_key
     * @param mixed $value
     * @param string $compare
     *
     * @return $this
     */
    public function setMetaQuery( $meta_key, $value = null, $compare = '=' ) {
        // if it's already present, we overwrite it
        $this->query_args['meta_query'][$meta_key] = array(
            'key'     => $meta_key,
            'value'   => $value,
            'compare' => $compare,
        );

        return $this;
    }
    
    /**
     * Set the post status we want to query
     * @param string|array $post_status
     *
     * @return $this
     */
    public function postStatus( $post_status = 'any' ) {
        if ( $post_status == 'any' ) {
            $post_status = array(
                'publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash'
            );
        }

        $this->query_args['post_status'] = $post_status;

        return $this;
    }
} 
