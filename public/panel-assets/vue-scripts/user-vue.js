new Vue({
    el: '#user_panel_module',
    data: {
        users: null,
        pagination: {current_page: 1},
    },
    created () {
        console.log('logagdsfsdfasdf', CSRF_TOKEN);
    },
    mounted () {
        this.getUsers();
    },
    methods: {
        getUsers(page=1)
        {
            fetch(base_url+'/admin/getusers?&page=' + this.pagination.current_page)
            .then(res=>res.json())
            .then(res=>{
                this.users = res.data.data;
                this.pagination = res.data;
            });
        }
    }

})