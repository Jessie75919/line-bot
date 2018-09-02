@extends('layouts.master')

@section('meta')

    @parent



@endsection

@section('content')

    <div class="container start">
        <!--主要內容-->
        <h1>商品管理<span> / 貼心提醒內容</span></h1>
        <div class="panel">
            <div class="panelBody">
                <h2>基本資料</h2>
                <!--編輯按鈕區塊-->
                <div class="editListBtn start">
                    <a href="#"
                       id="submit"><i class="fas fa-edit"></i>確認儲存</a>
                </div>
                <!--編輯按鈕區塊 end-->
                <!--新增商品資訊-->
                <form action="/product/notices"
                      method="post"
                      id="formcontent">
                    @csrf
                    <div class="formList">
                        <div class="formName full">內容<span class="mark">*</span></div>
                        <div class="formInfo full">
                            <ul class="note">
                                <li>上傳檔案之檔名: 請勿包含中文 / 空白 / 特殊符號(例如: &^%%#!)('">)，避免瀏覽器無法支援造成破圖或程式執行錯誤情形。</li>
                                <li>上傳檔案之圖檔: 高度請留空白，僅設定寬度即可，避免其他裝置瀏覽時造成圖片變形。</li>
                                <li>上傳檔案之圖檔: 寬度請至少 250px 以上，避免其他裝置瀏覽時造成圖片放大失真。</li>
                            </ul>
                        </div>
                    </div>
                    <div class="editorArea">
                        <!--ck editor-->
                        <textarea name="ckeditor"
                                  id="ckeditor"
                                  class="ckeditor"
                                  rows="10"
                                  cols="10">
                                {{ $notice }}
                        </textarea>
                        <!--ck editor end-->
                    </div>
                </form>
            </div>
        </div>
        <!--主要內容 end-->
        @include('consoles.components.footer')
    </div>

    <script src="/js/ckeditor/ckeditor.js"></script>
    <script>
        @if(session('notice_saved'))
        swal({
            title  : "貼心小提醒新增成功囉！",
            text   : "",
            icon   : "success",
            button : "OK",
        });
        @endif
        
        
        ckEditorInit();

        function ckEditorInit(){
            CKEDITOR.replace('ckeditor', {
                width  : '100%',
                height : 300,
            });
        }

        $('#submit').on('click', function(e){
            e.preventDefault();
            $('#formcontent').submit();
        })
    </script>


@endsection