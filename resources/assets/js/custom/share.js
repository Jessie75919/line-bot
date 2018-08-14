var isMenuClosed = false;

$(window).on('load', function(){
    //側邊主選單
    $('nav > ul > li').click(function(){
        if(!window.sessionStorage['currentSection'] ||
            window.sessionStorage['currentSection'] !== $(this).attr('id')) {
            
            window.sessionStorage['currentSection'] = $(this).attr('id');
            $('nav > ul > li').removeClass('active');
            $('ul.dropdown').stop(true, true).slideUp(500);
            if(!$(this).find('ul').is(':visible')) {
                $(this).find('ul').slideDown();
                $(this).addClass('active');
            }
        }else {
            $('ul.dropdown').stop(true, true).slideUp(500);
            $('nav > ul > li').removeClass('active');
            window.sessionStorage['currentSection'] = null;
        }
    })

    $('.dropdown > li').on('click',function(e){
        e.stopPropagation();
    });

    $('.open').click(function(){
        $('aside').stop(true, true).toggleClass('asideClose').removeClass('start');
        $('.container').stop(true, true).toggleClass('full').removeClass('start');
        $('.editListBtn').stop(true, true).toggleClass('full').removeClass('start');
        if($(window).width() < 900) {
            $('.closeBg').fadeToggle();
        }
        return false;
    })

    $('.closeBg').click(function(){
        $('aside').stop(true, true).addClass('asideClose');
        $('.container').stop(true, true).addClass('full');
        $(this).fadeOut();
        return false;

    })

    if($(window).width() < 900) {
        closeMenu();
    }


    function openMenu(){
        $('aside').removeClass('asideClose');
        $('.container').removeClass('full');
        $('.editListBtn').removeClass('full');
        $('.closeBg').fadeOut();
    }


    function closeMenu(){
        $('aside').addClass('asideClose');
        $('.container').addClass('full');
        $('.editListBtn').addClass('full');

    }


    $(window).resize(function(){
        if($(window).width() > 900) {
            if(isMenuClosed === true) {
                openMenu();
                isMenuClosed = false;
                return false;
            }
        }

        if($(window).width() <= 900) {
            if(isMenuClosed === false) {
                closeMenu();
                isMenuClosed = true;
            }
            return false;
        }
    })

    //header下拉
    $('.headerRight > ul > li').click(function(){
        $('.headerRight > ul > li').removeClass('on');
        $('.headerRight > ul > li ul').stop(true, true).slideUp(500);
        if(!$(this).children('ul').is(':visible')) {
            $(this).children('ul').slideDown();
            $(this).addClass('on');
        }
        if($(this).children('ul').length == 0) {
            $(this).removeClass('on');
        }

    })

    $('main').click(function(){
        $('.headerRight > ul > li ul').stop(true, true).slideUp(500);
        $('.headerRight > ul > li').removeClass('on');
    })


})
