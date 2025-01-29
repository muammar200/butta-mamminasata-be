<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use App\Models\Slider;
use App\Models\Category;
use App\Models\Property;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        //count categories
        $categories = Category::count();

        //count housings
        $housings = Property::whereHas('category', function ($q) {
            $q->where('type', 'rumah');
        })->count();

        //count housings
        $kavlings = Property::whereHas('category', function ($q) {
            $q->where('type', 'kavling');
        })->count();

        //count sliders
        $sliders = Slider::count();

        //count users
        $users = User::count();

        return response()->json([
            'success'   => true,
            'message'   => 'Statistik Data',
            'data'      => [
                'categories' => $categories,
                'housings'     => $housings,
                'kavlings'   => $kavlings,
                'sliders'   => $sliders,
                'users'  => $users
            ]
        ]);
    }
}
