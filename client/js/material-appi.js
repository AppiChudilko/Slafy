var $document = $(document);
var $window = $(window);


$document.ready(function() {

});

$.footerBottom = function() {
    var docHeight = $(window).height();
    var footerHeight = $('footer').height();
    var footerTop = $('footer').position().top + footerHeight;

    if (footerTop < docHeight) {
        $('footer').css('margin-top', (docHeight - footerTop) - 10 + 'px');
    }
};

$.setClientUTC = function() {
    setCookie("UTC", $.getClientUTC(), {
        express: 99999,
        domain: 'adaptation-usa.com',
        path: '/',
    });
    //document.cookie = "UTC="+$.getClientUTC();
};

$.getClientUTC = function() {
    return new Date().getTimezoneOffset() / -60;
};

$.nl2br = function ($str, $is_xhtml) {
    var $breakTag = ($is_xhtml || typeof $is_xhtml === 'undefined') ? '<br />' : '<br>';
    return ($str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1'+ $breakTag +'$2');
};

$.isEmpty = function($el) {
    return !$.trim($el.html())
};

/* *
 *
 * JavaScript
 *
 * */

function secondsToTime(secs)
{
    var hours = Math.floor(secs / (60 * 60));

    var divisor_for_minutes = secs % (60 * 60);
    var minutes = Math.floor(divisor_for_minutes / 60);

    var divisor_for_seconds = divisor_for_minutes % 60;
    var seconds = Math.ceil(divisor_for_seconds);

    String.prototype.padLeft = function (length, character) {
        return new Array(length - this.length + 1).join(character || '0') + this;
    };

    return minutes + ":" + seconds.toString().padLeft(2, '0');
}

function getCookie(name) {
    var matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ));
    return matches ? decodeURIComponent(matches[1]) : undefined;
}

function setCookie(name, value, options) {
    options = options || {};

    var expires = options.expires;

    if (typeof expires == "number" && expires) {
        var d = new Date();
        d.setTime(d.getTime() + expires * 1000);
        expires = options.expires = d;
    }
    if (expires && expires.toUTCString) {
        options.expires = expires.toUTCString();
    }

    value = encodeURIComponent(value);

    var updatedCookie = name + "=" + value;

    for (var propName in options) {
        updatedCookie += "; " + propName;
        var propValue = options[propName];
        if (propValue !== true) {
            updatedCookie += "=" + propValue;
        }
    }

    document.cookie = updatedCookie;
}

function deleteCookie(name) {
    setCookie(name, "", {
        expires: -1
    })
}