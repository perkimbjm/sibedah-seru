<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\House;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ReviewController
 */
final class ReviewControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $reviews = Review::factory()->count(3)->create();

        $response = $this->get(route('reviews.index'));

        $response->assertOk();
        $response->assertViewIs('review.index');
        $response->assertViewHas('reviews');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('reviews.create'));

        $response->assertOk();
        $response->assertViewIs('review.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ReviewController::class,
            'store',
            \App\Http\Requests\ReviewControllerStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $renovated_house = House::factory()->create();
        $name = $this->faker->name();
        $rating = $this->faker->numberBetween(-10000, 10000);
        $house = House::factory()->create();

        $response = $this->post(route('reviews.store'), [
            'renovated_house_id' => $renovated_house->id,
            'name' => $name,
            'rating' => $rating,
            'house_id' => $house->id,
        ]);

        $reviews = Review::query()
            ->where('renovated_house_id', $renovated_house->id)
            ->where('name', $name)
            ->where('rating', $rating)
            ->where('house_id', $house->id)
            ->get();
        $this->assertCount(1, $reviews);
        $review = $reviews->first();

        $response->assertRedirect(route('reviews.index'));
        $response->assertSessionHas('review.id', $review->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $review = Review::factory()->create();

        $response = $this->get(route('reviews.show', $review));

        $response->assertOk();
        $response->assertViewIs('review.show');
        $response->assertViewHas('review');
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $review = Review::factory()->create();

        $response = $this->get(route('reviews.edit', $review));

        $response->assertOk();
        $response->assertViewIs('review.edit');
        $response->assertViewHas('review');
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ReviewController::class,
            'update',
            \App\Http\Requests\ReviewControllerUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $review = Review::factory()->create();
        $renovated_house = House::factory()->create();
        $name = $this->faker->name();
        $rating = $this->faker->numberBetween(-10000, 10000);
        $house = House::factory()->create();

        $response = $this->put(route('reviews.update', $review), [
            'renovated_house_id' => $renovated_house->id,
            'name' => $name,
            'rating' => $rating,
            'house_id' => $house->id,
        ]);

        $review->refresh();

        $response->assertRedirect(route('reviews.index'));
        $response->assertSessionHas('review.id', $review->id);

        $this->assertEquals($renovated_house->id, $review->renovated_house_id);
        $this->assertEquals($name, $review->name);
        $this->assertEquals($rating, $review->rating);
        $this->assertEquals($house->id, $review->house_id);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $review = Review::factory()->create();

        $response = $this->delete(route('reviews.destroy', $review));

        $response->assertRedirect(route('reviews.index'));

        $this->assertModelMissing($review);
    }
}
