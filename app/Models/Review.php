<?php

namespace App\Models;

use App\Models\Product;
use App\Models\Customer;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory;

    protected $table = 'reviews';

    protected $primaryKey = 'review_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'media' => 'array',
    ];

    protected $fillable = [
        'customer_id',
        'product_id',
        'customer_name',
        'customer_email',
        'customer_type',
        'stars_review',
        'review_title',
        'review_product',
        'media',
        'status',
        'review_date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($review) {
            $review->review_id = Str::random(8);
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}
