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
                <li><a class="dropdown-item" @click="bulkStatusChange('failed_resend')">Active</a></li>
                <li><a class="dropdown-item" @click="bulkStatusChange('cancel_refund')">Paused</a></li>
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
             <th>User</th>
             <th>Username</th>
             <th scope="col" width="150">Quantity</th>
             <th scope="col" width="150">Posts</th>
             <th scope="col" width="150">Delay</th>
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
                Service
             </th>
             <th scope="col" width="120">Status</th>
             <th scope="col" width="100">Created</th>
             <th scope="col" width="100">Updated</th>
          </tr>
       </thead>
       <tbody>
          <tr v-for="(o,i) in orders">
             <td>
                <input type="checkbox" name="order_checkbox" class="order_checkbox" v-model="order_checkbox" :value="o.id" />
             </td>
             <td>@{{ o.id }}</td>
             <td>
                @{{ o.username }}</td>
             <td>@{{ o.charges }}</td>
             <td>@{{ o.link }}</td>
             <td>@{{ o.start_counter }}</td>
             <td>@{{ o.quantity }}</td>
             <td>@{{ o.service_name }}</td>
             <td class="status-value">@{{ o.status }}</td>
             <td>@{{ o.remains }}</td>
             <td>@{{ o.created_at }}</td>
             <td>@{{ o.mode }}</td>
          </tr>
       </tbody>
    </table>
 </div>