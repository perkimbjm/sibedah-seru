<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\House;
use App\Models\HousePhoto;
use App\Models\Rtlh;
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
        $housePhotos = HousePhoto::factory()->count(3)->create();

        $response = $this->get(route('house-photos.index'));

        $response->assertOk();
        $response->assertViewIs('housePhoto.index');
        $response->assertViewHas('housePhotos');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('house-photos.create'));

        $response->assertOk();
        $response->assertViewIs('housePhoto.create');
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
        $house = House::factory()->create();
        $photo_url = $this->faker->word();
        $rtlh = Rtlh::factory()->create();

        $response = $this->post(route('house-photos.store'), [
            'house_id' => $house->id,
            'photo_url' => $photo_url,
            'rtlh_id' => $rtlh->id,
        ]);

        $housePhotos = HousePhoto::query()
            ->where('house_id', $house->id)
            ->where('photo_url', $photo_url)
            ->where('rtlh_id', $rtlh->id)
            ->get();
        $this->assertCount(1, $housePhotos);
        $housePhoto = $housePhotos->first();

        $response->assertRedirect(route('housePhotos.index'));
        $response->assertSessionHas('housePhoto.id', $housePhoto->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $housePhoto = HousePhoto::factory()->create();

        $response = $this->get(route('house-photos.show', $housePhoto));

        $response->assertOk();
        $response->assertViewIs('housePhoto.show');
        $response->assertViewHas('housePhoto');
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $housePhoto = HousePhoto::factory()->create();

        $response = $this->get(route('house-photos.edit', $housePhoto));

        $response->assertOk();
        $response->assertViewIs('housePhoto.edit');
        $response->assertViewHas('housePhoto');
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
        $housePhoto = HousePhoto::factory()->create();
        $house = House::factory()->create();
        $photo_url = $this->faker->word();
        $rtlh = Rtlh::factory()->create();

        $response = $this->put(route('house-photos.update', $housePhoto), [
            'house_id' => $house->id,
            'photo_url' => $photo_url,
            'rtlh_id' => $rtlh->id,
        ]);

        $housePhoto->refresh();

        $response->assertRedirect(route('housePhotos.index'));
        $response->assertSessionHas('housePhoto.id', $housePhoto->id);

        $this->assertEquals($house->id, $housePhoto->house_id);
        $this->assertEquals($photo_url, $housePhoto->photo_url);
        $this->assertEquals($rtlh->id, $housePhoto->rtlh_id);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $housePhoto = HousePhoto::factory()->create();

        $response = $this->delete(route('house-photos.destroy', $housePhoto));

        $response->assertRedirect(route('housePhotos.index'));

        $this->assertModelMissing($housePhoto);
    }
}
