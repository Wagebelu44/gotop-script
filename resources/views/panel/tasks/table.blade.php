<div class="tab-content table-responsive-xl" id="orders_tble">
    <div class="d-flex top-caret-bar">
        <div class="d-flex service-checkbox-action bg-danger">
            <div>
                <span style="color:#fff; padding: 0px 5px">Order Selected</span>
            </div>
            <div class="dropdown show">
                <a class="btn btn-sm dropdown-toggle" style="background: #fff; border: 1px solid #d4d4d4" href="#" role="button" id="dropdownMenuLink"
                   data-toggle="dropdown"
                   aria-haspopup="true" aria-expanded="false">
                    Actions
                </a>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                    <li><a class="dropdown-item" @click="bulkStatusChange('failed_resend')">Resend Orders</a></li>
                    <li><a class="dropdown-item" @click="bulkStatusChange('cancel_refund')">Cancal and refund</a></li>
                    <li><a class="dropdown-item" @click="bulkStatusChange('pending')">Pending</a></li>
                    <li><a class="dropdown-item" @click="bulkStatusChange('inprogress')">In Progress</a></li>
                    <li><a class="dropdown-item" @click="bulkStatusChange('processing')">Processing</a></li>
                    <li><a class="dropdown-item" @click="bulkStatusChange('completed')">Completed</a></li>

                </ul>
            </div>
        </div>
    </div>
    <table class="table">
        <thead>
        <tr>
            <th> <input type="checkbox" @click="bulkSelect" /></th>
            <th scope="col">ID</th>
                <th>
                    <div class="dropdown __dropdown_buttons">
                        <button class="btn btn-default dropdown-toggle" type="button"
                                id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                            Users
                        </button>
                        <div class="dropdown-menu service-dropdown" aria-labelledby="dropdownMenuButton">
                            <div class="custom-searchBar">
                                <input type="text" id="search-by-user" class="form-control">
                                <i class="fa fa-search search-icon-users" aria-hidden="true"></i>
                            </div>
                            <div id="user_filter_type">
                               <a data-key="" class="dropdown-item type-dropdown-item"></a>
                            </div>
                        </div>
                        <form action="" method="get" class="user_filter_type_form">
                            <input type="hidden" name="user">
                        </form>
                    </div>
                </th>
            <th scope="col">Charge</th>
            <th scope="col">Link</th>
            <th scope="col" width="150">Start count</th>
            <th scope="col" width="150">Quantity</th>
            <th scope="col">
                <div class="dropdown __dropdown_buttons">
                    <button class="btn btn-default dropdown-toggle" type="button"
                            id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                        Services
                    </button>
                    <div class="dropdown-menu service-dropdown" aria-labelledby="dropdownMenuButton" id="service_filter_type">
                        <a data-key="" class="dropdown-item type-dropdown-item">
                            <span style="padding: 2px; border: 1px solid rgba(0,0,0,0.7); font-size:10px; font-weight: 700">111</span>
                        </a>
                    </div>
                    <form action="" method="get" class="service_filter_type_form">
                        <input type="hidden" name="services">
                        <input type="hidden" name="search" value="">
                        <input type="hidden" name="filter_type" value="">
                    </form>
                </div>
                Service
            </th>
            <th scope="col" width="120">Status</th>
                <th>Remains</th>
            <th scope="col" width="100">Created</th>
                <th>
                    <div class="dropdown __dropdown_buttons">
                        <button class="btn btn-default dropdown-toggle" type="button"
                                id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                            Mode
                        </button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" id="mode_filter_type">
                            <a data-key="all" class="dropdown-item type-dropdown-item">All</a>
                            <a data-key="manual" class="dropdown-item type-dropdown-item">Manual</a>
                            <a data-key="auto" class="dropdown-item type-dropdown-item">Auto</a>
                        </div>
                        <form action="" method="get" class="mode-filter-type-form">
                            <input type="hidden" name="mode">
                        </form>
                    </div>
                </th>
                <th>Actions</th>
                <th>Mode</th>
        </tr>
        </thead>
        <tbody>
            <tr v-for="(o,i) in orders">
                <td>
                    <input type="checkbox" name="order_checkbox" class="order_checkbox" v-model="order_checkbox" :value="o.id" />
                </td>
                <td>@{{ o.id }}</td>
                <td>
                    @{{ o.username }}
                    <div class="badge badge-secondary" v-if="o.drip_feed_id !== null">Drip Feed</div>
                    <div class="badge badge-secondary" v-if="o.source ==='API'">API</div>
                </td>
                <td>@{{ o.charges }}</td>
                <td>
                    @{{ o.link }}
                        <span v-if="o.service_type ==='SEO'">
                            <a  class="service_type_tags" @click="modalVIsible('text_area_1', o )">Keywords</a>
                        </span>
                        <span v-else-if="o.service_type ==='SEO2'">
                            <a  class="service_type_tags" @click="modalVIsible('text_area_1', o )">Keywords</a>
                            <a  class="service_type_tags" @click="modalVIsible('additional_comment_owner_username_visible', o )">Email</a>
                        </span>
                        <span v-else-if="o.service_type ==='Custom Comments' || o.service_type ==='Custom Comments Package'">
                            <a  class="service_type_tags" @click="modalVIsible('text_area_1', o )">Comments</a>
                        </span>
                        <span v-else-if="o.service_type ==='Comment Likes' || o.service_type ==='Mentions Users Followers'">
                            <a  class="service_type_tags" @click="modalVIsible('additional_comment_owner_username_visible', o )">Username</a>
                        </span>
                        <span v-else-if="o.service_type ==='Mentions Custom List' || o.service_type ==='Mentions'">
                            <a  class="service_type_tags" @click="modalVIsible('text_area_1', o )">Username</a>
                        </span>
                        <span v-else-if="o.service_type ==='Mentions with Hashtags'">
                            <a  class="service_type_tags" @click="modalVIsible('text_area_1', o )">Username</a>
                            <a  class="service_type_tags" @click="modalVIsible('text_area_2', o )">Hastags</a>
                        </span>
                        <span v-else-if="o.service_type ==='Comment Replies'">
                            <a  class="service_type_tags" @click="modalVIsible('additional_comment_owner_username_visible', o )">Username</a>
                            <a  class="service_type_tags" @click="modalVIsible('text_area_1', o )">Comments</a>
                        </span>
                        <span v-else-if="o.service_type ==='Mentions Hashtag'">
                            <a  class="service_type_tags" @click="modalVIsible('additional_comment_owner_username_visible', o )">Hastags</a>
                        </span>
                        <span v-else-if="o.service_type ==='Mentions Media Likers'">
                            <a  class="service_type_tags" @click="modalVIsible('additional_comment_owner_username_visible', o )">Mediua URLs</a>
                        </span>
               
                </td>
                <td>@{{ o.start_counter }}</td>
                <td>@{{ o.quantity }}</td>
                <td>@{{ o.service_name }}</td> 
                <td class="status-value">@{{ o.status }}</td>
                <td>@{{ o.remains }}</td>
                <td>@{{ o.created_at }}</td>
                <td>@{{ o.mode }}</td>
                <td>
                    <div class="dropdown show goTopDropdown">
                        <a class="btn btn-secondary dropdown-toggle custom-dropdown-button" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Actions
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            @if($order->refill_order_status == 'pending')
                                <li>
                                    <form action="#" method="post">
                                        @csrf
                                        <input type="hidden" name="order_table_id"  value="{{$order->id}}" />
                                        <input type="hidden" name="order_id"  value="{{$order->order_id}}" />
                                        <input type="hidden" name="refill_order_status"  value="success" />
                                        <button class="dropdown-item type-dropdown-item">Success</button>
                                    </form>
                                </li>
                                <li>
                                    <form action="#" method="post">
                                        @csrf
                                    <input type="hidden" name="order_table_id"  value="{{$order->id}}" />
                                    <input type="hidden" name="order_id"  value="{{$order->order_id}}" />
                                    <input type="hidden" name="refill_order_status"  value="rejected" />
                                            <button class="dropdown-item type-dropdown-item">Reject</button>
                                    </form>
                                </li>
                            @else
                            <li>
                                <form action="#" method="post">
                                    @csrf
                                        <button type="button" class="dropdown-item type-dropdown-item">Details</button>
                                </form>
                            </li>
                            @endif
                        </ul>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
