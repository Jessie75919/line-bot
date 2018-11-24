@extends('layouts.master')

@section('meta')

    @parent

@endsection

@section('product')
    <div class="container start">
        <!--主要內容-->
        <h1>商品管理<span> / 類別管理</span></h1>
        <div class="panel">
            <div class="panelBody bt-wrapper">
                <h2>商品類別列表一覽</h2>
                <!--編輯按鈕區塊-->
                <div class="editListBtn">
                    <a href="cata-add.htm"><i class="fas fa-plus"></i>新增類別</a>
                    <a href="#"
                       id="update_order"
                       data-section="type"><i class="fas fa-redo-alt"></i>更新排序</a>
                    <a href="#"
                       id="delete_selected"
                       data-section="type"><i class="fas fa-trash-alt"></i>刪除勾選</a>
                </div>
                <!--編輯按鈕區塊 end-->
                <div class="rwdTable">
                    <table>
                        <thead>
                        <tr>
                            <th>
                                <input id="master_checkbox"
                                       type="checkbox"
                                       name="checkbox">
                            </th>
                            <th>排序</th>
                            <th>類別名稱</th>
                            <th>內容數</th>
                            <th>狀態</th>
                            <th>功能</th>
                        </tr>
                        </thead>
                        <tbody>
                        <!--列表每頁10則-->
                        <tr>
                            @foreach($productTypes as $productType)
                                <td>
                                    @if($productType->name == '其他商品')
                                    @else
                                        <input id="checkbox"
                                               type="checkbox"
                                               name="checkbox"
                                               class="batched_action"
                                               value="{{ $productType->id }}">
                                    @endif
                                </td>

                                {{--排序--}}
                                <td>
                                    <input type="text"
                                           class="order"
                                           value="{{ $productType->order }}"
                                           id="{{$productType->id}}">
                                </td>

                                {{--類別名稱--}}
                                <td>{{ $productType->name }}</td>

                                {{--內容數--}}
                                @if(count($productType->products)!=0)
                                    <td>
                                        <a href="/product/content/search?onlineStatus=*&productType={{$productType->id}}&keyword=">{{ count($productType->products) }}
                                            個</a>
                                    </td>
                                @else
                                    <td>
                                        <a href="#">0 個</a>
                                    </td>
                                @endif
                                {{--上架狀態--}}
                                <td>
                                    @if($productType->is_launch == 1)
                                        <input type="checkbox"
                                               checked
                                               class="js-switch launch_status"
                                               value="{{$productType->id}}"
                                               data-section="productType"/>
                                    @else
                                        <input type="checkbox"
                                               class="js-switch launch_status"
                                               value="{{$productType->id}}"
                                               data-section="productType"/>
                                    @endif
                                </td>
                                <td>
                                    @if($productType->name !== '其他商品')
                                        <a class="btn circle"
                                           href="{{$productType->pathUrl()}}/edit"
                                           title="編輯"><i class="fas fa-edit"></i></a>

                                        {{--DELETE BUTTON--}}
                                        <a class="btn circle"
                                           href="{{$productType->pathUrl()}}"
                                           title="刪除"
                                           data-method="delete"
                                           data-confirm="確定要刪除嗎？">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    @endif
                                </td>
                        </tr>
                        @endforeach
                        <!--列表每頁10則 end-->
                        </tbody>
                    </table>
                </div>
                <!--頁碼-->
                <!--頁碼-->
                @include('consoles.components.pagination',
                [
                 'paginator' => $productTypes,
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