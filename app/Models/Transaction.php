<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'change_type',
        'quantity_changed',
        'user_id',
        'reason',
    ];

    protected $casts = [
        'quantity_changed' => 'integer',
        'created_at' => 'datetime',
    ];

    /**
     * Get the product whose stock was changed.
     */
    public function product()
    {
        return $this->belongsTo(Product::class); 
    }

    /**
     * Get the user who initiated the stock change.
     */
    public function user()
    {
        return $this->belongsTo(User::class); 
    }
}