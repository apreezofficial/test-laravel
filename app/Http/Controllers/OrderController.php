<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(OrderRequest $request)
    {
        //Start Transaction
        DB::beginTransaction();

        try {
            $productsData = $request->validated('products');
            $orderProductsPivot = [];
            $subtotal = 0;

            //Validate Stock and Calculate Subtotal
            foreach ($productsData as $item) {
                $product = Product::lockForUpdate()->find($item['product_id']);

foreach ($productsData as $item) {
    $product = Product::lockForUpdate()->find($item['product_id']);

    // If product ID is invalid
    if (!$product) {
        DB::rollBack();
        return response()->json(['message' => "Invalid product ID: {$item['product_id']}"], 422);
    }
    
    //If stock is insufficient
    if ($product->quantity < $item['quantity']) {
        DB::rollBack();
        return response()->json(['message' => "Insufficient stock for product: {$product->name}"], 422);
    }
}

                // Prepare data for pivot table
                $orderProductsPivot[$product->id] = [
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ];

                $subtotal += $product->price * $item['quantity'];
            }

            // discount
            $totalAmount = $subtotal;
            if ($subtotal > 500) {
                $totalAmount *= 0.90;
            }

            // Create Order
            $order = Order::create([
                'user_id' => auth()->id(),
                'order_number' => 'ORD-' . time() . rand(100, 999), // Unique order_number
                'status' => 'pending', // Default status
                'total_amount' => $totalAmount, // Final amount 
            ]);
            $order->products()->attach($orderProductsPivot);

            // Reduce Stock and Log Transactions
            foreach ($productsData as $item) {
                $product = Product::find($item['product_id']); 

                // Reduce stock quantities
                $product->decrement('quantity', $item['quantity']);

                // Log every stock change
                Transaction::create([
                    'product_id' => $product->id,
                    'change_type' => 'out', 
                    'quantity_changed' => $item['quantity'],
                    'user_id' => auth()->id(),
                    'reason' => 'order',
                ]);
            }

            DB::commit();

            return response()->json(['message' => 'Order placed successfully', 'order' => $order->load('products')], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            // Log error here
            return response()->json(['message' => 'Order failed due to a server error.'], 500);
        }
    }

    public function index()
    {
        $user = auth()->user();
        if ($user->isAdmin()) {
            return Order::with('user', 'products')->get(); // Admin can view all orders
        }
        // User can only manage their own order
        return $user->orders()->with('products')->get();
    }
}