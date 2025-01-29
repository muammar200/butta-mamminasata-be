<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'address',
        'office_hours',
        'type',
        'image',
        'latitude',
        'longitude'
    ];

    /**
     * places
     *
     * @return void
     */
    public function places()
    {
        return $this->hasMany(Place::class);
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    /**
     * getImageAttribute
     *
     * @param  mixed $image
     * @return void
     */
    public function getImageAttribute($image)
    {
        return url('storage/categories/' . $image);
    }
}
