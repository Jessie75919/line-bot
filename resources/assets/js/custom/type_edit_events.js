$(function(){

    init();

    $('#updateBtn').on('click', function(){
        if(!$("#formContent").valid()) {
            return false ;
        }

        let section       = $(this).attr('data-section');
        let productTypeId = $(this).attr('data-id');
        let data          = {
            name : $("input[name='name']").val(),
        };

        let sendTextTypeData = () =>{
            return axios({
                method : 'put',
                url    : `/product/${section}/${productTypeId}`,
                data   : data
            })
        };

        sendTextTypeData()
            .then((res) =>{
                if(res.data.includes('SUCCESS')) {
                    swal("類別更新成功！", {
                        icon : "success",
                    }).then(res =>{
                        console.log(res);
                        window.location.replace(`/product/${section}`);
                    });
                }
            })
            .catch(err => {
                swal("類別更新失敗！", "請洽詢工程師處理！", {
                    icon : "error",
                });
                console.log(err);
            });
    });
});

function init(){
    jqValidateInit();
}

function jqValidateInit(){
    $("#formContent").validate({
        wrapper  : 'span',
        rules    : {
            name : {
                required  : true,
                maxlength : 20
            },
        },
        messages : {
            name : {
                required  : "商品名稱不可為空白!",
                maxlength : "商品名稱不可超過20字喔！"
            },
        }
    });
}