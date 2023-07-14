var $document = $(document);
var $window = $(window);


let isCheckedLogo = true;
let isCheckedMath = false;
let pageError404 = '';
let tabUrl = false;
let timerInputLocation = null;
let feedPage = 2;
let feedPageLoaded = true;
let likeAnimation = ['flip', 'jello','tada', 'swing', 'rubberBand', 'bounce'];
let likeAnimationOut = ['bounceOut', 'flipOutX','rotateOut', 'zoomOut'];
let likeAnimationBig = ['rubberBand', 'tada'];

$document.ready(function() {
    setInterval($.lpNotify, 1000);
    setInterval($.lpMessage, 1000);
    setInterval($.lpOnline, 50000);
    $.showMain();
});

$window.scroll(function() {
    $.buttonCheckScroll();
    $.checkScroll();
});

$window.resize(function() {
    $('#fixed-block').width($('#fixed-block').parent().width());
    $('#fixed-block-order').width($('#fixed-block-order').parent().width());
});

$window.bind("popstate", function () {
    if (!tabUrl) {
        var $pathname = location.pathname.replace('/', '');
        $.showPage(($pathname !== '') ? $pathname : 'index', true);
    }
});

$.redirectPage = function(url) {
    tabUrl = true;
    var newUrlParts = url.split("#");
    var currentUrlParts = window.location.href.split("#");
    window.location.href = url;
    if(newUrlParts[0] === currentUrlParts[0])
        window.location.reload(true);
    tabUrl = false;
};

$.enableLightTheme = function(enabled) {
    if (!enabled) {
        $('#theme-color').attr('content', '#fff');
        $('#theme-css').attr('href','/client/css/light-theme.css');
    }
    else {
        $('#theme-color').attr('content', '#000');
        $('#theme-css').attr('href','/client/css/dark-theme.css?' + $.getRandomInt(0, 99999));
    }

    $.ajax({
        type: 'POST',
        url: '/ajax.php',
        data: 'ajax=true&action=enable-light-theme&enabled=' + enabled,
        success: function(data) {
            console.log(data);
        },
        error: function (data, data2, data3) {
            console.log(data, data2, data3);
            M.toast({html: 'Ошибка ответа сервера', classes: 'rounded'});
        }
    });
};

$.eventClickAForm = function(event) {
    if($(this).attr('aform-click') != undefined) {
        $.aFormExecute($(this).attr('aform-click'));
        event.stopPropagation();
        event.preventDefault();
        return false;
    }
}

$.eventChangeAsyncSwitch = function(event) {
    if($(this).attr('async-swtich') != undefined) {
        $.aSwitchExecute($(this));
        event.stopPropagation();
        event.preventDefault();
        return false;
    }
}

$.eventClickSpa = function(event){
    if($(this).attr('spa') != undefined) {
        $.showPage($(this).attr('spa'));
        event.stopPropagation();
        event.preventDefault();
        return false;
    }
}

$.eventClickLike = function(event) {
    if($(this).attr('aform-click') != undefined) {
        $.aFormExecute($(this).attr('aform-click'));
        event.stopPropagation();
        event.preventDefault();
        return false;
    }
}

