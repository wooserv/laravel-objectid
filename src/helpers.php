<?php

use WooServ\ObjectId\ObjectId;

if (!function_exists('objectid')) {
    /**
     * Generate a new ObjectId string.
     */
    function objectid(): string
    {
        return ObjectId::generate();
    }
}
