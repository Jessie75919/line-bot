<!--header 拆成共用-->
<header>
    <figure class="logo"><a href="{{route('productsConsole.index')}}">
            <img src="/images/share/logo.svg" alt="CHU.C" title="CHU.C"></a>
    </figure>
    <div class="open"><i class="fas fa-bars" title="MENU"></i></div>
    <!--上方選單-->
    <div class="headerRight">
        <ul>
            <li class="account"><a href="#" title="帳號"><i class="fas fa-user-circle"></i><span> {{ Auth::user()->name }}
                        <i class="fas fa-angle-down"></i></span></a>
                <ul>
                    <li><a href="../account/account.htm" target="_blank">我的帳號</a></li>
                    <li><a href="../admin/list.htm">帳號管理</a></li>
                    <li><a href="#">登出<i class="fas fa-sign-out-alt"></i></a></li>
                </ul>
            </li>
            <li class="language"><a href="#" title="語言"><i class="fas fa-globe"></i><span>繁中<i
                                class="fas fa-angle-down"></i></span></a>
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