$.showMain = function() {

    if (
        window.location.href.substr(0, 19) == 'https://adaptation-usa.com/im' ||
        window.location.href.substr(0, 27) == 'https://adaptation-usa.com/im/archive' ||
        window.location.href.substr(0, 27) == 'https://adaptation-usa.com/im/request' ||
        window.location.href.substr(0, 26) == 'https://adaptation-usa.com/im/hidden'
    ) {
        $('#nav-top-menu').addClass('hide');
        $('#nav-bottom-menu').addClass('hide');

        if ($('.message-dialog-chat').get(0))
            $('.message-dialog-chat').scrollTop($('.message-dialog-chat').get(0).scrollHeight);
        if (
            window.location.href.substr(0, 20) == 'https://adaptation-usa.com/im/' ||
            window.location.href.substr(0, 28) == 'https://adaptation-usa.com/im/archive/' ||
            window.location.href.substr(0, 28) == 'https://adaptation-usa.com/im/request/' ||
            window.location.href.substr(0, 27) == 'https://adaptation-usa.com/im/hidden/'
        )
            $(document).scrollTop($('body').get(0).scrollHeight);
    }
    else {
        $('#nav-top-menu').removeClass('hide');
        $('#nav-bottom-menu').removeClass('hide');
    }

    M.AutoInit();

    $('body').css('overflow', 'auto');

    feedPage = 2;
    feedPageLoaded = true;

    $.setClientUTC();

    //$('.button-collapse').sideNav();

    $('input.autocomplete').autocomplete({
        data: {},
    });

    $('#input-geo').keyup(() => {
        clearTimeout(timerInputLocation);

        timerInputLocation = setTimeout(() => {
            $.ajax({
                type: 'GET',
                //url: 'https://nominatim.openstreetmap.org/search?q=' + $('#input-geo').val() + '&format=jsonv2&addressdetails=1',
                url: '/ajax.php?ajax=true&action=get:location&q=' + $('#input-geo').val(),
                cache: false,
                eof: true,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(data) {


                    console.log(data);

                    let updateData = {};
                    data.data.forEach(item => {
                        updateData[item.label] = null;
                    })

                    //$('#input-geo').updateData(updateData);

                    $('#input-geo').autocomplete({
                        data: updateData,
                    });

                    $('#input-geo').autocomplete('open');
                },
                error: function (respond, textStatus, jqXHR) {
                    console.log(respond, textStatus, jqXHR);
                }
            });
        }, 500);
    });
    //$('select').material_select();

    //$('.chips').material_chip();
    /*$('.chips-placeholder').material_chip({
     placeholder: 'ID нарушителей',
     secondaryPlaceholder: ' ',
     });*/
    $('.message-dialog-chat').scroll(function() {
        $.checkScroll();
    });

    $("nav #hover").hover(
        function() {
            if ($('#theme-css').attr('href').substr(0, 22) === '/client/css/dark-theme') {
                $("nav #hover").attr("style", "color: rgba(255, 255, 255, 0.3) !important");
                $(this).attr("style", "color: #FFF !important");
            }
            else {
                $("nav #hover").attr("style", "color: rgba(0, 0, 0, 0.3) !important");
                $(this).attr("style", "color: #000 !important");
            }
        },
        function() {
            if ($('#theme-css').attr('href').substr(0, 22) === '/client/css/dark-theme')
                $("nav #hover").attr("style", "color: #FFF !important");
            else
                $("nav #hover").attr("style", "color: #000 !important");
        }
    );

    $('#fixed-block').width($('#fixed-block').parent().width());
    $('#fixed-block-order').width($('#fixed-block-order').parent().width());

    window.mobileAndTabletCheck = function () {
        let check = false;
        (function (a) {
            if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|android|ipad|playbook|silk/i.test(a) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0, 4))) check = true;
        })(navigator.userAgent || navigator.vendor || window.opera);
        return check;
    };

    if (!window.mobileAndTabletCheck()) {
        if ($(window).height() > 600)
            $('.fullsize').height($(window).height() + 'px');
        else
            $('.fullsize').height('600px');
    }
    else {
        $("div").removeClass('vertical-center');
        $("div").removeClass('vertical-container');
    }

    setTimeout(function () {
        $('.chips input').keyup(function(e) {

            let arrayToString = [];

            $('.chips').material_chip('data').forEach(item => {
                arrayToString.push(item.tag);
            });

            $('.inputChipSend').val(arrayToString.toString());

            if (e.which === 13)
                $('#btnSendReport').removeAttr('disabled');
        });
    }, 500);

    $('main').show();
    $.showMainExtended();
    try {
        let slider = document.getElementById('price-slider');
        if (slider) {
            noUiSlider.create(slider, {
                start: [0, 150],
                connect: true,
                step: 1,
                orientation: 'horizontal',
                range: {
                    'min': 0,
                    'max': 150
                },
                format: wNumb({
                    decimals: 0
                })
            });
        }
    }
    catch (e) {}
};

$.loadAsyncForm = function() {
    $('aform').each(async function( index ) {
        let generateId = await $.sha256(new Date().getTime() + $.getRandomInt(-999999999, 999999999));
        $(this).attr('id', generateId);

        $(this).find('button').attr('aform-click', generateId);
        $(this).find('button').attr('id', 'btn-' + generateId);

        $(this).find('textarea[data-enter-event="true"]').attr('aform-click', generateId);

        $(this).find('a').attr('aform-click', generateId);
        $(this).find('a').attr('id', 'btn-' + generateId);
    });
};

