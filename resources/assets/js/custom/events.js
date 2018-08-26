$(function(){

    /* 上架狀態切換 */
    $('.launch_status').on('change', function(){
        let section = $(this).attr('data-section');
        httpGetWithId(`${section}/status_switch`, $(this).val());
    });


    /* 更新排序 */
    $('#update_order').on('click', function(){
        let section     = $(this).attr('data-section');
        let UpdateItems = $('.order').toArray();
        let data        = {
            data : UpdateItems.map(item =>{
                return {id : item.id, order : item.value};
            })
        };

        httpPostWithData(`${section}/update_order`, data, function(data, textStatus, jqXHR){
            swal("排序更新成功！", {
                icon : "success",
            }).then(res =>{
                window.location.replace(`/product/${section}`);
            });
        });
    });


    /* 點擊總checkbox */
    $('#master_checkbox').on('change', function(){
        $('.batched_action').not(this).prop('checked', this.checked);
    });


    /* 批次刪除 */
    $('#delete_selected').on('click', function(){

        let section     = $(this).attr('data-section');
        let deleteItems =
                $('.batched_action:checked').toArray();

        if(deleteItems.length === 0) {
            return false;
        }


        swal({
            title      : 'Are you sure?',
            text       : "確定要批次刪除嗎？",
            icon       : "warning",
            buttons    : true,
            dangerMode : true,
        }).then((result) =>{
            if(result) {
                let data = {
                    data : deleteItems.map(item =>{
                        return {id : item.value};
                    })
                };

                httpPostWithData(
                    `${section}/multi_delete`,
                    data,
                    function(data, textStatus, jqXHR){
                        swal("類別刪除成功！", {
                            icon : "success",
                        }).then(res =>{
                            window.location.replace(`/product/${section}`);
                        });
                    }
                );
            }
        })
    });


});



