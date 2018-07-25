
function httpGetWithId(url, id){
    $.get(`/api/v1/products/${url}/${id}` ,
        function (data, textStatus, jqXHR) {
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