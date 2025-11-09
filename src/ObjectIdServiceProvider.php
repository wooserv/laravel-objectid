<?php

namespace WooServ\LaravelObjectId;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Schema\Blueprint;

class ObjectIdServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Blueprint macro for migrations
        Blueprint::macro('objectId', function ($column = 'id', $primary = true) {
            /** @var \Illuminate\Database\Schema\Blueprint $this */
            $col = $this->string($column, 24);
            if ($primary) {
                $col->primary();
            }
            return $col;
        });
    }

    public function register(): void
    {
        // Optional: include helpers
        if (file_exists(__DIR__ . '/helpers.php')) {
            require_once __DIR__ . '/helpers.php';
        }
    }
}
