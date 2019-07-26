<?php

namespace App\Http\Controllers;

use App\Repository\Pos\HomeImageRepository;
use App\Traits\GetShopIdFromUser;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use const true;
use function compact;
use function view;

class HomeImageController extends Controller
{
    use GetShopIdFromUser;
    private $paginationNumber = 5;


    public function index()
    {
        /** @var LengthAwarePaginator $products */
        $homeImages = HomeImageRepository::getPaginationByShopId(
            $this->getShop(true),
            $this->paginationNumber
        );

        return view('consoles.homeImage.index', compact('homeImages'));
    }


    public function search(Request $request)
    {
        $shopId = $this->getShop('id');

        $lastQueryStatus = $request->onlineStatus;

        $queryCondition = $this->getQueryCondition($request);

        $homeImages =
            HomeImageRepository::getPaginationByShopIdWithSearchQuery(
                $shopId,
                $this->paginationNumber,
                $queryCondition
            );

        $lastQueryString = $this->getQueryString($request);

        return view(
            'consoles.homeImage.index',
            compact(
                'homeImages',
                'lastQueryStatus',
                'lastQueryString'
            )
        );
    }


    private function getQueryString($request)
    {
        return "onlineStatus={$request->onlineStatus}";
    }


    private function getQueryCondition($request)
    {
        return [
            'is_launch' => $request->onlineStatus,
        ];
    }
}
