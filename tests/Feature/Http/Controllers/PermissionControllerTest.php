<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PermissionController
 */
final class PermissionControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $permissions = Permission::factory()->count(3)->create();

        $response = $this->get(route('permissions.index'));

        $response->assertOk();
        $response->assertViewIs('permission.index');
        $response->assertViewHas('permissions');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('app.permissions.create'));

        $response->assertOk();
        $response->assertViewIs('permission.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PermissionController::class,
            'store',
            \App\Http\Requests\PermissionControllerStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $name = $this->faker->name();

        $response = $this->post(route('permissions.store'), [
            'name' => $name,
        ]);

        $permissions = Permission::query()
            ->where('name', $name)
            ->get();
        $this->assertCount(1, $permissions);
        $permission = $permissions->first();

        $response->assertRedirect(route('permissions.index'));
        $response->assertSessionHas('permission.id', $permission->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $permission = Permission::factory()->create();

        $response = $this->get(route('permissions.show', $permission));

        $response->assertOk();
        $response->assertViewIs('permission.show');
        $response->assertViewHas('permission');
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $permission = Permission::factory()->create();

        $response = $this->get(route('permissions.edit', $permission));

        $response->assertOk();
        $response->assertViewIs('permission.edit');
        $response->assertViewHas('permission');
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PermissionController::class,
            'update',
            \App\Http\Requests\PermissionControllerUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $permission = Permission::factory()->create();
        $name = $this->faker->name();

        $response = $this->put(route('permissions.update', $permission), [
            'name' => $name,
        ]);

        $permission->refresh();

        $response->assertRedirect(route('permissions.index'));
        $response->assertSessionHas('permission.id', $permission->id);

        $this->assertEquals($name, $permission->name);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $permission = Permission::factory()->create();

        $response = $this->delete(route('permissions.destroy', $permission));

        $response->assertRedirect(route('permissions.index'));

        $this->assertModelMissing($permission);
    }
}
