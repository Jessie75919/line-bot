@extends('layouts.master')

@section('meta')
    <title>sideNav</title>
    <style>
        .searchFrom {
            width: 100%;
        }

        .stutsList {
            display: inline-block;
        }
    </style>


@endsection

@section('content')

    <div class="container start">
        <!--主要內容-->
        <h1>商品管理<span> / <a href="/productsConsole">內容管理</a></span></h1>
        <div class="panel">
            <div class="panelBody bt-wrapper">
                <h2>單元列表一覽</h2>
                <!--編輯按鈕區塊-->
                <div class="editListBtn">
                    <a href="add.htm"><i class="fas fa-plus"></i>新增內容</a>
                    <a href="#" id="update_order"><i class="fas fa-redo-alt"></i>更新排序</a>
                    <a href="#" id="delete_selected"><i class="fas fa-trash-alt"></i>刪除勾選</a>

                </div>
                <!--編輯按鈕區塊 end-->
                <!--搜尋商品狀態-->
                <div class="stuts">
                    <form class="searchFrom" action="/productsConsole/search" method="get">
                        {{--上線狀態--}}
                        <div class="stutsList">
                            上線狀態
                            <select name="onlineStatus" id="onlineStatus">
                                @if(isset($lastQueryStatus))
                                    @switch($lastQueryStatus)
                                    @case("*")
                                    <option value="*">全部</option>
                                    <option value="1">上線中</option>
                                    <option value="0">下線中</option>
                                    @break
                                    @case("1")
                                    <option value="1" selected="selected">上線中</option>
                                    <option value="*">全部</option>
                                    <option value="0">下線中</option>
                                    @break
                                    @case("0")
                                    <option value="0" selected="selected">下線中</option>
                                    <option value="1">上線中</option>
                                    <option value="*">全部</option>
                                    @break
                                    @endswitch
                                @else
                                    <option value="*">全部</option>
                                    <option value="1">上線中</option>
                                    <option value="0">下線中</option>
                                @endif
                            </select>
                        </div>
                        {{--選擇類別--}}
                        <div class="stutsList">
                            選擇類別
                            <select name="productType" id="productType">
                                <option value="*">全部</option>
                                @foreach($productTypes as $productType)
                                    @if(isset($lastQueryType))
                                        @if($productType->id == $lastQueryType)
                                            <option value="{{$productType->id}}"
                                                    selected="selected">{{$productType->name}}</option>
                                        @else
                                            <option value="{{$productType->id}}">{{$productType->name}}</option>
                                        @endif
                                    @else
                                        <option value="{{$productType->id}}">{{$productType->name}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        {{--關鍵字--}}
                        <div class="stutsList">
                            關鍵字
                            @if(isset($lastQuerykeyword))
                                <input type="text" name="keyword" id="keyword" value="{{$lastQuerykeyword}}">
                            @else
                                <input type="text" name="keyword" id="keyword">
                            @endif
                        </div>

                        <div class="stutsList">
                            <input type="submit" value="送出">
                        </div>
                    </form>
                </div>
                <!--搜尋商品狀態 end-->
                <div class="rwdTable">
                    <table>
                        <thead>
                        <tr>
                            <th><input id="master_checkbox" type="checkbox" name="checkbox"></th>
                            <th>排序</th>
                            <th>縮圖</th>
                            <th>品名</th>
                            <th>類別</th>
                            <th>狀態</th>
                            <th>功能</th>
                        </tr>
                        </thead>
                        <tbody>
                        <!--列表每頁10則-->
                        @foreach($products as $product)
                            <tr>
                                <td><input class="batched_action" type="checkbox" name="checkbox"
                                           value="{{$product->id}}">
                                </td>

                                <td>
                                    <input class="order" type="text" value="{{$product->order}}" id="{{$product->id}}">
                                </td>

                                <td><img src="{{$product->thumbnailUrl('product')}}"></td>

                                <td>{{$product->name}}</td>

                                <td>{{$product->productType->name}}</td>

                                <td>
                                    @if($product->is_launch == 1)
                                        <input type="checkbox" checked class="js-switch launch_status"
                                               value="{{$product->id}}"/>
                                    @else
                                        <input type="checkbox" class="js-switch launch_status"
                                               value="{{$product->id}}"/>
                                    @endif
                                </td>

                                <td>
                                    {{--EDIT BUTTON--}}
                                    <a class="btn circle" href="{{$product->pathUrl()}}/edit" title="編輯">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    {{--CLONE BUTTON--}}
                                    <a class="btn circle" href="{{$product->pathUrl()}}/clone" title="複製">
                                        <i class="fas fa-clone"></i>
                                    </a>

                                    {{--DELETE BUTTON--}}
                                    <a class="btn circle"
                                       href="{{$product->pathUrl()}}"
                                       title="刪除"
                                       data-method="delete"
                                       data-confirm="確定要刪除嗎？">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>

                            </tr>
                        @endforeach
                        <!--列表每頁10則 end-->
                        </tbody>
                    </table>
                </div>
                <!--頁碼-->
                <div class="page">
                    <p>共 {{ceil($products->total() / 5 )}} 頁 / 總共 {{$products->total()}} 筆記錄</p>
                    @if(isset($lastQueryString))
                        <p>
                            <a href="{{$products->previousPageUrl()}}&{{$lastQueryString}}"><i
                                        class="fas fa-angle-left"></i></a>
                            @foreach(range(1, ceil($products->total()/ 5)) as $page)
                                <a href="{{$products->url($page)}}&{{$lastQueryString}}">{{$page}} </a>
                            @endforeach
                            <a href="{{$products->nextPageUrl()}}"><i class="fas fa-angle-right"></i></a>
                        </p>
                    @else
                        <p>
                            <a href="{{$products->previousPageUrl()}}"><i class="fas fa-angle-left"></i></a>
                            @foreach(range(1, ceil($products->total()/ 5)) as $page)
                                <a href="{{$products->url($page)}}">{{$page}} </a>
                            @endforeach
                            <a href="{{$products->nextPageUrl()}}"><i class="fas fa-angle-right"></i></a>
                        </p>
                    @endif
                </div>

            </div>
        </div>

        <!--主要內容 end-->


        @include('consoles.products.components.footer')
    </div>

    <script>



    </script>


@endsection