@extends('layouts.master')

@section('meta')
    <title>sideNav</title>
    <style>

    </style>


@endsection



@section('content')

    <div class="container start">
        <!--主要內容-->
        <h1>商品管理<span> / 新增內容</span></h1>
        <div class="panel">
            <div class="panelBody bt-wrapper">
                <h2>基本資料</h2>
                <!--編輯按鈕區塊-->
                <div class="editListBtn start"><a href="list.htm"><i class="fas fa-reply"></i>回上一頁</a><a href="#"><i
                                class="fas fa-edit"></i>確認新增</a></div>
                <!--編輯按鈕區塊 end-->
                <!--新增商品資訊-->
                <form action="list.htm" method="post" id="formcontent">
                    <div class="formList">
                        <div class="formName">商品名稱<span class="mark">*</span></div>
                        <div class="formInfo"><input type="text" name="name" id="name"></div>
                    </div>
                    <div class="formList">
                        <div class="formName">所屬類別<span class="mark">*</span></div>
                        <div class="formInfo"><select name="selectItem" class="validate[required]">
                                <option value="" selected>請選擇</option>
                                <option value="1">耳環</option>
                                <option value="2">項鍊</option>
                                <option value="3">手鍊</option>
                                <option value="4">髮飾</option>
                                <option value="5">鑰匙圈</option>
                            </select></div>
                    </div>
                    <div class="formList">
                        <div class="formName">上線狀態<span class="mark">*</span></div>
                        <div class="formInfo"><input type="checkbox" class="js-switch" checked/></div>
                    </div>
                    <div class="formList">
                        <div class="formName">圖片批次上傳<span class="mark">*</span></div>
                        <div class="formInfo">
                            <div class="dropzone" id="dropzone"></div>
                        </div>
                    </div>
                    <div class="formList">
                        <div class="formName">文章標籤<span class="mark">*</span></div>
                        <div class="formInfo">
                            <div id="productTag"></div>
                        </div>
                    </div>
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
                        <!--ck editor--><textarea name="ckeditor" id="ckeditor" class="ckeditor" rows="10"
                                                  cols="10"></textarea>
                        <!--ck editor end-->
                    </div>
                </form>
            </div>
        </div>
        <!--主要內容 end-->
        @include('consoles.products.components.footer')

    </div>


    <script>

        Dropzone.autoDiscover = true;
        var order             = 0;

        var myDropzone = new Dropzone('#sortable', {
            url              : "/productsConsole",
            headers          : {
                'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
            },
            parallelUploads  : 10, // Uploads one (1) file at a time, change to whatever you like.
            autoProcessQueue : false,
            uploadMultiple   : true,
            addRemoveLinks   : true,
        });

        myDropzone.on("complete", function(file){
            myDropzone.removeFile(file);
        });


        myDropzone.on('addedfile', function(file){
            $('#sortable div:last-of-type').not('.dz-error-mark').not('.dz-filename').attr('id', ++order).addClass("custom_order");
        });


        myDropzone.on('removedfile', function(file){
            var files = $('body').find('.custom_order');
            files.removeAttr('id');
            for(var i = 0 ; i < files.length ; i++) {
                $(files[i]).attr('id', i + 1);
            }
        });


        myDropzone.on("sending", function(file, xhr, formData){

            let files = $('body').find('.custom_order').toArray();
            let order = files.map(function(item){
                return item.id;
            });


            formData.append("order", order);

        });

        $('#imgsubbutt').click(function(){

            myDropzone.processQueue();
        });

        myDropzone.on('success', function(){
            order = 0;
        });


        let el       = document.getElementById('sortable');
        let sortable = Sortable.create(el);


        // tag --------------------------------------------------------

        //    window.$.ajaxSetup({
        //        headers : {
        //            'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
        //        }
        //    });


        let getAllTags = () =>{
            return axios({
                method : 'get',
                url    : '/api/v1/tag/shop/1'
            })
                .then(res => res.data.data)
                .then(data => data.map(item => item.name))
        };


        let getProductTags = () =>{
            return axios({
                method : 'get',
                url    : '/api/v1/tag/product/1',
            })
                .then(res => res.data.data)
                .then(data => data.map(item => item.name))
        };


        //    $('#tags').

        let tagInit = async function(){
            let allTags     = await getAllTags();
            let productTags = await getProductTags();

            $('#demo1').tagEditor({
                initialTags     : productTags,
                autocomplete    : {
                    delay    : 0, // show suggestions immediately
                    position : {collision : 'flip'}, // automatic menu position up/down
                    source   : allTags
                },
                delimiter       : ', ', /* space and comma */
                placeholder     : 'Enter tags ...',
                onChange        : function(field, editor, tags){
                    $('#response').prepend(
                        'Tags changed to: ' + (tags.length ? tags.join(', ') : '----') + '<hr>'
                    );
                },
                beforeTagSave   : function(field, editor, tags, tag, val){
                    console.log(val);
                    axios.post(
                        '/api/v1/tag/1/1', {tag : val}
                    );
                },
                beforeTagDelete : function(field, editor, tags, val){
                    let q = confirm('確定要刪除 『' + val + '』 嗎?');
                    if(q) {
                        axios.delete(
                            '/api/v1/tag/1', {params : {tag : val}}
                        );
                    }
                    return q;
                }
            })
        };

        tagInit();


    </script>

@endsection
