<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'category_id',
    ];

    public function address()
    {
        return $this->morphOne(Address::class, 'addressable');
    }
    // public function product()
    // {
    //     return $this->hasMany(Product::class);
    // }
}