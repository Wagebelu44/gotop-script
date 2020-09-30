const orderModule = new Vue({
    el: '#order_module',
    mixins: [mixin],
    data: {
        pagination: {current_page: 1},
        orders: [],
        users: [],
        loader: false,
        services: [],
        order_mode_count: null,
        order_checkbox: [],
        checkAllOrders: false,
        filter: {
            status: "", 
            search: "",
            user: "",
            service: "",
            mode: "",
            filter_type: {
                type: 'order_id',
                data: '',
            }
        },
        datalink : null,
        dataStartCount : null,
        dataRemain : null,
        dataPartical : null,

        visiblelink :false,
        visibleStartCount :false,
        visiblePartical :false,
        visibleRemain :false,

        
        orderStatus : '',
        order_id : null,
        
        editable_id: null,

        order_page: 'order',
    },
    created () {
       
    },
    watch: {
        checkAllOrders(oldval, newval)
        {
            if (oldval) {
                this.order_checkbox = this.orders.map(it=>it.id);
            }
            else this.order_checkbox = [];
        }  
    },
    mounted () {
        this.getSubscription();
    },
   
    methods: {

        getSubscription()
        {
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
            fetch(getLists + page_id)
                .then(res => res.json())
                .then(res => {
                    console.log(res, 'adfas');
                    this.loader = false;
                    this.orders = res.data;
                    this.users = res.users;
                    this.services = res.services;
                    this.order_mode_count = res.order_mode_count;
                    this.pagination = res;
                });
        },
        filterStatus(status)
        {
            this.filter.status = status;
            this.getSubscription();
        },
        filterType()
        {
            
        },
        update_service()
        { 

        },
        yes()
        {

        },
        no()
        {

        },
    }
});