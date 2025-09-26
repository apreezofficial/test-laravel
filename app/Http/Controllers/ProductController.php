<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $product = Product::create($request->validated());
        return response()->json($product, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
   public function destroy(Product $product)
    {

        if ($product->orders()->exists()) {
            return response()->json([
                'message' => 'Product cannot be deleted because it is linked to one or more orders, contact support'
            ], 409); 
        }

        $product->delete();

        return response()->noContent();
    }
}
