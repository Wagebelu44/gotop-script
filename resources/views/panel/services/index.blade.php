@extends('layouts.panel')
@section('styles')
<link rel="stylesheet" href="https://unpkg.com/vue-select@3.10.3/dist/vue-select.css">
<style>
    .style-chooser .vs__search::placeholder,
    .style-chooser .vs__dropdown-toggle,
    .style-chooser .vs__dropdown-menu {
        background: #ffffff;
        border: 1px solid #d3d3d3;
        color: #000;
    }
    .style-chooser .vs__dropdown-menu li{
        border: 1px solid #d3d3d331;
        padding: 8px 5px;
    }
    .style-chooser .vs__dropdown-menu li:hover{
        background-color: #337AB7;
    }

    .style-chooser .vs__clear,
    .style-chooser .vs__open-indicator {
        fill: #394066;
    }
    .style-chooser .vs__selected-options{
        flex-wrap: nowrap;
    }
    .switch-custom .switch{position:relative;display:inline-block;width:34px;height:18px;margin-bottom:0;padding-bottom:0}
    .switch-custom .switch input{display:none}
    .switch-custom .slider{position:absolute;cursor:pointer;top:0;left:0;right:0;bottom:0;background-color:#e6e6e6;-webkit-transition:.4s;transition:.4s}
    .switch-custom .slider:before{position:absolute;content:"";height:14px;width:14px;bottom:2px;left:2px;background-color:#fff;-webkit-transition:.4s;transition:.4s;border-radius: 10px}
    .switch-custom input:checked+.slider{background-color:#3479b7}
    .switch-custom input:focus+.slider{box-shadow:0 0 1px #3479b7}
    .switch-custom input:checked+.slider:before{-webkit-transform:translateX(16px);-ms-transform:translateX(16px);transform:translateX(16px)}
    .switch-custom input:disabled+.slider{opacity:.3;cursor:no-drop}
    .switch-custom .slider.round{border-radius:34px}
    .switch-custom__table .switch{vertical-align:-6px}
    .round { color: #fff; width: 34px !important;height: 18px !important;display: inline-block;text-align: center;line-height: 28px !important;}
    .dropdown-submenu {
        position: relative;
    }
    .dropdown-submenu a::after {
        transform: rotate(270deg);
        position: absolute;
        top: 18px;
    }
    .dropdown-submenu .dropdown-menu {
        top: -12px;
        left: 100%;
        margin-left: .1rem;
        margin-right: .1rem;
    }
    .sub-price{
        font-size: 14px;
        color: rgba(0, 0, 0, 0.31);
    }
    .form-element-group{
         border: 1px solid #d3d3d3;
         position: relative;
    }
    .form-element-group div:first-child{
        font-size: 10px;
        position: absolute;
        left: 6px;
        top: 2px;
        color: #989898
    }
    .form-element-group div:last-child{
        font-size: 10px;
        position: absolute;
        right: 6px;
        top: 2px;
        color: #989898
    }
    .form-element-group input{
        width: 100px;
        border: none !important;
        border-radius: 4px;
        outline: none !important;
        padding: 12px 5px 2px;
    }
    .custom-rate-class{
        color: #000;
        font-size: 10px;
        border: 1px solid #d4d4d4;
        background: #fff;
        padding: 0px 10px;
        font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif; 
        flex-basis: 50%;
        height: 20px;
    }
    .custom-rate-class input{
        outline: none !important;
        border: none !important;
        background: transparent !important;
        flex-basis: 90%;
        padding: 0 !important;
    }
    .custom-rate-class span{
        flex-basis: 10%;
    }
    .custom-rate-class input.overlay-class, .custom-rate-class.overlay-class{
        background: #d3d3d3 !important;
        height: 100%;
        width: 100%;
    }
</style>
@endsection
@section('content')
    <div class="container-fluid p-0 ml-0  mr-0 all-mt-30">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body pt-0 pl-1 pr-1">
                        <form  id="service_type_filter_form" action=""  method="get" >
                            <input type="hidden" name="serviceTypefilter">
                        </form>
                        <form action="" method="get" id="status_type_filter_form">
                            <input type="hidden" name="status">
                        </form>
                        <div class="__table-container" id="serviceApp">
                            <div class="overlay-loader" v-if="loader">
                                <div class="loader-holder">
                                    <img src="{{asset('loader.gif')}}" alt="">
                                </div>
                            </div>
                            <!-- Modal -->
                            <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog"
                                 aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered __modal_dialog_custom" role="document">
                                    <div class="modal-content">
                                        <form method="post"
                                              id="category_form"
                                              @submit.prevent="submitCategoryForm">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLongTitle"> <strong>Create Category</strong> </h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for=""><strong>Category Name</strong></label>
                                                            <input type="text" v-model="category.name" name="name" class="form-control custom-form-control" placeholder="Name">
                                                            <span class="text-danger" v-if="errors.category && errors.category['name']">@{{ errors.category['name'][0] }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary custom-button"><i class="fa fa-check"></i> Save</button>
                                                <button type="button" class="btn btn-danger custom-button" data-dismiss="modal">Close</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            {{-- end category modal --}}

                            {{--subscription Modal--}}
                            <div class="modal fade" id="subscriptionModal" tabindex="-1" role="dialog"
                            aria-labelledby="serviceModalTitle" aria-hidden="true">
                           <div class="modal-dialog modal-dialog-centered __modal_dialog_custom" role="document">
                               <div class="modal-content">
                                   <form method="post"
                                             id="subscription_form"
                                             @submit="submitServiceForm">
                                       <div class="modal-header">
                                           <h5 class="modal-title" id="exampleModalLongTitle">
                                               <strong>Add Subscription</strong>
                                           </h5>
                                           <button type="button" class="close" @click="closesubscriptionModal" aria-label="Close">
                                               <span aria-hidden="true">&times;</span>
                                           </button>
                                       </div>
                                       <div class="modal-body">
                                           <div class="row">
                                               <div class="col-md-12">
                                                   <div class="form-group">
                                                       <div class="controls">
                                                           <label for="name"> <strong>Service Name<span class="badge badge-pill badge-dark"> English </span> </strong>  </label>
                                                           <input type="text" name="name" class="form-control"
                                                                   placeholder="Service Name" v-model="services.form_fields.name">
                                                                   <span class="text-danger" v-if="errorFilter('name')!==''"> @{{ errorFilter('name') }} </span>
                                                       </div>
                                                   </div>
                                               </div>
                                               <div class="col-md-12">
                                                   <div class="form-group">
                                                       <div class="controls">
                                                           <label for="name"> <strong>Category</strong>  </label>
                                                           <select name="category_id" class="form-control"
                                                                   v-model="services.form_fields.category_id">
                                                               <option value="" selected>Choose category</option>
                                                               <option v-for="(c,i) in category_services" :value="c.id">@{{c.name}}</option>
                                                           </select>
                                                           <span class="text-danger" v-if="errorFilter('category_id')!==''"> @{{ errorFilter('category_id') }} </span>
                                                       </div>
                                                   </div>
                                               </div>
                                               <div class="col-md-12">
                                                   <div class="form-group">
                                                       <label> <strong>Subscription</strong> </label>
                                                       <select name="subscription_type"  id="subscription_type" class="form-control" >
                                                           <option value="Instagram Auto Likes">Instagram Auto Likes</option>
                                                           <option value="Instagram Auto Views">Instagram Auto Views</option>
                                                           <option value="Instagram Auto Comments">Instagram Auto Comments</option>
                                                           <option value="Twitter Auto Likes">Twitter Auto Likes</option>
                                                           <option value="Twitter Auto Retweets">Twitter Auto Retweets</option>
                                                           <option value="Twitter Auto Views">Twitter Auto Views</option>
                                                           <option value="Youtube Auto Views">Youtube Auto Views</option>
                                                           <option value="Facebook Auto Likes (for pages only)">Facebook Auto Likes (for pages only)</option>
                                                       </select>
                                                   </div>
                                               </div>
               
                                           </div>
                                           <div class="row">
                                               <div class="col-12">
                                                   <div class="card">
                                                       <div class="card-header">
                                                           <div class="form-group">
                                                               <label for="name"> <strong>Mode</strong>  </label>
                                                               <select name="mode" class="form-control"
                                                                       v-model="service_mode">
                                                                   <option>Auto</option>
                                                               </select>
                                                           </div>
                                                           <div class="form-group" v-if="services.visibility.provider">
                                                               <label for=""><strong>Provider</strong></label>
                                                               <v-select :options="providers_lists"
                                                                           v-model="services.form_fields.provider_id"
                                                                           class="style-chooser"
                                                                           :reduce="domain => domain.id" label="domain"></v-select>
                                                               <input type="hidden" name="provider_id" v-model="services.form_fields.provider_id">
                                                               <span class="text-danger" v-if="errorFilter('provider_id')!==''"> @{{ errorFilter('provider_id') }} </span>
                                                           </div>
                                                           <span style="color: red" v-if="service_mode == 'Auto' && services.validations.provider_service_not_found !==''">@{{services.validations.provider_service_not_found}}</span>
                                                           <div class="form-group" v-if="services.visibility.service_id_by_provider">
                                                           <label for=""><strong>Services </strong></label>
                                                               <v-select :options="provider_subscription_computed"
                                                                           class="style-chooser"
                                                                           v-model="services.form_fields.provider_service_id"
                                                                           :reduce="services => services.id" label="display_name"></v-select>
                                                               <input type="hidden" name="provider_service_id" v-model="services.form_fields.provider_service_id">
                                                           </div>
                                                           <input type="hidden" name="service_type"  v-if="!services.visibility.service_type" v-model="service_type_selected">
                                                           <input type="hidden" name="provider_selected_service_data" :value="JSON.stringify(provider_service_selected)">
                                                       </div>
                                                   </div>
                                               </div>
                                           </div>
                                           <label for=""> <strong>Rate Per 1000</strong></label>
                                           <div class="row auto-mode-input-field" v-if="service_mode == 'Auto'  && services.form_fields.provider_service_id != null">
                                               <div class="col-11 d-flex" v-if="services.visibility.auto_per_rate">
                                                   <div class="form-group">
                                                       <label for=""> Fixed 1.0</label>
                                                       <input type="number" step="any" v-model="auto_price_plus" class="form-control" placeholder="0" aria-describedby="helpId">
                                                   </div>
                                                   <div class="form-group">
                                                       <label for=""> Percent, %</label>
                                                       <input type="number" step="any"  v-model="auto_price_percent" class="form-control" placeholder="0" aria-describedby="helpId">
                                                   </div>
                                                   <div class="form-group">
                                                       <div class="price_box">
                                                           <span>@{{services.form_fields.price}}</span>
                                                           <span>@{{services.form_fields.price_original}} USD</span>
                                                           <input type="hidden" name="price" v-model="services.form_fields.price">
                                                       </div>
                                                   </div>
                                                   <span class="text-danger" v-if="errorFilter('price')!==''"> @{{ errorFilter('price') }} </span>
                                               </div>
                                              
                                               <div class="col-11" v-else>
                                                   <div class="form-group">
                                                       <input type="number" step="any" class="form-control" name="price" v-model="services.form_fields.price">
                                                       <label for="">@{{services.form_fields.price_original}} USD</label>
                                                   </div>
                                               </div>
                                               <div class="col-1">
                                                   <div class="switch-custom switch-custom__table">
                                                       <label class="switch">
                                                           <input type="checkbox" class="toggle-page-visibility"  v-model="auto_per_rate_toggler" >
                                                           <span class="slider round"></span>
                                                       </label>
                                                   </div>
                                               </div>
                                           </div>
                                           <div class="row" v-else>
                                               <div class="col-12">
                                                   <div class="form-group">
                                                       <input type="number" step="any" name="price" class="form-control"
                                                               v-model="services.form_fields.price" placeholder="Price">
                                                               <span class="text-danger" v-if="errorFilter('price')!==''"> @{{ errorFilter('price') }} </span>
                                                   </div>
                                                   <div class="price_validation_messages" v-if='services.validations.price.visibility' >
                                                       <p class="text-danger">@{{services.validations.price.msg}}</p>
                                                   </div>
                                               </div>
                                           </div>
               
                                           <div class="row mt-3 mb-3">
                                               <div class="col-md-6">
                                                   <label for=""><strong>Min Order</strong></label>
                                                   <div class="row order_limit" v-if="service_mode == 'Auto'  && services.form_fields.provider_service_id != null">
                                                       <div class="col-5">
                                                           <div class="form-group">
                                                               <input type="number" step="any" class="form-control" name="min_quantity" v-model='services.form_fields.min_quantity'>
                                                               <label for="">@{{services.form_fields.auto_min_quantity}} USD</label>
                                                               <span class="text-danger" v-if="errorFilter('min_quantity')!==''"> @{{ errorFilter('min_quantity') }} </span>
                                                           </div>
                                                           <div class="overlay" v-if="auto_min_rate_toggler"></div>
                                                       </div>
                                                       <div class="col-1">
                                                           <div class="switch-custom switch-custom__table">
                                                               <label class="switch">
                                                                   <input type="checkbox" class="toggle-page-visibility"  v-model="auto_min_rate_toggler" >
                                                                   <span class="slider round"></span>
                                                               </label>
                                                           </div>
                                                       </div>
                                                   </div>
                                                   <div  v-else >
                                                       <div class="form-group">
                                                           <div class="controls">
                                                               <input type="number" step="any" name="min_quantity"
                                                                       class="form-control"
                                                                       placeholder="Min quantity"
                                                                       v-model="services.form_fields.min_quantity"
                                                                       :class="{disabled :services.disable.min}"
                                                                       :disabled="services.disable.min"
                                                               />
                                                               <span class="text-danger" v-if="errorFilter('price')!==''"> @{{ errorFilter('price') }} </span>
                                                           </div>
                                                       </div>
                                                     
                                                   </div>
               
               
                                               </div>
               
                                               <div class="col-md-6">
                                                   <label for=""><strong>Max Order</strong></label>
                                                   <div class="row order_limit" v-if="service_mode == 'Auto'  && services.form_fields.provider_service_id != null">
                                                       <div class="col-5">
                                                           <div class="form-group">
                                                               <input type="number"  class="form-control" v-model='services.form_fields.max_quantity' name="max_quantity">
                                                               <label for="">@{{services.form_fields.auto_max_quantity}} USD</label>
                                                               <span class="text-danger" v-if="errorFilter('max_quantity')!==''"> @{{ errorFilter('max_quantity') }} </span>
                                                           </div>
                                                           <div class="overlay" v-if="auto_max_rate_toggler"></div>
                                                       </div>
                                                       <div class="col-1">
                                                           <div class="switch-custom switch-custom__table">
                                                               <label class="switch">
                                                                   <input type="checkbox" class="toggle-page-visibility"  v-model="auto_max_rate_toggler" >
                                                                   <span class="slider round"></span>
                                                               </label>
                                                           </div>
                                                       </div>
                                                   </div>
                                                   <div v-else>
                                                       <div class="form-group">
                                                           <div class="controls">
               
                                                               <input type="number"  name="max_quantity"
                                                                       class="form-control"
                                                                       placeholder="Max quantity"
                                                                       v-model="services.form_fields.max_quantity"
                                                                       :class="{disabled :services.disable.max}"
                                                                       :disabled="services.disable.max"
                                                               />
                                                               <span class="text-danger" v-if="errorFilter('max_quantity')!==''"> @{{ errorFilter('max_quantity') }} </span>
                                                           </div>
                                                       </div>
                                                   </div>
               
                                               </div>
                                           </div>
               
                                           <div class="row mb-3" v-if="service_mode == 'Auto'">
                                               <div class="col-12">
                                                   <div class="switch-custom switch-custom__table">
                                                       <label class="switch">
                                                           <input type="checkbox" class="toggle-page-visibility" name="provider_sync_status"  v-model="provider_sync_status" >
                                                           <span class="slider round"></span>
                                                       </label>
                                                       <p class="d-inline"><strong> Sync service status with provider </strong></p>
                                                   </div>
                                               </div>
                                           </div>
                                           <div class="row" v-if="services.visibility.overflow">
                                               <div class="col-12">
                                                   <div class="form-group">
                                                       <label for=""> <strong>Overflow</strong>  </label>
                                                       <input type="number" step="any"
                                                               v-model="services.form_fields.auto_overflow"
                                                               class="form-control" name="auto_overflow">
                                                   </div>
                                               </div>
                                           </div>
               
                                           <div v-if="errors.common" class="error-display">
                                            <p class="error-display-item"> @{{ errors.common }}</p>
                                        </div>
                                       </div>
                                       <div class="modal-footer">
                                           <button type="submit" class="btn btn-primary custom-button"><i class="fa fa-check"></i> Save</button>
                                           <button type="button" class="btn btn-danger custom-button" @click="closesubscriptionModal">Close</button>
                                       </div>
                                   </form>
                               </div>
                           </div>
                       </div>
                            {{--        Service Modal--}}
                            <div class="modal fade" id="serviceAddModal" tabindex="-1" role="dialog"
                                 aria-labelledby="serviceModalTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered __modal_dialog_custom" role="document">
                                    <div class="modal-content">

                                        <form method="post"
                                              id="service_form"
                                              @submit="submitServiceForm">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLongTitle"> <strong>Add Services</strong> </h5>
                                                <button type="button" class="close" aria-label="Close" data-dismiss="modal">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label for="name"> <strong>Service Name <span class="badge badge-pill badge-dark"> English </span> </strong>  </label>
                                                                <input type="text" v-model="services.form_fields.name" name="name" class="form-control" id="name" placeholder="Service Name">
                                                                <span class="text-danger" v-if="errorFilter('name')!==''"> @{{ errorFilter('name') }} </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label for="name"> <strong>Category</strong>  </label>
                                                                <select name="category_id" class="form-control" v-model='services.form_fields.category_id'>
                                                                    <option value="" selected>Choose category</option>
                                                                    <option v-for="(c,i) in category_services" :value="c.id">@{{c.name}}</option>
                                                                </select>
                                                                <span class="text-danger" v-if="errorFilter('category_id')!==''"> @{{ errorFilter('category_id') }} </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="card">
                                                            <div class="card-header">
                                                                <div class="form-group">
                                                                    <label for="name"> <strong>Mode</strong>  </label>
                                                                    <select name="mode" id="mode" class="form-control"
                                                                            v-model="service_mode">
                                                                        <option>Auto</option>
                                                                        <option>Manual</option>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group" v-if="services.visibility.provider">
                                                                    <label for=""><strong>Provider</strong></label>
                                                                    <v-select :options="providers_lists"
                                                                                v-model="services.form_fields.provider_id"
                                                                                class="style-chooser"
                                                                                :reduce="domain => domain.id" label="domain"></v-select>
                                                                    <input type="hidden" name="provider_id" v-model="services.form_fields.provider_id">
                                                                    <span class="text-danger" v-if="errorFilter('provider_id')!==''"> @{{ errorFilter('provider_id') }} </span>
                                                                </div>
                                                                
                                                                <span style="color: red" v-if="service_mode == 'Auto' && services.validations.provider_service_not_found !==''">@{{services.validations.provider_service_not_found}}</span>
                                                                <div class="form-group" v-if="services.visibility.service_id_by_provider">
                                                                <label for=""><strong>Services</strong></label>
                                                                    <v-select :options="provider_services_computed"
                                                                                class="style-chooser"
                                                                                v-model="services.form_fields.provider_service_id"
                                                                                :reduce="services => services.id" label="display_name"></v-select>
                                                                    <input type="hidden" name="provider_service_id" v-model="services.form_fields.provider_service_id">
                                                                    <span class="text-danger" v-if="errorFilter('provider_service_id')!==''"> @{{ errorFilter('provider_service_id') }} </span>
                                                                </div>
                                                                <input type="hidden" name="service_type"  v-if="!services.visibility.service_type" v-model="service_type_selected">
                                                                <input type="hidden" name="provider_selected_service_data" :value="JSON.stringify(provider_service_selected)">
                                                                <div class="form-group" v-if="services.visibility.service_type">
                                                                    <label for=""><strong>Service Type</strong></label>
                                                                    <select name="service_type" id="service_type"
                                                                            class="form-control" v-model="service_type_selected">
                                                                        <option v-for="(st, ind) in service_type">@{{ ind }}</option>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group" v-if="services.visibility.drip_feed">
                                                                    <label for=""><strong>Drip Feed</strong></label>
                                                                    <select name="drip_feed_status" id="drip_feed_status"
                                                                            class="form-control"
                                                                            v-model="services.form_fields.drip_feed_status">
                                                                        <option>Allow</option>
                                                                        <option>Disallow</option>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group" v-if="services.visibility.re_fill">
                                                                    <label for=""><strong>Re-Fill</strong></label>
                                                                    <select name="refill_status" id="refill_status" class="form-control"
                                                                            v-model="services.form_fields.refill_status">
                                                                        <option>Allow</option>
                                                                        <option>Disallow</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <label for=""> <strong>Rate Per 1000</strong></label>
                                                <div  v-if="service_mode == 'Auto'  && services.form_fields.provider_service_id != null" class="row auto-mode-input-field" >
                                                    <div class="col-11 d-flex"   v-if="services.visibility.auto_per_rate">
                                                        <div class="form-group">
                                                            <label for=""> Fixed 1.0</label>
                                                            <input type="number" step="any" v-model="auto_price_plus" class="form-control" placeholder="0" aria-describedby="helpId">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for=""> Percent, %</label>
                                                            <input type="number" step="any"  v-model="auto_price_percent" class="form-control" placeholder="0" aria-describedby="helpId">
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="price_box">
                                                                <span>@{{services.form_fields.price}}</span>
                                                                <span>@{{services.form_fields.price_original}} USD</span>
                                                                <input type="hidden" name="price" v-model="services.form_fields.price">
                                                            </div>
                                                        </div>
                                                        <span class="text-danger" v-if="errorFilter('price')!==''"> @{{ errorFilter('price') }} </span>
                                                    </div>
                                                    <div class="col-11 test-class" v-else>
                                                        <div class="form-group">
                                                            <input type="text" class="form-control" name="price" v-model="services.form_fields.price">
                                                            <label for="">@{{services.form_fields.price_original}} USD</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-1">
                                                        <div class="switch-custom switch-custom__table">
                                                            <label class="switch">
                                                                <input type="checkbox" class="toggle-page-visibility"  v-model="auto_per_rate_toggler" >
                                                                <span class="slider round"></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                    
                                                <div class="row test-class-row" v-else>
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <input type="number" name="price" step="any" class="form-control"
                                                                    v-model="services.form_fields.price" placeholder="Price">
                                                            <span class="text-danger" v-if="errorFilter('price')!==''"> @{{ errorFilter('price') }} </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row mt-3 mb-3">
                                                    <div class="col-md-6">
                                                        <label for=""><strong>Min Order</strong></label>
                                                        <div  v-if="service_mode == 'Auto'  && services.form_fields.provider_service_id != null" class="row order_limit">
                                                            <div class="col-5">
                                                                <div class="form-group">
                                                                    <input type="number" step="any" class="form-control" name="min_quantity" v-model='services.form_fields.min_quantity'>
                                                                    <label for="">@{{services.form_fields.auto_min_quantity}} USD</label>
                                                                    <span class="text-danger" v-if="errorFilter('min_quantity')!==''"> @{{ errorFilter('min_quantity') }} </span>
                                                                </div>
                                                                <div class="overlay" v-if="auto_min_rate_toggler"></div>
                                                            </div>
                                                            <div class="col-1">
                                                                <div class="switch-custom switch-custom__table">
                                                                    <label class="switch">
                                                                        <input type="checkbox" class="toggle-page-visibility"  v-model="auto_min_rate_toggler" >
                                                                        <span class="slider round"></span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div  v-else>
                                                            <div class="form-group">
                                                                <div class="controls">
                                                                    <input type="number" step="any" name="min_quantity"
                                                                            class="form-control"
                                                                            placeholder="Min quantity"
                                                                            v-model="services.form_fields.min_quantity"
                                                                            :class="{disabled :services.disable.min}"
                                                                            :disabled="services.disable.min"
                                                                    />
                                                                    <span class="text-danger" v-if="errorFilter('min_quantity')!==''"> @{{ errorFilter('min_quantity') }} </span>
                                                                </div>
                                                            </div>
                                                            
                                                        </div>
                    
                    
                                                    </div>
                    
                                                    <div class="col-md-6">
                                                        <label for=""><strong>Max Order</strong></label>
                                                        <div class="row order_limit" v-if="service_mode == 'Auto'  && services.form_fields.provider_service_id != null">
                                                            <div class="col-5">
                                                                <div class="form-group">
                                                                    <input type="number" step="any" class="form-control" v-model='services.form_fields.max_quantity' name="max_quantity">
                                                                    <label for="">@{{services.form_fields.auto_max_quantity}} USD</label>
                                                                    <span class="text-danger" v-if="errorFilter('max_quantity')!==''"> @{{ errorFilter('max_quantity') }} </span>
                                                                </div>
                                                                <div class="overlay" v-if="auto_max_rate_toggler"></div>
                                                            </div>
                                                            <div class="col-1">
                                                                <div class="switch-custom switch-custom__table">
                                                                    <label class="switch">
                                                                        <input type="checkbox" class="toggle-page-visibility"  v-model="auto_max_rate_toggler" >
                                                                        <span class="slider round"></span>
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div v-else>
                                                            <div class="form-group">
                                                                <div class="controls">
                                                                    <input type="number" step="any" name="max_quantity"
                                                                            class="form-control"
                                                                            placeholder="Max quantity"
                                                                            v-model="services.form_fields.max_quantity"
                                                                            :class="{disabled :services.disable.max}"
                                                                            :disabled="services.disable.max"
                                                                    />
                                                                    <span class="text-danger" v-if="errorFilter('max_quantity')!==''"> @{{ errorFilter('max_quantity') }} </span>
                                                                </div>
                                                            </div>
                                                            
                                                        </div>
                    
                                                    </div>
                                                </div>
                                                @if ($gs->average_time == 1)
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for=""> <strong>Average Time</strong></label>
                                                                <input type="text" name="service_average_time" v-model="services.form_fields.service_average_time" class="form-control">
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label for=""> <strong>Link duplicates</strong></label>
                                                            <select name="link_duplicates" id="link_duplicates" class="form-control"
                                                                    v-model="link_duplicate_selected">
                                                                <option>Allow</option>
                                                                <option>Disallow</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                    
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label > <strong>Increment</strong>    <span class="fa fa-exclamation-circle" data-toggle="tooltip" data-placement="top" title="Restricted to accept quantity. Multiple of setted value"></span></label>
                                                            {{-- <label for=""> Increment <i class="fas fa-info-circle"></i> </label> --}}
                                                            <input type="number" step="any" class="form-control" name="increment"
                                                                    v-model="services.form_fields.increment">
                                                            <span class="text-danger" v-if="errorFilter('increment')!==''"> @{{ errorFilter('increment') }} </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" v-if="services.visibility.overflow">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label for=""> <strong>Overflow</strong>  </label>
                                                            <input type="number" step="any"
                                                                    v-model="services.form_fields.auto_overflow"
                                                                    class="form-control" name="auto_overflow">
                                                                    <span class="text-danger" v-if="errorFilter('auto_overflow')!==''"> @{{ errorFilter('auto_overflow') }} </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div v-if="errors.common" class="error-display">
                                                    <p class="error-display-item"> @{{ errors.common }}</p>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" id="service_save_button" class="btn btn-primary custom-button">
                                                    <i class="fa fa-check"></i> Save</button>
                                                <button type="button" class="btn btn-danger custom-button" data-dismiss="modal">Close</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            {{--end Service Modal--}}
                            <div class="modal fade" id="serviceDescription" tabindex="-1" role="dialog"
                                 aria-labelledby="serviceDescriptionTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered __modal_dialog_custom" role="document">
                                    <div class="modal-content">
                                        <form method="post"
                                              id="formDescription"
                                              @submit="updateServiceDescription"
                                              enctype="multipart/form-data" novalidate>
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLongTitle"> <strong>Description Edit</strong> </h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="id" v-model="service_edit_id"/>
                                                <div class="form-group">
                                                    <label for="description"> <strong>Description</strong> </label>
                                                    <textarea name="description"
                                                                id="serviceDescription_edit"
                                                                class="form-control custom_summernote"
                                                                v-model="services.form_fields.description"></textarea>
                    
                                                </div>
                                                <div v-if="errors.category.length != 0" class="error-display">
                                                    <p class="error-display-item" v-for="errC in errors.category"> @{{ errC.desc }}</p>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary custom-button"><i class="fa fa-check"></i> Save</button>
                                                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="bulkCategoryAssgin" tabindex="-1" role="dialog"
                                 aria-labelledby="bulkCategoryAssginTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered __modal_dialog_custom" role="document">
                                    <div class="modal-content">
                                        <form method="post"
                                              id="formBulkCategory"
                                              @submit="service_bulk_category"
                                              enctype="multipart/form-data" novalidate>
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLongTitle"> <strong>Bulk Category Assign</strong> </h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <input type="hidden" name="id" v-model="service_edit_id">
                                                <div class="form-group">
                                                    <select name="bulk_category_id" id="bulk_category_id" class="form-control custom-form-control">
                                                        <option value="" selected>Choose category</option>
                                                        <option v-for="(c,i) in category_services" :value="c.id">@{{c.name}}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary custom-button"><i class="fa fa-check"></i> Save</button>
                                                <button type="button" class="btn btn-danger custom-button" data-dismiss="modal">Close</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="import" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog"
                                 aria-labelledby="serviceModalTitle" aria-hidden="true">
                                <div class="modal-dialog __modal_dialog_custom" role="document">
                                    <div class="modal-content">
                                        <form method="post" action="{{ route('admin.provider.services.import') }}">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLongTitle"> <strong>Import services</strong> </h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group"  v-show="!is_nextLevel">
                                                            <label for=""><strong>Provider</strong></label>
                                                            <v-select :options="providers_lists"
                                                                v-model="provider_id"
                                                                class="style-chooser"
                                                                :reduce="domain => domain.id"
                                                                label="domain"
                                                                @input="getProviderServices">
                                                            </v-select>
                                                            <input type="hidden" name="provider_id" v-model='provider_id'>
                                                        </div>
                                                        <div v-show="is_nextLevel">
                                                            <div class="row">
                                                                <div class="col-6 d-flex">
                                                                    <div class="form-element-group">
                                                                        <div> Fixed (1.00) </div>
                                                                        <input type="number" step="any" @keyup="calculateRaise()"  @change="calculateRaise()" v-model.number='fixedRaisei' class="form-control"> 
                                                                    </div>
                                                                     <span>+</span>
                                                                     <div class="form-element-group">
                                                                        <div> Percent (%)</div>
                                                                        <input type="number" step="any"  @keyup="calculateRaise()"  @change="calculateRaise()" v-model.number="percentRaisei"  class="form-control">
                                                                     </div>
                                                                </div>
                                                                <div class="col-6" style="text-align: right" >
                                                                    <button type="button" @click="resetRaise()" class="btn btn-default">Reset Rate</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for=""><strong>Services </strong></label>
                                                            <div class="card" style="height: 400px; overflow-y: scroll; overflow-x: hidden">
                                                                <div v-show="!is_nextLevel" >
                                                                    <div class="material-card card" v-for="(category, index) in categories">
                                                                        <table  class="table table-hover">
                                                                            <thead>
                                                                            <tr>
                                                                                <th colspan="2">
                                                                                    <div class="row">
                                                                                        <div class="col">
                                                                                            @{{ category.category }}
                                                                                        </div>
                                                                                        <div class="col">
                                                                                            <div class="dropdown show goTopDropdown">
                                                                                                <a class="btn btn-secondary dropdown-toggle" :class="'cat' + index" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                                                    Create category
                                                                                                </a>
                                                                                                <ul class="dropdown-menu service-dropdown" aria-labelledby="dropdownMenuLink">
                                                                                                    <li v-for="(cs) in  category_services"><a class="dropdown-item" @click="selectDropDown(index, cs.name, cs.id)" > @{{ cs.name }} </a></li>
                                                                                                </ul>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </th>
                                                                            </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td>
                                                                                        <label>
                                                                                            <input type="checkbox" @click="checkUncheckAll(index, $event)"> Select all
                                                                                        </label>
                                                                                    </td>
                                                                                    <td class="text-right">
                                                                                        Rate, USD
                                                                                    </td>
                                                                                </tr>
                                                                                <tr v-for="service in category.services">
                                                                                    <td>
                                                                                        <label>
                                                                                            <input type="checkbox"  class="d-none" name="categories[]" :class="'catControl' + index" value="create">
                                                                                            <input @change="checkSibling($event)" type="checkbox" :value="JSON.stringify(service)" :class="'category' + index"> @{{ service.name }}
                                                                                        </label>
                                                                                    </td>
                                                                                    <td class="text-right">@{{ service.rate }}</td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                        
                                                                    </div>
                                                                </div>
                                                                <div v-show="is_nextLevel">
                                                                    <div class="material-card card"  v-for="(category, index) in selectedCategories">
                                                                        <table class="table table-hover">
                                                                            <thead>
                                                                                <tr>
                                                                                    <th colspan="2">
                                                                                        @{{ category.category }}
                                                                                    </th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <tr v-for="ser in category.services">
                                                                                    <td>
                                                                                       <span>@{{ ser.service }} -@{{ ser.name }}</span> 
                                                                                    </td>
                                                                                    <td class="text-right">
                                                                                        <div class="d-flex justify-content-between">
                                                                                            <div class="d-flex custom-rate-class align-items-center justify-content-center" :class="{'overlay-class':!ser.custome_rate_visible}">
                                                                                                <input type="text" step="any" :class="{'overlay-class':!ser.custome_rate_visible}" :readonly="!ser.custome_rate_visible?true:false" class="form-control" :value="ser.custome_rate" >
                                                                                                <input type="hidden" name="services[]" :value="JSON.stringify(ser)">
                                                                                                <span @click="lockSeviceRate(ser)" v-if="ser.custome_rate_visible"><i class="fas fa-lock-open"></i></span>
                                                                                                <span @click="lockSeviceRate(ser)" v-if="!ser.custome_rate_visible"><i class="fas fa-lock"></i></span>
                                                                                            </div>
                                                                                            <span>@{{ ser.rate }}</span>
                                                                                        </div>
                                                                                    </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <div class="d-flex justify-content-between w-100">
                                                    <div>
                                                        <div class="switch-custom switch-custom__table" v-if="selected_provider !== null && selected_provider.is_our===true">
                                                            <label class="switch">
                                                                <input type="checkbox" name="copy_description" class="toggle-page-visibility">
                                                                <span class="slider round"></span>
                                                            </label>
                                                            <span style="font-size: 12px; color:rgba(0, 0, 0, 0.79)">Copy Description</span>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <button type="button" v-if="!is_nextLevel" @click="is_nextLevel = true"   :disabled="selected_services.length === 0?true: false " class="btn btn-primary custom-button"><i class="fa fa-check"></i> Continue </button>
                                                        <button type="submit" v-if="is_nextLevel"  class="btn btn-primary custom-button"><i class="fa fa-check"></i> Import Services </button>
                                                        <button type="button" v-if="is_nextLevel"  @click="is_nextLevel = false" class="btn btn-default">Go back</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="__control_panel">
                                <div class="__left_control_panel">
                                    @can('add service')
                                        <button class="btn btn-outline-secondary"  type="button" data-toggle="modal" data-target="#serviceAddModal">Add Service</button>
                                    @endcan
                                    @can('add service subscription')
                                        <button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#subscriptionModal">Add Subscription</button>
                                    @endcan
                                    @can('add category')
                                        <button class="btn btn-outline-secondary" data-toggle="modal" data-target="#exampleModalCenter">Create Category</button>
                                    @endcan
                                    <div v-if="service_checkbox.length >0" class="d-inline service-checkbox-action">
                                        <span>service selected @{{ service_checkbox.length }}</span>
                                        <div class="dropdown __dropdown_buttons service_action">
                                            <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenuButton"
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                Action
                                            </button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                <a class="dropdown-item" href="#" @click="bulkEnable">Enable All</a>
                                                <a class="dropdown-item" href="#" @click="bulkDisable">Disable All</a>
                                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#bulkCategoryAssgin">Assign
                                                    Category</a>
                                                {{-- <a class="dropdown-item" href="#" @click="resetCustomRates">Reset Custom rates</a> --}}
                                                <a class="dropdown-item" href="#" @click="bulkDelete">Delete All</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="__right_control_panel">
                                    @can('import service')
                                        <button class="btn btn-link" data-target="#import" data-toggle="modal">Import</button>
                                    @endcan
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-search" aria-hidden="true"></i></span>
                                        </div>
                                        <input type="text" id="searchmyInput" class="form-control" placeholder="Search Services">
                                    </div>
                                </div>
                            </div>
                            <div class="__table_header __table_header_dropdown">
                                <div class="__th no-drop-down" style=" grid-column: span 2"><input type="checkbox" id="selectAllService"></div>
                                <div class="__th no-drop-down">ID</div>
                                <div class="__th __service_th no-drop-down">Service</div>
                                <div class="__th __service_th_type_type">
                                    <div class="dropdown __dropdown_buttons">
                                        <button class="btn btn-default dropdown-toggle" type="button"
                                                id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false">
                                            Type
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item type-dropdown-item"  @click="serviceTypeFilter(ind)" v-for="(st, ind) in service_type">@{{ ind }} (@{{ st }}) </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="__th no-drop-down __service_th_type_mode">Provider</div>
                                <div class="__th no-drop-down __service_th_type_rate">Rate</div>
                                <div class="__th no-drop-down __service_th_type">Min</div>
                                <div class="__th no-drop-down __service_th_type_max">Max</div>
                                <div class="__th __service_th_type">
                                    <div class="dropdown __dropdown_buttons">
                                        <button class="btn btn-default dropdown-toggle" type="button"
                                                id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false">
                                            Status
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item type-dropdown-item" @click="serviceStatusFilter(ind)" v-for="(st, ind) in autoManualCount">@{{ ind }} (@{{ st }}) </a>
                                        </div>

                                    </div>
                                </div>
                                <div class="__th no-drop-down __service_th_type" style="text-align:right;padding-right:4px; grid-column: span 4">
                                    <div style="cursor: pointer" onclick="toggleAllcategory()">
                                        <i class="fas fa-expand-arrows-alt" id="expand" style="display: none"></i>
                                        <i class="fas fa-compress" id="compress" ></i>
                                    </div>

                                </div>
                            </div>
                            <div class="__table_body category-sortable">
                                <div class="__row_wrapper" v-for="(cate_item, index) in category_services">
                                    <div class="__cate_service_wrapper">
                                        <div class="__category_row">
                                            <div class="__catename_action">
                                                <svg xmlns="http://www.w3.org/2000/svg" style="cursor: pointer" class="category-handle" viewBox="0 0 20 20"><title>Drag-Handle</title>
                                                    <path
                                                        d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z"></path>
                                                </svg>
                                                <span class="category_title" style="font-weight: 500;color: rgba(0, 0, 0, 0.79);">
                                                    @{{cate_item.name}}
                                                    <input type="hidden" class="category_hidden_id" :value="cate_item.id">
                                                </span>
                                                <span class="category_action"></span>
                                                <div class="dropdown __dropdown_buttons">
                                                    <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenuButton"
                                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Action
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                        @can('edit category')
                                                            <a class="dropdown-item type-dropdown-item"    @click="categoryEdit(cate_item.id)">Edit Category</a>
                                                        @endcan
                                                        @can('change category status')
                                                        <a class="dropdown-item type-dropdown-item"  @click="updateCategoryStatus(cate_item.id)" > 
                                                            <span v-if="cate_item.status=='Active'">Disable</span> <span v-else> Enable</span>  Category</a>
                                                        @endcan
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="__cate_toggler">
                                                <div class="service-block__collapse-block" onclick="hideService(this)">
                                                    <div class="service-block__collapse-button ">
                                                        <i class="fa fa-caret-down ml-1" aria-hidden="true"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="service_rows">
                                            <div class="__service_row serviceRow" id="sortable" v-for='(service, index) in cate_item.services'>
                                                <div class="__service_td drag-handler-container" style="padding: 0; grid-column: span 2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="service-handle" viewBox="0 0 20 20"><title>Drag-Handle</title>
                                                        <path
                                                            d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z"></path>
                                                    </svg>
                                                    <input type="checkbox" :value='service.id' class="service_checkbox" style="margin-top: 5px" v-model="service_checkbox"
                                                    :value="service.id">
                                                </div>
                                                <div class="__service_td">
                                                    @{{service.id}}
                                                </div>
                                                <div class="__service_td __service_th_td" id="sName">
                                                    @{{service.name}}
                                                </div>
                                                <div class="__service_td __service_td_span_type" id="sType">
                                                    @{{service.service_type}} 
                                                        <span v-if="service.drip_feed_status === 'allow'">
                                                            <i class="fas fa-tint"></i>
                                                        </span>
                                                        
                                                </div>
                                                <div class="__service_td __service_td_span_mode" id="sMode">
                                                    <span v-if="service.mode == 'auto'">
                                                        <span v-if="service.provider_info!==null">
                                                            @{{service.provider_info?service.provider_info.domain:null}} 
                                                        </span>
                                                    </span>
                                                    <span v-else>@{{service.mode}}</span>
                                                </div>
                                                <div class="__service_td __service_td_price" id="sPrice">
                                                    <span class="d-block">$@{{Number(service.price)}}</span>
                                                    <span class="d-block sub-price"> 
                                                        <span v-if="service.provider!==null">
                                                            $@{{service.provider?service.provider.rate:null}} 
                                                        </span>
                                                    </span>
                                                </div>
                                                <div class="__service_td __service_td_span" id="sMinQty">
                                                    <span class="d-block"> @{{service.min_quantity}}  </span>
                                                    <span class="d-block sub-price"> @{{service.provider?service.provider.min:null}} </span>
                                                </div>
                                                <div class="__service_td __service_td_max" id="sMaxQty">
                                                    <span class="d-block">  @{{service.max_quantity}} </span>
                                                    <span class="d-block sub-price"> @{{service.provider?service.provider.max:null}} </span>
                                                </div>
                                                <div class="__service_td __service_td_span" id="sStatus">
                                                    <span v-if="service.status === 'Deactivated'">Disabled</span>
                                                    <span v-if="service.status === 'Active'">Enabled</span>
                                                </div>
                                                <div class="__service_td __service_td_span" style="grid-column: span 4 / auto;padding: 0;text-align: right;">
                                                    <div class="dropdown __dropdown_buttons">
                                                        <button class="btn btn-default dropdown-toggle" type="button"
                                                                id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="false">
                                                            Action
                                                        </button>
                                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                            @can('edit service')
                                                                <a class="dropdown-item type-dropdown-item" @click="serviceEdit(service.id)">Edit service</a>
                                                            @endcan
                                                            @can('edit service description')
                                                                <a class="dropdown-item type-dropdown-item" @click="serviceDescription(service.id)">Edit description</a>
                                                            @endcan
                                                            @can('change service status')
                                                            <a class="dropdown-item type-dropdown-item"  @click="serviceEnableDisable(service.id)">
                                                                <span v-show="service.status=='Active'">Disabled</span> 
                                                                <span v-show="service.status=='Deactivated'">Enabled</span> service
                                                            </a>
                                                            @endcan
                                                            @can('reset service custom rates')
                                                            {{-- <a class="dropdown-item type-dropdown-item" >Reset custom rates</a> --}}
                                                            @endcan
                                                            @can('delete service')
                                                                <a class="dropdown-item type-dropdown-item" @click="serviceDelete(service.id)">Delete service</a>
                                                            @endcan
                                                            @can('duplicate service')
                                                                <a class="dropdown-item type-dropdown-item" @click="serviceDuplicate(service.id)">Duplicate</a>
                                                            @endcan
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        var sortServiceRoute = '{{route("admin.service.sort.data")}}';
        var sortCategoryRoute = '{{route("admin.category.sort.data")}}';
        var resetCustomRatesRoute = '{{route("admin.service.custom.rate.reset.all")}}';
    </script>
    <script src="{{ asset('panel-assets/libs/jquery-ui.min.js') }}"></script>
    <script src="https://unpkg.com/vue-select@3.10.3/dist/vue-select.js"></script>
    <script src="{{asset('/panel-assets/vue-scripts/service-vue.js?var=0.30')}}"></script>
    <script>
        $('#serviceAddModal, #subscriptionModal, #exampleModalCenter').on('hidden.bs.modal', function () {
            ServiceApp.formReset();
            ServiceApp.errors.services = [];
            ServiceApp.errors.common =  null;
            ServiceApp.errors.category =  [];
            ServiceApp.subscription_modal = false;
        });
        $("#subscriptionModal").on('shown.bs.modal', function() {
            ServiceApp.subscription_modal = true;
        });
        $("#selectAllService").on('change', function(evt){
            console.log(evt.target.checked, evt.target);
            if (evt.target.checked) {
                $(".service_checkbox").each((i,v) => {
                    ServiceApp.service_checkbox.push($(v).val());
                })
            } else {
                ServiceApp.service_checkbox = [];
            }
        })
        
    </script>
@endsection
