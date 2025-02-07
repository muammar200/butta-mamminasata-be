<?php

namespace App\Http\Controllers\Api\Web;

use App\Models\Category;
use App\Models\Property;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\PropertyResource;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get categories
        $categories = Category::latest()->get();

        //return with Api Resource
        return new CategoryResource(true, 'List Data Categories', $categories);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $category = Category::with('properties.category', 'properties.images')->where('slug', $slug)->first();

        $properties = Property::with('category', 'images')
        ->where('category_id', $category->id)
        ->paginate(8);

        if ($properties) {
            return new PropertyResource(true, 'List Data Properties By Category', $properties);
        }

        return new PropertyResource(false, 'Data Tidak Ditemukan!', null);
    }

    public function all_places()
    {
        //get places
        $properties = Category::latest()->get();

        //return with Api Resource
        return new CategoryResource(true, 'List Data Places', $properties);
    }

}
