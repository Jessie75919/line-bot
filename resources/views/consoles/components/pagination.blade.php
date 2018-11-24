<div class="page">
    <p>共 {{ceil($paginator->total() / 5 )}} 頁 / 總共 {{$paginator->total()}} 筆記錄</p>
    <p>
        <a href="{{$paginator->previousPageUrl()}}
        @if(isset($lastQueryString))
                &{{$lastQueryString}}
        @endif
                ">
            <i class="fas fa-angle-left"></i></a>
        @foreach(range(1, ceil($paginator->total()/ 5)) as $page)
            <a href="{{$paginator->url($page)}}
            @if(isset($lastQueryString))
                    &{{$lastQueryString}}
            @endif
                    ">{{$page}} </a>
        @endforeach
        <a href="{{$paginator->nextPageUrl()}}"><i class="fas fa-angle-right"></i></a>
    </p>
</div>