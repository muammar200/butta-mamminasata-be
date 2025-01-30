<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Category;
use App\Models\Property;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\PropertyResource;
use Illuminate\Support\Facades\Validator;

class KavlingController extends Controller
{
    public function index()
    {
        // //get housings properties
        $items = Category::when(request()->q, function ($items) {
            $items = $items->where('name', 'like', '%' . request()->q . '%');
        })->where('type', 'kavling')->latest()->paginate(5);

        //return with Api Resource
        return new CategoryResource(true, 'List Data Lokasi Kavling', $items);
    }

    public function getKavlings()
    {
        //get kavlings
        $kavlings = Property::with('category', 'images')
        ->when(request()->q, function ($query) {
            $query->where(function ($q) {
                $q->where('price', 'like', '%' . request()->q . '%')
                    ->orWhere('phone', 'like', '%' . request()->q . '%')
                    ->orWhere('type', 'like', '%' . request()->q . '%')
                    ->orWhere('size', 'like', '%' . request()->q . '%')
                    ->orWhere('area', 'like', '%' . request()->q . '%')
                    ->orWhereHas('category', function ($q2) {
                        $q2->where('name', 'like', '%' . request()->q . '%');
                    });
            });
        })->whereHas('category', function ($q2) {
            $q2->where('type', 'kavling')->where('id', request()->route('id'));
        })
            ->latest()
            ->paginate(8);

        // return with Api Resource
        return new PropertyResource(true, 'List Data Kavling', $kavlings);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id'   => 'required',
            'type' => 'required',
            'price'   => 'required',
            'phone'       => 'required',
            'description'     => 'required',
            'size'     => 'required',
            'area'     => 'required',
            'image'     => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //create property
        $property = Property::create([
            'user_id'       => auth()->guard('api')->user()->id,
            'category_id'   => $request->category_id,
            'type' => $request->type,
            'price'   => $request->price,
            'phone'       => $request->phone,
            'size' => $request->size,
            'area' => $request->area,
            'description'     => $request->description,
        ]);

        //check request file
        if ($request->hasFile('image')) {

            //get request file image
            $images = $request->file('image');

            //loop file image
            foreach ($images as $image) {

                //move to storage folder
                $image->storeAs('public/properties', $image->hashName());

                //insert database
                $property->images()->create([
                    'image'     => $image->hashName(),
                    'property_id'  => $property->id
                ]);
            }
        }

        if ($property) {
            //return success with Api Resource
            return new PropertyResource(true, 'Data Kavling Berhasil Disimpan!', $property);
        }

        //return failed with Api Resource
        return new PropertyResource(false, 'Data Kavling Gagal Disimpan!', null);
    }

    public function show($id)
    {
        $property = Property::whereId($id)->first();

        if ($property) {
            //return success with Api Resource
            return new PropertyResource(true, 'Detail Data Kavling!', $property);
        }

        //return failed with Api Resource
        return new PropertyResource(false, 'Detail Data Kavling Tidak Ditemukan!', null);
    }

    public function update(Request $request, $id)
    {
        $property = Property::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'category_id'   => 'required',
            'type'          => 'required',
            'price'         => 'required',
            'phone'         => 'required',
            'description'   => 'required',
            'size'          => 'required',
            'area'          => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //update property
        $property->update([
            'user_id'       => auth()->guard('api')->user()->id,
            'category_id'   => $request->category_id,
            'type'          => $request->type,
            'price'         => $request->price,
            'phone'         => $request->phone,
            'size'          => $request->size,
            'area'          => $request->area,
            'description'   => $request->description,
        ]);

        //check request file
        if ($request->hasFile('image')) {

            //hapus gambar lama
            foreach ($property->images as $image) {
                Storage::delete('public/properties/' . $image->image);
                $image->delete();
            }

            //get request file image
            $images = $request->file('image');

            //loop file image
            foreach ($images as $image) {

                //move to storage folder
                $image->storeAs('public/properties', $image->hashName());

                //insert database
                $property->images()->create([
                    'image'     => $image->hashName(),
                    'property_id'  => $property->id
                ]);
            }
        }

        if ($property) {
            //return success with Api Resource
            return new PropertyResource(true, 'Data Kavling Berhasil Diupdate!', $property);
        }

        //return failed with Api Resource
        return new PropertyResource(false, 'Data Kavling Gagal Diupdate!', null);
    }

    public function destroy($id)
    {
        //find property by ID
        $property = Property::findOrFail($id);

        //loop image from relationship
        foreach ($property->images()->get() as $image) {

            //remove image
            Storage::disk('local')->delete('public/properties/' . basename($image->image));

            //remove child relation
            $image->delete();
        }

        if ($property->delete()) {
            //return success with Api Resource
            return new PropertyResource(true, 'Data Kavling Berhasil Dihapus!', null);
        }

        //return failed with Api Resource
        return new PropertyResource(false, 'Data Kavling Gagal Dihapus!', null);
    }
}
