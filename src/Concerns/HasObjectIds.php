<?php

namespace WooServ\LaravelObjectId\Concerns;

use WooServ\ObjectId\ObjectId;

trait HasObjectIds
{
    protected static function bootHasObjectIds(): void
    {
        static::creating(function ($model) {
            $key = $model->getKeyName();
            if (empty($model->$key)) {
                $model->$key = (string) ObjectId::generate();
            }
        });
    }

    public function getIncrementing(): bool
    {
        return false;
    }

    public function getKeyType(): string
    {
        return 'string';
    }
}
