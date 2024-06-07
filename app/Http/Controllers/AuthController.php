<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Store;
use App\Models\User;
use App\Traits\HelperFn;
use Illuminate\Http\Request;
use stdClass;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function user()
    {
        $user = User::with(['roles.permissions'])
            ->where('id', auth()->user()->id)
            ->first();

        return response()->json(HelperFn::user($user));
    }

    public function destroy()
    {
        auth()->user()->currentAccessToken()->delete();

        return response()->json('Logged out', 200);
    }
    public function get_categories()
    {
        $product_categories = Category::select(['id', 'name'])
            ->where('category_id', null)
            ->get();

        return response()->json($product_categories);
    }
    public function get_sub_categories(Request $request)
    {
        $catId = $request->id;
        $sub_categories = Category::select(['id', 'name'])
            ->where('category_id', $catId)
            ->get();

        return response()->json($sub_categories);
    }
    public function create_store(Request $request)
    {
        $name = $request->store_name;
        $catId = $request->catId;//id
        $countryId = $request->country;//id
        $stateId = $request->state;//id
        $localGovId = $request->city;//id
        $currency = $request->currency;
        $address = $request->address;

        $store = auth()->user()->stores()->create([
            'name' => $name,
            'category_id' => intval($catId),
        ]);

        $store->address()->create([
            'country_id' => intval($countryId),
            'state_id' => intval($stateId),
            'local_govt_id' => intval($localGovId),
            'description' => $address,
        ]);

        $stores = Store::with([
            'address.country' => fn($query) =>
                $query->select('id', 'name', 'currency_name', 'currency_symbol')
            ,
            'address.state',
            'address.city'
        ])
            ->where('id', $store->id)
            ->where('user_id', auth()->user()->id)
            ->get();

        $store_formatted = HelperFn::stores($stores);

        return response()->json($store_formatted);
    }

    public function get_stores()
    {

        $stores = Store::with([
            'address.country' => fn($query) =>
                $query->select('id', 'name', 'currency_name', 'currency_symbol')
            ,
            'address.state',
            'address.city'
        ])
            ->where('user_id', auth()->user()->id)
            ->get();

        $formatted_store = HelperFn::stores($stores);

        return response()->json($formatted_store);
    }
}