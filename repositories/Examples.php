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