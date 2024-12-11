<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Document;
use App\Models\House;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\DocumentController
 */
final class DocumentControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $documents = Document::factory()->count(3)->create();

        $response = $this->get(route('documents.index'));

        $response->assertOk();
        $response->assertViewIs('document.index');
        $response->assertViewHas('documents');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('documents.create'));

        $response->assertOk();
        $response->assertViewIs('document.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\DocumentController::class,
            'store',
            \App\Http\Requests\DocumentControllerStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $renovated_house = House::factory()->create();
        $document_url = $this->faker->word();

        $response = $this->post(route('documents.store'), [
            'renovated_house_id' => $renovated_house->id,
            'document_url' => $document_url,
        ]);

        $documents = Document::query()
            ->where('renovated_house_id', $renovated_house->id)
            ->where('document_url', $document_url)
            ->get();
        $this->assertCount(1, $documents);
        $document = $documents->first();

        $response->assertRedirect(route('documents.index'));
        $response->assertSessionHas('document.id', $document->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $document = Document::factory()->create();

        $response = $this->get(route('documents.show', $document));

        $response->assertOk();
        $response->assertViewIs('document.show');
        $response->assertViewHas('document');
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $document = Document::factory()->create();

        $response = $this->get(route('documents.edit', $document));

        $response->assertOk();
        $response->assertViewIs('document.edit');
        $response->assertViewHas('document');
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\DocumentController::class,
            'update',
            \App\Http\Requests\DocumentControllerUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $document = Document::factory()->create();
        $renovated_house = House::factory()->create();
        $document_url = $this->faker->word();
        $house = House::factory()->create();

        $response = $this->put(route('documents.update', $document), [
            'renovated_house_id' => $renovated_house->id,
            'document_url' => $document_url,
        ]);

        $document->refresh();

        $response->assertRedirect(route('documents.index'));
        $response->assertSessionHas('document.id', $document->id);

        $this->assertEquals($renovated_house->id, $document->renovated_house_id);
        $this->assertEquals($document_url, $document->document_url);

    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $document = Document::factory()->create();

        $response = $this->delete(route('documents.destroy', $document));

        $response->assertRedirect(route('documents.index'));

        $this->assertModelMissing($document);
    }
}
