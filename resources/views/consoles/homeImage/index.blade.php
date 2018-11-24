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
        <h1>首頁管理<span> / 主圖管理</span></h1>
        <div class="panel">
            <div class="panelBody">
                <h2>圖片列表一覽</h2>
                <!--編輯按鈕區塊-->
                <div class="editListBtn">
                    <a href="add.htm"><i class="fas fa-plus"></i>新增內容</a>
                    <a href="#"><i class="fas fa-redo-alt"></i>更新排序</a>
                    <a href="#"><i class="fas fa-trash-alt"></i>刪除勾選</a>
                </div>
                <!--編輯按鈕區塊 end-->
                <!--搜尋商品狀態-->
                <div class="stuts">
                    <form class="searchFrom"
                          action="/homeImage/search"
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
                        <div class="stutsList sendBtn">
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
                            <th>
                                <input id="master_checkbox"
                                       type="checkbox"
                                       name="checkbox">
                            </th>
                            <th>排序</th>
                            <th>主圖</th>
                            <th>標題</th>
                            <th>建立時間</th>
                            <th>狀態</th>
                            <th>功能</th>
                        </tr>
                        </thead>
                        <tbody>
                        <!--列表每頁5則-->
                        @foreach($homeImages as $homeImage)
                            <tr>
                                <td>
                                    <input class="batched_action"
                                           type="checkbox"
                                           name="checkbox"
                                           value="{{$homeImage->id}}">
                                </td>

                                <td>
                                    <input class="order"
                                           type="text"
                                           value="{{$homeImage->order}}"
                                           id="{{$homeImage->id}}">
                                </td>

                                <td>
                                    <img src="{{$homeImage->image_url}}">
                                </td>

                                <td>{{$homeImage->name}}</td>

                                <td>{{ $homeImage->updated_at->toDateTimeString() }}</td>

                                <td>
                                    @if($homeImage->is_launch == 1)
                                        <input type="checkbox"
                                               checked
                                               class="js-switch launch_status"
                                               value="{{$homeImage->id}}"
                                               data-section="homeImage"/>
                                    @else
                                        <input type="checkbox"
                                               class="js-switch launch_status"
                                               value="{{$homeImage->id}}"
                                               data-section="homeImage"/>
                                    @endif
                                </td>
                                <td>
                                    {{--EDIT BUTTON--}}
                                    <a class="btn circle"
                                       href="{{$homeImage->pathUrl()}}/edit"
                                       title="編輯">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    {{--CLONE BUTTON--}}
                                    <a class="btn circle"
                                       href="{{$homeImage->pathUrl()}}/clone"
                                       title="複製">
                                        <i class="fas fa-clone"></i>
                                    </a>

                                    {{--DELETE BUTTON--}}
                                    <a class="btn circle"
                                       href="{{$homeImage->pathUrl()}}"
                                       title="刪除"
                                       data-method="delete"
                                       data-confirm="確定要刪除嗎？">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>

                        @endforeach

                        <!--列表每頁5則 end-->
                        </tbody>
                    </table>
                </div>
                <!--頁碼-->
                @include('consoles.components.pagination',
                [
                 'paginator' => $homeImages,
                 'lastQueryString' => isset($lastQueryString) ?  $lastQueryString : null
                 ])

            </div>
        </div>

        <!--主要內容 end-->

        <footer>©CHU.C 啾囍 All Rights Reserved.</footer>


    </div>

    <script>


    </script>


@endsection