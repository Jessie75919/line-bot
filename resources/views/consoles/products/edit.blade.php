@extends('layouts.master')

@section('meta')
    <title>sideNav</title>
@endsection

@section('content')

    <div class="container start">
        <!--主要內容-->
        <h1>商品管理<span> / 新增內容</span></h1>
        <div class="panel">
            <div class="panelBody bt-wrapper">
                <h2>基本資料</h2>
                <!--編輯按鈕區塊-->
                <div class="editListBtn start">
                    <a href="{{URL::previous()}}"> <i class="fas fa-reply"></i>回上一頁 </a>
                    <a href="#"><i class="fas fa-edit"></i>確認編輯</a>
                </div>
                <!--編輯按鈕區塊 end-->
                <!--新增商品資訊-->
                <form action="#" method="post" id="formContent" enctype="multipart/form-data">
                    <div class="formList">
                        <div class="formName">商品名稱<span class="mark">*</span></div>
                        <div class="formInfo">
                            <input type="text" name="name" id="name" value="{{$product->name}}">
                        </div>
                    </div>


                    <div class="formList">
                        <div class="formName">價格<span class="mark">*</span></div>
                        <div class="formInfo">
                            <input type="text" name="price" id="price" value="{{$product->price}}">
                        </div>
                    </div>


                    <div class="formList">
                        <div class="formName">所屬類別<span class="mark">*</span></div>
                        <div class="formInfo">
                            <select name="productTypeId" class="validate[required]">
                                @foreach($productTypes as $productType)
                                    @if($productType->id == $product->product_type_id)
                                        <option value="{{$productType->product_type_id}}"
                                                selected>{{$productType->name}}</option>
                                    @else
                                        <option value="{{$productType->id}}">{{$productType->name}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{--Status--}}
                    <div class="formList">
                        <div class="formName">上線狀態</div>
                        <div class="formInfo">
                            <input type="checkbox" name="is_launched" class="js-switch"
                                   @if($product->is_launch == 1)
                                   checked
                                    @endif/>
                        </div>
                    </div>


                    {{--Existing Images--}}
                    <div class="formList">
                        <div class="formName">已上傳圖檔<span class="mark">*</span></div>
                        <div class="formInfo">
                            <div class="dbImg" id="sortableExist">

                                @foreach($product->getImages('product') as $image)
                                    <figure id="pro_{{ $image->id }}" data-order="{{ $image->order }}">
                                        <img src="{{ $image->image_url }}">
                                        <a id="pro_del_{{ $image->id }}" href="#"><i class="fas fa-times"></i></a>
                                    </figure>
                                @endforeach

                            </div>
                        </div>
                    </div>


                    {{--Dropzone--}}
                    <div class="formList" name="uploads">
                        <div class="formName">圖片批次上傳<span class="mark">*</span></div>
                        <div class="formInfo">
                            <div class="dropzone" id="sortableAdd"></div>
                        </div>
                    </div>

                    {{--Tags--}}
                    <div class="formList">
                        <div class="formName">文章標籤</div>
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

                    <input type="hidden" id="productId">


                    <!--ck editor-->
                    <div class="editorArea">
                        <textarea name="ckeditor"
                                  id="ckeditor"
                                  class="ckeditor"
                                  rows="10"
                                  cols="10">
                            {{ $product->description }}
                        </textarea>
                        <!--ck editor end-->
                        {{--<textarea name="text" id="text">This is a demo .</textarea><script> CKEDITOR.replace( 'text' );</script>--}}
                    </div>
                </form>
            </div>
        </div>
        <!--主要內容 end-->
        @include('consoles.products.components.footer')

    </div>

    <script src="/js/ckeditor/ckeditor.js"></script>

    <script>
        var order = 0;
        var myDropzone;

        /* Dropzone */
        dropzoneInit();

        $(function(){
            /* Sortable */
            sortableInit();
            /* Tag */
            tagInit();

        });

        /*ck edit*/
        ckEditorInit();
        /* jq validation */
        jqValidateInit();

        deleteImage();

        /* submit button action */
        $('#submitBtn').on('click', function(e){
            e.preventDefault();

            /* check text input */
            if(!$("#formContent").valid()) {
                return
            }

            /* check upload images  */
            if($('body').find('.custom_order').length === 0) {
                alert('最少要上傳一張商品圖片喔！');
                return
            }

            /* check CkEditor Content */
            if(CKEDITOR.instances.ckeditor.getData().trim().length === 0) {
                alert('商品內容不可為空白喔！！');
                return
            }

            /* if all validation pass */
            sendData();


        });


        function deleteImage(event){
            $('figure > a').on('click', function(){
                var regex = /^pro_del_(\d+)$/;
                var id    = regex.exec($(this).attr('id'))[1];
                axios({
                    method : 'delete',
                    url    : `/api/v1/productImage/${id}`,
                }).then(function(){
                    $("#pro_" + id).remove();
                    updateOrder('#sortableExist');
                });
            });
        }


        function jqValidateInit(){
            $("#formContent").validate({
                wrapper  : 'span',
                rules    : {
                    name          : {
                        required : true,
                    },
                    price         : {
                        required : true,
                        digits   : true
                    },
                    productTypeId : {
                        required : true,
                    },
                    focusInvalid  : true,
                },
                messages : {
                    name          : {
                        required : "商品名稱不可為空白!",
                    },
                    price         : {
                        required : "商品價格不可為空白!",
                        digits   : "商品價格需為數字喔！"
                    },
                    productTypeId : {
                        required : "商品類別不可為空白!",
                    }
                }
            });
        }


        function sortableInit(){
            let existImg      = document.getElementById('sortableExist');
            let sortableExist = new Sortable(existImg, {
                onUpdate : function(/**Event*/evt){
                    updateOrder('#sortableExist');
                },
            });
            let addImg        = document.getElementById('sortableAdd');
            let sortableAdd   = new Sortable(addImg, {
                onUpdate : function(/**Event*/evt){
                    updateOrder('#sortableAdd');
                },
            });
        }


        function updateOrder(container){
            var files ;
            if(container === '#sortableExist') {
                files = $(container).children();
            } else if(container === '#sortableAdd') {
                files = $(container).children().not('.dz-default');
            }
            
            for(var i = 0 ; i < files.length ; i++) {
                files[i].dataset.order = i + 1;
            }
        }


        function ckEditorInit(){
            CKEDITOR.replace('ckeditor', {
                width  : '100%',
                height : 300,
            });
        }


        function dropzoneInit(){
            myDropzone = new Dropzone('#sortableAdd', {
                url              : "/productsConsole/storeImages",
                headers          : {
                    'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content')
                },
                parallelUploads  : 10, // Uploads one (1) file at a time, change to whatever you like.
                autoProcessQueue : false,
                uploadMultiple   : true,
                addRemoveLinks   : true,
            });

            Dropzone.autoDiscover = false;

            myDropzone.on("complete", function(file){
                myDropzone.removeFile(file);
            });


            myDropzone.on('addedfile', function(file){
                let index = ++order;
                $('#sortableAdd div:last-of-type')
                    .not('.dz-error-mark')
                    .not('.dz-filename')
                    .attr('name', `name_${index}`)
                    .attr("data-order", index);
            });


            myDropzone.on('removedfile', function(file){
                var files = $('body').find('.custom_order');
                files.removeAttr('id');
                for(var i = 0 ; i < files.length ; i++) {
                    $(files[i]).attr('id', i + 1);
                }
            });


            myDropzone.on("sending", function(file, xhr, formData){
                let files     = $('body').find('.custom_order').toArray();
                let productId = $('#productId').val();
                let order     = files.map(function(item){
                    return item.id;
                });

                formData.append("order", order);
                formData.append("productId", productId);
            });


            myDropzone.on('success', function(){
                order = 0;
            });

        }


        async function tagInit(){

            /* Tags */
            let getAllTags = () =>{
                return axios({
                    method : 'get',
                    url    : '/api/v1/tag/shop/{{$shopId}}'
                })
                    .then(res => res.data.data)
                    .then(data => data.map(item => item.name))
            };


            let getProductTags = () =>{
                return axios({
                    method : 'get',
                    url    : '/api/v1/tag/product/{{$product->id}}',
                })
                    .then(res => res.data.data)
                    .then(data => data.map(item => item.name))
            };


            let allTags     = await getAllTags();
            let productTags = await getProductTags();

            $('#productTag').tagEditor({
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
                        '/api/v1/tag/{{$shopId}}/{{$product->id}}', {tag : val}
                    );
                },
                beforeTagDelete : function(field, editor, tags, val){
                    let q = confirm('確定要刪除 『' + val + '』 嗎?');
                    if(q) {
                        axios.delete(
                            '/api/v1/tag/{{$shopId}}', {params : {tag : val}}
                        );
                    }
                    return q;
                }
            })
        }

        function sendData(){
            // send the text type to backend
            let data = {
                // 商品名稱
                name            : $("input[name='name']").val(),
                // 商品價格
                price           : $("input[name='price']").val(),
                // 所屬類別
                productTypeId   : $("select[name='productTypeId']").val(),
                // 上線狀態
                is_launched     : $("input[name='is_launched']").prop("checked"),
                // Tags
                tags            : $('#productTag').tagEditor('getTags')[0].tags,
                // Ckeditor 內容
                ckeditorContent : CKEDITOR.instances.ckeditor.getData()
            };

            let sendTextTypeData = () =>{
                return axios({
                    method : 'post',
                    url    : '/productsConsole',
                    data   : data
                })
            };

            // send the images to backend     => 圖片批次上傳
            let sendImagesTypeData = () =>{
                myDropzone.processQueue();
            };


            let handle = async() =>{
                let productId = await sendTextTypeData().then(res => res.data.id);
                $('#productId').val(productId);
                await sendImagesTypeData();
            };

            handle()
                .then(() =>{
                    alert('upload successfully');
                    location.replace('{{route('productsConsole.index')}}');
                })
                .catch(() =>{
                    alert('upload Failed!');
                });
        }

    </script>

@endsection
