$(function(){

    //  內容管理 上架狀態切換
    $('.launch_status').on('change', function(){
        httpGetWithId('status_switch', $(this).val());
    });


    /* 更新排序 */
    $('#update_order').on('click', function(){

        let products = $('.order').toArray();
        let data     = {
            data : products.map(item =>{
                return {id : item.id, order : item.value};
            })
        };

        httpPostWithData('update_order', data, function(data, textStatus, jqXHR){
            window.location.replace("/product/content");
        });
    });


    /* 點擊總checkbox */
    $('#master_checkbox').on('change', function(){
        $('.batched_action').not(this).prop('checked', this.checked);
    });


    /* 批次刪除 */
    $('#delete_selected').on('click', function(){

        let deleteItems = $('.batched_action:checked').toArray();
        if(deleteItems.length === 0) {
            return false;
        }

        if(confirm("確定要批次刪除嗎？")) {

            console.log(deleteItems[0]);



            let data = {
                data : deleteItems.map(item =>{
                    return {id : item.value};
                })
            };
            console.log(data);
            httpPostWithData('multi_delete', data, function(data, textStatus, jqXHR){
                window.location.replace("/product/content");
            });
        }
    });


});



