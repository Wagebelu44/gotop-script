const capitalize = (s) => {
    if (typeof s !== 'string') return ''
    return s.charAt(0).toUpperCase() + s.slice(1)
}
Vue.component('v-select', VueSelect.VueSelect);
const App = new Vue({
    el: '#serviceApp',
    data: {
        options: [ {country: 'atik', code: 1}, {country: 'sudip', code: 2},],
        providers_lists: [],
        errors: {
            category: [],
            services: null,
        },
        success: {
            category: '',
        },
        loader: {
            category: false,
            service: false,
            page: false,
            description: false,
        },
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
        service_type: [
            'Default',
            'SEO',
            'SEO2',
            'Custom Comments',
            'Custom Comments Package',
            'Comment Likes',
            'Mentions',
            'Mentions with Hashtags',
            'Mentions Custom List',
            'Mentions Hashtag',
            'Mentions Users Followers',
            'Mentions Media Likers',
            'Package',
            'Poll',
            'Comment Replies',
            'Invites From Groups',
        ],
        service_type_selected: 'Default',
        category: {
            name: null,
            star: null,
            short_description: '',
            description: '',
            icon: null,
            status: null,
        },
        category_services: null,
        service_checkbox: [],
        provider_services: [],
        categories: [],
        provider_service_selected: null,
        provider_id: null,
        subscription_modal: false,
        service_modal: false,
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
                this.getProviderServices(oldval);
            },
            deep: true,
        },
        'services.form_fields.provider_service_id': {
            handler: function(oldval,newval)
            {
                this.changeSelected();
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
        this.providers_lists = [];
        this.services.visibility.auto_per_rate = this.auto_per_rate_toggler;
    },
    mounted () {
        this.getCategoryServices();
    },
    methods: {
        getCategoryServices()
        {
            fetch(base_url+"/admin/get-category-services")
            .then(res=>res.json())
            .then(res=>{
                this.category_services = res;
            });
        },
        submitCategoryForm(evt) {
            this.loader.category = true;
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
                    this.category_edit = false;
                    this.category_edit_id = null;
                    setTimeout(() => {
                        this.loader.category = false;
                        toastr["success"](res.message);
                        document.getElementById('category_form').reset();
                        $('#exampleModalCenter').modal('hide');
                    }, 2000);
                }

            }).catch(err => {
                setTimeout(() => {
                    this.loader.category = false;
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
            this.loader.service = true;
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
                            this.loader.service = false;
                            toastr["success"](res.message);
                            document.getElementById('service_form').reset();
                            $('#serviceModal').modal('hide');
                            if (isEdit) 
                            {
                                var row = res.data;
                                var status = (row.status == 'active')?'Enabled':'Disabled';
                                document.getElementById('sName_'+row.id).innerHTML=row.name;
                                document.getElementById('sType_'+row.id).innerHTML=row.service_type;
                                document.getElementById('sMode_'+row.id).innerHTML=row.mode;
                                document.getElementById('sPrice_'+row.id).innerHTML=row.price;
                                document.getElementById('sMinQty_'+row.id).innerHTML=row.min_quantity;
                                document.getElementById('sMaxQty_'+row.id).innerHTML=row.max_quantity;
                                document.getElementById('sStatus_'+row.id).innerHTML=status;
                            } 
                            else 
                            {
                                window.location.reload();
                            }
                        }, 2000);
                    }
                    else if(res.status === 401)
                    {
                        this.loader.service = false;
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
                console.log(this.errors.services, 'service');
        },
        editHelper() {
            this.service_mode = capitalize(this.services.form_fields.mode);
            this.services.form_fields.drip_feed_status = capitalize(this.services.form_fields.drip_feed_status);
            this.services.form_fields.refill_status = capitalize(this.services.form_fields.refill_status);
            this.link_duplicate_selected = capitalize(this.services.form_fields.link_duplicates);
        },
        subscriptionEdit(service_id) {
            this.loader.page = true;
            this.service_edit_id = service_id;
            fetch('showService/' + service_id).then(res => res.json())
                .then(res => {
                    this.loader.page = false;
                    this.loader.service = true;
                    this.service_edit = true;
                    $('#subscriptionModal').modal('show');
                    this.services.form_fields = {...res.data};
                    this.service_mode = this.services.form_fields.mode;
                    this.service_type_selected = this.services.form_fields.service_type;
                    this.manipulateInputs();
                    this.editHelper();
                    this.loader.service = false;
                })
        },
        serviceEdit(service_id) {
            this.loader.page = true;
            this.service_edit_id = service_id;
            fetch('showService/' + service_id).then(res => res.json())
                .then(res => {
                    this.loader.page = false;
                    this.loader.service = true;
                    this.service_edit = true;
                    $('#serviceModal').modal('show');
                    this.services.form_fields = {...res.data};
                    this.service_mode = this.services.form_fields.mode;
                    this.service_type_selected = this.services.form_fields.service_type;
                    this.manipulateInputs();
                    this.editHelper();
                    this.loader.service = false;
                })
        },
        serviceDescription(service_id) {
            this.loader.page = true;
            fetch('showService/' + service_id).then(res => res.json())
                .then(res => {
                    this.loader.page = false;
                    this.loader.description = true;
                    $('#serviceDescription').modal('show');
                    this.services.form_fields.description = res.data.description;
                    $("#serviceDescription_edit").summernote('code', res.data.description, {
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
                    this.loader.description = false;
                    this.service_edit_id = service_id;
                })
        },
        updateServiceDescription(evt) {
            evt.preventDefault();
            this.loader.description = true;
            evt.preventDefault();
            let service_form = new FormData(document.getElementById('formDescription'));
            fetch('updateService/' + this.service_edit_id, {
                headers: {
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
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
                    console.log(res);
                    if (res.status === 200) {
                        this.service_edit = false;
                        setTimeout(() => {
                            this.loader.description = false;
                            toastr["success"](res.message);
                            document.getElementById('formDescription').reset();
                            $('#serviceDescription').modal('hide');
                            //window.location.reload();
                        }, 2000);
                    }

                })
                .catch(err => {
                    console.log(err);
                    setTimeout(() => {
                        this.loader.description = false;
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
            this.loader.page = true;
            fetch('enableService/' + service_id).then(res => res.json())
                .then(res => {
                    this.loader.page = false;

                    toastr["success"](res.message);
                    if (res.status === 200) {
                        setTimeout(() => {
                            document.getElementById('sStatus_'+service_id).innerHTML = res.action;
                            document.getElementById('sStatusAction_'+service_id).innerHTML = res.actionToBe+' service';
                        }, 100);
                    }                            
                })
        },
        serviceResetRate(service_id) {
            this.loader.page = true;
            fetch('resetCustomRate/' + service_id).then(res => res.json())
                .then(res => {
                    this.loader.page = false;
                    toastr["success"](res.message);
                })
        },
        serviceDelete(service_id) {
            this.loader.page = true;
            fetch('deleteService/' + service_id).then(res => res.json())
                .then(res => {
                    this.loader.page = false;
                    toastr["success"](res.message);
                    setTimeout(() => {
                        $('.serviceRow_'+service_id).remove();
                    }, 100);
                })
        },
        serviceDuplicate(service_id, catStatus) {
            this.loader.page = true;
            fetch('duplicate/service/' + service_id).then(res => res.json())
                .then(res => {
                    this.loader.page = false;
                    toastr["success"](res.message);
                    var row = res.data;
                    setTimeout(() => {
                        var catStatusclass = (catStatus == 'inactive')?'__service_row-services-disable __service_row-overlay':'';
                        var serStatusclass = (row.status == 'inactive')?'__service_row-services-disable __service_row-overlay':'';
                        var html = `<div class="__service_row serviceRow_`+row.id+` `+catStatusclass+` `+serStatusclass+`" id="sortable">
                                <div class="__service_td drag-handler-container" style="padding: 0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="service-handle" viewBox="0 0 20 20"><title>Drag-Handle</title>
                                        <path
                                            d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z"></path>
                                    </svg>
                                    <input type="checkbox" class="service_checkbox" style="margin-top: 5px" v-model="service_checkbox"
                                           value="`+row.id+`">
                                </div>
                                <div class="__service_td">
                                    `+row.id+`
                                </div>
                                <div class="__service_td __service_th_td">
                                    `+row.name+`
                                </div>
                                <div class="__service_td __service_td_span_type">
                                    `+row.service_type+`
                                </div>
                                <div class="__service_td __service_td_span_mode">
                                    {!! ($service->mode == "manual") ? 'Manual': $service->provider->domain.'<br>'.$service->provider_service_id !!}
                                </div>
                                <div class="__service_td __service_td_span">
                                    `+row.price+`
                                </div>
                                <div class="__service_td __service_td_span">
                                    `+row.min_quantity+`
                                </div>
                                <div class="__service_td __service_td_span">
                                    `+row.max_quantity+`
                                </div>
                                <div class="__service_td __service_td_span" id="sStatus_`+row.id+`">`;
                                    if(row.status == 'active') {
                                        html += `Enabled`;
                                    } else {
                                        html += `Disabled`;
                                    }
                                html += `</div>`;
                                html += `<div class="__service_td __service_td_span" style="grid-column: span 3 / auto;padding: 0;text-align: right;">
                                    <div class="dropdown __dropdown_buttons">
                                        <button class="btn btn-default dropdown-toggle" type="button"
                                                id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false">
                                            Action
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            @if ($service->service_type == 'Subscriptions')
                                            <a class="dropdown-item type-dropdown-item" href="#" @click="subscriptionEdit(`+row.id+`)">Edit
                                                service</a>
                                            @else
                                            <a class="dropdown-item type-dropdown-item" href="#" @click="serviceEdit(`+row.id+`)">Edit
                                                service</a>
                                                @endif
                                            <a class="dropdown-item type-dropdown-item" href="#"
                                               @click="serviceDescription(`+row.id+`)">Edit description</a>

                                            <a class="dropdown-item type-dropdown-item" href="#"
                                               @click="serviceEnableDisable(`+row.id+`)" id="sStatusAction_`+row.id+`">`;
                                                if(row.status == 'active') {
                                                    html += `Disable service`;
                                                } else {
                                                    html += `Enable service`;
                                                }
                                                html += `</a>`;

                                            html += `<a class="dropdown-item type-dropdown-item" href="#"
                                            @click="serviceResetRate(`+row.id+`)">Reset custom rates</a>

                                            <a class="dropdown-item type-dropdown-item" href="#"
                                            @click="serviceDelete(`+row.id+`)">Delete
                                                service</a>

                                            <a class="dropdown-item type-dropdown-item" href="#"
                                            @click="serviceDuplicate(`+row.id+`)" >Duplicate</a>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                        $(html).insertAfter('.serviceRow_'+service_id);
                    }, 100);
                })
        },
        categoryEdit(category_id) {
            this.loader.page = true;
            this.category_edit = true;
            this.category_edit_id = category_id;
            fetch('showCategory/' + category_id).then(res => res.json())
                .then(res => {
                    this.loader.page = false;
                    this.loader.category = true;
                    this.category = {...res.data};
                    $('#exampleModalCenter').modal('show');
                    if (this.category_edit) {
                        $("#cate_short_desc").summernote('code', res.data.short_description);
                        $("#cate_long_desc").summernote('code', res.data.description);
                    }
                    this.loader.category = false;
                });

        },
        bulkEnable() {
            this.loader.page = true;
            if (this.service_checkbox.length !== 0) {
                //console.log(this.service_checkbox);
                let forD = new FormData();
                forD.append('service_ids', this.service_checkbox);
                fetch('service_bulk_enable', {
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
                                this.loader.page = false;
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
            this.loader.page = true;
            if (this.service_checkbox.length !== 0) {
                //console.log(this.service_checkbox);
                let forD = new FormData();
                forD.append('service_ids', this.service_checkbox);
                fetch('service_bulk_disable', {
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
                                this.loader.page = false;
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
            this.loader.page = true;
            if (this.service_checkbox.length !== 0) {
                //console.log(this.service_checkbox);
                let forD = new FormData();
                forD.append('service_ids', this.service_checkbox);
                fetch('{{route("reseller.service.custom.rate.reset.all")}}', {
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
                                this.loader.page = false;
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
                this.loader.page = true;
                if (this.service_checkbox.length !== 0) {
                    //console.log(this.service_checkbox);
                    let forD = new FormData();
                    forD.append('service_ids', this.service_checkbox);
                    fetch('service_bulk_delete', {
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
                                    this.loader.page = false;
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
            this.loader.page = true;
            if (this.service_checkbox.length !== 0) {
                //console.log(this.service_checkbox);
                let forD = new FormData(document.getElementById('formBulkCategory'));
                forD.append('service_ids', this.service_checkbox);
                fetch('service_bulk_category', {
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
                                this.loader.page = false;
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
            if (this.services.form_fields.provider_id !== null && this.services.form_fields.provider_id !== '') {
                this.loader.page = true;
                let forD = new FormData();
                forD.append('provider_id', this.services.form_fields.provider_id);
                fetch('{{route("reseller.service.get.provider.data")}}', {
                    headers: {
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
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
                        if (res.status) {
                            if (res.data !==null) {
                                this.loader.page = false;
                                this.provider_services = res.data;
                                this.services.visibility.service_id_by_provider = true;
                                this.services.validations.provider_service_not_found= '';
                            }
                            else
                            {
                                this.loader.page = false;
                                this.services.visibility.service_id_by_provider = false;
                                this.services.validations.provider_service_not_found= 'Nothing found';
                                this.services.form_fields.provider_service_id = null;

                            }
                        }
                    })
                    .catch(err=> {
                        err.text().then(errMessage=>{
                            this.services.validations.provider_service_not_found= errMessage;
                            this.services.form_fields.provider_service_id = null;
                        })
                    });
            }

        },
        getProviderServicesByCategory() {
            if (!this.provider_id) {
                return false;
            }

            this.loader.page = true;

            let originUrl = window.location.origin;
            if (originUrl == 'http://localhost') {
                originUrl += '/go2top/public/';
            }
            else
            {
                originUrl += '/';
            }

            fetch(originUrl + '/reseller/providers/' + this.provider_id + '/services', {
                headers: {
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                },
                credentials: "same-origin",
                method: "POST",
            })
                .then(res => {
                    if (!res.ok) {
                        throw res;
                    }

                    return res.json();
                })
                .then(response => {
                    this.loader.page = false;

                    if (response.status == 200) {
                        this.categories = response.data;

                        setTimeout(function () {
                            $('.dropdown-menu a.dropdown-toggle').on('click', function(e) {
                                if (!$(this).next().hasClass('show')) {
                                    $(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
                                }
                                var $subMenu = $(this).next(".dropdown-menu");
                                $subMenu.toggleClass('show');


                                $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) {
                                    $('.dropdown-submenu .show').removeClass("show");
                                });

                                return false;
                            });
                        }, 1000);
                    } else {
                        alert(response.msg);
                    }
                })
                .catch(err => {
                    console.log(err);
                    this.loader.page = false;
                    alert(err);
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
                /* if (this.provider_service_selected.dripfeed === true) {
                    this.services.visibility.drip_feed = true;
                }
                else
                {
                    this.services.visibility.drip_feed = false;
                } */

                this.services.form_fields.price = this.provider_service_selected.rate;
                this.services.form_fields.price_original = this.provider_service_selected.rate;

                this.services.form_fields.min_quantity = this.provider_service_selected.min;
                this.services.form_fields.max_quantity = this.provider_service_selected.max;
                this.services.form_fields.auto_min_quantity = this.provider_service_selected.min;
                this.services.form_fields.auto_max_quantity = this.provider_service_selected.max;
                this.service_type_selected = this.provider_service_selected.type;
                console.log(this.service_type_selected);

            }

        },
        checkUncheckAll(index, event) {
            if (event.target.checked) {
                $('.category' + index).prop('checked', true);
                $('.catControl' + index).prop('checked', true);
            } else {
                $('.category' + index).prop('checked', false);
                $('.catControl' + index).prop('checked', false);
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
        serviceModalToggle() {
            
            if (!this.service_modal) {
                $("#serviceAddModal").modal('show');
                this.service_modal = true;
            }
            else
            {
                $("#serviceAddModal").modal('hide');
                this.service_modal = false;
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
                    drip_feed_status: null,
                    refill_status: null,
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
        }
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
        url: '{{route("reseller.category.sort.data")}}',
        data: {'category_ids': allcategory_ids, "_token": "{{ csrf_token() }}"},
        success: function (data) {
            console.log(data);
        }
    });
    console.log(allcategory_ids);
}
function serviceSortable() {
    let allservice_ids = [];
    $(".service_checkbox").each(function(i,v){
        allservice_ids.push($(this).val());
    });

    $.ajax({
        type: "POST",
        dataType: "json",
        url: '{{route("reseller.service.sort.data")}}',
        data: {'services_ids': allservice_ids, "_token": "{{ csrf_token() }}"},
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
        console.log(value);
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

document.getElementById('service_type_filter').addEventListener('click', evt=>{
        document.querySelector('input[name=serviceTypefilter]').value = evt.target.getAttribute('data-key');
        document.getElementById('service_type_filter_form').submit();
});
document.getElementById('status_type_filter').addEventListener('click', evt=>{
        document.querySelector('input[name=status]').value = evt.target.getAttribute('data-key');
        document.getElementById('status_type_filter_form').submit();
});
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