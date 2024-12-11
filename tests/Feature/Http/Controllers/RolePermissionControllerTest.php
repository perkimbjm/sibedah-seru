<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\RolePermissionController
 */
final class RolePermissionControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $rolePermissions = RolePermission::factory()->count(3)->create();

        $response = $this->get(route('role-permissions.index'));

        $response->assertOk();
        $response->assertViewIs('rolePermission.index');
        $response->assertViewHas('rolePermissions');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('role-permissions.create'));

        $response->assertOk();
        $response->assertViewIs('rolePermission.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\RolePermissionController::class,
            'store',
            \App\Http\Requests\RolePermissionControllerStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $role = Role::factory()->create();
        $permission = Permission::factory()->create();

        $response = $this->post(route('role-permissions.store'), [
            'role_id' => $role->id,
            'permission_id' => $permission->id,
        ]);

        $rolePermissions = RolePermission::query()
            ->where('role_id', $role->id)
            ->where('permission_id', $permission->id)
            ->get();
        $this->assertCount(1, $rolePermissions);
        $rolePermission = $rolePermissions->first();

        $response->assertRedirect(route('rolePermissions.index'));
        $response->assertSessionHas('rolePermission.id', $rolePermission->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $rolePermission = RolePermission::factory()->create();

        $response = $this->get(route('role-permissions.show', $rolePermission));

        $response->assertOk();
        $response->assertViewIs('rolePermission.show');
        $response->assertViewHas('rolePermission');
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $rolePermission = RolePermission::factory()->create();

        $response = $this->get(route('role-permissions.edit', $rolePermission));

        $response->assertOk();
        $response->assertViewIs('rolePermission.edit');
        $response->assertViewHas('rolePermission');
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\RolePermissionController::class,
            'update',
            \App\Http\Requests\RolePermissionControllerUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $rolePermission = RolePermission::factory()->create();
        $role = Role::factory()->create();
        $permission = Permission::factory()->create();

        $response = $this->put(route('role-permissions.update', $rolePermission), [
            'role_id' => $role->id,
            'permission_id' => $permission->id,
        ]);

        $rolePermission->refresh();

        $response->assertRedirect(route('rolePermissions.index'));
        $response->assertSessionHas('rolePermission.id', $rolePermission->id);

        $this->assertEquals($role->id, $rolePermission->role_id);
        $this->assertEquals($permission->id, $rolePermission->permission_id);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $rolePermission = RolePermission::factory()->create();

        $response = $this->delete(route('role-permissions.destroy', $rolePermission));

        $response->assertRedirect(route('rolePermissions.index'));

        $this->assertModelMissing($rolePermission);
    }
}
