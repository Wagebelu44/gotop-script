document.getElementById('card-header-order').addEventListener('click', function(evt){
    let ab = evt.target.getAttribute('data-id');
    let agclass = document.getElementsByClassName('tab-bar');
    for (let index = 0; index < agclass.length; index++) {
        agclass[index].classList.remove('order_active');
    }
    evt.target.classList.add('order_active');
    if (ab === 'new-order') 
    {
        document.getElementById('new_order').style = 'display: block';
        document.getElementById('mass_order').style = 'display: none';
    }
    else if (ab === 'mass-order') 
    {
        document.getElementById('mass_order').style = 'display: block';
        document.getElementById('new_order').style = 'display: none';
    }
});


document.getElementById('card-header-id').addEventListener('click', function(evt){
    let ab = evt.target.getAttribute('data-id');
    let agclass = document.getElementsByClassName('tab-bar');
    for (let index = 0; index < agclass.length; index++) {
        agclass[index].classList.remove('news-tab-active');
    }
    evt.target.classList.add('news-tab-active');
    if (ab === 'latestNews') 
    {
        document.getElementById('latest-news').style = 'display: block';
        document.getElementById('general-news').style = 'display: none';
    }
    else if (ab === 'genralnews') 
    {
        document.getElementById('general-news').style = 'display: block';
        document.getElementById('latest-news').style = 'display: none';
    }
});