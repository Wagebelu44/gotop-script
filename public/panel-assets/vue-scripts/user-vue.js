const userModule = new Vue({
    el: '#user_panel_module',
    data: {
        users: [],
        pagination: {current_page: 1},
        formUser:{
            panel_id: 1,
            email: '',
            username: '',
            skype_name: '',
            phone: '',
            password: '',
            password_confirmation: '',
            status: '',
            balance: 0.00,
            payment_methods: [],
        },
        global_payment_methods: [],
        filter: {
            status: "",
            search: "",
        },
        validationErros: [],
        edit_user_id: null,
        formFunc: null,
        userServices: [],
        categoryServices: [],
        current_user_id: null,
        selectedUsers: [],
        checkAlluser: false,
        isEdit: false,
        current_user: null,
        custom_rated_users: [],
        user_login_logs: [],
    },  
    mixins: [mixin],
    created () {
        let myUrl = new URL (window.location.href);
        if (myUrl.search.includes('page=')) {
            this.pagination.current_page = myUrl.search.split('page=')[1];
        }
    },
    watch: {
        checkAlluser(oldval, newval) {
            if (oldval) {
                this.selectedUsers = this.users.map(it=>it.id);
            }
            else this.selectedUsers = [];
        }
    },
    mounted () {
        this.getUsers();
        this.getCategoryServices();
    },
    updated () {
        this.formFunc =  this.edit_user_id===null? this.saveUserInfo:this.updateUser;
    },
    methods: {
        getCategoryServices() {
            this.loader = true;
            fetch(base_url+'/admin/category-services')
            .then(res => res.json())
            .then(res => {
                this.loader = false;
                this.categoryServices = res;
            });
        },
        getUsers(page=1) {
            this.loader = true;
            let page_number = this.pagination.current_page;
            let page_id = '?&page=' +page_number;
            if (page_number > 1) {
                const state = { 'page': page_number};
                const title = '';
                const url = base_url+'/admin/users'+ page_id;
                history.pushState(state, title, url)
            }
            if (this.filter.status !== "") {
                const state = { 'status': this.filter.status};
                const title = '';
                page_id += '&status='+this.filter.status
                const url = base_url+'/admin/users'+ page_id;
                history.pushState(state, title, url)
            }

            if (this.filter.search !== "") {
                const state = { 'username': this.filter.search};
                const title = '';
                page_id += '&username='+this.filter.search;
                const url = base_url+'/admin/users'+ page_id;
                history.pushState(state, title, url)
            }
            fetch(base_url+'/admin/getusers'+ page_id)
                .then(res => res.json())
                .then(res => {
                    this.loader = false;
                    this.users = res.data.data;
                    this.global_payment_methods = res.global_payment_methods;
                    this.pagination = res.data;
                });
        },
        saveUserInfo() {
            this.loader = true;
            fetch(base_url+'/admin/users', {
                headers: {
                    "Accept": "application/json, text/plain, */*'",
                    "X-CSRF-TOKEN": CSRF_TOKEN,
                    "Content-Type": "application/json, text/plain, */*'",
                },
                credentials: "same-origin",
                method: "POST",
                body: JSON.stringify(this.formUser)
            }).then(res => {
                if (!res.ok) {
                    throw res;
                }
                return res.json();
            })
            .then(res => {
                this.loader = false;
                if (res.status) {
                    this.users =  [res.data, ...this.users];
                    this.formClear();
                    $("#userModal").modal('hide');
                    $( '#user-form' ).each(function(){
                        this.reset();
                    });
                }
            })
            .catch(res => {
                this.loader = false;
                res.text().then(err => {
                    let errMsgs = Object.entries(JSON.parse(err).errors);
                    for (let i = 0; i < errMsgs.length; i++) {
                        let obj = {};
                        obj.name = errMsgs[i][0];
                        obj.desc = errMsgs[i][1][0];
                        this.validationErros.push(obj);
                    }
                });
            });
        },
        formClear() {
            this.formUser = {
                panel_id: 1,
                email: '',
                username: '',
                skype_name: '',
                phone: '',
                password: '',
                password_confirmation: '',
                status: '',
                payment_methods: [],
            };
        },
        editUser(id) {
            this.loader = true;
            fetch(base_url+'/admin/users/'+id).then(res => {
                if (!res.ok) {
                    throw res;
                }
                return res.json();
            })
            .then(res => {
                this.loader = false;
                $("#userModal").modal('show');
                this.formUser = {...res};
                let payment_ids = res.payment_methods.map(it=>it.payment_id);
                this.formUser.payment_methods = [...payment_ids]; // res.payment_methods.map(it=>it.payment_id);
                this.edit_user_id = res.id;
                this.isEdit = true;
            })
            .catch(res => {
                res.text().then(err=>{
                    let errMsgs = Object.entries(JSON.parse(err).errors);
                    for (let i = 0; i < errMsgs.length; i++) {
                        let obj = {};
                        obj.name = errMsgs[i][0];
                        obj.desc = errMsgs[i][1][0];
                        this.validationErros.push(obj);
                    }
                });
            });
        },
        updateUser() {
            let formData = {};
            formData.email = this.formUser.email;
            formData.skype_name = this.formUser.skype_name;
            formData.username = this.formUser.username;
            formData.password = this.formUser.password;
            formData.password_confirmation = this.formUser.password_confirmation;
            formData.payment_methods = this.formUser.payment_methods;
            formData.status = this.formUser.status;
            this.loader = true;
            fetch(base_url+'/admin/users/'+this.edit_user_id, {
                headers: {
                    "Accept": "application/json, text/plain, */*'",
                    "X-CSRF-TOKEN": CSRF_TOKEN,
                    "Content-Type": "application/json, text/plain, */*'",
                },
                credentials: "same-origin",
                method: "PUT",
                body: JSON.stringify(formData)
            }).then(res => {
                if (!res.ok) {
                    throw res;
                }
                return res.json();
            })
            .then(res => {
                this.loader = false;
                if (res.status) {
                    this.users = this.users.map(item => {
                        if (item.id === res.data.id) {
                            return res.data;
                        }
                        return item;
                    });
                    this.formClear();
                    $("#userModal").modal('hide');
                    this.isEdit = false;
                }
            })
            .catch(res => {                
                this.loader = false;
                res.text().then(err => {
                    let errMsgs = Object.entries(JSON.parse(err).errors);
                    for (let i = 0; i < errMsgs.length; i++) {
                        let obj = {};
                        obj.name = errMsgs[i][0];
                        obj.desc = errMsgs[i][1][0];
                        this.validationErros.push(obj);
                    }
                });
            });
        },
        suspendUser(user_id) {
            if (confirm('Are you sure?')) {
                this.loader = true;
                fetch(base_url+'/admin/suspendUser', {
                    headers: {
                        "Accept": "application/json, text/plain, */*'",
                        "X-CSRF-TOKEN": CSRF_TOKEN,
                        "Content-Type": "application/json, text/plain, */*'",
                    },
                    credentials: "same-origin",
                    method: "POST",
                    body: JSON.stringify({user_id: user_id})
                }).then(res => {
                    if (!res.ok) {
                        throw res;
                    }
                    return res.json();
                })
                .then(res => {
                    this.loader = false;
                    if (res.status) {
                       this.getUsers();
                    }
                })
                .catch(res => {
                    console.log(res);
                    // res.text().then(err => {
                    //     let errMsgs = Object.entries(JSON.parse(err).errors);
                    //     for (let i = 0; i < errMsgs.length; i++) {
                    //         let obj = {};
                    //         obj.name = errMsgs[i][0];
                    //         obj.desc = errMsgs[i][1][0];
                    //         this.validationErros.push(obj);
                    //     }
                    // });
                });
            }
        },
        customeRate(user_id) {
            this.current_user_id = user_id;
            this.current_user = this.users.find(u => u.id == user_id);
            this.loader = true;
            fetch(base_url+'/admin/users-services/'+ user_id)
            .then(res => res.json())
            .then(res => {
                this.loader = false;
                this.userServices = [];
                if (res.length>0) {
                    res.forEach(item=>{
                        let provider_service = null;
                        let rateObj = {};
                        rateObj.service_id = item.id;
                        rateObj.name = item.name;
                        rateObj.original_price =  item.price;
                        rateObj.price =  item.pivot.price;
                        rateObj.panel_id = item.pivot.panel_id;
                        rateObj.user_id = item.pivot.user_id;
                        rateObj.provider_price = provider_service!==null?provider_service.rate: null;
                        rateObj.back_end =  true;
                        this.userServices.push(rateObj);
                    });
                }

                $('#customRateAddModal').modal('show');
            });
        },
        userLoginLog(user_id) {
            this.current_user_id = user_id;
            this.current_user = this.users.find(u => u.id == user_id);
            this.loader = true;
            $("#user_login_log").modal('show');
            fetch(base_url+'/admin/get-user-login-log/'+this.current_user_id)
            .then(res=>res.json())
            .then(res=>{
                this.user_login_logs = res;
                this.loader = false;
            })
        },
        copyCustomRate(user_id) {
            this.current_user_id = user_id;
            this.current_user = this.users.find(u => u.id == user_id);
            fetch(customRateduserUrl)
            .then(res=>res.json())
            .then(res=>{
                this.custom_rated_users = res;
            });
            $("#copyCustomRateModal").modal('show');
            setTimeout(() => {
                $('#select2-payment-user').select2();
                $('#select2-payment-user').val(this.users).trigger('change');                      
            }, 100);
        },
        copyRatesSubmit() {
            let formD = new FormData(document.getElementById('copy-custom-rate-form'));
            fetch(copyratestouser, {
                headers: {
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": CSRF_TOKEN,
                },
                credentials: "same-origin",
                method: "POST",
                body: formD
            }).then(res => {
                if (!res.ok) {
                    throw res;
                }
                return res.json();
            })
            .then(res=>{
                if (res.status) {
                    $("#copyCustomRateModal").modal('hide');
                    this.getUsers();
                }
            })
            .catch(res => {
                this.loader = false;
                res.text().then(err => {
                    let errMsgs = Object.entries(JSON.parse(err).errors);
                    for (let i = 0; i < errMsgs.length; i++) {
                        let obj = {};
                        obj.name = errMsgs[i][0];
                        obj.desc = errMsgs[i][1][0];
                        this.validationErros.push(obj);
                    }
                });
            });
        },
        resetPassword(user_id) {
            this.edit_user_id = user_id;
            $('#passwordUpdateModal').modal('show');
        },
        updatePassword() {
            let formD = new FormData(document.getElementById('password-update-form'));
            this.loader = true;
            fetch(base_url+'/admin/updatePassword/', {
                headers: {
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": CSRF_TOKEN,
                },
                credentials: "same-origin",
                method: "POST",
                body: formD
            }).then(res => {
                if (!res.ok) {
                    throw res;
                }
                return res.json();
            })
            .then(res => {
                this.loader = false;
                if (res.status) {
                    document.getElementById('password-update-form').reset();
                    $("#passwordUpdateModal").modal('hide');
                }
            })
            .catch(res => {
                res.text().then(err => {
                    let errMsgs = Object.entries(JSON.parse(err).errors);
                    for (let i = 0; i < errMsgs.length; i++) {
                        let obj = {};
                        obj.name = errMsgs[i][0];
                        obj.desc = errMsgs[i][1][0];
                        this.validationErros.push(obj);
                    }
                });
            });
        },
        statusFilter(txt) {
            this.filter.status = txt;
            this.getUsers();
        },
        searchFilter() {
            this.getUsers();
        },
        addCustomRate(obj)
        {
            let service_ids = obj.id;
            let provider_service = null; //provider_rate(service_ids);

            let pushAble = false;
            if (this.userServices.length>0) {
                this.userServices.forEach(item=>{
                    if (item.service_id == obj.id) {
                        pushAble=true;
                    }
                });
            }
            if (!pushAble)
            {
                let rateObj = {};
                rateObj.service_id = obj.id;
                rateObj.name = obj.name;
                rateObj.panel_id = obj.panel_id;
                rateObj.user_id = obj.user_id;
                rateObj.price =  obj.price;
                rateObj.original_price =  obj.price;
                rateObj.provider_price =  provider_service!==null?provider_service.rate: null;
                rateObj.back_end =  false;
                this.userServices.unshift(rateObj);
            }
        },
        removeCustomRate(service_id)
        {
            let originUrl = window.location.origin;
            if (originUrl == 'http://localhost') {
                originUrl += '/go2top/public/';
            }
            else
            {
                originUrl +='/';
            }
            if (this.userServices.length>0) {
                this.userServices.forEach(item=>{
                    if (item.service_id == service_id) {
                        if (item.back_end)
                        {
                            console.log(item, 'servic');
                            fetch(deleteSingleService, {
                                headers: {
                                    "X-CSRF-TOKEN": CSRF_TOKEN,
                                    "Content-Type": "application/json",
                                },
                                credentials: "same-origin",
                                method: "DELETE",
                                body: JSON.stringify(item),
                            }).then(res => {
                                if (!res.ok) {
                                    throw res;
                                }
                                return res.json();
                            })
                            .then(res=>{
                                if (res.status) {
                                    this.userServices.splice(this.userServices.indexOf(item), 1); 
                                }
                            })
                        }
                        else
                        {
                            this.userServices.splice(this.userServices.indexOf(item), 1);
                        }
                    }
                });
            }
        },
        storeUserService()
        {
            if (this.userServices.length===0) {
                alert('No Item is selected');
            }
            else
            {

                let user_data = {};
                user_data.user_id =  this.current_user_id;
                user_data.services = JSON.stringify(this.userServices);
                this.loader = true;
                fetch(base_url+'/admin/store-service', {
                    headers: {
                        "X-CSRF-TOKEN": CSRF_TOKEN,
                        "Content-Type": "application/json",
                    },
                    credentials: "same-origin",
                    method: "POST",
                    body: JSON.stringify(user_data),
                }).then(res => {
                    if (!res.ok) {
                        throw res;
                    }
                    return res.json();
                })
                .then(res => {
                    if (res.status) {
                        this.loader = false;
                        $("#customRateAddModal").modal('hide');
                        this.getUsers();
                    }
                })
                .catch(res => {
                    res.text().then(err => {
                        let errMsgs = Object.entries(JSON.parse(err).errors);
                        for (let i = 0; i < errMsgs.length; i++) {
                            let obj = {};
                            obj.name = errMsgs[i][0];
                            obj.desc = errMsgs[i][1][0];
                            this.validationErros.push(obj);
                        }
                    });
                });
            }
        },
        updateInput(obj, service_id)
        {
            this.userServices.map(item=>{
                if (item.service_id === service_id) {
                    item.update_price = obj.target.value;
                }
                return item;
            });
        },
        deleteAllUserService()
        {
            if (this.userServices.length===0) {
                alert('No Item is selected');
            }
            else
            {
                this.loader = true;
                fetch(base_url+'/admin/delete-user-service', {
                    headers: {
                        "X-CSRF-TOKEN": CSRF_TOKEN,
                        "Content-Type": "application/json",
                    },
                    credentials: "same-origin",
                    method: "DELETE",
                    body: JSON.stringify({user_id: this.current_user_id}),
                }).then(res => {
                    if (!res.ok) {
                        throw res;
                    }
                    return res.json();
                })
                .then(res => {
                    if (res.status) {
                        this.loader = false;
                        $("#customRateAddModal").modal('hide');
                        this.getUsers();
                    }
                })
                .catch(res => {
                    res.text().then(err => {
                        let errMsgs = Object.entries(JSON.parse(err).errors);
                        for (let i = 0; i < errMsgs.length; i++) {
                            let obj = {};
                            obj.name = errMsgs[i][0];
                            obj.desc = errMsgs[i][1][0];
                            this.validationErros.push(obj);
                        }
                    });
                });
            }
        },
        activeAlluser()
        {
            if (this.selectedUsers.length>0) {

                fetch(base_url+'/admin/bulk-status-update', {
                    headers: {
                        "X-CSRF-TOKEN": CSRF_TOKEN,
                        "Content-Type": "application/json",
                    },
                    credentials: "same-origin",
                    method: "POST",
                    body: JSON.stringify({user_ids: this.selectedUsers, status: 'Active'}),
                }).then(res => {
                    if (!res.ok) {
                        throw res;
                    }
                    return res.json();
                })
                .then(res => {
                    if (res.status) {
                        window.location.reload();
                    }
                })
                .catch(res => {
                    res.text().then(err => {
                        let errMsgs = Object.entries(JSON.parse(err).errors);
                        for (let i = 0; i < errMsgs.length; i++) {
                            let obj = {};
                            obj.name = errMsgs[i][0];
                            obj.desc = errMsgs[i][1][0];
                            this.validationErros.push(obj);
                        }
                    });
                });
            }
            else
            {
                alert('No User is selected');
            }
        },
        suspendAlluser()
        {
            if (this.selectedUsers.length>0) {
                fetch(base_url+'/admin/bulk-status-update', {
                    headers: {
                        "X-CSRF-TOKEN": CSRF_TOKEN,
                        "Content-Type": "application/json",
                    },
                    credentials: "same-origin",
                    method: "POST",
                    body: JSON.stringify({user_ids: this.selectedUsers, status: 'Deactivated'}),
                }).then(res => {
                    if (!res.ok) {
                        throw res;
                    }
                    return res.json();
                })
                .then(res => {
                    if (res.status) {
                        window.location.reload();
                    }
                })
                .catch(res => {
                    res.text().then(err => {
                        let errMsgs = Object.entries(JSON.parse(err).errors);
                        for (let i = 0; i < errMsgs.length; i++) {
                            let obj = {};
                            obj.name = errMsgs[i][0];
                            obj.desc = errMsgs[i][1][0];
                            this.validationErros.push(obj);
                        }
                    });
                });
            }
            else
            {
                alert('No User is selected');
            }

        },
        resetAlluserRate()
        {
            if (this.selectedUsers.length>0) {
                fetch(base_url+'/admin/bulk-status-update', {
                    headers: {
                        "X-CSRF-TOKEN": CSRF_TOKEN,
                        "Content-Type": "application/json",
                    },
                    credentials: "same-origin",
                    method: "POST",
                    body: JSON.stringify({user_ids: this.selectedUsers, status: 'rate_reset'}),
                }).then(res => {
                    if (!res.ok) {
                        throw res;
                    }
                    return res.json();
                })
                .then(res => {
                    if (res.status) {
                        window.location.reload();
                    }
                })
                .catch(res => {
                    res.text().then(err => {
                        let errMsgs = Object.entries(JSON.parse(err).errors);
                        for (let i = 0; i < errMsgs.length; i++) {
                            let obj = {};
                            obj.name = errMsgs[i][0];
                            obj.desc = errMsgs[i][1][0];
                            this.validationErros.push(obj);
                        }
                    });
                });
            }
            else
            {
                alert('No User is selected');
            }
        }

    }
});
