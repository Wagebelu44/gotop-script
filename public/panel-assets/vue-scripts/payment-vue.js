const orderModule = new Vue({
    el: '#payment_module',
    data: {
        pagination: {current_page: 1},
        payments: [],
        global_payments: [],
        users: [],
        services: [],
        order_mode_count: null,
        order_checkbox: [],
        checkAllOrders: false,
        filter: {
            status: "", 
            search: "",
        },

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
        this.getPayments();
    },
   
    methods: {
        //
        getPayments(page=1) {
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
            fetch(base_url+'/admin/payments-lists'+ page_id)
                .then(res => res.json())
                .then(res => {
                    console.log(res);
                    this.payments = res.payments.data;
                    this.global_payments = res.globalMethods;
                    this.users = res.users;
                });
        },
       
    }
});