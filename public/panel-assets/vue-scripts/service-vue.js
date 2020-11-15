const capitalize = (s) => {
    if (typeof s !== 'string') return ''
    return s.charAt(0).toUpperCase() + s.slice(1)
}
Vue.component('v-select', VueSelect.VueSelect);
const ServiceApp = new Vue({
    el: '#serviceApp',
    data: {
        options: [ {country: 'atik', code: 1}, {country: 'sudip', code: 2},],
        selected_services: [],
        is_nextLevel: false,
        fixedRaisei: 0,
        percentRaisei: 0,
        providers_lists: [],
        service_filter: {
            service_type: '',
            status: '',
        },
        errors: {
            common: null,
            category: [],
            services: [],
        },
        success: {
            category: '',
        },
        loader: false,
        service_edit: false,
        service_edit_id: null,
        category_edit: false,
        category_edit_id: null,
        auto_per_rate_toggler: true,
        auto_min_rate_toggler: true,
        auto_max_rate_toggler: true,
        auto_price_plus: null,
        auto_price_percent: null,
        provider_sync_status: true,
        services: {
            visibility: {
                drip_feed: false,
                re_fill: false,
                service_id_by_provider: false,
                provider: false,
                service_type: false,
                overflow: false,
                auto_per_rate: false,
            },
            disable: {
                min: false,
                max: false,
            },
            form_fields: {
                name: '',
                score: 0,
                category_id: null,
                number: 0,
                mode: null,
                provider_id: null,
                provider_service_id: null,
                service_type: null,
                drip_feed_status: null,
                refill_status: null,
                short_name: '',
                price: null,
                price_original: null,
                min_quantity: null,
                max_quantity: null,
                auto_min_quantity: null,
                auto_max_quantity: null,
                link_duplicates: null,
                increment: null,
                auto_overflow: null,
                subscription_type: null,
                service_average_time: null,
            },
            validations: {
                price :{
                    visibility: false,
                    msg: ''
                },
                minQuantity :{
                    visibility: false,
                    msg: ''
                },
                maxQuantity :{
                    visibility: false,
                    msg: ''
                },
                provider_service_not_found: '',
            }

        },
        link_duplicate_selected: 'Allow',
        service_mode: 'Auto',
        service_type: null,
        autoManualCount: null,
        service_type_selected: 'Default',
        category: {
            name: null,
            status: null,
        },
        category_services: null,
        service_checkbox: [],
        provider_services: [],
        provider_service_selected: null,
        provider_id: null,
        subscription_modal: false,
        service_modal: false,
        selected_provider: null,
    },
    computed: {
        provider_services_computed()
        {
            if (this.provider_services===null) return null;
            return this.provider_services.filter(item=>item.type !=='Subscriptions').map(item=>{
                     return {
                        id: item.service,
                        name: item.name,
                        display_name: item.service+" - "+item.name,
                    }
            });
        },
        provider_subscription_computed()
        {
            if (this.provider_services===null) return null;
            return this.provider_services.filter(item=>item.type==='Subscriptions').map(item=>{
                     return {
                        id: item.service,
                        name: item.name,
                        display_name: item.service+" - "+item.name,
                    }
            });
        },
        categories()
        {
            let p_categories = [];
            if (this.provider_services.length>0) {
                this.provider_services.forEach((item, index)=>{
                    let flag  = false;
                    p_categories.forEach((it, ind) => {
                        if (it.category) {
                            if (item.category == it.category) {
                                flag = true;
                            }
                        }
                    });

                    if (p_categories.length==0) {
                        let cobj = {};
                        cobj.category = item.category;
                        cobj.services = [];
                        cobj.cate_id = index;
                        cobj.services.push(item);
                        p_categories.push(cobj); 
                    }
                    else
                    {
                        if (flag) {
                            p_categories.forEach((it, ind) => {
                                if (it.category) {
                                    if (item.category == it.category) {
                                        it.services.push(item);
                                    }
                                }
                            }); 
                        }
                        else
                        {
                            let cobj = {};
                            cobj.category = item.category;
                            cobj.services = [];
                            cobj.cate_id = index;
                            cobj.services.push(item);
                            p_categories.push(cobj); 
                        }
                    }

                });
            }
            return p_categories;
        },
        selectedCategories()
        {
            let p_categories = [];
            if (this.selected_services.length>0) {
                this.selected_services.forEach((item, index)=>{
                    let flag  = false;
                    p_categories.forEach((it, ind) => {
                        if (it.category) {
                            if (item.category == it.category) {
                                flag = true;
                            }
                        }
                    });

                    if (p_categories.length==0) {
                        let cobj = {};
                        cobj.category = item.category;
                        cobj.services = [];
                        cobj.cate_id = index;
                        cobj.services.push(item);
                        p_categories.push(cobj); 
                    }
                    else
                    {
                        if (flag) {
                            p_categories.forEach((it, ind) => {
                                if (it.category) {
                                    if (item.category == it.category) {
                                        it.services.push(item);
                                    }
                                }
                            }); 
                        }
                        else
                        {
                            let cobj = {};
                            cobj.category = item.category;
                            cobj.services = [];
                            cobj.cate_id = index;
                            cobj.services.push(item);
                            p_categories.push(cobj); 
                        }
                    }

                });
            }
            return p_categories;
        },
    },
    watch: {
        service_mode(newval, oldval) {
            this.manipulateInputs();
        },
        service_type_selected(newval, oldval) {
            this.manipulateInputs();
        },
        'services.form_fields.price': {
            handler: function(oldval,newval)
            {
                if (isNaN(oldval)) {
                    this.services.validations.price.visibility = true;
                    this.services.validations.price.msg = 'Please, Input Numbers Only';
                }
                else
                {
                    this.services.validations.price.visibility = false;
                    this.services.validations.price.msg = ' ';
                }
            },
            deep: true,
        },
        'services.form_fields.min_quantity': {
            handler: function(oldval,newval)
            {
                if (isNaN(oldval)) {
                    this.services.validations.minQuantity.visibility = true;
                    this.services.validations.minQuantity.msg = 'Please, Input Numbers Only';
                }
                else
                {
                    this.services.validations.minQuantity.visibility = false;
                    this.services.validations.minQuantity.msg = ' ';
                }
            },
            deep: true,
        },
        'services.form_fields.max_quantity': {
            handler: function(oldval,newval)
            {
                if (isNaN(oldval)) {
                    this.services.validations.maxQuantity.visibility = true;
                    this.services.validations.maxQuantity.msg = 'Please, Input Numbers Only';
                }
                else
                {
                    this.services.validations.maxQuantity.visibility = false;
                    this.services.validations.maxQuantity.msg = ' ';
                }
            },
            deep: true,
        },
        'services.form_fields.provider_id': {
            handler: function(oldval,newval)
            {
                if (oldval!==null) {
                    
                    this.getProviderServices(oldval);

                }
            },
            deep: true,
        },
        'services.form_fields.provider_service_id': {
            
            handler: function(oldval,newval)
            {
                if (oldval!==null) {
                    
                    this.changeSelected();
                }
            },
            deep: true,
        },
        auto_per_rate_toggler(newval, oldval)
        {
            this.services.visibility.auto_per_rate = newval;
        },
        auto_min_rate_toggler(newval, oldval){
            if (newval===true) {
                this.services.form_fields.min_quantity = this.services.form_fields.auto_min_quantity;
            }
        },
        auto_max_rate_toggler(newval, oldval){
            if (newval===true) {
                this.services.form_fields.max_quantity = this.services.form_fields.auto_max_quantity;
            }
        },
        auto_price_plus(newval, oldval){
            if (newval !== null) {
                // price +(percent*price/100)+fixed
                this.services.form_fields.price = parseFloat(Number(this.services.form_fields.price_original)  +  Number(newval) + (Number(this.services.form_fields.price_original) * Number(this.auto_price_percent) / 100 ));
            }
        },
        auto_price_percent(newval, oldval){
            if (newval !== null) {
                //this.services.form_fields.price = Number(this.services.form_fields.price)  +  (Number(newval)/100) * Number(this.services.form_fields.price);
                this.services.form_fields.price = parseFloat(Number(this.services.form_fields.price_original)  +  Number(this.auto_price_plus) + (Number(this.services.form_fields.price_original) * Number(newval) / 100 ));
            }
        },
    },
    created() {
        this.manipulateInputs();
        this.service_edit = false;
        if (this.service_edit === false) {
            this.services.form_fields.name = '';
            this.services.form_fields.score = 0;
            this.services.form_fields.category_id = '';
            this.services.form_fields.number = 0;
            this.services.form_fields.mode = this.service_mode;
            this.services.form_fields.provider_id = '';
            this.services.form_fields.provider_service_id = null;
            this.services.form_fields.service_type = null;
            this.services.form_fields.drip_feed_status = 'Allow';
            this.services.form_fields.refill_status = 'Allow';
            this.services.form_fields.short_name = '';
            this.services.form_fields.price = null;
            this.services.form_fields.min_quantity = null;
            this.services.form_fields.max_quantity = null;
            this.services.form_fields.link_duplicates = this.link_duplicate_selected;
            this.services.form_fields.increment = null;
            this.services.form_fields.auto_overflow = null;
            this.services.form_fields.description = '';
            this.services.form_fields.subscription_type = null;
        }
        this.services.visibility.auto_per_rate = this.auto_per_rate_toggler;

        // filter reload
        if (this.getParameterByName('service_type')) {
            this.service_filter.service_type = this.getParameterByName('service_type');
        }

        if (this.getParameterByName('status')) {
            this.service_filter.status = this.getParameterByName('status');
        }
    },
    mounted () {
        this.getCategoryServices();
        this.loadProviders();
    },
    methods: {
        lockSeviceRate(service) {
            if (this.selected_services.length>0) {
                this.selected_services = this.selected_services.map(item => {
                    if (item.service == service.service) {
                        item.custome_rate_visible = !item.custome_rate_visible;
                    }
                    return item;
                });
            }
        },
        calculateRaise(evt) {
            if (this.selected_services.length>0) {
                this.selected_services = this.selected_services.map(item => {
                    if (item.custome_rate_visible) {
                        item.custome_rate = (Number(item.rate) + Number(this.fixedRaisei)) + (( (Number(item.rate) + Number(this.fixedRaisei)) * this.percentRaisei ) / 100);
                    }
                    return item;
                });
            }
        },
        resetRaise() {
            if (this.selected_services.length>0) {
                this.selected_services = this.selected_services.map(item => {
                    item.custome_rate = item.rate;
                    item.custome_rate_visible = true;
                    return item;
                });
            }
            this.fixedRaisei = 0;
            this.percentRaisei = 0;
        },
        getParameterByName(name, url) {
            if (!url) url = window.location.href;
            name = name.replace(/[\[\]]/g, '\\$&');
            var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
                results = regex.exec(url);
            if (!results) return null;
            if (!results[2]) return '';
            return decodeURIComponent(results[2].replace(/\+/g, ' '));
        },
        loadProviders()
        {
            fetch(base_url+'/admin/service_provider')
            .then(res=>res.json())
            .then(res=>{
                this.providers_lists = res;
            });
        },
        serviceTypeFilter(name)
        {
            this.service_filter.service_type = name;
            this.getCategoryServices();
        },
        serviceStatusFilter(name)
        {
            this.service_filter.status = name;
            this.getCategoryServices();
        },
        getCategoryServices()
        {
            this.loader = true;
            let url = base_url+"/admin/get-category-services?";
            let top_Url = base_url+'/admin/services?';
            if (this.service_filter.service_type!=='') {
                url +='&service_type='+this.service_filter.service_type;
                top_Url +='&service_type='+this.service_filter.service_type;
                const state = { 'service_type': this.service_filter.service_type};
                const title = '';
                history.pushState(state, title, top_Url)
            }
            if (this.service_filter.status!=='') {
                url +='&status='+this.service_filter.status;
                top_Url +='&status='+this.service_filter.status;
                const state = { 'status': this.service_filter.status};
                const title = '';
                history.pushState(state, title, top_Url)
            }
            fetch(url)
            .then(res=>res.json())
            .then(res=>{
                this.loader = false;
                this.category_services = res.data;
                this.service_type = res.service_type_count;
                this.autoManualCount = res.autoManualCount;
            });
        },
        submitCategoryForm(evt) {
            this.loader = true;
            let categoryForm = new FormData(document.getElementById('category_form'));
            if (this.category_edit) {
                categoryForm.append('edit_id', this.category_edit_id);
                categoryForm.append('edit_mode', true);
            }
            fetch(base_url+'/admin/category-store', 
            {
                headers: 
                {
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": CSRF_TOKEN,
                },
                credentials: "same-origin",
                method: "POST",
                body: categoryForm
            })
            .then(res => {
                if (!res.ok) {
                    throw res.json();
                }
                return res.json();
            })
            .then(res => {
                if (res.status === 200) {
                    if (this.category_edit === true)
                    {
                        this.updateCategoryLists(this.category_edit_id, res.data);
                    }
                    else
                    {
                        this.category_services.push(res.data);
                    }
                    this.category_edit = false;
                    this.category_edit_id = null;
                    this.category = {...res.data};
                    setTimeout(() => {
                        this.loader = false;
                        toastr["success"](res.message);
                        document.getElementById('category_form').reset();
                        $('#exampleModalCenter').modal('hide');
                    }, 2000);
                }

            })
            .catch(error => {
                this.loader = false;
                error.then(erMesg => {
                    this.errors.category = erMesg.errors;
                });
            });
            // .catch(err => {
            //     setTimeout(() => {
            //         this.loader.category = false;
            //         let prepare = [];
            //         err.then(erMesg => {
            //             let errMsgs = Object.entries(erMesg.errors);    
            //             for (let i = 0; i < errMsgs.length; i++) {
            //                 let obj = {};
            //                 obj.name = errMsgs[i][0];
            //                 obj.desc = errMsgs[i][1][0];
            //                 prepare.push(obj);
            //             }
            //             this.errors.category = [...prepare];
            //         });
            //     }, 2000);
               
            // });
        },
        updateCategoryStatus(id)
        {
            this.loader = true;
            fetch(base_url+'/admin/category-status-change/'+id, 
            {
                headers: 
                {
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": CSRF_TOKEN,
                },
                credentials: "same-origin",
                method: "POST",
                body: {}
            })
            .then(res => {
                if (!res.ok) {
                    throw res.json();
                }
                return res.json();
            })
            .then(res => {
                if (res.status === 200) {
                    this.loader = false;
                    this.category = {...res.data};
                    this.updateCategoryLists(id, res.data);
                }
            }).catch(err => {
                setTimeout(() => {
                    this.loader = false;
                    let prepare = [];
                    err.then(erMesg => {
                        let errMsgs = Object.entries(erMesg.errors);
                        for (let i = 0; i < errMsgs.length; i++) {
                            let obj = {};
                            obj.name = errMsgs[i][0];
                            obj.desc = errMsgs[i][1][0];
                            prepare.push(obj);
                        }
                        this.errors.category = prepare;
                    });
                }, 2000);
            });
        },
        manipulateInputs() {
            let mode = capitalize(this.service_mode);
            if (mode === 'Auto') {
                this.services.visibility.provider = true;
                this.services.visibility.drip_feed = false;
                this.services.visibility.re_fill = false;
                this.services.visibility.service_type = false;
                this.services.visibility.overflow = true;
            } else {
                this.services.visibility.provider = false;
                this.services.visibility.drip_feed = true;
                this.services.visibility.re_fill = true;
                this.services.visibility.service_type = true;
                this.services.visibility.overflow = false;
            }

            if ((mode === 'Manual' && this.service_type_selected === 'Default')
                || (mode === 'Manual' && this.service_type_selected === 'Invites From Groups')
            )
            {
                this.services.visibility.drip_feed = true;
                this.services.visibility.re_fill = true;
            } else if (mode === 'Manual' && this.service_type_selected === 'Comment Likes') {
                this.services.visibility.drip_feed = false;
                this.services.visibility.re_fill = false;
            } else {
                this.services.visibility.re_fill = false;
                this.services.visibility.drip_feed = false;
            }

            if ((mode === 'Manual' && this.service_type_selected === 'Custom Comments Package') || (mode === 'Manual' && this.service_type_selected === 'Package')) {
                this.services.disable.min = true;
                this.services.disable.max = true;
                this.services.form_fields.min_quantity = 1;
                this.services.form_fields.max_quantity = 1;
            } 
            else 
            {
                this.services.disable.min = false;
                this.services.disable.max = false;
            }

        },
        submitServiceForm(evt) 
        {
            this.loader = true;
            evt.preventDefault();
            let service_form = null;
            if (this.subscription_modal) {
                 service_form = new FormData(document.getElementById('subscription_form'));
            }
            else
            {
                 service_form = new FormData(document.getElementById('service_form'));
            }
            if (this.service_edit) {
                service_form.append('edit_id', this.service_edit_id);
                service_form.append('edit_mode', true);
            }
            fetch(base_url+'/admin/services', {
                headers: {
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": CSRF_TOKEN,
                },
                credentials: "same-origin",
                method: "POST",
                body: service_form
            })
            .then(res => {
                if (!res.ok) {
                    throw res.json();
                }
                return res.json();
            })
            .then(res => {
                if (res.status === 200) {
                    var isEdit = this.service_edit;
                    this.service_edit = false;
                    this.service_edit_id = null;
                    setTimeout(() => {
                        this.loader = false;
                        toastr["success"](res.message);
                        document.getElementById('service_form').reset();
                        if (res.data.service_type =='Subscriptions') {
                            $('#subscriptionModal').modal('hide');
                        }
                        else
                        {
                            this.service_modal = false;
                            $('#serviceAddModal').modal('hide');
                        }
                        
                        if (isEdit) 
                        {
                            var row = res.data;
                            this.updateServiceLists(row);
                            var status = (row.status == 'active')?'Enabled':'Disabled';
                        } 
                        else 
                        {
                            var row = res.data;
                            this.addnewServicetoLists(row);
                        }


                    }, 2000);
                }
                else if (res.status === 401)
                {
                    this.loader = false;
                    this.errors.services = res.data;
                }

            })
            .catch(err => 
            {
              
                setTimeout(() => {
                    this.loader = false;
                    let prepare = [];
                    err.then(erMesg => {
                        console.log(erMesg, 'error');
                        if ('errors' in erMesg) {
                            let errMsgs = Object.entries(erMesg.errors);
                            for (let i = 0; i < errMsgs.length; i++) {
                                let obj = {};
                                obj.name = errMsgs[i][0];
                                obj.desc = errMsgs[i][1][0];
                                prepare.push(obj);
                            }
                            this.errors.services = prepare;
                        }
                        else if ('data' in erMesg)
                        {
                            this.errors.common = erMesg.data;
                        }
                    });
                }, 2000);
            });
          
        },
        editHelper() {
            this.service_mode = capitalize(this.services.form_fields.mode);
            this.services.form_fields.drip_feed_status = capitalize(this.services.form_fields.drip_feed_status);
            this.services.form_fields.refill_status = capitalize(this.services.form_fields.refill_status);
            this.link_duplicate_selected = capitalize(this.services.form_fields.link_duplicates);
        },
        subscriptionEdit(service_id) {
            this.loader = true;
            this.service_edit_id = service_id;
            fetch('showService/' + service_id).then(res => res.json())
                .then(res => {
                    this.loader = true;
                    this.service_edit = true;
                    $('#subscriptionModal').modal('show');
                    this.services.form_fields = {...res.data};
                    this.service_mode = this.services.form_fields.mode;
                    this.service_type_selected = this.services.form_fields.service_type;
                    this.manipulateInputs();
                    this.editHelper();
                })
        },
        serviceEdit(service_id) {
            this.loader = true;
            this.service_edit_id = service_id;
            fetch(base_url+'/admin/services/' + service_id).then(res => res.json())
                .then(res => {
                    this.loader = false;
                    this.service_edit = true;
                    this.services.form_fields = {...res};
                    this.service_mode = this.services.form_fields.mode;
                    this.service_type_selected = this.services.form_fields.service_type;
                    if (this.service_type_selected === 'Subscriptions') {
                        $('#subscriptionModal').modal('show');
                    } else {
                        $('#serviceAddModal').modal('show');
                    }
                    this.manipulateInputs();
                    this.editHelper();
                    this.loader = false;
                })
        },
        serviceDescription(service_id) {
            this.loader = true;
            fetch(base_url+'/admin/services/' +  service_id).then(res => res.json())
                .then(res => {
                    console.log(res);
                    this.loader = false;
                    $('#serviceDescription').modal('show');
                    this.services.form_fields.description = res.description;
                    $("#serviceDescription_edit").summernote('code', res.description, {
                            placeholder: 'Hello stand alone ui',
                            tabsize: 2,
                            height: 300,
                            toolbar: [
                                ['style', ['style']],
                                ['font', ['bold', 'underline', 'clear']],
                                ['color', ['color']],
                                ['para', ['ul', 'ol', 'paragraph']],
                                ['table', ['table']],
                                ['view', ['fullscreen', 'codeview', 'help']],
                                
                            ], 
                        });
                    this.service_edit_id = service_id;
                })
        },
        updateServiceDescription(evt) {
            evt.preventDefault();
            this.loader = true;
            evt.preventDefault();
            let service_form = new FormData(document.getElementById('formDescription'));
            fetch('updateService/' + this.service_edit_id, {
                headers: {
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": CSRF_TOKEN,
                },
                credentials: "same-origin",
                method: "POST",
                body: service_form
            })
            .then(res => {
                if (!res.ok) {
                    throw res.json();
                }
                return res.json();
            })
            .then(res => {
                console.log(res, 'updated description');
                if (res.status === 200) {
                    this.service_edit = false;
                    setTimeout(() => {
                        this.loader = false;
                        toastr["success"](res.message);
                        document.getElementById('formDescription').reset();
                        $('#serviceDescription').modal('hide');
                    }, 2000);
                }

            })
            .catch(err => {
                console.log(err);
                setTimeout(() => {
                    this.loader = false;
                    let prepare = [];
                    err.then(erMesg => {
                        let errMsgs = Object.entries(erMesg.errors);
                        for (let i = 0; i < errMsgs.length; i++) {
                            let obj = {};
                            obj.name = errMsgs[i][0];
                            obj.desc = errMsgs[i][1][0];
                            prepare.push(obj);
                        }
                        this.errors.category = prepare;
                    });
                }, 2000);
            });
        },
        serviceEnableDisable(service_id) {
            this.loader = true;
            fetch(base_url+'/admin/enableService/' + service_id).then(res => res.json())
                .then(res => {
                    this.loader = false;
                    toastr["success"](res.message);
                    if (res.data) {
                        let row  = res.data;
                        this.updateServiceLists(row);
                    }          
                })
        },
        serviceResetRate(service_id) {
            this.loader = true;
            fetch('resetCustomRate/' + service_id).then(res => res.json())
                .then(res => {
                    this.loader = false;
                    toastr["success"](res.message);
                })
        },
        serviceDelete(service_id) {
            if (confirm('Are you sure?')) {
                this.loader = true;
                fetch(base_url+'/admin/deleteService/' + service_id, {
                    headers: 
                    {
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": CSRF_TOKEN,
                    },
                    credentials: "same-origin",
                    method: "DELETE",
                    body: {} 
                }).then(res => res.json())
                    .then(res => {
                        this.loader = false;
                        toastr["success"](res.message);
                        if (res.status === 200) {
                            let row = res.data;
                            this.deleteService(row);
                        }
                    })
            }
        },
        serviceDuplicate(service_id, catStatus) {
            this.loader = true;
            fetch(base_url+'/admin/duplicate/service/' + service_id).then(res => res.json())
                .then(res => {
                    this.loader = false;
                    toastr["success"](res.message);
                    var row = res.data;
                    this.addnewServicetoLists(row);
                })
        },
        categoryEdit(category_id) {
            this.loader = true;
            this.category_edit = true;
            this.category_edit_id = category_id;
            fetch(base_url+'/admin/show-category/' + category_id).then(res => res.json())
                .then(res => {
                    this.loader = false;
                    this.category = {...res};
                    $('#exampleModalCenter').modal('show');
                });

        },
        bulkEnable() {
            this.loader = true;
            if (this.service_checkbox.length !== 0) {
                //console.log(this.service_checkbox);
                let forD = new FormData();
                forD.append('service_ids', this.service_checkbox);
                fetch(base_url+'/admin/service_bulk_enable', {
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
                                this.loader = false;
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
        bulkDisable() {
            this.loader = true;
            if (this.service_checkbox.length !== 0) {
                //console.log(this.service_checkbox);
                let forD = new FormData();
                forD.append('service_ids', this.service_checkbox);
                fetch(base_url+'/admin/service_bulk_disable', {
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
                                this.loader = false;
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
        resetCustomRates() {
            this.loader = true;
            if (this.service_checkbox.length !== 0) {
                //console.log(this.service_checkbox);
                let forD = new FormData();
                forD.append('service_ids', this.service_checkbox);
                fetch(resetCustomRatesRoute, {
                    headers: {
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    },
                    credentials: "same-origin",
                    method: "POST",
                    body: forD,
                }).then(res => res.json())
                    .then(res => {

                        if (res.status === 200) {
                            setTimeout(() => {
                                this.loader = false;
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
        bulkDelete() {
            if (confirm('Are you sure?')) {
                this.loader = true;
                if (this.service_checkbox.length !== 0) {
                    //console.log(this.service_checkbox);
                    let forD = new FormData();
                    forD.append('service_ids', this.service_checkbox);
                    fetch(base_url+'/admin/service_bulk_delete', {
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
                                    this.loader = false;
                                    toastr["success"](res.message);
                                    window.location.reload();
                                }, 2000);
                            }

                            console.log(res);
                        })
                } else {
                    alert('No check box is selected');
                }
            }

        },
        service_bulk_category(evt) {
            evt.preventDefault();
            this.loader = true;
            if (this.service_checkbox.length !== 0) {
                //console.log(this.service_checkbox);
                let forD = new FormData(document.getElementById('formBulkCategory'));
                forD.append('service_ids', this.service_checkbox);
                fetch('service_bulk_category', {
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
                                this.loader = false;
                                toastr["success"](res.message);
                                $("#serviceDescription").modal('hide');
                                window.location.reload();
                            }, 2000);
                        }

                        console.log(res);
                    })
            } else {
                alert('No check box is selected');
            }
        },
        getProviderServices() {
            let forD = new FormData();
            this.loader = true;
            if (this.services.form_fields.provider_id !== null && this.services.form_fields.provider_id !== '') {
                forD.append('provider_id', this.services.form_fields.provider_id);
            }
            else if(this.provider_id !== null)
            {
                if (!this.provider_id) {
                    return false;
                }
                forD.append('provider_id', this.provider_id);
            }

            this.providers_lists.forEach(item => {
                if (item.id == this.provider_id) {
                    this.selected_provider = item;
                }
            });

            fetch(base_url+'/admin/provider/get/services', {
                headers: {
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": CSRF_TOKEN,
                },
                credentials: "same-origin",
                method: "POST",
                body:forD,
            })
            .then(res => {
                if (!res.ok) {
                    throw res;
                }
                return res.json();
            })
            .then(res => {
                this.loader = false;
                if (res.status) {
                    if (res.data !==null) {
                        this.provider_services = res.data;
                        this.services.visibility.service_id_by_provider = true;
                        this.services.validations.provider_service_not_found= '';
                    }
                    else
                    {
                        this.services.visibility.service_id_by_provider = false;
                        this.services.validations.provider_service_not_found= 'Nothing found';
                        this.services.form_fields.provider_service_id = null;

                    }
                }
            })
            .catch(err=> {
                this.loader = false;
                err.text().then(errMessage=>{
                    this.services.validations.provider_service_not_found= errMessage;
                    this.services.form_fields.provider_service_id = null;
                })
            });

        },
        changeSelected() {
            this.provider_services.forEach(item => {
                if (item.service  == this.services.form_fields.provider_service_id) {
                    this.provider_service_selected = item;
                }
            });
            if (this.provider_service_selected !== null) {
                this.services.visibility.drip_feed = true;
                this.services.form_fields.price = this.provider_service_selected.rate;
                this.services.form_fields.price_original = this.provider_service_selected.rate;

                this.services.form_fields.min_quantity = this.provider_service_selected.min;
                this.services.form_fields.max_quantity = this.provider_service_selected.max;
                this.services.form_fields.auto_min_quantity = this.provider_service_selected.min;
                this.services.form_fields.auto_max_quantity = this.provider_service_selected.max;
                this.service_type_selected = this.provider_service_selected.type;

            }

        },
        checkUncheckAll(index, event) {
            if (event.target.checked) {
                $('.category' + index).prop('checked', true);
                $('.catControl' + index).prop('checked', true);
                if (this.categories.length>0) {
                    this.categories.forEach((item, ind) => {
                        if (ind == index) {
                            if (item.services.length>0) {
                                item.services.forEach(it => {
                                    let custom_service = {...it};
                                    custom_service.custome_rate = it.rate;
                                    custom_service.custome_rate_visible = true;
                                    this.selected_services.push(custom_service);
                                });
                            }
                        }
                    });
                }
            } else {
                $('.category' + index).prop('checked', false);
                $('.catControl' + index).prop('checked', false);
                if (this.categories.length>0) {
                    this.categories.forEach((item, ind) => {
                        if (ind == index) {
                            if (item.services.length>0) {
                                item.services.forEach(it => {
                                    this.selected_services.splice(this.selected_services.indexOf(it), 1);
                                });
                            }
                        }
                    });
                }
            }
        },
        selectDropDown(index, value, id) {
            $('.cat' + index).text(value);

            if (value == 'Create category') {
                $('.catControl' + index).val('create');
            } else {
                $('.catControl' + index).val(id);
            }
        },
        checkSibling(e) {
            $(e.target).siblings().prop('checked', e.target.checked);
            let service = JSON.parse(e.target.value);
            if (e.target.checked) {
                let custom_service = {...service};
                custom_service.custome_rate = service.rate;
                custom_service.custome_rate_visible = true;
                this.selected_services.push(custom_service);
            } else {
                this.selected_services.splice(this.selected_services.indexOf(service), 1);
            }
        },
        openSubscriptionModal() {
            if (!this.subscription_modal) {
                $("#subscriptionModal").modal('show');
                this.subscription_modal = true;
            }
            else
            {
                $("#subscriptionModal").modal('hide');
                this.subscription_modal = false;
                this.formReset();
            }
        },
        formReset() {
            this.services = {
                visibility: {
                    drip_feed: false,
                    re_fill: false,
                    service_id_by_provider: false,
                    provider: false,
                    service_type: false,
                    overflow: false,
                    auto_per_rate: false,
                },
                disable: {
                    min: false,
                    max: false,
                },
                form_fields: {
                    name: '',
                    score: 0,
                    category_id: null,
                    number: 0,
                    mode: 'Auto',
                    provider_id: null,
                    provider_service_id: null,
                    service_type: null,
                    drip_feed_status: 'Allow',
                    refill_status: 'Allow',
                    short_name: '',
                    price: null,
                    price_original: null,
                    min_quantity: null,
                    auto_min_quantity: null,
                    auto_max_quantity: null,
                    link_duplicates: null,
                    increment: null,
                    auto_overflow: null,
                    subscription_type: null,
                },
                validations: {
                    price :{
                        visibility: false,
                        msg: ''
                    },
                    minQuantity :{
                        visibility: false,
                        msg: ''
                    },
                    maxQuantity :{
                        visibility: false,
                        msg: ''
                    },
                    provider_service_not_found: '',
                }
            }
        this.service_mode =  'Auto';
        this.manipulateInputs();
        },
        closeServiceForm() {
            $("#serviceModal").modal('hide');
                this.subscription_modal = false;
                this.formReset();
        },
        closesubscriptionModal() {
            $("#subscriptionModal").modal('hide');
                this.service_modal = false;
                this.formReset();
        },
        updateCategoryLists(id, obj)
        {
            this.category_services = this.category_services.map(item=>{
                if (item.id === id) {
                    return obj;
                }
                return item;
            })
        },
        updateServiceLists(obj)
        {
            let category = this.category_services.find(item=>item.id == obj.category_id);
            if (category !== undefined) {
               let servicesss =  category.services.map(ser=>{
                   if (ser.id===obj.id)
                   {
                       return obj;
                   }
                   return ser;
                });
                this.category_services = this.category_services.map(item=>{
                    if (item.id === category.id) {
                         item.services = [...servicesss];
                    }
                    return item;
               })
            }
        },
        addnewServicetoLists(obj)
        {
            this.category_services = this.category_services.map(item=>{
                if (item.id == obj.category_id) {
                        if (item.services) {
                            item.services.push(obj);
                        }
                        else
                        {
                            item.services = [];
                            item.services.push(obj);
                        }
                        
                }
                return item;
            });
        },
        deleteService(obj)
        {
            if (obj!==null) {
                let category = this.category_services.find(item=>item.id == obj.category_id);
                
                if (category !== undefined) {
                    let servicce = category.services.find(it=>it.id==obj.id);
                    category.services.splice(category.services.indexOf(servicce), 1);
                    let  servicesss= category.services;
                    this.category_services = this.category_services.map(item=>{
                        if (item.id === category.id) {
                            item.services = [...servicesss];
                        }
                        return item;
                    })
                }
            }
        },
        errorFilter(name)
        {
            let txt = '';
            if (this.errors.services.length>0) 
            {
                this.errors.services.forEach(item=>{
                    if (item.name === name) {
                        txt = item.desc;
                    }
                });
            }
            return txt;
        },
    },
});
function categorysortable() {
    let allcategory_ids = [];
    $(".category_hidden_id").each(function(i,v){
        allcategory_ids.push($(this).val());
    });
    $.ajax({
        type: "POST",
        dataType: "json",
        url: sortCategoryRoute,
        data: {'category_ids': allcategory_ids, "_token": CSRF_TOKEN},
        success: function (data) {
            console.log(data);
        }
    });
    console.log(allcategory_ids);
}
function serviceSortable() {
    alert();
    let allservice_ids = [];
    $(".service_checkbox").each(function(i,v){
        allservice_ids.push($(this).val());
    });

    $.ajax({
        type: "POST",
        dataType: "json",
        url: sortServiceRoute,
        data: {'services_ids': allservice_ids, "_token": CSRF_TOKEN},
        success: function (data) {
            console.log(data);
        }
    });
    console.log(allservice_ids);
}
$(document).ready(function () {

    $('#selectAllService').click(function () {
        let allids = [];
        if ($(this).prop('checked')) {
            $('.service_checkbox').each(function (i, v) {
                allids.push($(this).val());
            });
            App.service_checkbox = allids;
        } else {
            App.service_checkbox = [];
        }

    });
    $("#searchmyInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $(".__table_body .__service_row").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    /* jquery UI sortable start here */
    $('.category-sortable').sortable({
        placeholder: "highlight",
        handle: '.category-handle',
        connectWith: ".category-sortable",
        update: categorysortable,
    });
    //$('.__category_row').sortable('disable');
    $('.service_rows').sortable({
        placeholder: "highlight",
        handle: '.service-handle',
        connectWith: ".service_rows",
        update: serviceSortable,
    });

    //$( "#sortable" ).sortable({placeholder: "ui-state-highlight",helper:'clone'});
});

function hideService(current)
{
    let serviceRow = $(current).closest('.__category_row').next('.service_rows');
    if ($(serviceRow).is(':visible'))
    {
        $(serviceRow).hide();
    }
    else
    {
        $(serviceRow).show();
    }

}
let allcategoryToggler = false;
function toggleAllcategory()
{
    if (allcategoryToggler==false) {
        $("#expand").show();
        $("#compress").hide();
        $('.__category_row').each(function(i,v){
            $(this).next('.service_rows').hide();
        });
        allcategoryToggler = true;
    }
    else
    {
        $("#expand").hide();
        $("#compress").show();
        $('.__category_row').each(function(i,v){
            $(this).next('.service_rows').show();
        });
        allcategoryToggler = false;
    }

}

// document.getElementById('service_type_filter').addEventListener('click', evt=>{
//         document.querySelector('input[name=serviceTypefilter]').value = evt.target.getAttribute('data-key');
//         document.getElementById('service_type_filter_form').submit();
// });
// document.getElementById('status_type_filter').addEventListener('click', evt=>{
//         document.querySelector('input[name=status]').value = evt.target.getAttribute('data-key');
//         document.getElementById('status_type_filter_form').submit();
// });
$('.custom_summernote').summernote({
        tabsize: 2,
        height: 120,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']]
        ]
    });