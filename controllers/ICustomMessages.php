<?php

interface ICustomMessages {
    /**
     * Merges my messages with others
     *
     * @param array $other_messages
     *
     * @return array
     */
    public function mergeCustomMessages( $other_messages );
} 