<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'price',
        'quantity',
        'image_path',
    ];

    /**
     * The orders that contain this product 
     */
    public function orders()
    {
        // Pivot table 'order_product' includes the quantity and price at the purchase time
        return $this->belongsToMany(Order::class)
                    ->withPivot('quantity', 'price'); 
    }

    /**
     * Get the stock transactions for the product.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}