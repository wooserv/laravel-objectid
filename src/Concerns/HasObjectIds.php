<?php

namespace WooServ\LaravelObjectId\Concerns;

use WooServ\ObjectId\ObjectId;
use Illuminate\Database\Eloquent\Concerns\HasUniqueStringIds;

trait HasObjectIds
{
    use HasUniqueStringIds;

    /**
     * Generate a new unique key for the model.
     *
     * @return string
     */
    public function newUniqueId(): string
    {
        return ObjectId::generate();
    }

    /**
     * Determine if given key is valid.
     *
     * @param  mixed  $value
     * @return bool
     */
    protected function isValidUniqueId($value): bool
    {
        return is_string($value) && ObjectId::isValid($value);
    }
}
