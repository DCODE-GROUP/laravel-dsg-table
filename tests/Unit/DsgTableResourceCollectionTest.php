<?php

namespace Dcodegroup\LaravelDsgTable\Tests\Unit;

use Dcodegroup\LaravelDsgTable\Http\Resources\DsgTableResourceCollection;
use Dcodegroup\LaravelDsgTable\Tests\Fixtures\Resources\UsersTableResource;
use Dcodegroup\LaravelDsgTable\Tests\Fixtures\Tables\UsersTable;
use Dcodegroup\LaravelDsgTable\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;

class DsgTableResourceCollectionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Route::get('/admin/users/{user}', fn () => '')->name('admin.users.show');
        Route::get('/admin/users/{user}/edit', fn () => '')->name('admin.users.edit');
        Route::delete('/admin/users/{user}', fn () => '')->name('admin.users.destroy');
    }

    public function test_it_resolves_actions_from_table_class_for_each_row(): void
    {
        $table = new UsersTable;
        $collection = UsersTableResource::collection(collect([
            (object) ['id' => 2, 'name' => 'Jane'],
        ]));

        $payload = DsgTableResourceCollection::forTable($collection, $table)->resolve();

        $this->assertCount(1, $payload);
        $this->assertSame(2, $payload[0]['id']);
        $this->assertArrayHasKey('actions', $payload[0]);
        $this->assertSame(route('admin.users.show', 2), $payload[0]['actions']['view']['link']);
    }

    public function test_it_does_not_override_actions_already_set_on_resource(): void
    {
        $table = new UsersTable;
        $resource = new class((object) ['id' => 2, 'name' => 'Jane']) extends JsonResource
        {
            public function toArray(Request $request): array
            {
                return [
                    'id' => $this->resource->id,
                    'actions' => ['custom' => ['link' => '/custom']],
                ];
            }
        };

        $collection = $resource::collection(collect([$resource->resource]));
        $payload = DsgTableResourceCollection::forTable($collection, $table)->resolve();

        $this->assertSame('/custom', $payload[0]['actions']['custom']['link']);
    }

    public function test_it_passes_table_param_to_actions_for(): void
    {
        $table = new UsersTable;
        $collection = UsersTableResource::collection(collect([
            (object) ['id' => 5, 'name' => 'Param User'],
        ]));

        $payload = DsgTableResourceCollection::forTable($collection, $table, 'account-42')->resolve();

        $this->assertArrayHasKey('actions', $payload[0]);
    }
}
