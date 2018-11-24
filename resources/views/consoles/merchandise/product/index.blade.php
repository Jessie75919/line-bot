@extends('layouts.master')

@section('meta')

    @parent
    <style>
        .searchFrom {
            width: 100%;
        }

        .stutsList {
            display: inline-block;
        }
    </style>


@endsection

@section('product')

    <div class="container start">
        <!--主要內容-->
        <h1>商品管理<span> / <a href="/product/content">內容管理</a></span></h1>
        <div class="panel">
            <div class="panelBody bt-wrapper">
                <h2>單元列表一覽</h2>
                <!--編輯按鈕區塊-->
                <div class="editListBtn">
                    <a href="/product/content/create"><i class="fas fa-plus"></i>新增內容</a>
                    <a href="#"
                       id="update_order"
                       data-section="product"
                    ><i class="fas fa-redo-alt"></i>更新排序</a>
                    <a href="#"
                       id="delete_selected"
                       data-section="product"
                    ><i class="fas fa-trash-alt"></i>刪除勾選</a>

                </div>
                <!--編輯按鈕區塊 end-->
                <!--搜尋商品狀態-->
                <div class="stuts">
                    <form class="searchFrom"
                          action="/product/content/search"
                          method="get">
                        {{--上線狀態--}}
                        <div class="stutsList">
                            上線狀態
                            <select name="onlineStatus"
                                    id="onlineStatus">
                                @if(isset($lastQueryStatus))
                                    @switch($lastQueryStatus)
                                    @case("*")
                                    <option value="*">全部</option>
                                    <option value="1">上線中</option>
                                    <option value="0">下線中</option>
                                    @break
                                    @case("1")
                                    <option value="1"
                                            selected="selected">上線中
                                    </option>
                                    <option value="*">全部</option>
                                    <option value="0">下線中</option>
                                    @break
                                    @case("0")
                                    <option value="0"
                                            selected="selected">下線中
                                    </option>
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
                            <select name="productType"
                                    id="productType">
                                <option value="*">全部</option>
                                @foreach($productTypes as $productType)
                                    @if(isset($lastQueryType))
                                        @if($productType->id == $lastQueryType)
                                            <option value="{{$productType->id}}"
                                                    selected="selected">{{$productType->name}}
                                                [{{count($productType->products)}}]
                                            </option>
                                        @else
                                            <option value="{{$productType->id}}">{{$productType->name}}
                                                [{{count($productType->products)}}]
                                            </option>
                                        @endif
                                    @else
                                        <option value="{{$productType->id}}">{{$productType->name}}
                                            [{{count($productType->products)}}]
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        {{--關鍵字--}}
                        <div class="stutsList">
                            關鍵字
                            @if(isset($lastQuerykeyword))
                                <input type="text"
                                       name="keyword"
                                       id="keyword"
                                       value="{{$lastQuerykeyword}}">
                            @else
                                <input type="text"
                                       name="keyword"
                                       id="keyword">
                            @endif
                        </div>

                        <div class="stutsList">
                            <input type="submit"
                                   value="送出">
                        </div>
                    </form>
                </div>
                <!--搜尋商品狀態 end-->
                <div class="rwdTable">
                    <table>
                        <thead>
                        <tr>
                            <th><input id="master_checkbox"
                                       type="checkbox"
                                       name="checkbox"></th>
                            <th>排序</th>
                            <th>縮圖</th>
                            <th>品名</th>
                            <th>價格</th>
                            <th>類別</th>
                            <th>上線狀態</th>
                            <th>功能</th>
                        </tr>
                        </thead>
                        <tbody>
                        <!--列表每頁10則-->
                        @foreach($products as $product)
                            <tr>
                                <td>
                                    <input class="batched_action"
                                           type="checkbox"
                                           name="checkbox"
                                           value="{{$product->id}}">
                                </td>

                                <td>
                                    <input class="order"
                                           type="text"
                                           value="{{$product->order}}"
                                           id="{{$product->id}}">
                                </td>

                                <td>
                                    <a href="{{$product->pathUrl()}}/edit"><img src="{{$product->thumbnailUrl('product')}}"></a>
                                </td>

                                <td>{{$product->name}}</td>

                                <td>{{$product->price}}</td>

                                <td>{{$product->productType->name}}</td>

                                <td>
                                    @if($product->is_launch == 1)
                                        <input type="checkbox"
                                               checked
                                               class="js-switch launch_status"
                                               value="{{$product->id}}"
                                               data-section="product"/>
                                    @else
                                        <input type="checkbox"
                                               class="js-switch launch_status"
                                               value="{{$product->id}}"
                                               data-section="product"/>
                                    @endif
                                </td>

                                <td>
                                    {{--EDIT BUTTON--}}
                                    <a class="btn circle"
                                       href="{{$product->pathUrl()}}/edit"
                                       title="編輯">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    {{--CLONE BUTTON--}}
                                    <a class="btn circle"
                                       href="{{$product->pathUrl()}}/clone"
                                       title="複製">
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
                @include('consoles.components.pagination',[
                    'paginator' => $products,
                    'lastQueryString' => isset($lastQueryString) ?  $lastQueryString : null
                    ]
                )

            </div>
        </div>

        <!--主要內容 end-->


        @include('consoles.components.footer')
    </div>

    <script>


    </script>


@endsection