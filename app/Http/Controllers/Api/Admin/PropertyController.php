<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\Property;
use App\Models\PropertyRoom;
use Illuminate\Http\Request;
use App\Models\PropertyFacility;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\PropertyResource;
use Illuminate\Support\Facades\Validator;

class PropertyController extends Controller
{
    public function index()
    {
        //get properties
        $properties = Property::with('category', 'images', 'property_facilities', 'property_rooms')->when(request()->q, function ($properties) {
            $properties = $properties->whereHas('category', function ($q) {
                $q->where('name', 'like', '%' . request()->q . '%');
            });
        })->latest()->paginate(8);

        //return with Api Resource
        return new PropertyResource(true, 'List Data Properties', $properties);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id'   => 'required',
            'price'   => 'required',
            'type'         => 'required',
            'phone'       => 'required',
            'office_hours'  => 'required',
            'address'       => 'required',
            'latitude'      => 'required',
            'longitude'     => 'required',
            'description'     => 'required',
            'size'     => 'required',
            'area'     => 'required',
            'facility_name.*' => 'nullable',
            'rooms.*.room_type' => 'nullable|in:kamar tidur,kamar mandi,ruang tamu,dapur',
            'rooms.*.quantity'  => 'nullable|integer|min:1',
        ]);


        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }


        //create property
        $property = Property::create([
            'user_id'       => auth()->guard('api')->user()->id,
            'category_id'   => $request->category_id,
            'price'   => $request->price,
            'type'         => $request->type,
            'phone'       => $request->phone,
            'office_hours'  => $request->office_hours,
            'size' => $request->size,
            'area' => $request->area,
            'address'       => $request->address,
            'latitude'      => $request->latitude,
            'longitude'     => $request->longitude,
            'description'     => $request->description,
        ]);


        // create facility
        if ($request->facility_name) {
            foreach ($request->facility_name as $facility) {
                if ($facility)
                    PropertyFacility::create([
                        'facility_name' => $facility,
                        'property_id' => $property->id,
                    ]);
            }
        }

        // create room
        if ($request->rooms) {
            foreach ($request->rooms as $room) {
                if ($room['room_type'] && $room['quantity']) {
                    PropertyRoom::create([
                        'property_id' => $property->id,
                        'room_type'   => $room['room_type'],
                        'quantity'    => $room['quantity'],
                    ]);
                }
            }
        }

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
            return new PropertyResource(true, 'Data Property Berhasil Disimpan!', $property);
        }

        //return failed with Api Resource
        return new PropertyResource(false, 'Data Property Gagal Disimpan!', null);
    }

    public function show($id)
    {
        $property = Property::with('category', 'images', 'property_facilities', 'property_rooms')->whereId($id)->first();

        if ($property) {
            //return success with Api Resource
            return new PropertyResource(true, 'Detail Data Property!', $property);
        }

        //return failed with Api Resource
        return new PropertyResource(false, 'Detail Data Property Tidak Ditemukan!', null);
    }

    public function update(Request $request, Property $property)
    {
        $validator = Validator::make($request->all(), [
            'category_id'   => 'required',
            'price'         => 'required',
            'type'          => 'required',
            'phone'         => 'required',
            'office_hours'  => 'required',
            'address'       => 'required',
            'latitude'      => 'required',
            'longitude'     => 'required',
            'description'   => 'required',
            'size'          => 'required',
            'area'          => 'required',
            'facility_name.*' => 'nullable',
            'rooms.*.room_type' => 'nullable|in:kamar tidur,kamar mandi,ruang tamu,dapur',
            'rooms.*.quantity'  => 'nullable|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //update property
        $property->update([
            'user_id'       => auth()->guard('api')->user()->id,
            'category_id'   => $request->category_id,
            'price'         => $request->price,
            'type'          => $request->type,
            'phone'         => $request->phone,
            'office_hours'  => $request->office_hours,
            'size'          => $request->size,
            'area'          => $request->area,
            'address'       => $request->address,
            'latitude'      => $request->latitude,
            'longitude'     => $request->longitude,
            'description'   => $request->description,
        ]);

        //update or create facility
        if ($request->facility_name) {
            $property->property_facilities()->delete(); //hapus fasilitas lama
            foreach ($request->facility_name as $facility) {
                if ($facility) {
                    PropertyFacility::create([
                        'facility_name' => $facility,
                        'property_id' => $property->id,
                    ]);
                }
            }
        }

        //update or create rooms
        if ($request->rooms) {
            $property->property_rooms()->delete(); //hapus ruangan lama
            foreach ($request->rooms as $room) {
                if ($room['room_type'] && $room['quantity']) {
                    PropertyRoom::create([
                        'property_id' => $property->id,
                        'room_type'   => $room['room_type'],
                        'quantity'    => $room['quantity'],
                    ]);
                }
            }
        }

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
            return new PropertyResource(true, 'Data Property Berhasil Diupdate!', $property);
        }

        //return failed with Api Resource
        return new PropertyResource(false, 'Data Property Gagal Diupdate!', null);
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
            return new PropertyResource(true, 'Data Property Berhasil Dihapus!', null);
        }

        //return failed with Api Resource
        return new PropertyResource(false, 'Data Property Gagal Dihapus!', null);
    }
}
