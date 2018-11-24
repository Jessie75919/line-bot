<!--側邊攔 拆成共用-->
<aside class="start">
    <nav>
        <p>常用單元</p>
        <ul>
            <!--所在的類別li要加上class="active"-->
            <li id="side-home"><a href="#"><i class="fas fa-home"></i>首頁管理</a><span>more</span>
                <ul class="dropdown" id="HomeManage">
                    <li><a href="{{ route('homeImage.index') }}">首頁主圖管理</a></li>
                </ul>
            </li>
            <li id="side-unit"><a href="#"><i class="fas fa-image"></i>單元主圖管理</a><span>more</span>
                <ul class="dropdown">
                    <li><a href="#">圖片列表</a></li>
                </ul>
            </li>
            <li id="side-product" class="active">
                <a href="#"><i class="fas fa-shopping-cart"></i>商品管理</a><span>more</span>
                <ul class="dropdown" id="product-manage">
                    <li><a href="{{ route('merchandise.product.index') }}">內容管理</a></li>
                    <li><a href="{{ route('merchandise.productType.index') }}">類別管理</a></li>
                    <li><a href="{{ route('merchandise.notices.index') }}">貼心提醒管理</a></li>
                </ul>
            </li>
            <li id="side-news"><a href="#"><i class="fas fa-newspaper"></i>新聞管理</a><span>more</span>
                <ul class="dropdown" id="newsManage">
                    <li><a href="#">內容管理</a></li>
                    <li><a href="#">類別管理</a></li>
                    <li><a href="#">搜尋</a></li>
                </ul>
            </li>

            <li id="side-contact"><a href="#"><i class="fas fa-envelope"></i>聯絡表單設定</a><span>more</span>
                <ul class="dropdown" id="contactManage">
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