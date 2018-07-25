<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait GetShopIdFromUser
{
    private function getShopId()
    {
        $user = Auth::user();


        if (!$user->shop) {
            return "This user has no any SHOP!";
        }

        return $user->shop->id;
    }
}