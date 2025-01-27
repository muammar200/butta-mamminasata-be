<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Property extends Model
{
    use HasFactory;

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'category_id',
        'price',
        'type',
        'phone',
        'office_hours',
        'address',
        'longitude',
        'latitude',
        'description',
        'size',
        'area',
    ];

    /**
     * user
     *
     * @return void
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * category
     *
     * @return void
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(PropertyImage::class);
    }

    public function property_facilities()
    {
        return $this->hasMany(PropertyFacility::class);
    }

    public function property_rooms()
    {
        return $this->hasMany(PropertyRoom::class);
    }
}
