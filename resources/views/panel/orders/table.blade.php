<div class="tab-content table-responsive-xl">
    <div class="d-flex top-caret-bar">
        <div class="d-flex service-checkbox-action bg-danger" v-if="order_checkbox.length>0">
            <div>
                <span style="color:#fff; padding: 0px 5px">Order Selected <span> @{{order_checkbox.length}} </span> </span>
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
                <th> <input type="checkbox" v-model="checkAllOrders" /></th>
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
                                    <a class="dropdown-item type-dropdown-item" v-for="(u, i) in users"> @{{ u.username }} </a>
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
                            <a class="dropdown-item type-dropdown-item" v-for="(s, i) in services">
                                <span style="padding: 2px; border: 1px solid rgba(0,0,0,0.7); font-size:10px; font-weight: 700">
                                    @{{ s.id }}
                                </span>
                                @{{ s.name }}
                                (@{{ s.totalOrder??0 }})
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
                            <a data-key="all" class="dropdown-item type-dropdown-item">All <span v-if="order_mode_count">@{{ order_mode_count.manual +   order_mode_count.auto }}</span></a>
                            <a data-key="manual" class="dropdown-item type-dropdown-item">Manual <span v-if="order_mode_count">@{{ order_mode_count.manual }}</span>  </a>
                            <a data-key="auto" class="dropdown-item type-dropdown-item">Auto <span v-if="order_mode_count">@{{ order_mode_count.auto }} </span> </a>
                        </div>
                        <form action="" method="get" class="mode-filter-type-form">
                            <input type="hidden" name="mode">
                        </form>
                    </div>
                </th>
                <th>Actions</th>
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
                <td v-if="order_page == 'order'">
                    <div class="dropdown show goTopDropdown">
                        <a class="btn btn-secondary dropdown-toggle custom-dropdown-button" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Actions
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <div v-if='o.mode!=="auto" && o.status !=="cancelled"'>
                                <li><a class="dropdown-item type-dropdown-item" @click="popModal('link', o.link, o.id)">Edit Link</a></li>
                                <li><a class="dropdown-item type-dropdown-item" @click="popModal('start_count',o.start_counter, o.id)">Set Start Count</a></li>
                                <li><a class="dropdown-item type-dropdown-item" @click="popModal('remain', o.remains, o.id)">Set Remain</a></li>
                                <li><a class="dropdown-item type-dropdown-item" @click="popModal('partial', o.remains, o.id)">Set Partial</a></li>
                                <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle type-dropdown-item" href="#">Change status</a>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item type-dropdown-item" @click="changeStatus('inprogress',o.id)">In Progress</a></li>
                                        <li><a class="dropdown-item type-dropdown-item" @click="changeStatus('processing',o.id)">Processing</a></li>
                                        <li><a class="dropdown-item type-dropdown-item" @click="changeStatus('completed',o.id)">Completed</a></li>
                                    </ul>
                                </li>
                                <li><a class="dropdown-item type-dropdown-item"  @click="changeStatus('cancel_refund',o.id)">Cancel and refund</a></li>
                            </div>
                            <div v-else>
                                <div v-if="actionConditionalB(o)">
                                    <div v-if="o.status == 'failed'">
                                        <li><a class="dropdown-item type-dropdown-item" onclick="#">Fail Detail</a></li>
                                        <li><a class="dropdown-item type-dropdown-item" href="#">Resend Order</a></li>
                                        <li><a class="dropdown-item type-dropdown-item" onclick="#">Edit Link</a></li>
                                    </div>
                                    <div v-else>
                                        <li><a class="dropdown-item type-dropdown-item" href="#">Order Detail</a></li>
                                    </div>
                                    <div v-if="o.drip_feed_id == null">
                                        <li><a class="dropdown-item type-dropdown-item" @click="popModal('start_count', o.start_counter, o.id)">Set Start Count</a></li>
                                    </div>
                                    <div v-if="o.status !=='partial' && o.status !=='cancelled'">
                                        <li><a class="dropdown-item type-dropdown-item" @click="popModal('partial', o.remains, o.id)">Set Partial</a></li>
                                    </div>
                                    <div v-if="actionConditionalC(o)">
                                        <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle type-dropdown-item" href="#">Change status</a>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item type-dropdown-item" @click="changeStatus('inprogress',o.id)">In Progress</a></li>
                                                <li><a class="dropdown-item type-dropdown-item" @click="changeStatus('processing',o.id)">Processing</a></li>
                                                <li><a class="dropdown-item type-dropdown-item" @click="changeStatus('completed',o.id)">Completed</a></li>
                                                <div v-if='o.status == "cancelled"'>
                                                    <li><a class="dropdown-item type-dropdown-item" @click="changeStatus('pending',o.id)">Pending</a></li>
                                                </div>
                                            </ul>
                                        </li>
                                    </div>
                                    <div v-if="o.status !== 'cancelled'">
                                        <li><a class="dropdown-item type-dropdown-item" @click="changeStatus('cancel_refund',o.id)">Cancel and refund</a></li>
                                    </div>
                                </div>
                            </div>
                        </ul>
                    </div>
                </td>
                <td v-else-if="order_page == 'task'">
                    <div class="dropdown show goTopDropdown">
                        <a class="btn btn-secondary dropdown-toggle custom-dropdown-button" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Actions
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                <div v-if="o.refill_order_status === 'pending'">
                                    <li>
                                        <form action="{{route('admin.task.change.status')}}" method="post">
                                            @csrf
                                            <input type="hidden" name="order_table_id"  :value="o.id" />
                                            <input type="hidden" name="order_id"  :value="o.id" />
                                            <input type="hidden" name="refill_order_status"  value="success" />
                                            <button class="dropdown-item type-dropdown-item">Success</button>
                                        </form>
                                    </li>
                                    <li>
                                        <form action="{{route('admin.task.change.status')}}" method="post">
                                            @csrf
                                        <input type="hidden" name="order_table_id"  :value="o.id" />
                                        <input type="hidden" name="order_id"  :value="o.id" />
                                        <input type="hidden" name="refill_order_status"  value="rejected" />
                                                <button class="dropdown-item type-dropdown-item">Reject</button>
                                        </form>
                                    </li>
                                </div>
                                <div v-else>
                                    <li>
                                        <form action="#" method="post">
                                            @csrf
                                                <button type="button" class="dropdown-item type-dropdown-item">Details</button>
                                        </form>
                                    </li>
                                </div>
                            
                        </ul>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
