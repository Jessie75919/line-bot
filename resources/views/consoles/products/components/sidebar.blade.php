<!--側邊攔 拆成共用-->
<aside class="start">
    <nav>
        <p>常用單元</p>
        <ul>
            <!--所在的類別li要加上class="active"-->
            <li><a href="#"><i class="fas fa-home"></i>首頁管理</a><span>more</span>
                <ul class="dropdown">
                    <li><a href="#">首頁主圖管理</a></li>
                </ul>
            </li>
            <li><a href="#"><i class="fas fa-image"></i>單元主圖管理</a><span>more</span>
                <ul class="dropdown">
                    <li><a href="#">圖片列表</a></li>
                </ul>
            </li>
            <li class="active"><a href="{{route('productsConsole.index')}}"><i class="fas fa-shopping-cart"></i>商品管理</a><span>more</span>
                <ul class="dropdown">
                    <li><a href="{{route('productsConsole.index')}}">內容管理</a></li>
                    <li><a href="{{route('productsConsole.create')}}">類別管理</a></li>
                    <li><a href="../product/search.htm">搜尋</a></li>
                </ul>
            </li>
            <li><a href="#"><i class="fas fa-newspaper"></i>新聞管理</a><span>more</span>
                <ul class="dropdown">
                    <li><a href="#">內容管理</a></li>
                    <li><a href="#">類別管理</a></li>
                    <li><a href="#">搜尋</a></li>
                </ul>
            </li>

            <li><a href="#"><i class="fas fa-envelope"></i>聯絡表單設定</a><span>more</span>
                <ul class="dropdown">
                    <li><a href="#">表單管理</a></li>
                    <li><a href="#">問題類別</a></li>
                    <li><a href="#">系統信設定</a></li>
                    <li><a href="#">搜尋</a></li>
                </ul>
            </li>
        </ul>
    </nav>
</aside>
<div class="closeBg"></div>
<!--側邊攔 end-->