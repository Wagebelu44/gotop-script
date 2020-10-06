<div class="tab-content table-responsive-xl">
    <table class="table">
        <thead>
        <tr>
            {{-- <th> <input type="checkbox" /> </th> --}}
            <th scope="col">ID</th>
            <th>User</th>
            <th>Total Charges</th>
            <th scope="col">Link</th>
            <th scope="col">Quantity</th>
            <th scope="col">
                Services
            </th>

            <th scope="col">Runs</th>
            <th scope="col" width="150">Interval</th>
            <th scope="col">Total Quantity</th>
            <th scope="col" width="100">Date</th>
            <th scope="col" width="120">Status</th>
            <th scope="col" width="120">Action</th>

        </tr>
        </thead>
        <tbody>
            <tr v-for="(dp, i) in driporders">
               {{--  <td> <input type="checkbox" /> </td> --}}
                <td> @{{ dp.id }} </td>
                <td> @{{ dp.user_name }} </td>
                <td> @{{ dp.total_charges }} </td>
                <td>
                    <a :href="dp.orders_link" target="_blank" class="link"><i class="fa fa-link"></i> @{{ dp.orders_link }} </a>
                </td>
                <td> @{{ dp.service_quantity }} </td>
                <td> @{{ dp.service_name }} </td>
                <td> @{{ dp.runOrders }} / @{{ dp.runs }} </td>
                <td> @{{ dp.interval }} </td>
                <td>@{{ dp.total_quantity }} </td>
                <td>@{{ dp.created_at }}</td>
                <td class="status-value">@{{ dp.status }}</td>
                <td>
                    <div class="dropdown show goTopDropdown">
                        <a class="btn btn-secondary dropdown-toggle custom-dropdown-button" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Actions
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            @can('change drip-feed status')
                            <li class="dropdown-submenu"><a class="dropdown-item type-dropdown-item dropdown-toggle" href="#">Change status</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item type-dropdown-item" @click="changeStatus('completed', dp.id)">Completed</a></li>
                                </ul>
                            </li>
                            @endcan
                            @can('cancel and refund drip-feed')
                                <li><a class="dropdown-item type-dropdown-item" @click="changeStatus('canceled', dp.id)">Cancel and refund</a></li>
                            @endcan
                        </ul>
                    </div>
                </td>

            </tr>
        </tbody>
    </table>
</div>
