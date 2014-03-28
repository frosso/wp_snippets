<?php

class MyMoviesCPTRepository extends MyAbstractPostRepository {
    protected function __construct( array $args = array() ) {
        parent::__construct( array( 'post_type' => 'movie' ) );
    }

    /**
     * To look for movies that have a year=$year postmeta
     *
     * @param $year
     *
     * @return $this
     */
    public function fromYear( $year ) {
        return $this->setMetaQuery( 'year', $year );
    }

    public function status( $post_status ) {
        return $this->setRawQueryArgs( array( 'post_status' => $post_status ) );
    }

    /**
     * To look for movies whose post status is 'publish'
     * @return $this
     */
    public function published() {
        return $this->status( 'publish' );
    }
}

class PostTableRepository extends MyAbstractTableRepository {
    protected function __construct( array $args = array() ) {
        parent::__construct( array( 'table_name' => 'posts' ) );
    }

    public function publishedBy( $user_id ) {
        return $this->setWhere( 'post_author', '=', $user_id );
    }

    public function postStatus( $status ) {
        return $this->setWhere( 'post_status', '=', $status, 'string' );
    }

    public function postType( $post_type ) {
        return $this->setWhere( 'post_type', '=', $post_type, 'string' );
    }

    public function pages() {
        return $this->postType( 'page' );
    }
}

// Example: var_dump( PostTableRepository::query()->postStatus( 'publish' )->pages()->result() );