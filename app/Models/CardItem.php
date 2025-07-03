<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CardItem extends Model
{
    /** @use HasFactory<\Database\Factories\CardItemFactory> */
    use HasFactory;

    protected $fillable = [
        'card_id',
        'product_id',
        'quantity',
        'price',
    ];

    public function card()
    {
        return $this->belongsTo(Card::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