$.showMainExtended = function() {
    $.loadAsyncForm();

    $('.dropdown-btn').dropdown({
            inDuration: 300,
            outDuration: 225,
            constrainWidth: false,
            hover: false,
            gutter: 39,
            belowOrigin: false,
            alignment: 'right',
            stopPropagation: false
        }
    );

    M.updateTextFields();
    $(".tabs .tab a").click(function() {
        $.redirectPage($(this).attr('href'));
    });

    $('.tooltipped').tooltip({delay: 50});
    $('textarea').height(30);
    $.buttonScroll();

    $('.collapsible').collapsible();
    $('.modal').modal();

    $('.card-feed').each(function( index ) {
        if($(this).attr('data-feed') != undefined) {
            let feedId = $(this).attr('data-feed');
            $(this).find('.card-image').dblclick(() => {
                $.feedLike(feedId);
            });
            $(this).find('.btn-feed-like').click(() => {
                $.feedLike(feedId);
            });
            $(this).find('.btn-feed-comment').click(() => {
                $.feedLoadComment(feedId);
            });
        };
    });

    $('input').each(function( index ) {
        if($(this).attr('autocomplete') === 'off')
            $(this).val('');
    });

    $('textarea').each(function( index ) {
        if($(this).attr('data-length') != undefined) {
            $(this).characterCounter();
        };
    });

    $('body').unbind('click', $.eventClickAForm);
    $('body').unbind('click', $.eventClickSpa);
    $('body').unbind('change', $.eventChangeAsyncSwitch);
    $('body').on('click', 'button', $.eventClickAForm);
    $('body').on('click', 'a', $.eventClickAForm);
    $('body').on('click', 'a', $.eventClickSpa);
    $('body').on('change', 'input', $.eventChangeAsyncSwitch);

    $('textarea').keyup(function(e) {
        if (window.screen.width > 600) {
            if(e.which == 13 && !e.shiftKey) {
                if($(this).attr('aform-click') != undefined) {
                    $.aFormExecute($(this).attr('aform-click'));
                    e.stopPropagation();
                    e.preventDefault();
                    return false;
                }
            }
        }
    });

    $('.message-chat-line').dblclick(function(e) {
        $.messageLike($(this));
    });

    $('a').each(function( index ) {
        if($(this).attr('spa') != undefined) {
            $(this).attr('href', '/' + $(this).attr('spa'));
        }
    });

    $('input, textarea').each(function( index ) {
        if($(this).attr('data-length') != undefined) {
            $(this).characterCounter();
        }
    });

    $('.carousel.carousel-slider').carousel({
        fullWidth: true,
        indicators: true
    });
};

$.hideMain = function() {
    $('main').hide();
};

let feedLikeBlock = false;
$.feedLike = function($id) {
    if (feedLikeBlock)
        return;
    feedLikeBlock = true;

    let likeClass = likeAnimation[$.getRandomInt(0, likeAnimation.length)];
    let $feedLikeBig = $('.feed-like-big');
    let $feedLikeBigI = $('.feed-like-big i');
    let $feedLike = $('#feed-like-' + $id);
    let $feedLikeI = $('#feed-like-' + $id + ' i');
    let isRemove = false;

    if ($feedLikeI.hasClass('red-text')) {
        let likeClassOut = likeAnimationOut[$.getRandomInt(0, likeAnimationOut.length)];
        $feedLikeBig.css('top', ($('#feed-' + $id).offset().top + 100) + 'px');
        $feedLikeBig.removeClass('hide');
        $feedLikeBigI.addClass(likeClassOut);
        $feedLike.addClass(likeClass);
        $feedLikeI.removeClass('red-text');
        $feedLikeI.addClass('bw-text');
        setTimeout(() => {
            feedLikeBlock = false;
            $feedLikeBig.addClass('hide');
            $feedLikeBigI.removeClass(likeClassOut);
            $feedLike.removeClass(likeClass);
        }, 500);
        isRemove = true;
    }
    else {
        $feedLikeBig.css('top', ($('#feed-' + $id).offset().top + 100) + 'px');
        $feedLikeBig.removeClass('hide');
        $feedLikeBigI.addClass(likeClass);
        $feedLike.addClass(likeClass);
        $feedLikeI.removeClass('bw-text');
        $feedLikeI.addClass('red-text');
        setTimeout(() => {
            feedLikeBlock = false;
            $feedLikeBig.addClass('hide');
            $feedLikeBigI.removeClass(likeClass);
            $feedLike.removeClass(likeClass);
        }, 500)
    }

    $.ajax({
        type: 'POST',
        url: '/ajax.php',
        data: 'ajax=true&action=feed-like&id=' + $id + '&isRemove=' + isRemove,
        dataType: 'html',
        success: function(data) {
            if (data == '') {
                M.toast({html: 'Данное действие запрещено', classes: 'rounded'});
                return;
            }
            console.log(data);
            $('#feed-like-count-' + $id).text(data);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log('ERROR', jqXHR, textStatus, errorThrown);
        }
    });
};

$.feedLoadComment = function($id, $isReset = true) {
    $('#feed-comment-id').val($id);
    $('#feed-comment-reply').val(0);
    $('#feed-comment-area').val('');
    $.ajax({
        type: 'POST',
        url: '/ajax.php',
        data: 'ajax=true&action=feed-comment-load&id=' + $id,
        dataType: 'html',
        success: function(data) {
            if ($isReset)
                $('#feed-comment-list').html(data);
            else
                $('#feed-comment-list').html($('#feed-comment-list').html() + data);

            $.loadAsyncForm();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log('ERROR', jqXHR, textStatus, errorThrown);
            $('#feed-comment-list').html('<h4 class="grey-text center">Ошибка соеденения</h4>');
        }
    });
};


