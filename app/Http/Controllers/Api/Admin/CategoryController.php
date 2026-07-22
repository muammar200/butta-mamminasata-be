<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\CategoryResource;
use Illuminate\Support\Facades\Validator;

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
        $categories = Category::when(request()->q, function ($categories) {
            $categories = $categories->where('name', 'like', '%' . request()->q . '%');
        })->latest()->paginate(5);

        //return with Api Resource
        return new CategoryResource(true, 'List Data Categories', $categories);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image'    => 'required|image|mimes:jpeg,jpg,png|max:2000',
            'name'     => 'required|unique:categories',
            'type' => 'required',
            'address' => 'required',
            'office_hours' => 'required',
            'latitude'      => 'required',
            'longitude'     => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/categories', $image->hashName());

        //create category
        $category = Category::create([
            'image' => $image->hashName(),
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
            'type' => $request->type,
            'address' => $request->address,
            'office_hours' => $request->office_hours,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        if ($category) {
            //return success with Api Resource
            return new CategoryResource(true, 'Data Category Berhasil Disimpan!', $category);
        }

        //return failed with Api Resource
        return new CategoryResource(false, 'Data Category Gagal Disimpan!', null);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = Category::whereId($id)->first();

        if ($category) {
            //return success with Api Resource
            return new CategoryResource(true, 'Detail Data Category!', $category);
        }

        //return failed with Api Resource
        return new CategoryResource(false, 'Detail Data Category Tidak Ditemukan!', null);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|unique:categories,name,' . $category->id,
            'type' => 'required',
            'address' => 'required',
            'office_hours' => 'required',
            'latitude'      => 'required',
            'longitude'     => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //check image update
        if ($request->file('image')) {

            //remove old image
            Storage::disk('local')->delete('public/categories/' . basename($category->image));

            //upload new image
            $image = $request->file('image');
            $image->storeAs('public/categories', $image->hashName());

            //update category with new image
            $category->update([
                'image' => $image->hashName(),
                'name' => $request->name,
                'slug' => Str::slug($request->name, '-'),
                'type' => $request->type,
                'address' => $request->address,
                'office_hours' => $request->office_hours,
            ]);
        }

        //update category without image
        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name, '-'),
            'type' => $request->type,
            'address' => $request->address,
            'office_hours' => $request->office_hours,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        if ($category) {
            //return success with Api Resource
            return new CategoryResource(true, 'Data Category Berhasil Diupdate!', $category);
        }

        //return failed with Api Resource
        return new CategoryResource(false, 'Data Category Gagal Diupdate!', null);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        //FIX: hapus lokasi otomatis menghapus seluruh properti (perumahan/kavling)
        //beserta gambar, fasilitas, dan ruangannya. Dibungkus transaction (all-or-nothing).
        DB::beginTransaction();

        try {
            foreach ($category->properties()->get() as $property) {

                //hapus file + record gambar properti
                foreach ($property->images()->get() as $image) {
                    Storage::disk('local')->delete('public/properties/' . basename($image->image));
                    $image->delete();
                }

                //hapus fasilitas & ruangan properti
                $property->property_facilities()->delete();
                $property->property_rooms()->delete();

                //hapus properti
                $property->delete();
            }

            //hapus data places lama (jika ada) agar tidak menghalangi delete
            $category->places()->delete();

            //remove image
            Storage::disk('local')->delete('public/categories/' . basename($category->image));

            $category->delete();

            DB::commit();

            //return success with Api Resource
            return new CategoryResource(true, 'Data Category Berhasil Dihapus!', null);
        } catch (\Exception $e) {
            DB::rollBack();

            //return failed with Api Resource
            return new CategoryResource(false, 'Data Category Gagal Dihapus! ' . $e->getMessage(), null);
        }
    }
}
