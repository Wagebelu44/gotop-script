const userModule = new Vue({
    el: '#order_module',
    data: {
        pagination: {current_page: 1},
        orders: [],
        service_checkbox: [],
        filter: {
            status: "", 
            search: "",
        },
    },
    created () {
       
    },
    watch: {
        
    },
    mounted () {
        this.getOrders();
        console.log(this.orders, 'lsits');
    },
   
    methods: {
        
        //
        getOrders(page=1) {
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
            fetch(base_url+'/admin/get-orders'+ page_id)
                .then(res => res.json())
                .then(res => {
                    this.orders = res.data.data;
                    this.pagination = res.data;
                });
        },
        bulkSelect()
        {

        }
        
       
    }
});