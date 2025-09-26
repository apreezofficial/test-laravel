<?php
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $headers;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $token = $this->user->createToken('test_token')->plainTextToken;
        $this->headers = ['Authorization' => "Bearer {$token}"];
    }

    /**
     * Test 1: Placing an order with insufficient stock must be rejected.
     */
    public function test_order_is_rejected_if_stock_is_insufficient()
    {
        // Arrange: Create a product with limited stock (e.g., 5)
        $product = Product::create(['name' => 'Limited Stock Item', 'sku' => 'LSI-001', 'price' => 10.00, 'quantity' => 5]);
        $initialQuantity = $product->quantity;

        // Act: Attempt to order 6 items
        $response = $this->postJson('/api/orders', [
            'products' => [
                ['product_id' => $product->id, 'quantity' => 6]
            ]
        ], $this->headers);

        // Assert 1: The order must be rejected (422 Unprocessable Entity)
        $response->assertStatus(422)
                 ->assertJsonFragment(['message' => 'Insufficient stock for product: Limited Stock Item']);

        // Assert 2: CRITICAL - Stock quantity must remain unchanged (Proves database rollback)
        $this->assertEquals($initialQuantity, $product->fresh()->quantity);
    }

    /**
     * Test 2: Discount rule
     */
    public function test_10_percent_discount_is_applied_correctly()
    {
        // Arrange: Create a high-value product to exceed the $500 threshold
        $product = Product::create(['name' => 'Expensive Item', 'sku' => 'EXP-001', 'price' => 300.00, 'quantity' => 10]);

        // Attempt to order 2 items. Subtotal: $600. Expected Total (10% off): $540.00
        $response = $this->postJson('/api/orders', [
            'products' => [
                ['product_id' => $product->id, 'quantity' => 2]
            ]
        ], $this->headers);

        // Assert: The order is created (201) and the total amount is correct
        $response->assertStatus(201)
                 ->assertJsonPath('order.total_amount', '540.00');

        // Arrange: Test for no discount (Subtotal: $450)
        $responseNoDiscount = $this->postJson('/api/orders', [
            'products' => [
                ['product_id' => $product->id, 'quantity' => 1]
            ]
        ], $this->headers);

        // Assert: The order is created and the total amount is $300.00
        $responseNoDiscount->assertStatus(201)
                           ->assertJsonPath('order.total_amount', '300.00');
    }
}