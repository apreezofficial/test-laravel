<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'status', 
        'total_amount',
    ];

    /**
     * Get the user who placed the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The products contained in the order
     */
    public function products()
    {
        return $this->belongsToMany(Product::class)
                    ->withPivot('quantity', 'price'); 
    }
}