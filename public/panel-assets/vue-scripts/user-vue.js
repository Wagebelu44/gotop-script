new Vue({
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
    },
    created () {
        let myUrl = new URL (window.location.href);
        if (myUrl.search.includes('page=')) {
            this.pagination.current_page = myUrl.search.split('page=')[1];
        }
    },
    mounted () {
        this.getUsers();
    },
    methods: {
        getUsers(page=1)
        {
            let page_number = this.pagination.current_page;
            let page_id = '?&page=' +page_number;
            if (page_number>1) {
                const state = { 'page': page_number};
                const title = '';
                const url = base_url+'/admin/users'+ page_id;
                history.pushState(state, title, url)
            }
            fetch(base_url+'/admin/getusers'+ page_id)
            .then(res=>res.json())
            .then(res=>{
                this.users = res.data.data;
                this.pagination = res.data;
            });
        },
        saveUserInfo()
        {
            fetch(base_url+'/admin/users', {
                headers: {
                    "Accept": "application/json, text/plain, */*'",
                    "X-CSRF-TOKEN": CSRF_TOKEN,
                    "Content-Type": "application/json, text/plain, */*'",
                },
                credentials: "same-origin",
                method: "POST",
                body: JSON.stringify(this.formUser)
            }).then(res=>res.json())
            .then(res=>{
                if (res.status) 
                {
                    this.users.unshift(res.data);
                    this.formClear();
                    $("#userModal").modal('hide');
                }
            });
        },
        formClear()
        {
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
        }
    }

})