$.messageLike = function($this) {
    console.log($this.attr('data-message-id'));
    $.ajax({
        type: 'POST',
        url: '/ajax.php',
        data: 'ajax=true&action=message-like&id=' + $this.attr('data-message-id'),
        dataType: 'html',
        success: function(data) {
            $this.find('.message-react').html(data);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log('ERROR', jqXHR, textStatus, errorThrown);
        }
    });
};

let timeoutPreload = null;
$.showPage = function($page, $isRepeat){
    //if((($page === 'index') ? '/' : '/' + $page) == location.pathname && $isRepeat !== true)
    //    return false;

    $('#search').val('');

    timeoutPreload = setTimeout(() => {
        $.hideMain();
        $.showPreloader();
    }, 1000)

    if($isRepeat !== true)
        window.history.pushState('Slafy', 'Slafy | ' + $page, ($page === 'index') ? '/' : '/' + $page);

    $('html, body').animate({scrollTop: 0}, 0, 'swing');

    $.ajax({
        type: 'POST',
        url: '/ajax.php' + window.location.search,
        dataType: 'html',
        data: 'ajax=true&action=show-page&page=' + $page,
        success: function(data) {
            if (data == 'refresh') {
                location.reload(true);
                return;
            }

            clearTimeout(timeoutPreload);

            $('main').html(data);
            $('title').text($('spa-title').text());
            $.hidePreloader();
            $.showMain();

            ym(91630220, 'hit', location.href, {title: $('spa-title').text()});
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log('ERROR', jqXHR, textStatus, errorThrown);
            $('main').html(pageError404);
            //$('main').html(pageError404 + '<div class="black-text"><br><hr>' + jqXHR + '<br><hr>' + textStatus + '<br><hr>' + errorThrown + '</div>');
            $('title').text($('spa-title').text());
            $.hidePreloader();
            $.showMain();
        }
    });
};

$.loadFeed = function($page, $limit, $hash = null){
    $.ajax({
        type: 'POST',
        url: '/ajax.php' + window.location.search,
        dataType: 'html',
        data: 'ajax=true&action=show-feed&page=' + $page + '&limit=' + $limit + '&hash=' + $hash,
        success: function(data) {
            $('#user-feed-content').html($('#user-feed-content').html() + data);
            $.showMainExtended();
            if (data != '')
                feedPageLoaded = true;
        },
        error: function (jqXHR, textStatus, errorThrown) {
            feedPageLoaded = true;
            console.log(jqXHR);
        }
    });
};

$.loadIm = function($page){
    let array = window.location.href.split('/');
    let val = parseInt(array[array.length - 1]);
    if (!val)
        return;
    $.ajax({
        type: 'POST',
        url: '/ajax.php' + window.location.search,
        dataType: 'html',
        data: 'ajax=true&action=show-im&page=' + $page + '&id=' + val,
        success: function(data) {

            if (data == '')
                return;

            if (window.screen.width < 600)
            {
                let height = $document.height();
                $('.message-dialog-chat').html($('.message-dialog-chat').html() + data);
                $document.scrollTop(($document.height() - height) + $document.scrollTop());
            }
            else
                $('.message-dialog-chat').html($('.message-dialog-chat').html() + data);

            $.showMainExtended();
            if (data != '')
                feedPageLoaded = true;
        },
        error: function (jqXHR, textStatus, errorThrown) {
            feedPageLoaded = true;
            console.log(jqXHR);
        }
    });
};

$.loadFeedProfile = function($page){
    $.ajax({
        type: 'POST',
        url: '/ajax.php' + window.location.search,
        dataType: 'html',
        data: 'ajax=true&action=show-feed-profile&page=' + $page + '&user=' + window.location.href.substr(18),
        success: function(data) {
            $('#feed-profile').html($('#feed-profile').html() + data);
            if (data != '')
                feedPageLoaded = true;
            $.showMainExtended();
        },
        error: function (jqXHR, textStatus, errorThrown) {
            feedPageLoaded = true;
            console.log(jqXHR);
        }
    });
};

