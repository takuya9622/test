<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Helpers\CreatesTestData;

class PaymentSelectTest extends TestCase
{
    use RefreshDatabase;
    use CreatesTestData;

    public function testPaymentMethodIsReflectedImmediately(): void
    {
        $buyer = User::factory()->create();

        $seller = $this->createUserWithItems(1);
        $item = $seller->items->first();

        $response = $this->actingAs($buyer)->get(route('purchase.create', ['itemId' => $item->id]));
        $response->assertStatus(200);
        $response->assertDontSee('value="card"', false);
        $response->assertDontSee('value="konbini"', false);

        $response = $this->get(route('purchase.create', ['itemId' => $item->id, 'option' => 'card']));
        $response->assertStatus(200);
        $response->assertSee('value="card"', false);
        $response->assertDontSee('value="konbini"', false);

        $response = $this->get(route('purchase.create', ['itemId' => $item->id, 'option' => 'konbini']));
        $response->assertStatus(200);
        $response->assertSee('value="konbini"', false);
        $response->assertDontSee('value="card"', false);
    }
}
