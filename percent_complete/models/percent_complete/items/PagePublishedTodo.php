<?php

class PagePublishedTodo implements ITodoItem {

    /**
     * @return sting|null
     */
    public function getDescription() {
        return 'Publish at least one page';
    }

    /**
     * @return bool
     */
    public function isComplete() {
        // dummy
        return false;
    }

    /**
     * @return string
     */
    public function getLink() {
        return admin_url( 'edit.php?post_type=page' );
    }
}

PercentComplete::getInstance()->register( new PagePublishedTodo() );