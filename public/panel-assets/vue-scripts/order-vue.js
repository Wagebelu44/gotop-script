const orderModule = new Vue({
    el: '#order_module',
    mixins: [mixin],
    data: {
        pagination: {current_page: 1},
        orders: [],
        users: [],
        loader: false,
        services: [],
        orders_counts: null,
        order_mode_count: null,
        single_order: null,
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
        if (this.getParameterByName('status')) {
            this.filter.status = this.getParameterByName('status');
        }
        if (this.getParameterByName('user')) {
            this.filter.user = this.getParameterByName('user');
        }
        if (this.getParameterByName('service')) {
            this.filter.service = this.getParameterByName('service');
        }
        if (this.getParameterByName('mode')) {
            this.filter.mode = this.getParameterByName('mode');
        }
        setTimeout(()=>{
            fetch(adminSeenRoute)
            .then(res=>res.json())
            .then(res=>{
                if (res.status) {
                    getInfo();    
                    this.getOrders(1, false);    
                }
            });
        }, 15000)
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
        this.getOrders();
    },
    computed: {
        all_orders_count() {
            if (this.orders_counts !== null) {
                
                return this.orders_counts.auto_order_count + this.orders_counts.manual_order_count
            }
            return 0;
        },
        none_order_count() {
        },
        api_order_count() {
            if (this.orders_counts !== null) {
                return this.orders_counts.api_order_count
            }
            return 0;
        },
        mass_order_count() {
            if (this.orders_counts !== null) {
                return this.orders_counts.mass_order_count
            }
            return 0;
        },
        subscription_order_count() {
            if (this.orders_counts !== null) {
                return this.orders_counts.subscription_order_count
            }
            return 0;
        },
        drip_feed_order_count() {
            if (this.orders_counts !== null) {
                return this.orders_counts.drip_feed_count
            }
            return 0;
        },
    },
    methods: {
        //
        orderDetailModal(order) {
            this.single_order = order;
            $("#order_service_type_detail").modal('show')
        },
        resendOrder(order) {
            this.loader = true;
            let formD = new FormData();
            formD.append('order_id', order.id);
            fetch(resendOrderRoute, {
                method: 'post',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body:formD
            })
            .then(res=>{
                if (!res.ok)
                    throw res.json();

                return res.json()
            })
            .then(res=>{
                if (res.status===200)
                {
                    this.loader = false;
                    $("#mi-modal").modal('hide');
                    $('#loader-page').hide();
                    this.updateOrderLists(res.data);
                }
            }).catch(err=>{
                console.log(err);
            });
        },
        getOrders(page=1, needLoding = true) {
            if (needLoding) {
                this.loader = true;
            }
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

            if (this.filter.user !== "") {
                const state = { 'user': this.filter.user};
                const title = '';
                page_id += '&user='+this.filter.user;
                const url = base_url+'/admin/orders'+ page_id;
                history.pushState(state, title, url)
            }
            if (this.filter.service !== "") {
                const state = { 'service': this.filter.service};
                const title = '';
                page_id += '&service='+this.filter.service;
                const url = base_url+'/admin/orders'+ page_id;
                history.pushState(state, title, url)
            }
            if (this.filter.mode !== "") {
                const state = { 'mode': this.filter.mode};
                const title = '';
                page_id += '&mode='+this.filter.mode;
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
            fetch(base_url+'/admin/get-orders'+ page_id)
                .then(res => res.json())
                .then(res => {
                    this.loader = false;
                    this.orders = res.orders.data;
                    this.users = res.users;
                    this.services = res.services;
                    this.orders_counts = res.row_counts;
                    this.order_mode_count = res.order_mode_count;
                    this.pagination = res.orders;
                });
        },
        actionConditionalA(order)
        {
            return order.mode !=='auto' && order.status !== 'cancelled';
        },
        actionConditionalB(order)
        {
            return ['completed', 'pending', 'in progress', 'processing', 'failed', 'partial', 'cancelled'].includes(order.status);
        },
        actionConditionalC(order)
        {
            return [ 'pending', 'in progress', 'processing', 'failed', 'cancelled'].includes(order.status);
        },
        popModal(field, data, id) {
            if (field === 'link')
            {
                 this.visiblelink = true;
                 setTimeout(() => {
                     $('#link_id').find('input').val(data);
                 }, 1000);
            }
            else if (field === 'start_count')
            {
                this.visibleStartCount = true;
                $('#start_count_id').find('input').val(data);
            }
            else if (field === 'remain')
            {
                this.visibleRemain = true;
                $('#remain_id').find('input').val(data);
            }
            else if (field === 'partial')
            {
                this.visiblePartical = true;
                $('#partial_id').find('input').val(data);
            }
            this.editable_id = id;
            $('#orderEdit-modal').modal('show');
        },
        update_service ()
        {
            this.loader = true;
            let statusForm = new FormData;
            if (this.visiblelink === true)
            {
                statusForm.append('link',$('input[name=link]').val());
            }
            else if (this.visibleStartCount === true)
            {
                statusForm.append('start_counter',$('input[name=start_counter]').val());
            }
            else if (this.visiblePartical === true)
            {
                statusForm.append('partial', $('input[name=partial]').val());
            }
            else if (this.visibleRemain === true)
            {
                statusForm.append('remains', $('input[name=remains]').val());
            }

            fetch(base_url+'/admin/orders/update/'+this.editable_id, {
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
                        this.loader = false;
                        this.updateOrderLists(res.data);
                        $('#orderEdit-modal').modal('hide');
                    }
                }).catch(err=>{
                console.log(err);
            });
        },
        updateOrderLists(order)
        {
            this.orders = this.orders.map(item=>{
                if (order.id === item.id) {
                    return order;
                }
                return item;
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
            this.loader = true;
            let statusForm = new FormData;
            statusForm.append('status', this.orderStatus);
            fetch(base_url+'/admin/orders/update/'+this.order_id, {
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
                    this.loader = false;
                    $("#mi-modal").modal('hide');
                    $('#loader-page').hide();
                    this.updateOrderLists(res.data);
                    //window.location.reload();

                }
            }).catch(err=>{
                console.log(err);
            });
        },
        no()
        {
            $("#mi-modal").modal('hide');
        },
        modalVIsible(type, obj){

            $("#order_service_type_detail").modal('show');
            let d = '';
            if (type  === 'text_area_1') {
                d = obj.text_area_1;
            }
            else if (type  === 'additional_comment_owner_username_visible') {
                d = obj.additional_inputs;
            }
            else if (type  === 'text_area_2') {
                d = obj.text_area_2;
            }
            $('#order-modal-detail').html(d);
        },
        bulkStatusChange(status) {
            $('#loader-page').show();
            if (this.order_checkbox.length !== 0) {
                let forD = new FormData();
                forD.append('service_ids', this.order_checkbox);
                forD.append('status', status);
                fetch(base_url+'/admin/orders/update/status', {
                    headers: {
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": CSRF_TOKEN,
                    },
                    credentials: "same-origin",
                    method: "POST",
                    body: forD,
                }).then(res => res.json())
                    .then(res => {
                        if (res.status === 200) {
                            setTimeout(() => {
                                $('#loader-page').hide();
                                toastr["success"](res.message);
                                window.location.reload();
                            }, 2000);
                        }

                        console.log(res);
                    })
            } else {
                alert('No check box is selected');
            }
        },
        filterStatus(status)
        {
            this.filter.status = status;
            this.getOrders();
        },
        statusUser(user_id)
        {
            this.filter.user = user_id;
            this.getOrders();
        },
        statusService(service)
        {
            this.filter.service = service;
            this.getOrders();
        },
        statusMode(mode)
        {
            this.filter.mode = mode;
            this.getOrders();
        },
        filterType()
        {
            this.getOrders();
        }

        
       
    }
});