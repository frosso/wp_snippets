<?php

class PostPublished  implements ITodoItem {

    /**
     * @return sting|null
     */
    public function getDescription() {
        return 'Publish at least one post';
    }

    /**
     * @return bool
     */
    public function isComplete() {
        // dummy
        return true;
    }

    /**
     * @return string
     */
    public function getLink() {
        return admin_url( 'edit.php' );
    }
}

PercentComplete::getInstance()->register( new PostPublished() );