<div class="tab-content table-responsive-xl" id="orders_tble">
    <div class="d-flex top-caret-bar">
        <div class="d-flex service-checkbox-action bg-danger" v-if="service_checkbox.length>0">
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
                     <li><a class="dropdown-item">Resend Orders</a></li>
                    <li><a class="dropdown-item">Cancal and refund</a></li>
                    <li><a class="dropdown-item">Pending</a></li>
                    <li><a class="dropdown-item">In Progress</a></li>
                    <li><a class="dropdown-item">Processing</a></li>
                    <li><a class="dropdown-item">Completed</a></li>

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
                    <input type="checkbox" name="service_checkbox" class="service_checkbox" v-model="service_checkbox" value="" />
                </td>
                <td>@{{ o.id }}</td>
                <td>@{{ o.username }}</td>
                <td>@{{ o.charges }}</td>
                <td>@{{ o.link }}</td>
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
                               
                                    <li v-if="actionConditionalA(o)"><a class="dropdown-item type-dropdown-item" onclick="popModal('link',o.link, o.id)">Edit Link</a></li>
                                    <li v-if="actionConditionalA(o)"><a class="dropdown-item type-dropdown-item" onclick="popModal('start_count', o.start_counter, o.id)">Set Start Count</a></li>
                                    <li v-if="actionConditionalA(o)"><a class="dropdown-item type-dropdown-item" onclick="popModal('remain', o.remains, o.id)">Set Remain</a></li>
                                    <li v-if="actionConditionalA(o)"><a class="dropdown-item type-dropdown-item" onclick="popModal('partial',  o.remains, o.id)">Set Partial</a></li>
                                    <li v-if="actionConditionalA(o)" class="dropdown-submenu"><a class="dropdown-item dropdown-toggle type-dropdown-item" href="#">Change status</a>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item type-dropdown-item" onclick="changeStatus('inprogress', o.id)">In Progress</a></li>
                                            <li><a class="dropdown-item type-dropdown-item" onclick="changeStatus('processing', o.id)">Processing</a></li>
                                            <li><a class="dropdown-item type-dropdown-item" onclick="changeStatus('completed', o.id)">Completed</a></li>
                                        </ul>
                                    </li>
                                    <li v-if="actionConditionalA(o)"><a class="dropdown-item type-dropdown-item"  onclick="changeStatus('cancel_refund', o.id)">Cancel and refund</a></li>
                        
                                    <li v-if="actionConditionalB(o) && o.status==='failed'"><a class="dropdown-item type-dropdown-item" onclick="#">Fail Detail</a></li>
                                    <li v-if="actionConditionalB(o) && o.status==='failed'"><a class="dropdown-item type-dropdown-item" href="#">Resend Order</a></li>
                                    <li v-if="actionConditionalB(o) && o.status==='failed'"><a class="dropdown-item type-dropdown-item" onclick="#">Edit Link</a></li>
                                
                                    <li v-if="actionConditionalB(o) && o.status!=='failed'"><a class="dropdown-item type-dropdown-item" href="#">Order Detail</a></li>
                                
                                    <li v-if="o.drip_feed_id === null"><a class="dropdown-item type-dropdown-item" onclick="popModal('start_count', o.start_counter, o.id)">Set Start Count</a></li>
                                    
                                    <li v-if="actionConditionalB(o) && (o.status!=='partial' && o.status!=='partial')"><a class="dropdown-item type-dropdown-item" onclick="popModal('partial',  o.remains, o.id)">Set Partial</a></li>
                                
                                
                                    <li v-if="actionConditionalC(o)" class="dropdown-submenu"><a class="dropdown-item dropdown-toggle type-dropdown-item" href="#">Change status</a>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item type-dropdown-item" onclick="changeStatus('inprogress', o.id)">In Progress</a></li>
                                            <li><a class="dropdown-item type-dropdown-item" onclick="changeStatus('processing', o.id)">Processing</a></li>
                                            <li><a class="dropdown-item type-dropdown-item" onclick="changeStatus('completed', o.id)">Completed</a></li>
                                            
                                            <li v-if="actionConditionalC(o) && o.status==='cancelled'"><a class="dropdown-item type-dropdown-item" onclick="changeStatus('pending', o.id)">Pending</a></li>
                                            
                                        </ul>
                                    </li>
                                      
                            </ul>
                        </div>
                    </td>
            </tr>
        </tbody>
    </table>
</div>
