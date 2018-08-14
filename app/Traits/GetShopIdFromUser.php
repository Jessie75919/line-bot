<?php

namespace App\Traits;

use const false;
use Illuminate\Support\Facades\Auth;

trait GetShopIdFromUser
{
    private function getShop($isId = false)
    {
        $user = Auth::user();
        if (!$user->shop) {
            return "This user has no any SHOP!";
        }

        if ($isId) {
            return $user->shop->id;
        }
        return $user->shop;

    }
}