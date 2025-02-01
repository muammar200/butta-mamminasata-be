<?php

namespace App\Http\Controllers\Api\Web;

use App\Models\Property;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PropertyResource;

class PropertyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get property
        $properties = Property::with('category', 'images')->when(request()->q, function ($properties) {
            $properties = $properties->where('type', 'like', '%' . request()->q . '%')->orWhere('price', 'like', '%' . request()->q . '%')->orWhere('size', 'like', '%' . request()->q . '%')->orWhere('area', 'like', '%' . request()->q . '%')->orWhere('description', 'like', '%' . request()->q . '%')->orWhereHas('category', function ($properties) {
                $properties->where('name', 'like', '%' . request()->q . '%')->orwhere('address', 'like', '%' . request()->q . '%')->orwhere('type', 'like', '%' . request()->q . '%');
            });
        })->latest()->paginate(8);

        //return with Api Resource
        return new PropertyResource(true, 'List Data Properties', $properties);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $property = Property::with('category', 'images', 'property_facilities', 'property_rooms')->where('id', $id)->first();

        if ($property) {
            //return success with Api Resource
            return new PropertyResource(true, 'Detail Data Property : ' . $property->title, $property);
        }

        //return failed with Api Resource
        return new PropertyResource(false, 'Data Property Tidak Ditemukan!', null);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all_places()
    {
        //get places
        $properties = Property::with('category', 'images')->latest()->get();

        //return with Api Resource
        return new PropertyResource(true, 'List Data Properties', $properties);
    }
}
