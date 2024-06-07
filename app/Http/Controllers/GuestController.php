<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Country;
use App\Models\Local_govt;
use App\Models\State;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class GuestController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'phoneNumber' => 'required',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        $user = User::where('phone_number', $request->phoneNumber)->first();
        // return response()->json($user);

        if (!$user || !Hash::check($request->password, $user->pin)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }
        $token = $user->createToken($request->device_name)->plainTextToken;
        return response()->json(['user' => $user, 'token' => $token]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'firstName' => 'required',
            'lastName' => 'required',
            'email' => 'required|email',
            'phoneNumber' => 'required',
            'storeName' => 'required',
            'storeCategory' => 'required',
            'pin' => 'required',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
            'area' => 'required',
            'device_name' => 'required',
        ]);
        // personal 
        $firstName = $request->firstName;
        $lastName = $request->lastName;
        $otherName = $request->otherName;
        $email = $request->email;
        $phoneNumber = $request->phoneNumber;
        $pin = $request->pin;
        // store 
        $storeName = $request->storeName;
        $storeCategory = $request->storeCategory;

        // user Address 
        $country = $request->country;
        $state = $request->state;
        $city = $request->city; //local government
        $area = $request->area;
        $currency = $request->currency;
        $device_name = $request->device_name;

        $user = new User();
        $user->first_name = $firstName;
        $user->last_name = $lastName;
        $user->other_name = $otherName;
        $user->email = $email;
        $user->phone_number = $phoneNumber;
        $user->pin = Hash::make($pin);
        $user->save(); //new user saved
        //
        $cat = Category::where('name', $storeCategory)->first();
        $user->stores()->create([
            'name' => $storeName,
            'category_id' => intval($cat->id),
        ]);

        $user->address()->create([
            'country_id' => intval($country),
            'state_id' => intval($state),
            'local_govt_id' => intval($city),
            'description' => $area,
        ]);

        return $user->createToken($device_name)->plainTextToken;

    }

    public function get_countries()
    {
        $country = Country::all(['id', 'name', 'currency_name', 'currency_symbol']);
        return response()->json($country);
    }
    public function get_states(Request $request)
    {
        $states = State::select(['id', 'name'])->where('country_id', $request->id)->get();
        return response()->json($states);
    }
    public function get_localGovts(Request $request)
    {
        $local = Local_govt::select(['id', 'name'])->where('state_id', $request->id)->get();
        return response()->json($local);
    }

    public function saveCategories(Request $request)
    {
        $subCats = json_decode($request->subCats);
        $cat = strtolower($request->category);
        //save new category
        $category = new Category();
        $category->name = ucwords($cat);
        $category->save();

        $sub_categories = [];
        foreach ($subCats as $key => $subCat) {
            $sub_categories[] = new Category([
                'name' => trim(ucwords($subCat))
            ]);
        }

        $category->subCategories()->saveMany($sub_categories);

        return response()->json("Successfully added!");
    }

}