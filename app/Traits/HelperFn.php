<?php

namespace App\Traits;

use stdClass;

trait HelperFn
{
    public static function user($query)
    {
        $user = new stdClass;
        $user->id = $query->id;
        $user->name = "$query->first_name $query->last_name $query->other_name";
        $user->phone_number = $query->phone_number;
        $user->email = $query->email;
        $user->email_verified_at = $query->email_verified_at;
        $user->created_at = self::custom_date_two($query->created_at);
        $user->roles = [];
        $user->permissions = [];
        $permissions = [];

        foreach ($query->roles as $key => $role) {
            $roleObj = new stdClass;
            $roleObj->id = $role->id;
            $roleObj->name = $role->name;
            $user->roles[] = $roleObj;

            foreach ($role->permissions as $key => $permit) {
                $permissions[] = $permit->name;
            }
        }

        $user->permissions[] = array_unique($permissions);

        $user->can_create = in_array('Create (employee, store & product)', array_unique($permissions));
        $user->can_delete = in_array('Delete (employee, store & product)', array_unique($permissions));
        $user->can_edit = in_array('Edit (employee, store & product)', array_unique($permissions));
        $user->can_undo = in_array('Reverse (undo or return) actions', array_unique($permissions));

        return $user;
    }
    public static function stores($query)
    {
        $stores = [];

        foreach ($query as $key => $val) {
            $store = new stdClass;
            $store->id = $val->id;
            $store->name = $val->name;
            $store->created_at = self::custom_date_two($val->created_at);
            $address = new stdClass;

            $address->id = $val->address->id;
            $address->description = $val->address->description;
            $address->country = $val->address->country;
            $address->state = $val->address->state;
            $address->city = $val->address->city;

            $store->address = $address;

            $stores[] = $store;
        }
        return $stores;
    }

    public static function custom_date_two($date, $format = "D jS M, Y")
    {
        $date_ = date_create($date);
        $format = date_format($date_, $format); //e.g Sat 14th Jun, 2020
        return $format;
    }
}
