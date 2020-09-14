const orderModule = new Vue({
    el: '#payment_module',
    data: {
        pagination: {current_page: 1},
        payments: [],
        global_payments: [],
        users: [],
        services: [],
        loader: {
            payment: false,
        },
        order_mode_count: null,
        order_checkbox: [],
        checkAllOrders: false,
        filter: {
            status: "", 
            search: "",
        },
        payment_edit_id: null,
        payment_edit: false,

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
        savePayment(evt)
        {
            this.loader.payment = true;
            let payment_form = null;
            payment_form = new FormData(document.getElementById('payment-form'));
            if (this.payment_edit_id) {
                payment_form.append('edit_id', this.payment_edit_id);
                payment_form.append('edit_mode', true);
            }
            fetch(base_url+'/admin/payments', {
                headers: {
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": CSRF_TOKEN,
                },
                credentials: "same-origin",
                method: "POST",
                body: payment_form
            })
            .then(res => {
                if (!res.ok) {
                    throw res.json();
                }
                return res.json();
            })
            .then(res => {
                if (res.status === 200) {
                    var isEdit = this.payment_edit;
                    this.payment_edit = false;
                    this.payment_edit_id = null;
                    setTimeout(() => {
                        this.loader.payment = false;
                        toastr["success"](res.message);
                        document.getElementById('payment-form').reset();
                        $('#paymentAddModal').modal('hide');

                        if (isEdit) 
                        {
                            var row = res.data;
                            this.updateServiceLists(row);
                            var status = (row.status == 'active')?'Enabled':'Disabled';
                        } 
                        else 
                        {
                            var row = res.data;
                            this.addToPaymentList(row);
                        }


                    }, 2000);
                }
                else if(res.status === 401)
                {
                    this.loader.payment = false;
                    this.errors.services = res.data;
                }

            })
            .catch(err => 
            {
                setTimeout(() => {
                    this.loader.service = false;
                    let prepare = [];
                    err.then(erMesg => {
                        let errMsgs = Object.entries(erMesg.errors);
                        for (let i = 0; i < errMsgs.length; i++) {
                            let obj = {};
                            obj.name = errMsgs[i][0];
                            obj.desc = errMsgs[i][1][0];
                            prepare.push(obj);
                        }
                        this.errors.services = prepare;
                    });
                }, 2000);
            });
        },
        addToPaymentList(payment)
        {
            this.payments.unshift(payment);
        }
       
    }
});