$.aFormExecute = function($formId, $type = 'json'){

    $('#btn-' + $formId).attr('disable', 'true');

    let query = '';
    $('#' + $formId).find('select').each(function () {
        query += '&' + $(this).attr('name') + '=' + $(this).val();
    });
    $('#' + $formId).find('input').each(function () {
        query += '&' + $(this).attr('name') + '=' + $(this).val();
    });
    $('#' + $formId).find('textarea').each(function () {
        query += '&' + $(this).attr('name') + '=' + $(this).val();
    });

    $.ajax({
        type: 'POST',
        url: '/ajax.php',
        dataType: $type,
        data: 'ajax=true&action=' + $('#btn-' + $formId).attr('name') + query,
        success: function(data) {
            if (data.message == 'refresh') {
                location.reload(true);
                return;
            }
            if (data.message)
                M.toast({html: data.message, classes: 'rounded'});

            if (data.button) {
                $('#btn-' + $formId).text(data.button);
                $('#btn-' + $formId).attr('name', data.buttonName);
            }

            switch (data.action) {
                case 'refreshComment':
                    $.feedLoadComment(data.id);
                    break;
                case 'feedCommentLike':
                    $('#feed-comment-' + data.id).find('.feed-comment-like span').text(data.feedCommentLikeCount);
                    $likeIconComment = $('#feed-comment-' + data.id).find('.feed-comment-like i');
                    $likeIconComment.removeClass('red-text');
                    $likeIconComment.removeClass('bw-text');
                    $likeIconComment.addClass(data.feedCommentColor);
                    break;
                case 'feedCommentDelete':
                    $('#feed-comment-' + data.id).hide();
                    break;
                case 'feedDelete':
                    $('#feed-' + data.id).hide();
                    break;
                case 'sendDialogMessage':
                    $('.message-new-icon').addClass('hide');
                    $('.message-dialog-area').val('');
                    $('.message-dialog-chat').html(data.msg + $('.message-dialog-chat').html());
                    $('.message-dialog-chat').scrollTop($('.message-dialog-chat').get(0).scrollHeight);
                    $(document).scrollTop($('body').get(0).scrollHeight);
                    break;
            }

            if (data.page)
                $.showPage(data.page);

            console.log('success', data);
            $('#btn-' + $formId).removeAttr('disabled');
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log('error', jqXHR.responseText, jqXHR);
            $('#btn-' + $formId).removeAttr('disabled');
            M.toast({html: 'Произошла ошибка сервера ;c', classes: 'rounded'});
        }
    });
};

$.aSwitchExecute = function($this){
    $.ajax({
        type: 'POST',
        url: '/ajax.php',
        dataType: 'json',
        data: 'ajax=true&action=' + $this.attr('name') + '&check=' + $this.is(':checked'),
        success: function(data) {
            if (data.message == 'refresh') {
                location.reload(true);
                return;
            }
            M.toast({html: data.message, classes: 'rounded'});
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log('error', jqXHR.responseText);
            M.toast({html: 'Произошла ошибка сервера ;c', classes: 'rounded'});
        }
    });
};

