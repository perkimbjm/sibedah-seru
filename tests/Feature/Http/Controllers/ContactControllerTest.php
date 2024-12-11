<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ContactController
 */
final class ContactControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $contacts = Contact::factory()->count(3)->create();

        $response = $this->get(route('contacts.index'));

        $response->assertOk();
        $response->assertViewIs('contact.index');
        $response->assertViewHas('contacts');
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('contacts.create'));

        $response->assertOk();
        $response->assertViewIs('contact.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ContactController::class,
            'store',
            \App\Http\Requests\ContactControllerStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $name = $this->faker->name();
        $phone_number = $this->faker->phoneNumber();
        $email = $this->faker->safeEmail();
        $address = $this->faker->word();

        $response = $this->post(route('contacts.store'), [
            'name' => $name,
            'phone_number' => $phone_number,
            'email' => $email,
            'address' => $address,
        ]);

        $contacts = Contact::query()
            ->where('name', $name)
            ->where('phone_number', $phone_number)
            ->where('email', $email)
            ->where('address', $address)
            ->get();
        $this->assertCount(1, $contacts);
        $contact = $contacts->first();

        $response->assertRedirect(route('contacts.index'));
        $response->assertSessionHas('contact.id', $contact->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $contact = Contact::factory()->create();

        $response = $this->get(route('contacts.show', $contact));

        $response->assertOk();
        $response->assertViewIs('contact.show');
        $response->assertViewHas('contact');
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $contact = Contact::factory()->create();

        $response = $this->get(route('contacts.edit', $contact));

        $response->assertOk();
        $response->assertViewIs('contact.edit');
        $response->assertViewHas('contact');
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ContactController::class,
            'update',
            \App\Http\Requests\ContactControllerUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $contact = Contact::factory()->create();
        $name = $this->faker->name();
        $phone_number = $this->faker->phoneNumber();
        $email = $this->faker->safeEmail();
        $address = $this->faker->word();

        $response = $this->put(route('contacts.update', $contact), [
            'name' => $name,
            'phone_number' => $phone_number,
            'email' => $email,
            'address' => $address,
        ]);

        $contact->refresh();

        $response->assertRedirect(route('contacts.index'));
        $response->assertSessionHas('contact.id', $contact->id);

        $this->assertEquals($name, $contact->name);
        $this->assertEquals($phone_number, $contact->phone_number);
        $this->assertEquals($email, $contact->email);
        $this->assertEquals($address, $contact->address);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $contact = Contact::factory()->create();

        $response = $this->delete(route('contacts.destroy', $contact));

        $response->assertRedirect(route('contacts.index'));

        $this->assertModelMissing($contact);
    }
}
