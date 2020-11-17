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
                @can('resend order')
                  <li><a class="dropdown-item" @click="bulkStatusChange('failed_resend')">Resend Orders</a></li>
                @endcan
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
             @can('see order user')
               <th>
                  <div class="dropdown  __dropdown_buttons">
                     <button class="btn btn-default dropdown-toggle" type="button"
                        id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
                     Users
                     </button>
                     <div class="dropdown-menu service-dropdown" aria-labelledby="dropdownMenuButton">
                        <div id="user_filter_type">
                           <a class="dropdown-item type-dropdown-item"> All (@{{ all_orders_count ?? 0 }} )</a>
                           <a class="dropdown-item type-dropdown-item"> None (@{{ none_order_count ?? 0 }} ) </a>
                           <a class="dropdown-item type-dropdown-item"> API (@{{ api_order_count ?? 0 }} )</a>
                           <a class="dropdown-item type-dropdown-item"> Mass (@{{ mass_order_count ?? 0 }} )</a>
                           <a class="dropdown-item type-dropdown-item"> Subscription (@{{ subscription_order_count ?? 0 }} )</a>
                           <a class="dropdown-item type-dropdown-item"> Drip-feed (@{{ drip_feed_order_count ?? 0 }} )</a>
                           <a class="dropdown-item type-dropdown-item"> Refiller (@{{ none_order_count ?? 0 }} )</a>
                        </div>
                     </div>
                  </div>
               </th>
             @endcan
             @can('see order charge')
               <th scope="col">Charge</th>
             @endcan
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
                   <div class="dropdown-menu service-dropdown" aria-labelledby="dropdownMenuButton">
                      <a class="dropdown-item type-dropdown-item" @click="statusService(s.id)" v-for="(s, i) in services">
                      <span style="padding: 2px; border: 1px solid rgba(0,0,0,0.7); font-size:10px; font-weight: 700">
                      @{{ s.id }}
                      </span>
                      @{{ s.name }}
                      (@{{ s.totalOrder??0 }})
                      </a>
                   </div>
                </div>
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
                   <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                      <a  @click="statusMode('all')" class="dropdown-item type-dropdown-item">All <span v-if="order_mode_count">@{{ order_mode_count.manual +   order_mode_count.auto }}</span></a>
                      <a  @click="statusMode('manual')" class="dropdown-item type-dropdown-item">Manual <span v-if="order_mode_count">@{{ order_mode_count.manual }}</span>  </a>
                      <a  @click="statusMode('auto')" class="dropdown-item type-dropdown-item">Auto <span v-if="order_mode_count">@{{ order_mode_count.auto }} </span> </a>
                   </div>
                </div>
             </th>
             <th>Actions</th>
          </tr>
       </thead>
       <tbody>
          <tr v-for="(o,i) in orders" :class="{ unseenOrder: o.admin_seen === 'Unseen'}">
             <td>
                <input type="checkbox" name="order_checkbox" class="order_checkbox" v-model="order_checkbox" :value="o.id" />
             </td>
             <td>@{{ o.id }}</td>
             @can('see order user')
               <td>
                  @{{ o.username }}
                  <div class="badge badge-secondary" v-if="o.drip_feed_id !== null">Drip Feed</div>
                  <div class="badge badge-secondary" v-if="o.source ==='API'">API</div>
               </td>
             @endcan
             @can('see order charge')
               <td> 
                  <span v-show="o.status !== 'cancelled'">@{{ o.charges }}</span>  
                  <span v-show="o.status === 'cancelled'">0</span>  
               </td>
             @endcan
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
             <td class="status-value text-capitalize" >@{{ o.status }}</td>
             <td>@{{ o.remains }}</td>
             <td>@{{ o.created_at }}</td>
             <td class="text-capitalize">@{{ o.mode }}</td>
             <td v-if="order_page == 'order'">
                <div class="dropdown show goTopDropdown">
                   <a class="btn btn-secondary dropdown-toggle custom-dropdown-button" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                   Actions
                   </a>
                   <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                      <div v-if='o.mode!=="auto" && o.status !=="cancelled"'>
                         @can('edit order link')
                           <li><a class="dropdown-item type-dropdown-item" @click="popModal('link', o.link, o.id)">Edit Link</a></li>
                         @endcan
                         @can('set start count order')
                         <li><a class="dropdown-item type-dropdown-item" @click="popModal('start_count', o.start_counter, o.id)">Set Start Count</a></li>
                         @endcan
                         @can('set remains order')
                           <li><a class="dropdown-item type-dropdown-item" @click="popModal('remain', o.remains, o.id)">Set Remain</a></li>
                         @endcan
                         <li><a class="dropdown-item type-dropdown-item" @click="popModal('partial', o.remains, o.id)">Set Partial</a></li>
                         @can('change order status')
                           <li class="dropdown-submenu">
                              <a class="dropdown-item dropdown-toggle type-dropdown-item" href="#">Change status</a>
                              <ul class="dropdown-menu">
                                 <li><a class="dropdown-item type-dropdown-item" @click="changeStatus('inprogress',o.id)">In Progress</a></li>
                                 <li><a class="dropdown-item type-dropdown-item" @click="changeStatus('processing',o.id)">Processing</a></li>
                                 <li><a class="dropdown-item type-dropdown-item" @click="changeStatus('completed',o.id)">Completed</a></li>
                              </ul>
                           </li>
                         @endcan
                         @can('cancel and refund order')
                           <li><a class="dropdown-item type-dropdown-item"  @click="changeStatus('cancel_refund',o.id)">Cancel and refund</a></li>
                         @endcan
                        </div>
                      <div v-else>
                         <div v-if="actionConditionalB(o)">
                            <div v-if="o.status == 'failed' || o.status == 'cancelled'">
                               <li><a class="dropdown-item type-dropdown-item" @click="orderDetailModal(o)">Order Detail</a></li>
                               @can('resend order')
                                 <li><a class="dropdown-item type-dropdown-item" @click="resendOrder(o)">Resend Order</a></li>
                               @endcan
                               @can('edit order link')
                                 <li><a class="dropdown-item type-dropdown-item" @click="popModal('link', o.link, o.id)">Edit Link</a></li>
                               @endcan
                            </div>
                            <div v-else>
                               <li><a class="dropdown-item type-dropdown-item"  @click="orderDetailModal(o)">Order Detail</a></li>
                            </div>
                            @can('set start count order')
                              <div v-if="o.drip_feed_id == null">
                                 <li><a class="dropdown-item type-dropdown-item" @click="popModal('start_count', o.start_counter, o.id)">Set Start Count</a></li>
                              </div>
                            @endcan
                            @can('set partial order')
                              <div v-if="o.status !=='partial' && o.status !=='cancelled'">
                                 <li><a class="dropdown-item type-dropdown-item" @click="popModal('partial', o.remains, o.id)">Set Partial</a></li>
                              </div>
                            @endcan
                            @can('change order status')
                              <div v-if="actionConditionalC(o)">
                                 <li class="dropdown-submenu">
                                    <a class="dropdown-item dropdown-toggle type-dropdown-item" href="#">Change status</a>
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
                            @endcan
                            @can('cancel and refund order')
                                 <li><a class="dropdown-item type-dropdown-item" @click="changeStatus('cancel_refund',o.id)">Cancel and refund</a></li>
                            @endcan
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