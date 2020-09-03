new Vue({
    el: '#user_panel_module',
    data: {
        users: null,
        pagination: {current_page: 1},
    },
    created () {
        console.log('logagdsfsdfasdf', CSRF_TOKEN);
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
        }
    }

})