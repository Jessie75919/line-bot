<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <title>sideNav</title>
    <!--css-->
    <link rel="stylesheet" href="{{mix('/css/all.css')}}">
</head>

<body>

<!--header 拆成共用-->
<header>
    <figure class="logo"><a href="../index/index.htm"><img src="../images/share/logo.svg" alt="CHU.C" title="CHU.C"></a></figure>
    <div class="open"><i class="fas fa-bars" title="MENU"></i></div>
    <!--上方選單-->
    <div class="headerRight">
        <ul>
            <li class="account"><a href="#" title="帳號"><i class="fas fa-user-circle"></i><span>Jackie Kuo<i class="fas fa-angle-down"></i></span></a>
                <ul>
                    <li><a href="../account/account.htm" target="_blank">我的帳號</a></li>
                    <li><a href="../admin/list.htm">帳號管理</a></li>
                    <li><a href="#">登出<i class="fas fa-sign-out-alt"></i></a></li>
                </ul>
            </li>
            <li class="language"><a href="#" title="語言"><i class="fas fa-globe"></i><span>繁中<i class="fas fa-angle-down"></i></span></a>
                <ul>
                    <li><a href="#">English</a></li>
                    <li><a href="#">日本語</a></li>
                </ul>
            </li>
            <li><a href="../contact/list.htm"><i class="fas fa-envelope"></i><span class="remind">10</span></a></li>
        </ul>
    </div>
    <!--上方選單 end-->
</header>
<!--header end-->


