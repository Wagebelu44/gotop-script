let app = new Vue({
    el: '#support_ticket_panel',
    delimiters: ['${', '}'],
    data: {
       order_types: false,
       payment_types: false,
       payment_type_value: 'Refill',
       order_ids: false,
       transaction_ids: false,
       testText: 'order page',
       ticket_subject: 'order page',
    },
    watch: {
     ticket_subject(newval, oldval){
         this.init();
     }
    },
    methods: {
         init(){
             if (this.ticket_subject === 'order') {
                 this.order_types = true;
                 this.order_ids = true;

                 this.payment_types = false;
                 this.transaction_ids = false;
             }
             else if(this.ticket_subject === 'payment')
             {
                 this.payment_types = true;
                 this.transaction_ids = true;

                 this.order_types = false;
                 this.order_ids = false;
             }
             else
             {
                 this.payment_types = false;
                 this.transaction_ids = false;
                 this.order_types = false;
                 this.order_ids = false;
             }
         },
    },
    created(){
        this.init();
    }
});