$.lpNotify = function(){
    $.ajax({
        type: 'POST',
        url: '/ajax.php',
        dataType: 'json',
        data: 'ajax=true&action=lp-notify',
        success: function(data) {
            let countNotify = (parseInt($('#notify-indicator').attr('data-notify-count')) || 0) + parseInt(data.countNotify);
            if (countNotify > 0) {
                $('#notify-indicator').attr('data-notify-count', countNotify);
                $('.notify-indicator').text(countNotify > 99 ? '99+' : countNotify);
                $('.notify-indicator').removeClass('hide');
            }

            let countNotifyDialog = (parseInt($('#notify-d-indicator').attr('data-notify-count')) || 0) + parseInt(data.countNotifyDialog);
            if (countNotifyDialog > 0) {
                $('#notify-d-indicator').attr('data-notify-count', countNotifyDialog);
                $('.notify-d-indicator').text(countNotify > 99 ? '99+' : countNotifyDialog);
                $('.notify-d-indicator').removeClass('hide');
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            if (jqXHR.responseText)
                console.log('error', jqXHR.responseText);
        }
    });
};

$.lpMessage = function() {

    if (
        window.location.href.substr(0, 20) != 'https://adaptation-usa.com/im/' &&
        window.location.href.substr(0, 28) != 'https://adaptation-usa.com/im/archive/' &&
        window.location.href.substr(0, 28) != 'https://adaptation-usa.com/im/request/' &&
        window.location.href.substr(0, 27) != 'https://adaptation-usa.com/im/hidden/'
    )
        return;
    let val = parseInt(window.location.href.substr(20, window.location.href.length));

    if (!val)
        return;

    $.ajax({
        type: 'POST',
        url: '/ajax.php',
        dataType: 'json',
        data: 'ajax=true&action=lp-message&id=' + val,
        success: function(data) {
            if (val != data.id)
                return;
            if (data.msgr) {
                $('.message-read-icon').addClass('blue-text');
            }
            if (data.msg != '') {
                $('.message-dialog-chat').html(data.msg + $('.message-dialog-chat').html());
                $('.message-dialog-chat').scrollTop($('.message-dialog-chat').get(0).scrollHeight);
                $(document).scrollTop($('body').get(0).scrollHeight);
                $('.message-read-icon').addClass('blue-text');
            }
            $('#message-user-online-status').text(data.msgt);
            if (data.msgo) {
                $('#dialog-id-' + val).find('.message-user-online').removeClass('hide');
                $('.message-dialog-info').find('.message-user-online').removeClass('hide');
            }
            else {
                $('#dialog-id-' + val).find('.message-user-online').addClass('hide');
                $('.message-dialog-info').find('.message-user-online').addClass('hide');
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            if (jqXHR.responseText)
                console.log('error', jqXHR.responseText);
        }
    });
};

$.lpKp = function() {
    if (
        window.location.href.substr(0, 20) != 'https://adaptation-usa.com/im/' &&
        window.location.href.substr(0, 28) != 'https://adaptation-usa.com/im/archive/' &&
        window.location.href.substr(0, 28) != 'https://adaptation-usa.com/im/request/' &&
        window.location.href.substr(0, 27) != 'https://adaptation-usa.com/im/hidden/'
    )
        return;
    let val = parseInt(window.location.href.substr(20, window.location.href.length));

    if (!val)
        return;

    $.ajax({
        type: 'POST',
        url: '/ajax.php',
        dataType: 'json',
        data: 'ajax=true&action=lp-kp&id=' + val,
        success: function(data) {},
        error: function (jqXHR, textStatus, errorThrown) {
            if (jqXHR.responseText)
                console.log('error', jqXHR.responseText);
        }
    });
};

$.lpOnline = function(){
    $.ajax({
        type: 'POST',
        url: '/ajax.php',
        dataType: 'json',
        data: 'ajax=true&action=lp-online',
        success: function(data) {},
        error: function (jqXHR, textStatus, errorThrown) {
            if (jqXHR.responseText)
                console.log('error', jqXHR.responseText);
        }
    });
};

let timeoutSearchDialog = null;
$.searchDialog = function() {
    if (timeoutSearchDialog)
        clearTimeout(timeoutSearchDialog);
    timeoutSearchDialog = setTimeout(function () {
        timeoutSearchDialog = null;
        let val = $('#input-dialog-list-search').val();
        if (val.trim() === '') {
            $('#dialog-main-list').removeClass('hide');
            $('#dialog-search-list').addClass('hide');
        }
        else {
            $('#dialog-main-list').addClass('hide');
            $('#dialog-search-list').removeClass('hide');
            $.ajax({
                type: 'POST',
                url: '/ajax.php',
                dataType: 'html',
                data: 'ajax=true&action=search-dialog&q=' + val,
                success: function(data) {
                    $('#dialog-search-list').html(data);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    if (jqXHR.responseText)
                        console.log('error', jqXHR.responseText);
                }
            });
        }
    }, 500);
};

$.numberFormat = function (currentMoney) {
    return currentMoney.toString().replace(/.+?(?=\D|$)/, function(f) {
        return f.replace(/(\d)(?=(?:\d\d\d)+$)/g, "$1,");
    });
};

$.getQueryVariable = function(variable) {
    let query = window.location.search.substring(1);
    let vars = query.split('&');
    for (let i = 0; i < vars.length; i++) {
        let pair = vars[i].split('=');
        if (decodeURIComponent(pair[0]) == variable) {
            return decodeURIComponent(pair[1]);
        }
    }
    console.log('Query variable %s not found', variable);
    return undefined;
};

$.showPreloader = function() {
    $('#preloader').removeClass('fadeOut');
    $('#preloader').addClass('fadeIn');
};

$.hidePreloader = function() {
    $('#preloader').removeClass('fadeIn');
    $('#preloader').addClass('fadeOut');
};

let insaTimeout = null;
$.getInstagramAccount = function(name) {

    $('#instagram-error').addClass('hide');
    $('#instagram-success').addClass('hide');
    $('#instagram-btn-next3').attr('disabled', 'true');

    if (insaTimeout)
        clearTimeout(insaTimeout);
    insaTimeout = setTimeout(() => {
        $.ajax({
            type: 'POST',
            url: '/ajax.php?ajax=true&action=get:instagram:account&account=' + name,
            cache: false,
            eof: true,
            dataType: 'json',
            processData: false,
            contentType: false,
            success: function(data) {
                if (data.error) {
                    $('#instagram-error').removeClass('hide');
                    $('#instagram-error-label').text(data.error);
                }
                else {
                    $('#instagram-success').removeClass('hide');
                    $('#instagram-img').attr('src', '/upload/instagram/' + data.login + '.jpg');
                    $('#instagram-btn-next3').removeAttr('disabled');
                    $('#instagram-nick').text(data.login);
                }
                console.log(data);
            },
            error: function (respond, textStatus, jqXHR) {
                $('#instagram-error').removeClass('hide');
                console.log('error', respond, textStatus, jqXHR);
            }
        });
        insaTimeout = null;
    }, 500)
}

$.instagramExport = function(name) {
    $.ajax({
        type: 'POST',
        url: '/ajax.php?ajax=true&action=export:instagram:account&account=' + name + '&feed=' + $('#instagram-export-feed').is(":checked") + '&story=' + $('#instagram-export-story').is(":checked"),
        cache: false,
        eof: true,
        dataType: 'json',
        processData: false,
        contentType: false,
        success: function(data) {
            M.toast({html: data.message, classes: 'rounded'});
        },
        error: function (respond, textStatus, jqXHR) {
            M.toast({html: 'Произошла ошибка экспорта данных', classes: 'rounded'});
        }
    });
}

$.getRandomInt = function(min, max) {
    min = Math.ceil(min);
    max = Math.floor(max);
    return Math.floor(Math.random() * (max - min)) + min; //
}

$.sha256 = async function(message) {
    const hashBuffer = await crypto.subtle.digest('SHA-256', new TextEncoder().encode(message));
    return Array.from(new Uint8Array(hashBuffer)).map(b => b.toString(16).padStart(2, '0')).join('');
}

$.getFormSignature = function() {
    let account = $('#pay-acc').val();
    let sum = $('#pay-sum').val();
    let desc = $('#pay-desc').val();
    $.ajax({
        type: 'POST',
        url: '/ajax.php?ajax=true&action=generate:signature&account=' + account + '&sum=' + sum + '&desc=' + desc,
        data: 'ajax=true&action=generate:signature&account=' + account + '&sum=' + sum + '&desc=' + desc,
        cache: false,
        eof: true,
        dataType: 'html',
        processData: false,
        contentType: false,
        success: function(data) {
            console.log(data);
            $('#pay-signature').val(data);
            $('#pay-btn').prop("disabled", false);
        },
        error: function (respond, textStatus, jqXHR) {
            console.log('error', respond, textStatus, jqXHR);

            //$('main').html(pageError404);
        }
    });
}

$.buttonScroll = function() {
    $('#scrollup').click( function() {
        $('html, body').animate({scrollTop: 0}, '500', 'swing');
        return false;
    });
    $('#scrolldown').click( function() {
        $('html, body').animate({scrollTop: $('.fullsize').height()}, '500', 'swing');
        return false;
    });
};

$.buttonCheckScroll = function() {

    if ($document.scrollTop() > 100 ) {
        $('#scrollup').css('opacity', 1);
        $('#scrollup').removeClass('bounceOutDown');
        $('#scrollup').addClass('bounceInUp');
    } else {
        $('#scrollup').removeClass('bounceInUp');
        $('#scrollup').addClass('bounceOutDown');
    }

    if ($document.scrollTop() < 10 ) {
        $('#scrolldown').css('opacity', 1);
        $('#scrolldown').removeClass('bounceOut');
        $('#scrolldown').addClass('bounceIn');
    } else {
        $('#scrolldown').css('opacity', 0);
        $('#scrolldown').removeClass('bounceIn');
        $('#scrolldown').addClass('bounceOut');
    }
};

$.checkScroll = function() {
    if (window.location.href.substr(0, 21) == 'https://adaptation-usa.com/feed')
    {
        if ($document.scrollTop() > ($document.height() - 1000) && feedPageLoaded) {
            feedPageLoaded = false;
            $.loadFeed(feedPage, 5);
            feedPage++;
        }
    }
    if (window.location.href.substr(0, 20) == 'https://adaptation-usa.com/uf/')
    {
        if ($document.scrollTop() > ($document.height() - 1000) && feedPageLoaded) {
            feedPageLoaded = false;
            $.loadFeed(feedPage, 5, window.location.href.substr(20));
            feedPage++;
        }
    }
    if (window.location.href.substr(0, 18) == 'https://adaptation-usa.com/@')
    {
        if ($document.scrollTop() > ($document.height() - 1000) && feedPageLoaded) {
            feedPageLoaded = false;
            $.loadFeedProfile(feedPage);
            feedPage++;
        }
    }
    if (
        window.location.href.substr(0, 20) == 'https://adaptation-usa.com/im/' ||
        window.location.href.substr(0, 28) == 'https://adaptation-usa.com/im/archive/' ||
        window.location.href.substr(0, 28) == 'https://adaptation-usa.com/im/request/' ||
        window.location.href.substr(0, 27) == 'https://adaptation-usa.com/im/hidden/'
    )
    {

        if (window.screen.width < 600)
        {
            if ($document.scrollTop() < 500 && feedPageLoaded) {
                feedPageLoaded = false;
                $.loadIm(feedPage);
                feedPage++;
            }
        }
        else {
            let scrollh = ($('.message-dialog-chat').get(0).scrollHeight - 800) * -1;
            if ($('.message-dialog-chat').scrollTop() < scrollh && feedPageLoaded) {
                feedPageLoaded = false;
                $.loadIm(feedPage);
                feedPage++;
            }
        }
    }
};

$(function(){
    $('.demo-noninputable').pastableNonInputable();
    $('.demo-textarea').on('focus', function(){
        var isFocused = $(this).hasClass('pastable-focus');
        console && console.log('[textarea] focus event fired! ' + (isFocused ? 'fake onfocus' : 'real onfocus'));
    }).pastableTextarea().on('blur', function(){
        var isFocused = $(this).hasClass('pastable-focus');
        console && console.log('[textarea] blur event fired! ' + (isFocused ? 'fake onblur' : 'real onblur'));
    });
    $('.demo-contenteditable').pastableContenteditable();
    $('.demo').on('pasteImage', function(ev, data)
    {
        var blobUrl = URL.createObjectURL(data.blob);
        var name = data.name != null ? ', name: ' + data.name : '';

        $.uploadImage(data, 'img:texture', $('#texture-name').val(), $('#texture-cat').val());

    }).on('pasteImageError', function(ev, data){
        alert('Oops: ' + data.message);
        if(data.url){
            alert('But we got its url anyway:' + data.url)
        }
    }).on('pasteText', function(ev, data){
        $('<div class="result"></div>').text('text: "' + data.text + '"').insertAfter(this);
    }).on('pasteTextRich', function(ev, data){
        $('<div class="result"></div>').text('rtf: "' + data.text + '"').insertAfter(this);
    }).on('pasteTextHtml', function(ev, data){
        $('<div class="result"></div>').text('html: "' + data.text + '"').insertAfter(this);
    });
});

$.uploadImage = function ($images, $action, $name, $catId) {

    console.log($images);

    files = $images.files;

    event.stopPropagation();
    event.preventDefault();

    let data = new FormData();

    if ($images.blob) {
        data.append('data', $images.blob);
    }
    else {
        $.each( files, function( key, value ){
            data.append( key, value );
        });
    }

    data.append('ajax', true);
    data.append('action', $action);
    data.append('name', $name);
    data.append('catId', parseInt($catId) || 0);

    $.ajax({

        url: '/ajax.php',
        type: 'POST',
        data: data,
        cache: false,
        eof: true,
        dataType: 'html',
        processData: false,
        contentType: false,
        success: function(respond, textStatus, jqXHR) {
            console.log(respond);
            if( typeof respond.error === 'undefined' ) {

                let result = JSON.parse(respond);

                if (typeof result.success === 'undefined')
                    M.toast({html: 'Картинка успешно загружена', classes: 'rounded'});
                else
                    M.toast({html: result.success.message, classes: 'rounded'});

                if ($catId === -1) {
                    $('#iphone-body').css('background', 'url("/upload/iphone/exclusive/' + result.success.files.files[0] + '") center no-repeat');
                    $('#iphone-body').css('background-size', 'cover');
                    $('[id=iphone-texture]').attr('src', '/upload/iphone/exclusive/' + result.success.files.files[0]);
                    $('#data-exclusive').val(result.success.files.files[0]);
                }
            }
            else {
                console.log('Ошибка сервера: ', respond.error);
                console.log(respond, textStatus, jqXHR);
                M.toast({html: 'Ошибка сервера: ' + respond.error, classes: 'rounded'});
            }
        },
        error: function(e, textStatus, errorThrown) {
            console.log('Ошибка Ajax: ', textStatus, errorThrown, e);
            M.toast({html: 'Ошибка ответа сервера', classes: 'rounded'});
        }
    });
};

$.parseUrl = function (name, url) {
    if (!url) url = location.href;
    name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
    var regexS = "[\\?&]"+name+"=([^&#]*)";
    var regex = new RegExp( regexS );
    var results = regex.exec( url );
    return results == null ? null : results[1];
}

$.fallbackCopyTextToClipboard = function (text) {
    var textArea = document.createElement("textarea");
    textArea.value = text;

    // Avoid scrolling to bottom
    textArea.style.top = "0";
    textArea.style.left = "0";
    textArea.style.position = "fixed";

    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    try {
        var successful = document.execCommand('copy');
        var msg = successful ? 'successful' : 'unsuccessful';
        console.log('Fallback: Copying text command was ' + msg);
    } catch (err) {
        console.error('Fallback: Oops, unable to copy', err);
    }

    document.body.removeChild(textArea);
}

$.copyTextToClipboard = function (text, notifyText = 'Текст был скопирован') {
    if (!navigator.clipboard) {
        $.fallbackCopyTextToClipboard(text);
        return;
    }
    navigator.clipboard.writeText(text).then(function() {
        M.toast({html: notifyText, classes: 'rounded'});
    }, function(err) {
    });
}