<main>
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
                <li class="active"><a href="#"><i class="fas fa-shopping-cart"></i>商品管理</a><span>more</span>
                    <ul class="dropdown">
                        <li><a href="../product/list.htm">內容管理</a></li>
                        <li><a href="../product/cata-list.htm">類別管理</a></li>
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

    <div class="container start">
        <!--主要內容-->
        <h1>商品管理<span> / 內容管理</span></h1>
        <div class="panel">
            <div class="panelBody bt-wrapper">
                <h2>單元列表一覽</h2>
                <!--編輯按鈕區塊-->
                <div class="editListBtn">
                    <a href="add.htm"><i class="fas fa-plus"></i>新增內容</a>
                    <a href="#"><i class="fas fa-redo-alt"></i>更新排序</a>
                    <a href="#"><i class="fas fa-trash-alt"></i>刪除勾選</a>

                </div>
                <!--編輯按鈕區塊 end-->
                <!--搜尋商品狀態-->
                <div class="stuts">
                    <div class="stutsList">
                        上線狀態
                        <select name="onlineStuts" id="onlineStuts">
                            <option value="all">全部</option>
                            <option value="on">上線中</option>
                            <option value="off">下線中</option>
                        </select>
                    </div>
                    <div class="stutsList">
                        選擇類別
                        <select name="onlineStuts" id="onlineStuts">
                            <option value="0">全部</option>
                            <option value="1">項鍊</option>
                            <option value="2">耳環</option>
                            <option value="3">手鍊</option>
                            <option value="4">髮飾</option>
                            <option value="5">鑰匙圈</option>
                        </select>
                    </div>
                    <div class="stutsList">
                        關鍵字
                        <input type="text" name="keyword" id="keyword">
                    </div>
                    <div class="stutsList">
                        <input type="button" value="送出">
                    </div>
                </div>
                <!--搜尋商品狀態 end-->
                <div class="rwdTable">
                    <table>
                        <thead>
                        <tr>
                            <th><input id="checkbox" type="checkbox" name="checkbox"></th>
                            <th>排序</th>
                            <th>縮圖</th>
                            <th>品名</th>
                            <th>類別</th>
                            <th>狀態</th>
                            <th>功能</th>
                        </tr>
                        </thead>
                        <tbody>
                        <!--列表每頁10則-->
                        <tr>
                            <td><input id="checkbox" type="checkbox" name="checkbox"></td>
                            <td><input type="text" value="1"></td>
                            <td><img src="../../shop-upload/shop001/product/p_20180709001.jpg"></td>
                            <td>你好!小浣熊童趣乾燥花耳環</td>
                            <td>耳環</td>
                            <td><input type="checkbox" class="js-switch"/></td>
                            <td><a class="btn circle" href="edit.htm" title="編輯"><i class="fas fa-edit"></i></a><a
                                        class="btn circle" href="#" title="複製"><i class="fas fa-clone"></i></a><a
                                        class="btn circle" href="#" title="刪除"><i class="fas fa-trash-alt"></i></a></td>
                        </tr>
                        <tr>
                            <td><input id="checkbox" type="checkbox" name="checkbox"></td>
                            <td><input type="text" value="2"></td>
                            <td><img src="../../shop-upload/shop001/product/p_20180709002.jpg"></td>
                            <td>層層疊疊乾燥花耳環</td>
                            <td>耳環</td>
                            <td><input type="checkbox" class="js-switch" checked/></td>
                            <td><a class="btn circle" href="edit.htm" title="編輯"><i class="fas fa-edit"></i></a><a
                                        class="btn circle" href="#" title="複製"><i class="fas fa-clone"></i></a><a
                                        class="btn circle" href="#" title="刪除"><i class="fas fa-trash-alt"></i></a></td>
                        </tr>
                        <tr>
                            <td><input id="checkbox" type="checkbox" name="checkbox"></td>
                            <td><input type="text" value="1"></td>
                            <td><img src="../../shop-upload/shop001/product/p_20180709001.jpg"></td>
                            <td>你好!小浣熊童趣乾燥花耳環</td>
                            <td>耳環</td>
                            <td><input type="checkbox" class="js-switch"/></td>
                            <td><a class="btn circle" href="edit.htm" title="編輯"><i class="fas fa-edit"></i></a><a
                                        class="btn circle" href="#" title="複製"><i class="fas fa-clone"></i></a><a
                                        class="btn circle" href="#" title="刪除"><i class="fas fa-trash-alt"></i></a></td>
                        </tr>
                        <tr>
                            <td><input id="checkbox" type="checkbox" name="checkbox"></td>
                            <td><input type="text" value="2"></td>
                            <td><img src="../../shop-upload/shop001/product/p_20180709002.jpg"></td>
                            <td>層層疊疊乾燥花耳環</td>
                            <td>耳環</td>
                            <td><input type="checkbox" class="js-switch" checked/></td>
                            <td><a class="btn circle" href="edit.htm" title="編輯"><i class="fas fa-edit"></i></a><a
                                        class="btn circle" href="#" title="複製"><i class="fas fa-clone"></i></a><a
                                        class="btn circle" href="#" title="刪除"><i class="fas fa-trash-alt"></i></a></td>
                        </tr>
                        <tr>
                            <td><input id="checkbox" type="checkbox" name="checkbox"></td>
                            <td><input type="text" value="2"></td>
                            <td><img src="../../shop-upload/shop001/product/p_20180709002.jpg"></td>
                            <td>層層疊疊乾燥花耳環</td>
                            <td>耳環</td>
                            <td><input type="checkbox" class="js-switch" checked/></td>
                            <td><a class="btn circle" href="edit.htm" title="編輯"><i class="fas fa-edit"></i></a><a
                                        class="btn circle" href="#" title="複製"><i class="fas fa-clone"></i></a><a
                                        class="btn circle" href="#" title="刪除"><i class="fas fa-trash-alt"></i></a></td>
                        </tr>
                        <!--列表每頁10則 end-->
                        </tbody>
                    </table>
                </div>
                <!--頁碼-->
                <div class="page">
                    <p>共 2 頁 / 總共 16 筆記錄</p>
                    <p><a href="#"><i class="fas fa-angle-left"></i></a><a href="#">1</a><a href="#">2</a><a
                                href="#">3</a><a href="#">4</a><a href="#">5</a><a href="#">6</a><a href="#">7</a><a
                                href="#">8</a><a href="#">9</a><a href="#">10</a><a href="#"><i
                                    class="fas fa-angle-right"></i></a></p>
                </div>

            </div>
        </div>

        <!--主要內容 end-->
        <!--footer 拆成共用-->
        <footer>©CHU.C 啾囍 All Rights Reserved.</footer>
        <!--footer end-->
        <!--main js-->
    </div>
</main>


<script src="{{mix('/js/all.js')}}"></script>
<!--狀態按鈕-->
<script>
    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));

    elems.forEach(function(html){
        var switchery = new Switchery(html);

    });

</script>
</body>

</html>
