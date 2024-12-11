<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\House;
use App\Models\HousePhoto;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\HousePhotoController
 */
final class HousePhotoControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $renovatedHousePhotos = HousePhoto::factory()->count(3)->create();

        $response = $this->get(route('renovated-house-photos.index'));

        $response->assertOk();
        $response->assertViewIs('renovatedHousePhoto.index');
        $response->assertViewHas('renovatedHousePhotos');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('renovated-house-photos.create'));

        $response->assertOk();
        $response->assertViewIs('renovatedHousePhoto.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\HousePhotoController::class,
            'store',
            \App\Http\Requests\HousePhotoControllerStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $renovated_house = House::factory()->create();
        $photo_url = $this->faker->word();
        $progres = $this->faker->randomFloat(/** float_attributes **/);
        $is_primary = $this->faker->boolean();
        $is_best = $this->faker->boolean();
        $house = House::factory()->create();

        $response = $this->post(route('renovated-house-photos.store'), [
            'renovated_house_id' => $renovated_house->id,
            'photo_url' => $photo_url,
            'progres' => $progres,
            'is_primary' => $is_primary,
            'is_best' => $is_best,
        ]);

        $renovatedHousePhotos = HousePhoto::query()
            ->where('renovated_house_id', $renovated_house->id)
            ->where('photo_url', $photo_url)
            ->where('progres', $progres)
            ->where('is_primary', $is_primary)
            ->where('is_best', $is_best)
            ->get();
        $this->assertCount(1, $renovatedHousePhotos);
        $renovatedHousePhoto = $renovatedHousePhotos->first();

        $response->assertRedirect(route('renovatedHousePhotos.index'));
        $response->assertSessionHas('renovatedHousePhoto.id', $renovatedHousePhoto->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $renovatedHousePhoto = HousePhoto::factory()->create();

        $response = $this->get(route('renovated-house-photos.show', $renovatedHousePhoto));

        $response->assertOk();
        $response->assertViewIs('renovatedHousePhoto.show');
        $response->assertViewHas('renovatedHousePhoto');
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $renovatedHousePhoto = HousePhoto::factory()->create();

        $response = $this->get(route('renovated-house-photos.edit', $renovatedHousePhoto));

        $response->assertOk();
        $response->assertViewIs('renovatedHousePhoto.edit');
        $response->assertViewHas('renovatedHousePhoto');
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\HousePhotoController::class,
            'update',
            \App\Http\Requests\HousePhotoControllerUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $renovatedHousePhoto = HousePhoto::factory()->create();
        $renovated_house = House::factory()->create();
        $photo_url = $this->faker->word();
        $progres = $this->faker->randomFloat(/** float_attributes **/);
        $is_primary = $this->faker->boolean();
        $is_best = $this->faker->boolean();
        $house = House::factory()->create();

        $response = $this->put(route('renovated-house-photos.update', $renovatedHousePhoto), [
            'renovated_house_id' => $renovated_house->id,
            'photo_url' => $photo_url,
            'progres' => $progres,
            'is_primary' => $is_primary,
            'is_best' => $is_best,
        ]);

        $renovatedHousePhoto->refresh();

        $response->assertRedirect(route('renovatedHousePhotos.index'));
        $response->assertSessionHas('renovatedHousePhoto.id', $renovatedHousePhoto->id);

        $this->assertEquals($renovated_house->id, $renovatedHousePhoto->renovated_house_id);
        $this->assertEquals($photo_url, $renovatedHousePhoto->photo_url);
        $this->assertEquals($progres, $renovatedHousePhoto->progres);
        $this->assertEquals($is_primary, $renovatedHousePhoto->is_primary);
        $this->assertEquals($is_best, $renovatedHousePhoto->is_best);
        $this->assertEquals($house->id, $renovatedHousePhoto->house_id);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $renovatedHousePhoto = HousePhoto::factory()->create();

        $response = $this->delete(route('renovated-house-photos.destroy', $renovatedHousePhoto));

        $response->assertRedirect(route('renovatedHousePhotos.index'));

        $this->assertModelMissing($renovatedHousePhoto);
    }
}
