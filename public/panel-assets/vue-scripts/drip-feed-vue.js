const orderModule = new Vue({
    el: '#drip_feed_module',
    data: {
        pagination: {current_page: 1},
        driporders: [],
        filter: {
            status: "", 
            search: "", 
            filter_type: {
                type: 'order_id',
                data: '',
            }
        },
        orderStatus : '',
        order_id:  null,
        loader: false,
    },
  
    watch: {
        
    },
    mounted () {
        this.getDripFeedOrders();
    },
   
    methods: {
        //
        getDripFeedOrders(page=1) {
            this.loader = true;
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

            if (this.filter.filter_type.data!=='') {
                const state = { 'filter_type': this.filter.filter_type.type, 'data': this.filter.filter_type.data};
                const title = '';
                page_id += '&filter_type='+this.filter.filter_type.type+'&data='+this.filter.filter_type.data;
                const url = base_url+'/admin/orders'+ page_id;
                history.pushState(state, title, url)
            }
            fetch(base_url+'/admin/drip-feed-lists'+ page_id)
                .then(res => res.json())
                .then(res => {
                    this.loader = false;
                    this.driporders = res.feed_lists.data;
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
        },
        filterStatus(status)
        {
            this.filter.status = status;
            this.getDripFeedOrders();
        },
        filterType()
        {
            this.getDripFeedOrders();
        }
    }
});