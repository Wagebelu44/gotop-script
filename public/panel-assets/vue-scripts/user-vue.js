const userModule = new Vue({
    el: '#user_panel_module',
    data: {
        users: null,
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
        },
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
    },
    created () {
        let myUrl = new URL (window.location.href);
        if (myUrl.search.includes('page=')) {
            this.pagination.current_page = myUrl.search.split('page=')[1];
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
        getCategoryServices()
        {
            fetch(base_url+'/admin/category-services/')
            .then(res => res.json())
            .then(res => {
                this.categoryServices = res;
            });
        },
        getUsers(page=1) {
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
                const state = { 'search': this.filter.search};
                const title = '';
                page_id += '&status='+this.filter.search;
                const url = base_url+'/admin/users'+ page_id;
                history.pushState(state, title, url)
            }
            fetch(base_url+'/admin/getusers'+ page_id)
                .then(res => res.json())
                .then(res => {
                    this.users = res.data.data;
                    this.pagination = res.data;
                });
        },
        saveUserInfo() {
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
                if (res.status) {
                    this.users.unshift(res.data);
                    this.formClear();
                    $("#userModal").modal('hide');
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
            };
        },
        editUser(id) {
            fetch(base_url+'/admin/users/'+id).then(res => {
                if (!res.ok) {
                    throw res;
                }
                return res.json();
            })
            .then(res => {
                $("#userModal").modal('show');
                this.formUser = res;
                this.edit_user_id = res.id;
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
            fetch(base_url+'/admin/users/'+this.edit_user_id, {
                headers: {
                    "Accept": "application/json, text/plain, */*'",
                    "X-CSRF-TOKEN": CSRF_TOKEN,
                    "Content-Type": "application/json, text/plain, */*'",
                },
                credentials: "same-origin",
                method: "PUT",
                body: JSON.stringify(this.formUser)
            }).then(res => {
                if (!res.ok) {
                    throw res;
                }
                return res.json();
            })
            .then(res => {
                if (res.status) {
                    this.users = this.users.map(item => {
                        if (item.id === res.data.id) {
                            return res.data;
                        }
                        return item;
                    });
                    this.formClear();
                    $("#userModal").modal('hide');
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
        suspendUser(user_id) {
            if (confirm('Are you sure?')) {
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
                    this.users = this.users.map(item => {
                        if (item.id === res.data.id) {
                            return res.data;
                        }
                        return item;
                    });
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
            fetch(base_url+'/admin/users-services/'+ user_id)
            .then(res => res.json())
            .then(res => {
                this.userServices = [];
                if (res.length>0) {
                    res.forEach(item=>{
                        let provider_service = null; //provider_rate(item.id);
                        let rateObj = {}; 
                        rateObj.service_id = item.id;
                        rateObj.name = item.name;
                        rateObj.price =  item.pivot.price;
                        rateObj.provider_price = provider_service!==null?provider_service.rate: null;
                        rateObj.back_end =  true;
                        this.userServices.unshift(rateObj);
                    });
                }
                
                $('#customRateAddModal').modal('show');
            });
        },
        resetPassword(user_id) {
            this.edit_user_id = user_id;
            $('#passwordUpdateModal').modal('show');
        },
        updatePassword() {
            let formD = new FormData(document.getElementById('password-update-form'));
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
                rateObj.price =  obj.price;
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
                           /*  $('#deleteCustomRate').attr('action', originUrl + 'reseller/users/' + current_user_id + '/services/' + service_id);
                            $('#deleteCustomRate').submit(); */
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
                        $("#customRateAddModal").modal('hide'); 
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
        }
       
    }
});