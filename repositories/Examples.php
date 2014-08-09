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

    /**
     * Query only published posts
     * 
     * @return $this
     */
    public function published() {
        $this->postStatus( 'publish' );

        return $this;
    }

    /**
     * To look for movies whose post status is 'publish'
     * @return $this
     */
    public function published() {
        return $this->status( 'publish' );
    }
}

/**
 * I'm using the wp_posts table here, but you can actually use any table you want. I use it with my custom tables.'
 */
class PostTableRepository extends MyAbstractTableRepository {
    protected function __construct( array $args = array() ) {
        parent::__construct( array( 'table_name' => 'posts' ) );
    }

    public function publishedBy( $user_id ) {
        return $this->andWhere( 'post_author', '=', $user_id );
    }

    public function postStatus( $status ) {
        return $this->andWhere( 'post_status', '=', $status, 'string' );
    }

    public function postType( $post_type ) {
        return $this->andWhere( 'post_type', '=', $post_type, 'string' );
    }

    public function pages() {
        return $this->postType( 'page' );
    }

    public function addMovies() {
        return $this->orWhere( 'post_type', '=', 'movie', 'string' );
    }
    
    public function paginate($count = null, $start_from = 0) {
        return $this->limit( $count, $start_from );
    }
}

// Returns movies and pages.
// Example: var_dump( PostTableRepository::query()->postStatus( 'publish' )->pages()->addMovies()->result(); );
