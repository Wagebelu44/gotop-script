jQuery(document).ready(function (e) {
    function t(e, t, i) {
        var a = e + "=" + encodeURIComponent(t);
        if (i) {
            var n = new Date;
            n.setTime(n.getTime() + 24 * i * 60 * 60 * 1e3), a += ";expires=" + n.toGMTString()
        }
        a += ";path=/", document.cookie = a
    }

    var i = e(".wp-sheet");

    function a() {
        i.is(":visible") ? (i.hide(), t("fbmsg-sheet-open", "0", 365)) : (i.show(), s(), t("fbmsg-sheet-open", "1", 365))
    }

    e(".wp-sheet-content");
    var n, o = e(".wp-sheet-content-part");

    /*function s() {
        var t = e(".wp-sheet-content-inner"), i = t.width(), a = t.height();
        o.html('<div class="fb-page" data-tabs="messages,timeline" data-href="https://www.facebook.com/%E0%A6%86%E0%A6%B7%E0%A6%BE%E0%A6%A2%E0%A6%BC%E0%A7%87-%E0%A6%97%E0%A6%AA%E0%A7%8D%E0%A6%AA%E0%A7%8B-624030524444856/" data-width="' + i + '" data-height="' + a + '" data-href="https://www.facebook.com/%E0%A6%86%E0%A6%B7%E0%A6%BE%E0%A6%A2%E0%A6%BC%E0%A7%87-%E0%A6%97%E0%A6%AA%E0%A7%8D%E0%A6%AA%E0%A7%8B-624030524444856/" data-small-header="true"  data-hide-cover="false" data-show-facepile="true" data-adapt-container-width="false"><div class="fb-xfbml-parse-ignore"><blockquote>Loading...</blockquote></div></div>'), "FB" in window && FB.XFBML.parse()
    }*/

    e(".fbmsg-badge").click(function () {
        return a(), !1
    }), e(".wp-sheet-head-close").click(function () {
        return a(), !1
    }), "1" == (n = "fbmsg-sheet-open", (n = (document.cookie.match("(^|; )" + n + "=([^;]*)") || 0)[2]) && decodeURIComponent(n)) ? (i.show(), s()) : i.hide();
    var c, d = (c = {}, function (e, t, i) {
        i || (i = "Don't call this twice without a uniqueId"), c[i] && clearTimeout(c[i]), c[i] = setTimeout(e, t)
    });
    e(window).resize(function () {
        o.html('<div class="wp-spin"></div>'), d(function () {
            s()
        }, 500, "some unique string")
    })
});
