const NewOrder = new Vue({
    el: '#vueHolder',
    data: {
        categoryjs : [],
        category_id: null,
        service_id: null,
        price: null,
        services: [],
        serviceDesc: '',
        quantity: 0,
        min: 0,
        max: 0,
        drip_feed_available: false,
        drip_feed: false,
        drip_feed_content: false,
        runs: null,
        interval: null,
        total_quantity: 0,
        quantity_validation:false,
        quantity_validation_msg: '',
        quantity_visibility: true,
        service_mode:null,
        keyword_visible: false,
        comments_visible: false,
        hastags_visible: false,
        current_service_type: null,
        perline_username_visible: false,
        additional_username_visible: false,
        additional_email_visible: false,
        additional_media_url_visible: false,
        additional_comment_owner_username_visible: false,
        additional_answer_number_visible: false,
        additional_hashtags_visible: false,
        inputDisable: false,
    },
    computed: {
      charge(){
         if (this.current_service_type === 'Package') {
            return this.price;
         }
         else
         {

              if (this.drip_feed && this.runs !== null)
              {
                    return ((this.price / 1000 * this.quantity) * this.runs).toFixed(2);
              }
              else
              {
                    return (this.price / 1000 * this.quantity).toFixed(2);
              }
         }

      }
    },
    watch:{
        category_id(newval, oldval)
        {
            if(newval !== null)
            {
                let services = this.categoryjs.find(item => item.id == newval);

                if(services !== null)
                {
                   let computedservices = services.services.map(item=>{

                       if (item.service_type == 'Package') {
                          item.display_name  = `ID: ${item.id} ${item.name} - ${item.price} Per 1`;
                       }
                       else
                       {
                          item.display_name  = `ID: ${item.id} ${item.name} - ${item.price}`;
                       }
                       return item;
                   });
                    this.services = computedservices;
                    if (this.services[0]) {
                       this.service_id = this.services[0].id;
                    }
                }

            }
        },
        service_id(newval, oldval)
        {
            if (newval !== null)
            {
                let serviceObj = this.services.find(item => item.id == newval);
                if (serviceObj !== undefined) {
                    this.serviceDesc = serviceObj.description ?? '';
                this.min = serviceObj.min_quantity ?? 0;
                this.max = serviceObj.max_quantity ?? 0;
                this.price = serviceObj.price ?? 0;
                this.service_mode = serviceObj.mode ?? 0;
                if (serviceObj.drip_feed_status === 'allow')
                {
                    this.drip_feed_available  = true;
                }

                this.keyword_visible=false;
                this.comments_visible=false;
                this.hastags_visible=false;
                this.perline_username_visible=false;
                this.additional_username_visible=false;
                this.additional_email_visible=false;
                this.additional_media_url_visible=false;
                this.additional_comment_owner_username_visible=false;
                this.additional_answer_number_visible=false;
                this.additional_hashtags_visible=false;
                this.inputDisable=false;
                this.quantity_visibility = true;


                this.current_service_type = serviceObj.service_type;
                /* extra fields condition */
                if (serviceObj.service_type == 'SEO')
                {
                    this.keyword_visible= true;
                }
                else if (serviceObj.service_type == 'SEO2')
                {
                    this.keyword_visible= true;
                    this.additional_email_visible=true;
                }
                else if (serviceObj.service_type == 'Default')
                {
                    console.log('do nothing');
                }
                  else if (serviceObj.service_type == 'Custom Comments')
                {
                    this.comments_visible = true;
                    this.inputDisable = true;
                }
                else if (serviceObj.service_type == 'Custom Comments Package')
                {
                    this.inputDisable = true;
                    this.comments_visible = true;
                }
                else if (serviceObj.service_type == 'Comment Likes')
                {
                    this.additional_comment_owner_username_visible = true;
                }
                else if (serviceObj.service_type == 'Mentions')
                {
                    this.perline_username_visible = true;
                }
                else if (serviceObj.service_type == 'Mentions with Hashtags')
                {
                    this.inputDisable = true;
                    this.perline_username_visible = true;
                    this.hastags_visible = true;
                }
                else if (serviceObj.service_type == 'Mentions Custom List')
                {
                    this.inputDisable = true;
                    this.perline_username_visible = true;
                }
                else if (serviceObj.service_type == 'Mentions Hashtag')
                {
                    this.additional_hashtags_visible = true;
                }
                else if (serviceObj.service_type == 'Mentions Users Followers')
                {
                    this.additional_username_visible = true;
                }
                else if (serviceObj.service_type == 'Mentions Media Likers')
               {
                    this.additional_media_url_visible = true;
                  }
                else if (serviceObj.service_type == 'Package')
                 {
                    this.inputDisable = true;
                    this.quantity = 1;
                    this.quantity_visibility = false;
                    this.drip_feed_available = false;
                       //everything should be invisible
                 }
                else if (serviceObj.service_type == 'Poll')
                {
                        this.additional_answer_number_visible = true;
                }
                else if (serviceObj.service_type == 'Comment Replies')
                {
                    this.inputDisable = true;
                    this.comments_visible = true;
                    this.additional_username_visible = true;
                }
                else if (serviceObj.service_type == 'Invites From Groups')
                    {
                        //make group option visible
                    }
                }


            }
        },
        drip_feed(newval, oldval)
        {
            if (newval)
            {
                this.drip_feed_content = true;
            }
            else
            {
                this.drip_feed_content = false;
            }
        },
        quantity(newval, oldval)
        {
            if (this.drip_feed && this.runs !== null)
            {
                this.total_quantity =  (this.quantity * this.runs);
            }

           if (newval < this.min || newval > this.max) {
              this.quantity_validation = true;
              this.quantity_validation_msg = "Quantity Limit exceeds , Min = "+this.min+" max = "+this.max;
           }
           else
           {
              this.quantity_validation = false;
              this.quantity_validation_msg = " ";
           }
        },
        runs(newval, oldval)
        {
            if (this.drip_feed && this.runs !== null)
            {
                this.total_quantity =  (this.quantity * this.runs);
            }
        },
    },
    created () {
        this.categoryjs =  <?= json_encode($categories)?>;
        let category_id_selected = document.getElementById('category_id').value;
        let services = this.categoryjs.find(item => item.id == category_id_selected);
        $submitted_order = <?=json_encode($order)?>;
        if(services !== null)
        {
           this.services = services.services.map(item=>{
                 if (item.service_type == 'Package') {
                    item.display_name  = `ID: ${item.id} ${item.name} - ${item.price} Per 1`;
                 }
                 else
                 {
                    item.display_name  = `ID: ${item.id} ${item.name} - ${item.price}`;
                 }
                 return item;
              });
           if (this.services[0]) {
              this.service_id = $submitted_order == null ? this.services[0].id : $submitted_order.service_id;
           }
        }
    },
    methods: {
        categoryChanges(evt){
            this.category_id = evt.target.value;
        },
        countPerLine(evt)
        {
            if (this.inputDisable) {
                if (evt.target.value.length > 0) {
                    let d = evt.target.value.split(/\r\n|\r|\n/).length;
                    this.quantity = d;
                }
                else
                {
                    this.quantity = 0;
                }
            }

        }
    },
});