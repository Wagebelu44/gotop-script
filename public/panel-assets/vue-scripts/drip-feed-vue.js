const orderModule = new Vue({
    el: '#drip_feed_module',
    data: {
        pagination: {current_page: 1},
        driporders: [],
        filter: {
            status: "", 
            search: "",
        },
        orderStatus : '',
        order_id:  null,
        
    },
  
    watch: {
        
    },
    mounted () {
        this.getDripFeedOrders();
    },
   
    methods: {
        //
        getDripFeedOrders(page=1) {
            let page_number = this.pagination.current_page;
            let page_id = '?&page=' +page_number;
            if (page_number > 1) {
                const state = { 'page': page_number};
                const title = '';
                const url = base_url+'/admin/orders'+ page_id;
                history.pushState(state, title, url)
            }

            if (this.filter.status !== "") {
                const state = { 'status': this.filter.status};
                const title = '';
                page_id += '&status='+this.filter.status
                const url = base_url+'/admin/orders'+ page_id;
                history.pushState(state, title, url)
            }

            if (this.filter.search !== "") {
                const state = { 'search': this.filter.search};
                const title = '';
                page_id += '&status='+this.filter.search;
                const url = base_url+'/admin/orders'+ page_id;
                history.pushState(state, title, url)
            }
            fetch(base_url+'/admin/drip-feed-lists'+ page_id)
                .then(res => res.json())
                .then(res => {
                    console.log(res);
                    this.driporders = res.feed_lists.data;
                    /* this.users = res.users;
                    this.services = res.services;
                    this.order_mode_count = res.order_mode_count;
                    this.pagination = res.orders; */
                });
        },

        changeStatus(status, id)
        {
            $("#mi-modal").modal('show');
            this.orderStatus = status;
            this.order_id = id;
        },
        yes()
        {
            $('#loader-page').show();
            let statusForm = new FormData;
            statusForm.append('status',this.orderStatus);
            fetch(base_url+'/admin/drip-feed/update/'+this.order_id, {
                method: 'post',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body:statusForm
            })
                .then(res=>{
                    if (!res.ok)
                        throw res.json();
    
                    return res.json()
                })
                .then(res=>{
                    if (res.status===200)
                    {
                        this.updateDripOrderLists(res.data);
                        $("#mi-modal").modal('hide');
                        $('#loader-page').hide();
                    }
                }).catch(err=>{
                console.log(err);
            });
        },
        updateDripOrderLists(order)
        {
            this.driporders = this.driporders.map(item=>{
                if (order.id === item.id) {
                    return order;
                }
                return item;
            });
        },
        no()
        {
            $("#mi-modal").modal('hide');
        }
    }
});