@extends('layouts.master')

@section('meta')
    @parent

@endsection

@section('product')

    <div class="container start">
        <!--主要內容-->
        <h1>商品管理<span> / 新增商品類別</span></h1>
        <div class="panel">
            <div class="panelBody bt-wrapper">
                <h2>基本資料</h2>
                <!--編輯按鈕區塊-->
                <div class="editListBtn start">
                    <a href="{{URL::previous()}}"> <i class="fas fa-reply"></i>回上一頁 </a>
                    <a href="#"
                       id="updateBtn"
                       data-section="type"
                       data-id="{{ $productType->id }}"><i class="fas fa-edit"></i>確認更新</a></div>
                <!--編輯按鈕區塊 end-->
                <!--新增商品資訊-->
                <form method="post"
                      id="formContent">
                    <div class="formList">
                        <div class="formName">類別名稱<span class="mark">*</span></div>
                        <div class="formInfo">
                            <input type="text"
                                   name="name"
                                   id="name"
                                   value="{{ $productType->name }}">
                        </div>
                    </div>

                    <div class="formList">
                        <div class="formName">上線狀態<span class="mark">*</span></div>
                        <div class="formInfo">
                            @if($productType->is_launch === 1)
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
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!--主要內容 end-->
        <!--footer 拆成共用-->
        <footer>©CHU.C 啾囍 All Rights Reserved.</footer>
        <!--footer end-->
        <!--main js-->
    </div>

@endsection