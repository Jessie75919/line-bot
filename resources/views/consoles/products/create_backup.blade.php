<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        window.Laravel = {
            csrfToken : "{{ csrf_token() }}"
        };
    </script>
    <script src="{{ mix('/js/app.js') }}"></script>
    <script src="{{ mix('/js/all.js') }}"></script>


    <link rel="stylesheet" href="{{ mix('/css/all.css')}}">
    <title>sideNav</title>


</head>

<body>


<div id="sortable" class="dropzone"></div>
<button id="imgsubbutt" class="btn btn-primary">SNED IMAGES</button>

<div id="demo1"></div>


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

</body>
</html>
