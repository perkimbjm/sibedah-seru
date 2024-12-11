<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Faq;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\FaqController
 */
final class FaqControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $faqs = Faq::factory()->count(3)->create();

        $response = $this->get(route('faqs.index'));

        $response->assertOk();
        $response->assertViewIs('faq.index');
        $response->assertViewHas('faqs');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('faqs.create'));

        $response->assertOk();
        $response->assertViewIs('faq.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\FaqController::class,
            'store',
            \App\Http\Requests\FaqControllerStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $question = $this->faker->word();
        $answer = $this->faker->word();

        $response = $this->post(route('faqs.store'), [
            'question' => $question,
            'answer' => $answer,
        ]);

        $faqs = Faq::query()
            ->where('question', $question)
            ->where('answer', $answer)
            ->get();
        $this->assertCount(1, $faqs);
        $faq = $faqs->first();

        $response->assertRedirect(route('faqs.index'));
        $response->assertSessionHas('faq.id', $faq->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $faq = Faq::factory()->create();

        $response = $this->get(route('faqs.show', $faq));

        $response->assertOk();
        $response->assertViewIs('faq.show');
        $response->assertViewHas('faq');
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $faq = Faq::factory()->create();

        $response = $this->get(route('faqs.edit', $faq));

        $response->assertOk();
        $response->assertViewIs('faq.edit');
        $response->assertViewHas('faq');
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\FaqController::class,
            'update',
            \App\Http\Requests\FaqControllerUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $faq = Faq::factory()->create();
        $question = $this->faker->word();
        $answer = $this->faker->word();

        $response = $this->put(route('faqs.update', $faq), [
            'question' => $question,
            'answer' => $answer,
        ]);

        $faq->refresh();

        $response->assertRedirect(route('faqs.index'));
        $response->assertSessionHas('faq.id', $faq->id);

        $this->assertEquals($question, $faq->question);
        $this->assertEquals($answer, $faq->answer);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $faq = Faq::factory()->create();

        $response = $this->delete(route('faqs.destroy', $faq));

        $response->assertRedirect(route('faqs.index'));

        $this->assertModelMissing($faq);
    }
}
