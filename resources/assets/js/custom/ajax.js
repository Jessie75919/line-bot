
function httpGetWithId(url, id, cb = null){
    $.get(`/api/v1/products/${url}/${id}` ,
        function (data, textStatus, jqXHR) {
            if(cb) {
                cb();
            }
        },
        "json"
    );
}


function httpPostWithData(url, data, success){
    $.post(`/api/v1/products/${url}`, data,
        success, "json"
    );
}


function get(){
    
}