<?php

namespace App\Http\Controllers;

use App\Repository\Pos\ShopRepository;
use App\Traits\GetShopIdFromUser;
use function compact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class ProductNoticeController extends Controller
{
    use GetShopIdFromUser;


    public function index()
    {
        /** @var ShopRepository $shopRepository */
        $notice = ($this->getShop())->notice;
        return view('consoles.products.notice.index',compact('notice'));
    }


    public function create(Request $request)
    {
        $v = Validator::make($input = Input::all(),
            ['ckeditor' => 'required|string'],
            ['required' => '貼心小提醒不可為空白喔！']
        );

        if ($v->fails()) {
            return redirect('/product/notices')->withErrors($v);
        }

        $shopRepository = ($this->getShop())->getRepository();
        $shopRepository->saveNotice($input['ckeditor']);

        $request->session()->flash("notice_saved", "貼心小提醒存檔成功囉！");

        return redirect('/product/notices');

    }

}
