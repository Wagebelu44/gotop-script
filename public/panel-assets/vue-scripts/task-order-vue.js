const orderModule = new Vue({
    el: '#task_order_module',
    data: {
        pagination: {current_page: 1},
        orders: [],
        users: [],
        services: [],
        order_mode_count: null,
        order_checkbox: [],
        checkAllOrders: false,
        filter: {
            status: "", 
            search: "",
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
        
        order_page: 'task',
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
            fetch(base_url+'/admin/tasks-lists'+ page_id)
                .then(res => res.json())
                .then(res => {
                    console.log(res);
                    this.orders = res.orders.data;
                    this.users = res.users;
                    this.services = res.services;
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
                $('#link_id').show();
                $('#start_count_id').hide();
                $('#remain_id').hide();
                 this.visiblelink = true;
                 this.visibleStartCount = false;
                 this.visiblePartical = false;
                 this.visibleRemain = false;
                $('#link_id').find('input').val(data);
            }
            else if (field === 'start_count')
            {
                $('#link_id').hide();
                $('#start_count_id').show();
                $('#remain_id').hide();
                this.visiblelink = false;
                this.visibleStartCount = true;
                this.visiblePartical = false;
                this.visibleRemain = false;
                $('#start_count_id').find('input').val(data);
            }
            else if (field === 'remain')
            {
                $('#link_id').hide();
                $('#start_count_id').hide();
                $('#partial_id').hide();
                $('#remain_id').show();
                this.visiblelink = false;
                this.visibleStartCount = false;
                this.visiblePartical = false;
                this.visibleRemain = true;
                $('#remain_id').find('input').val(data);
            }
            else if (field === 'partial')
            {
                $('#link_id').hide();
                $('#start_count_id').hide();
                $('#remain_id').hide();
                $('#partial_id').show();
                this.visiblelink = false;
                this.visibleStartCount = false;
                this.visiblePartical = true;
                this.visibleRemain = false;
                $('#partial_id').find('input').val(data);
            }
            this.editable_id = id;
            $('#orderEdit-modal').modal('show');
        },
        update_service ()
        {
            $('#loader-page').show();
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
                    if(!res.ok)
                        throw res.json();

                    return res.json()
                })
                .then(res=>{
                    if (res.status===200)
                    {
                        $("#mi-modal").modal('hide');
                        $('#loader-page').hide();
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
            $('#loader-page').show();
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
                if(!res.ok)
                    throw res.json();

                return res.json()
            })
            .then(res=>{
                if (res.status===200)
                {
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

        
       
    }
});