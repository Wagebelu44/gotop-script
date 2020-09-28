const paymentModule = new Vue({
    el: '#payment_module',
    mixins: [mixin],
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
            txn_flag: '',
            payment_method:"",
            filter_type: {
                type: 'user',
                data: '',
            }
        },
        errors: {
            payment: [],
            common: "",

        },
        payment_edit_id: null,
        payment_edit: false,

        payment_obj: {
            user_id: null,
            amount: null,
            reseller_payment_methods_setting_id: null,
            memo: null,
        },
        api_payment_obj: null,

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
                const url = base_url+'/admin/payments'+ page_id;
                history.pushState(state, title, url)
            }

            if (this.filter.payment_method !== "") {
                const state = { 'payment_method': this.filter.payment_method};
                const title = '';
                page_id += '&payment_method='+this.filter.payment_method
                const url = base_url+'/admin/orders'+ page_id;
                history.pushState(state, title, url)
            }
            
            if (this.filter.txn_flag !== "") {
                const state = { 'txn_flag': this.filter.txn_flag};
                const title = '';
                page_id += '&txn_flag='+this.filter.txn_flag
                const url = base_url+'/admin/orders'+ page_id;
                history.pushState(state, title, url)
            }
            
            fetch(base_url+'/admin/payments-lists'+ page_id)
                .then(res =>
                    {
                        if (!res.ok) {
                            throw res.json();
                        }
                        return res.json();
                    })
                .then(res => {
                    this.loader = false;
                    this.payments = res.payments.data;
                    this.global_payments = res.globalMethods;
                    this.users = res.users;
                    setTimeout(() => {
                        $('#select2-payment-user').select2();
                        $('#select2-payment-user').val(this.users).trigger('change');
                        
                        $('#select2-redeem-user').select2();
                        $('#select2-redeem-user').val(this.users).trigger('change');                        
                    }, 100);
                })
                
        },
        savePayment(evt)
        {
            this.loader.payment = true;
            let payment_form = null;
            payment_form = new FormData(document.getElementById('payment-form'));
            var userId = document.getElementById('select2-payment-user').value;
            console.log(userId, 'asdfdas');
            payment_form.append('user_id', userId);
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
                        $('#paymentAddModal').modal('hide');
                        if (isEdit) 
                        {
                            var row = res.data;
                            this.updatePaymentLists(row);
                        } 
                        else 
                        {
                            var row = res.data;
                            this.addToPaymentList(row);
                        }


                    }, 2000);
                }
                else if (res.status === 401)
                {
                    this.loader.payment = false;
                    this.errors.services = res.data;
                }

            }).
            catch(err=>{
                this.loader = false;
                let prepare = [];
                err.then(erMesg => {
                    if ('errors' in erMesg) {
                        let errMsgs = Object.entries(erMesg.errors);
                        for (let i = 0; i < errMsgs.length; i++) {
                            let obj = {};
                            obj.name = errMsgs[i][0];
                            obj.desc = errMsgs[i][1][0];
                            prepare.push(obj);
                        }
                        this.errors.payment = prepare;
                    }
                    else if ('data' in erMesg)
                    {
                        this.errors.common = erMesg.data;
                    }
                });
            });
        },
        errorFilter(name)
        {
            let txt = '';
            if (this.errors.payment.length>0) 
            {
                this.errors.payment.forEach(item=>{
                    if (item.name === name) {
                        txt = item.desc;
                    }
                });
            }
            return txt;
        },
        addToPaymentList(payment)
        {
            this.payments.unshift(payment);
        },
        async getPayment(payment_id)
        {
          return await fetch(base_url+'/admin/payments/'+payment_id)
            .then(res=>res.json())
            .then(res=>{
                return this.api_payment_obj = res;
            });
        },
        editPayment(payment_id)
        {
            this.getPayment(payment_id).then(re=>{
                this.payment_edit = true;
                this.payment_edit_id = payment_id;
                this.payment_obj = {...re};
                $('#paymentAddModal').modal('show');
            });
         
        },
        updatePaymentLists(payment)
        {
            this.payments = this.payments.map(item=>{
                if (payment.id === item.id) {
                    return payment;
                }
                return item;
            });
        },
        filterType()
        {
            this.getPayments();
        },
        filterPaymentMethod(method_id)
        {
            this.filter.payment_method = method_id;
            this.getPayments();
        },
        searchPayment(flag)
        {
            this.filter.txn_flag = flag;
            this.getPayments();
        }

       
    }
});