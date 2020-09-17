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

    @media (min-width: 576px)
    {
        #import .__modal_dialog_custom {
            max-width: 800px!important;
        }
    }
</style>
@endsection
@section('content')
    <div class="container-fluid all-mt-30">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form  id="service_type_filter_form" action=""  method="get" >
                            <input type="hidden" name="serviceTypefilter">
                        </form>
                        <form action="" method="get" id="status_type_filter_form">
                            <input type="hidden" name="status">
                        </form>
                        <div class="__table-container" id="serviceApp">
                            <div class="overlay-loader">
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
                                                <div class="overlay-loader">
                                                    <div class="loader-holder">
                                                        <img src="{{asset('loader.gif')}}" alt="">
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for=""><strong>Category Name</strong></label>
                                                            <input type="text" v-model="category.name" name="name" class="form-control custom-form-control" placeholder="Name">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="error-display">
                                                    <p class="error-display-item" v-for="errC in errors.category"></p>
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
                                           <div class="overlay-loader" v-if="loader.service">
                                               <div class="loader-holder">
                                                   <img src="{{asset('loader.gif')}}" alt="">
                                               </div>
                                           </div>
               
                                           <div class="row">
                                               <div class="col-md-12">
                                                   <div class="form-group">
                                                       <div class="controls">
                                                           <label for="name"> <strong>Service Name <span class="badge badge-pill badge-dark"> English </span> </strong>  </label>
                                                           <input type="text" name="name" class="form-control"
                                                                   placeholder="Service Name" v-model="services.form_fields.name">
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
                                                       </div>
                                                   </div>
                                               </div>
                                               <div class="col-md-12">
                                                   <div class="form-group">
                                                       <label> <strong>Subscription</strong> </label>
                                                       <select  v-model="services.form_fields.subscription_type" class="form-control" name="subscription_type">
                                                           <option>Instagram Auto Likes</option>
                                                           <option>Instagram Auto Views</option>
                                                           <option>Instagram Auto Comments</option>
                                                           <option>Twitter Auto Likes</option>
                                                           <option>Twitter Auto Retweets</option>
                                                           <option>Twitter Auto Views</option>
                                                           <option>Youtube Auto Views</option>
                                                           <option>Facebook Auto Likes (for pages only)</option>
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
                                                       <input type="text" v-model="auto_price_plus" class="form-control" placeholder="0" aria-describedby="helpId">
                                                   </div>
                                                   <div class="form-group">
                                                       <label for=""> Percent, %</label>
                                                       <input type="text"  v-model="auto_price_percent" class="form-control" placeholder="0" aria-describedby="helpId">
                                                   </div>
                                                   <div class="form-group">
                                                       <div class="price_box">
                                                           <span>@{{services.form_fields.price}}</span>
                                                           <span>@{{services.form_fields.price_original}} USD</span>
                                                           <input type="hidden" name="price" v-model="services.form_fields.price">
                                                       </div>
                                                   </div>
                                               </div>
                                               <div class="col-11" v-else>
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
                                           <div class="row" v-else>
                                               <div class="col-12">
                                                   <div class="form-group">
                                                       <input type="text" name="price" class="form-control"
                                                               v-model="services.form_fields.price" placeholder="Price">
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
                                                               <input type="text" class="form-control" name="min_quantity" v-model='services.form_fields.min_quantity'>
                                                               <label for="">@{{services.form_fields.auto_min_quantity}} USD</label>
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
                                                               <input type="text" name="min_quantity"
                                                                       class="form-control"
                                                                       placeholder="Min quantity"
                                                                       v-model="services.form_fields.min_quantity"
                                                                       :class="{disabled :services.disable.min}"
                                                                       :disabled="services.disable.min"
                                                               />
                                                           </div>
                                                       </div>
                                                       <div class="price_validation_messages" v-if='services.validations.minQuantity.visibility' >
                                                           <p class="text-danger">@{{services.validations.minQuantity.msg}}</p>
                                                       </div>
                                                   </div>
               
               
                                               </div>
               
                                               <div class="col-md-6">
                                                   <label for=""><strong>Max Order</strong></label>
                                                   <div class="row order_limit" v-if="service_mode == 'Auto'  && services.form_fields.provider_service_id != null">
                                                       <div class="col-5">
                                                           <div class="form-group">
                                                               <input type="text" class="form-control" v-model='services.form_fields.max_quantity' name="max_quantity">
                                                               <label for="">@{{services.form_fields.auto_max_quantity}} USD</label>
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
               
                                                               <input type="text" name="max_quantity"
                                                                       class="form-control"
                                                                       placeholder="Max quantity"
                                                                       v-model="services.form_fields.max_quantity"
                                                                       :class="{disabled :services.disable.max}"
                                                                       :disabled="services.disable.max"
                                                               />
                                                           </div>
                                                       </div>
                                                       <div class="price_validation_messages" v-if='services.validations.maxQuantity.visibility' >
                                                           <p class="text-danger">@{{services.validations.maxQuantity.msg}}</p>
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
                                                       <input type="text"
                                                               v-model="services.form_fields.auto_overflow"
                                                               class="form-control" name="auto_overflow">
                                                   </div>
                                               </div>
                                           </div>
               
                                           <div v-if="errors.category.length != 0" class="error-display">
                                               <p class="error-display-item" v-for="errC in errors.category"> @{{ errC.desc }}</p>
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
                                                <div class="overlay-loader">
                                                    <div class="loader-holder">
                                                        <img src="{{asset('loader.gif')}}" alt="">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <div class="controls">
                                                                <label for="name"> <strong>Service Name <span class="badge badge-pill badge-dark"> English </span> </strong>  </label>
                                                                <input type="text" v-model="services.form_fields.name" name="name" class="form-control" id="name" placeholder="Service Name">
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
                                                                </div>
                                                                
                                                                <span style="color: red" v-if="service_mode == 'Auto' && services.validations.provider_service_not_found !==''">@{{services.validations.provider_service_not_found}}</span>
                                                                <div class="form-group" v-if="services.visibility.service_id_by_provider">
                                                                <label for=""><strong>Services</strong></label>
                                                                    <v-select :options="provider_services_computed"
                                                                                class="style-chooser"
                                                                                v-model="services.form_fields.provider_service_id"
                                                                                :reduce="services => services.id" label="display_name"></v-select>
                                                                    <input type="hidden" name="provider_service_id" v-model="services.form_fields.provider_service_id">
                                                                  
                                                                </div>
                                                                <input type="hidden" name="service_type"  v-if="!services.visibility.service_type" v-model="service_type_selected">
                                                                <input type="hidden" name="provider_selected_service_data" :value="JSON.stringify(provider_service_selected)">
                                                                <div class="form-group" v-if="services.visibility.service_type">
                                                                    <label for=""><strong>Service Type</strong></label>
                                                                    <select name="service_type" id="service_type"
                                                                            class="form-control" v-model="service_type_selected">
                                                                        <option v-for="st in service_type">@{{ st }}</option>
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
                                                            <input type="text" v-model="auto_price_plus" class="form-control" placeholder="0" aria-describedby="helpId">
                                                        </div>
                                                        <div class="form-group">
                                                            <label for=""> Percent, %</label>
                                                            <input type="text"  v-model="auto_price_percent" class="form-control" placeholder="0" aria-describedby="helpId">
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="price_box">
                                                                <span>@{{services.form_fields.price}}</span>
                                                                <span>@{{services.form_fields.price_original}} USD</span>
                                                                <input type="hidden" name="price" v-model="services.form_fields.price">
                                                            </div>
                                                        </div>
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
                                                            <input type="text" name="price" class="form-control"
                                                                    v-model="services.form_fields.price" placeholder="Price">
                                                        </div>
                                                        <div class="price_validation_messages" v-if='services.validations.price.visibility' >
                                                            <p class="text-danger">@{{services.validations.price.msg}}</p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row mt-3 mb-3">
                                                    <div class="col-md-6">
                                                        <label for=""><strong>Min Order</strong></label>
                                                        <div  v-if="service_mode == 'Auto'  && services.form_fields.provider_service_id != null" class="row order_limit">
                                                            <div class="col-5">
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control" name="min_quantity" v-model='services.form_fields.min_quantity'>
                                                                    <label for="">@{{services.form_fields.auto_min_quantity}} USD</label>
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
                                                                    <input type="text" name="min_quantity"
                                                                            class="form-control"
                                                                            placeholder="Min quantity"
                                                                            v-model="services.form_fields.min_quantity"
                                                                            :class="{disabled :services.disable.min}"
                                                                            :disabled="services.disable.min"
                                                                    />
                                                                </div>
                                                            </div>
                                                            <div class="price_validation_messages" v-if='services.validations.minQuantity.visibility' >
                                                                <p class="text-danger">@{{services.validations.minQuantity.msg}}</p>
                                                            </div>
                                                        </div>
                    
                    
                                                    </div>
                    
                                                    <div class="col-md-6">
                                                        <label for=""><strong>Max Order</strong></label>
                                                        <div class="row order_limit" v-if="service_mode == 'Auto'  && services.form_fields.provider_service_id != null">
                                                            <div class="col-5">
                                                                <div class="form-group">
                                                                    <input type="text" class="form-control" v-model='services.form_fields.max_quantity' name="max_quantity">
                                                                    <label for="">@{{services.form_fields.auto_max_quantity}} USD</label>
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
                    
                                                                    <input type="text" name="max_quantity"
                                                                            class="form-control"
                                                                            placeholder="Max quantity"
                                                                            v-model="services.form_fields.max_quantity"
                                                                            :class="{disabled :services.disable.max}"
                                                                            :disabled="services.disable.max"
                                                                    />
                                                                </div>
                                                            </div>
                                                            <div class="price_validation_messages" v-if='services.validations.maxQuantity.visibility' >
                                                                <p class="text-danger">@{{services.validations.maxQuantity.msg}}</p>
                                                            </div>
                                                        </div>
                    
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label for=""> <strong>Average Time</strong></label>
                                                            <input type="text" name="service_average_time" v-model="services.form_fields.service_average_time" class="form-control">
                                                        </div>
                                                    </div>
                                                </div>
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
                                                            <input type="text" class="form-control" name="increment"
                                                                    v-model="services.form_fields.increment">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" v-if="services.visibility.overflow">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label for=""> <strong>Overflow</strong>  </label>
                                                            <input type="text"
                                                                    v-model="services.form_fields.auto_overflow"
                                                                    class="form-control" name="auto_overflow">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div v-if="errors.category.length != 0" class="error-display">
                                                    <p class="error-display-item" v-for="errC in errors.category"> @{{ errC.desc }}</p>
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
                                                <div class="overlay-loader" v-if="loader.description">
                                                    <div class="loader-holder">
                                                        <img src="{{asset('loader.gif')}}" alt="">
                                                    </div>
                                                </div>
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
                                                <div class="overlay-loader">
                                                    <div class="loader-holder">
                                                        <img src="{{asset('loader.gif')}}" alt="">
                                                    </div>
                                                </div>
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
                            <div class="modal fade" id="import" tabindex="-1" role="dialog"
                                 aria-labelledby="serviceModalTitle" aria-hidden="true">
                                <div class="modal-dialog __modal_dialog_custom" role="document">
                                    <div class="modal-content">
                                        <form method="post" action="">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLongTitle"> <strong>Import services</strong> </h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="overlay-loader">
                                                    <div class="loader-holder">
                                                        <img src="{{asset('loader.gif')}}" alt="">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label for=""><strong>Provider</strong></label>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for=""><strong>Services</strong></label>
                                                            <div class="card" style="height: 400px; overflow-y: scroll; overflow-x: hidden">
                                                                <div class="material-card card" v-for="(category, index) in categories">
                                                                    <table class="table table-hover">
                                                                        <thead>
                                                                        <tr>
                                                                            <th colspan="2">
                                                                                <div class="row">
                                                                                    <div class="col">
                                                                                        Category
                                                                                    </div>
                                                                                    <div class="col">
                                                                                        <div class="dropdown show goTopDropdown">
                                                                                            <a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                                                Create category
                                                                                            </a>
                                                                                            <ul class="dropdown-menu service-dropdown" aria-labelledby="dropdownMenuLink">
                                                                                                <li><a class="dropdown-item">List name</a></li>
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
                                                                                    <input type="checkbox"> Select all
                                                                                </label>
                                                                            </td>
                                                                            <td class="text-right">
                                                                                Rate, USD
                                                                            </td>
                                                                        </tr>
                                                                        <tr v-for="service in category.services">
                                                                            <td>
                                                                                <label>
                                                                                    <input type="checkbox" name="categories[]" class="d-none" value="create">
                                                                                    <input type="checkbox" name="services[]"> Service name
                                                                                </label>
                                                                            </td>
                                                                            <td class="text-right">
                                                                                service rate
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
                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary custom-button"><i class="fa fa-check"></i> Save</button>
                                                <button type="button" class="btn btn-danger custom-button" data-dismiss="modal">Close</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="__control_panel">
                                <div class="__left_control_panel">
                                    <button class="btn btn-outline-secondary"  type="button" @click="serviceModalToggle">Add Service</button>
                                    <button class="btn btn-outline-secondary" type="button" @click="openSubscriptionModal()">Add Subscription</button>
                                    <button class="btn btn-outline-secondary" data-toggle="modal" data-target="#exampleModalCenter">Create Category</button>
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
                                    <button class="btn btn-link" data-target="#import" data-toggle="modal">Import</button>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fa fa-search" aria-hidden="true"></i></span>
                                        </div>
                                        <input type="text" id="searchmyInput" class="form-control" placeholder="Search Services">
                                    </div>
                                </div>
                            </div>
                            <div class="__table_header __table_header_dropdown">
                                <div class="__th no-drop-down"><input type="checkbox" id="selectAllService"></div>
                                <div class="__th no-drop-down">ID</div>
                                <div class="__th __service_th no-drop-down">Service</div>
                                <div class="__th __service_th_type_type">
                                    <div class="dropdown __dropdown_buttons">
                                        <button class="btn btn-default dropdown-toggle" type="button"
                                                id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false">
                                            Type
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton" id="service_type_filter">
                                            <a class="dropdown-item type-dropdown-item"></a>
                                        </div>
                                    </div>
                                </div>
                                <div class="__th no-drop-down __service_th_type_mode">Provider</div>
                                <div class="__th no-drop-down __service_th_type">Rate</div>
                                <div class="__th no-drop-down __service_th_type">Min</div>
                                <div class="__th no-drop-down __service_th_type">Max</div>
                                <div class="__th __service_th_type">
                                    <div class="dropdown __dropdown_buttons">
                                        <button class="btn btn-default dropdown-toggle" type="button"
                                                id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                                aria-expanded="false">
                                            Status
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton"  id="status_type_filter">
                                            <a data-key="all" class="dropdown-item type-dropdown-item">All</a>
                                            <a data-key="Deactivated" class="dropdown-item type-dropdown-item">Disabled</a>
                                            <a data-key="Active" class="dropdown-item type-dropdown-item">Enabled</a>
                                        </div>

                                    </div>
                                </div>
                                <div class="__th no-drop-down __service_th_type" style="text-align:right;padding-right:4px; grid-column: span 3">
                                    <div style="cursor: pointer">
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
                                                <svg xmlns="http://www.w3.org/2000/svg" class="category-handle" viewBox="0 0 20 20"><title>Drag-Handle</title>
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
                                                        <a class="dropdown-item type-dropdown-item"    @click="categoryEdit(cate_item.id)">Edit Category</a>
                                                        <a class="dropdown-item type-dropdown-item"  @click="updateCategoryStatus(cate_item.id)" > 
                                                            <span v-if="cate_item.status=='active'">Disable</span> <span v-else> Enable</span>  Category</a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="__cate_toggler">
                                                <div class="service-block__collapse-block">
                                                    <div class="service-block__collapse-button ">
                                                        <i class="fa fa-caret-down ml-1" aria-hidden="true"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="service_rows">
                                            <div class="__service_row serviceRow" id="sortable" v-for='(service, index) in cate_item.services'>
                                                <div class="__service_td drag-handler-container" style="padding: 0">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="service-handle" viewBox="0 0 20 20"><title>Drag-Handle</title>
                                                        <path
                                                            d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z"></path>
                                                    </svg>
                                                    <input type="checkbox" class="service_checkbox" style="margin-top: 5px" v-model="service_checkbox"
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
                                                    @{{service.mode}} 
                                                </div>
                                                <div class="__service_td __service_td_span" id="sPrice">
                                                    <span class="d-block">$@{{service.price}}</span>
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
                                                <div class="__service_td __service_td_span" id="sMaxQty">
                                                    <span class="d-block">  @{{service.max_quantity}} </span>
                                                    <span class="d-block sub-price"> @{{service.provider?service.provider.max:null}} </span>
                                                </div>
                                                <div class="__service_td __service_td_span" id="sStatus">
                                                    @{{service.status}}
                                                </div>
                                                <div class="__service_td __service_td_span" style="grid-column: span 3 / auto;padding: 0;text-align: right;">
                                                    <div class="dropdown __dropdown_buttons">
                                                        <button class="btn btn-default dropdown-toggle" type="button"
                                                                id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                                                aria-expanded="false">
                                                            Action
                                                        </button>
                                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                            <a class="dropdown-item type-dropdown-item" @click="serviceEdit(service.id)">Edit service</a>
                                                            <a class="dropdown-item type-dropdown-item" @click="serviceDescription(service.id)">Edit description</a>
                                                            <a class="dropdown-item type-dropdown-item"  @click="serviceEnableDisable(service.id)">
                                                                <span v-if="service.status=='active'">Active</span> <span v-else> Inactive</span> service
                                                            </a>
                                                            {{-- <a class="dropdown-item type-dropdown-item" >Reset custom rates</a> --}}
                                                            <a class="dropdown-item type-dropdown-item" @click="serviceDelete(service.id)">Delete service</a>
                                                            <a class="dropdown-item type-dropdown-item" @click="serviceDuplicate(service.id)">Duplicate</a>
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
    <script src="https://unpkg.com/vue-select@3.10.3/dist/vue-select.js"></script>
    <script src="{{asset('/panel-assets/vue-scripts/service-vue.js')}}"></script>
@endsection
