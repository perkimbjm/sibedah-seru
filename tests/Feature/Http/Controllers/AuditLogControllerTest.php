<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Model;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\AuditLogController
 */
final class AuditLogControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $auditLogs = AuditLog::factory()->count(3)->create();

        $response = $this->get(route('audit-logs.index'));

        $response->assertOk();
        $response->assertViewIs('auditLog.index');
        $response->assertViewHas('auditLogs');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('audit-logs.create'));

        $response->assertOk();
        $response->assertViewIs('auditLog.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AuditLogController::class,
            'store',
            \App\Http\Requests\AuditLogControllerStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $user = User::factory()->create();
        $action = $this->faker->word();
        $model_type = $this->faker->word();
        $model = Model::factory()->create();
        $ip_address = $this->faker->word();

        $response = $this->post(route('audit-logs.store'), [
            'user_id' => $user->id,
            'action' => $action,
            'model_type' => $model_type,
            'model_id' => $model->id,
            'ip_address' => $ip_address,
        ]);

        $auditLogs = AuditLog::query()
            ->where('user_id', $user->id)
            ->where('action', $action)
            ->where('model_type', $model_type)
            ->where('model_id', $model->id)
            ->where('ip_address', $ip_address)
            ->get();
        $this->assertCount(1, $auditLogs);
        $auditLog = $auditLogs->first();

        $response->assertRedirect(route('auditLogs.index'));
        $response->assertSessionHas('auditLog.id', $auditLog->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $auditLog = AuditLog::factory()->create();

        $response = $this->get(route('audit-logs.show', $auditLog));

        $response->assertOk();
        $response->assertViewIs('auditLog.show');
        $response->assertViewHas('auditLog');
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $auditLog = AuditLog::factory()->create();

        $response = $this->get(route('audit-logs.edit', $auditLog));

        $response->assertOk();
        $response->assertViewIs('auditLog.edit');
        $response->assertViewHas('auditLog');
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\AuditLogController::class,
            'update',
            \App\Http\Requests\AuditLogControllerUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $auditLog = AuditLog::factory()->create();
        $user = User::factory()->create();
        $action = $this->faker->word();
        $model_type = $this->faker->word();
        $model = Model::factory()->create();
        $ip_address = $this->faker->word();

        $response = $this->put(route('audit-logs.update', $auditLog), [
            'user_id' => $user->id,
            'action' => $action,
            'model_type' => $model_type,
            'model_id' => $model->id,
            'ip_address' => $ip_address,
        ]);

        $auditLog->refresh();

        $response->assertRedirect(route('auditLogs.index'));
        $response->assertSessionHas('auditLog.id', $auditLog->id);

        $this->assertEquals($user->id, $auditLog->user_id);
        $this->assertEquals($action, $auditLog->action);
        $this->assertEquals($model_type, $auditLog->model_type);
        $this->assertEquals($model->id, $auditLog->model_id);
        $this->assertEquals($ip_address, $auditLog->ip_address);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $auditLog = AuditLog::factory()->create();

        $response = $this->delete(route('audit-logs.destroy', $auditLog));

        $response->assertRedirect(route('auditLogs.index'));

        $this->assertModelMissing($auditLog);
    }
}
