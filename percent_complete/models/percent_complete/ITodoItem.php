<?php


interface ITodoItem {

    /**
     * @return sting|null
     */
    public function getDescription();

    /**
     * @return bool
     */
    public function isComplete();

    /**
     * @return string
     */
    public function getLink();

} 