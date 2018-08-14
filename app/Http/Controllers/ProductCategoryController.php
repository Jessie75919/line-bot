<?php

namespace App\Http\Controllers;

use App\Repository\Pos\ProductTypeRepository;
use App\Traits\GetShopIdFromUser;
use function compact;

class ProductCategoryController extends Controller
{

    use GetShopIdFromUser;
    private $paginationNumber = 5;


    public function index()
    {
        $shop         = $this->getShop();
        $productTypes = ProductTypeRepository::getPaginationByShopId($shop->id, $this->paginationNumber);

        return view('consoles.products.category.index', compact('productTypes', 'shop'));
    }
}
