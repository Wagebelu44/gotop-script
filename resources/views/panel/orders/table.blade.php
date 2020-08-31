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
                                    <a class="dropdown-item type-dropdown-item"> </a>
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
                            <a class="dropdown-item type-dropdown-item">
                                <span style="padding: 2px; border: 1px solid rgba(0,0,0,0.7); font-size:10px; font-weight: 700"></span>
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
            <tr>
                <td>
                    <input type="checkbox" name="service_checkbox" class="service_checkbox" v-model="service_checkbox" value="" />
                </td>
                <td>

                </td>
                <td></td>
                <td>

                </td>
                <td>

                </td>
                <td></td>
                <td></td>
                <td></td>
                <td class="status-value"></td>
                <td></td>
                <td></td>
                <td></td>
                    <td>
                        <div class="dropdown show goTopDropdown">
                            <a class="btn btn-secondary dropdown-toggle custom-dropdown-button" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Actions
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                    <li><a class="dropdown-item type-dropdown-item">Edit Link</a></li>
                                    <li><a class="dropdown-item type-dropdown-item">Set Start Count</a></li>
                                    <li><a class="dropdown-item type-dropdown-item">Set Remain</a></li>
                                    <li><a class="dropdown-item type-dropdown-item">Set Partial</a></li>
                                    <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle type-dropdown-item" href="#">Change status</a>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item type-dropdown-item">In Progress</a></li>
                                            <li><a class="dropdown-item type-dropdown-item">Processing</a></li>
                                            <li><a class="dropdown-item type-dropdown-item">Completed</a></li>
                                        </ul>
                                    </li>
                                    <li>
                                    <a class="dropdown-item type-dropdown-item">Cancel and refund</a></li>
                                        <li><a class="dropdown-item type-dropdown-item">Fail Detail</a></li>
                                        <li><a class="dropdown-item type-dropdown-item" href="">Resend Order</a></li>
                                        <li><a class="dropdown-item type-dropdown-item">Edit Link</a></li>
                                        <li><a class="dropdown-item type-dropdown-item" href="#">Order Detail</a></li>
                                        <li><a class="dropdown-item type-dropdown-item">Set Start Count</a></li>
                                        <li><a class="dropdown-item type-dropdown-item">Set Partial</a></li>
                                        <li class="dropdown-submenu"><a class="dropdown-item dropdown-toggle type-dropdown-item" href="#">Change status</a>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item type-dropdown-item">In Progress</a></li>
                                                <li><a class="dropdown-item type-dropdown-item">Processing</a></li>
                                                <li><a class="dropdown-item type-dropdown-item">Completed</a></li>
                                                    <li><a class="dropdown-item type-dropdown-item">Pending</a></li>
                                            </ul>
                                        </li>
                                        <li>
                                    <a class="dropdown-item type-dropdown-item">Cancel and refund</a></li>
                            </ul>
                        </div>
                    </td>
                    <td>
                        <div class="dropdown show goTopDropdown">
                            <a class="btn btn-secondary dropdown-toggle custom-dropdown-button" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Actions
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                    <li>
                                        <form action="" method="post">
                                            @csrf
                                            <input type="hidden" name="order_table_id"  value="" />
                                            <input type="hidden" name="order_id"  value="" />
                                            <input type="hidden" name="refill_order_status"  value="success" />
                                            <button class="dropdown-item type-dropdown-item">Success</button>
                                        </form>
                                    </li>
                                    <li>
                                        <form action="" method="post">
                                            @csrf
                                            <input type="hidden" name="order_table_id"  value="" />
                                            <input type="hidden" name="order_id"  value="" />
                                            <input type="hidden" name="refill_order_status"  value="rejected" />
                                            <button class="dropdown-item type-dropdown-item">Reject</button>
                                        </form>
                                    </li>
                                    <li>
                                        <form action="#" method="post">
                                            @csrf
                                            <button type="button" class="dropdown-item type-dropdown-item">Details</button>
                                        </form>
                                    </li>
                            </ul>
                        </div>
                    </td>
            </tr>
        </tbody>
    </table>
</div>
