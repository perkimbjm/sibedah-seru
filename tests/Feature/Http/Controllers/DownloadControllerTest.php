<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Download;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\DownloadController
 */
final class DownloadControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $downloads = Download::factory()->count(3)->create();

        $response = $this->get(route('downloads.index'));

        $response->assertOk();
        $response->assertViewIs('download.index');
        $response->assertViewHas('downloads');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('downloads.create'));

        $response->assertOk();
        $response->assertViewIs('download.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\DownloadController::class,
            'store',
            \App\Http\Requests\DownloadControllerStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $title = $this->faker->sentence(4);
        $year = $this->faker->numberBetween(-10000, 10000);
        $slug = $this->faker->slug();
        $file_url = $this->faker->text();

        $response = $this->post(route('downloads.store'), [
            'title' => $title,
            'year' => $year,
            'slug' => $slug,
            'file_url' => $file_url,
        ]);

        $downloads = Download::query()
            ->where('title', $title)
            ->where('year', $year)
            ->where('slug', $slug)
            ->where('file_url', $file_url)
            ->get();
        $this->assertCount(1, $downloads);
        $download = $downloads->first();

        $response->assertRedirect(route('downloads.index'));
        $response->assertSessionHas('download.id', $download->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $download = Download::factory()->create();

        $response = $this->get(route('downloads.show', $download));

        $response->assertOk();
        $response->assertViewIs('download.show');
        $response->assertViewHas('download');
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $download = Download::factory()->create();

        $response = $this->get(route('downloads.edit', $download));

        $response->assertOk();
        $response->assertViewIs('download.edit');
        $response->assertViewHas('download');
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\DownloadController::class,
            'update',
            \App\Http\Requests\DownloadControllerUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $download = Download::factory()->create();
        $title = $this->faker->sentence(4);
        $year = $this->faker->numberBetween(-10000, 10000);
        $slug = $this->faker->slug();
        $file_url = $this->faker->text();

        $response = $this->put(route('downloads.update', $download), [
            'title' => $title,
            'year' => $year,
            'slug' => $slug,
            'file_url' => $file_url,
        ]);

        $download->refresh();

        $response->assertRedirect(route('downloads.index'));
        $response->assertSessionHas('download.id', $download->id);

        $this->assertEquals($title, $download->title);
        $this->assertEquals($year, $download->year);
        $this->assertEquals($slug, $download->slug);
        $this->assertEquals($file_url, $download->file_url);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $download = Download::factory()->create();

        $response = $this->delete(route('downloads.destroy', $download));

        $response->assertRedirect(route('downloads.index'));

        $this->assertModelMissing($download);
    }
}
