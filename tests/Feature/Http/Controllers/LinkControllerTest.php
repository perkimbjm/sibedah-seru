<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Link;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\LinkController
 */
final class LinkControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $links = Link::factory()->count(3)->create();

        $response = $this->get(route('links.index'));

        $response->assertOk();
        $response->assertViewIs('link.index');
        $response->assertViewHas('links');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('links.create'));

        $response->assertOk();
        $response->assertViewIs('link.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\LinkController::class,
            'store',
            \App\Http\Requests\LinkControllerStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $title = $this->faker->sentence(4);
        $url = $this->faker->url();

        $response = $this->post(route('links.store'), [
            'title' => $title,
            'url' => $url,
        ]);

        $links = Link::query()
            ->where('title', $title)
            ->where('url', $url)
            ->get();
        $this->assertCount(1, $links);
        $link = $links->first();

        $response->assertRedirect(route('links.index'));
        $response->assertSessionHas('link.id', $link->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $link = Link::factory()->create();

        $response = $this->get(route('links.show', $link));

        $response->assertOk();
        $response->assertViewIs('link.show');
        $response->assertViewHas('link');
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $link = Link::factory()->create();

        $response = $this->get(route('links.edit', $link));

        $response->assertOk();
        $response->assertViewIs('link.edit');
        $response->assertViewHas('link');
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\LinkController::class,
            'update',
            \App\Http\Requests\LinkControllerUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $link = Link::factory()->create();
        $title = $this->faker->sentence(4);
        $url = $this->faker->url();

        $response = $this->put(route('links.update', $link), [
            'title' => $title,
            'url' => $url,
        ]);

        $link->refresh();

        $response->assertRedirect(route('links.index'));
        $response->assertSessionHas('link.id', $link->id);

        $this->assertEquals($title, $link->title);
        $this->assertEquals($url, $link->url);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $link = Link::factory()->create();

        $response = $this->delete(route('links.destroy', $link));

        $response->assertRedirect(route('links.index'));

        $this->assertModelMissing($link);
    }
}
