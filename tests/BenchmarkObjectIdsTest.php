<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase;
use WooServ\ObjectId\ObjectId;
use Illuminate\Support\Str;
use WooServ\LaravelObjectId\ObjectIdServiceProvider;

class BenchmarkObjectIdsTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ObjectIdServiceProvider::class];
    }

    protected int $iterations = 10000;

    public function test_benchmark_objectid_vs_uuid_vs_ulid()
    {
        $results = [];

        // ObjectId
        $results['ObjectId'] = $this->measure(function () {
            ObjectId::generate();
        });

        // Laravel helper
        $results['objectid() helper'] = $this->measure(function () {
            objectid();
        });

        // UUID
        $results['UUID'] = $this->measure(function () {
            Str::uuid()->toString();
        });

        // ULID
        $results['ULID'] = $this->measure(function () {
            Str::ulid()->toBase32();
        });

        echo "\n\nLaravel ObjectId Benchmark (" . $this->iterations . " iterations)\n";
        echo "----------------------------------------------------------\n";

        foreach ($results as $key => $avg) {
            echo sprintf("%-20s : %.3f µs per ID\n", $key, $avg * 1_000_000);
        }

        $fastest = array_keys($results, min($results))[0];
        echo "----------------------------------------------------------\n";
        echo "Fastest: {$fastest}\n";

        $this->assertTrue(true);
    }

    public function test_database_insert_benchmark()
    {
        Schema::create('bench', function ($t) {
            $t->string('id', 36)->primary();
            $t->string('type');
        });

        $iterations = 1000;
        $drivers = [
            'ObjectId' => fn() => ObjectId::generate(),
            'UUID' => fn() => Str::uuid()->toString(),
            'ULID' => fn() => Str::ulid()->toBase32(),
        ];

        echo "\n\nDatabase Insert Benchmark ({$iterations} inserts)\n";
        echo "----------------------------------------------------------\n";

        foreach ($drivers as $name => $generator) {
            DB::table('bench')->delete();

            $start = hrtime(true);
            for ($i = 0; $i < $iterations; $i++) {
                DB::table('bench')->insert([
                    'id' => $generator(),
                    'type' => strtolower($name),
                ]);
            }
            $elapsed = (hrtime(true) - $start) / 1e6; // ms

            echo sprintf("%-10s : %.2f ms total (%.3f ms/insert)\n",
                $name,
                $elapsed,
                $elapsed / $iterations
            );
        }

        echo "----------------------------------------------------------\n";
        $this->assertTrue(true);
    }

    protected function measure(callable $callback): float
    {
        $start = hrtime(true);

        for ($i = 0; $i < $this->iterations; $i++) {
            $callback();
        }

        $elapsed = hrtime(true) - $start;

        return $elapsed / $this->iterations / 1e9;
    }
}
