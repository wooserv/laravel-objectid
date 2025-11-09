<?php

use Orchestra\Testbench\TestCase;
use WooServ\LaravelObjectId\Concerns\HasObjectIds;
use WooServ\LaravelObjectId\ObjectIdServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class TestModel extends Model
{
    use HasObjectIds;
    protected $guarded = [];
    protected $table = 'test_models';
}

class HasObjectIdsTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [ObjectIdServiceProvider::class];
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Create main test table using objectId macro
        Schema::create('test_models', function (Blueprint $table) {
            $table->objectId(); // Default: id + primary
            $table->string('name')->nullable();
            $table->timestamps();
        });

        // Create another table with custom column name
        Schema::create('custom_ids', function (Blueprint $table) {
            $table->objectId('uuid', false); // Custom column (not primary)
            $table->string('title')->nullable();
        });
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    public function test_objectid_is_assigned_automatically()
    {
        $model = TestModel::create(['name' => 'Hamada']);
        $this->assertMatchesRegularExpression('/^[a-f0-9]{24}$/i', $model->id);
    }

    public function test_multiple_models_have_unique_ids()
    {
        $ids = [];
        for ($i = 0; $i < 10; $i++) {
            $model = TestModel::create(['name' => 'Row ' . $i]);
            $this->assertNotContains($model->id, $ids);
            $ids[] = $model->id;
        }

        $this->assertCount(10, $ids);
        $this->assertCount(count(array_unique($ids)), $ids);
    }

    public function test_macro_creates_custom_objectid_column()
    {
        $columns = Schema::getColumnListing('custom_ids');

        // Verify column exists
        $this->assertContains('uuid', $columns);

        // Verify column is not the primary key
        $connection = Schema::getConnection();
        $primaryKeys = $connection->select(
            "PRAGMA table_info('custom_ids')"
        );
        $hasPrimary = array_filter($primaryKeys, fn ($col) => $col->pk == 1);

        $this->assertEmpty($hasPrimary, 'custom_ids table should not have a primary key');
    }

    public function test_helper_function_returns_valid_objectid()
    {
        $id = objectid();

        $this->assertIsString($id, 'objectid() should return a string');
        $this->assertSame(24, strlen($id), 'ObjectId should be exactly 24 characters long');
        $this->assertMatchesRegularExpression('/^[a-f0-9]{24}$/i', $id, 'ObjectId format should be hexadecimal');
    }
}
