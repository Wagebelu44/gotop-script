@extends('layouts.app')
@section('style')

@endsection
@section('content')
@php
    $colors = [
           'completed' => [
               'bg'=> "#008000",
               'cl'=> "#fff",
           ] ,
           'cancelled' =>[
               'bg'=> "#ff0000",
               'cl'=> "#fff",
           ],
           'canceled' => [
               'bg'=> "#ff0000",
               'cl'=> "#fff",
           ],
           'processing' =>  [
               'bg'=> "#73cef7",
               'cl'=> "#fff",
           ],
           'inprogress' => [
               'bg'=> "#0000ff",
               'cl'=> "#fff",
           ],
           'partial' => [
               'bg'=> "#f68331",
               'cl'=> "#fff",
           ],
           'failed' => [
               'bg'=> "#000000",
               'cl'=> "#fff",
           ],
           'error' => [
               'bg'=> "#17a2b8",
               'cl'=> "#fff",
           ],
           'awaiting' => [
               'bg'=> "#17a2b8",
               'cl'=> "#fff",
           ],
           'pending' => [
               'bg'=> "#828282",
               'cl'=> "#fff",
           ],
           'refunded' => [
                'bg'=> "#ff0000",
                'cl'=> "#fff",
           ],
       ];
@endphp
<div class="inner-page-common orders-new">
    <div class="clearfix" style="height: 20px;"></div>
<section class="service-search-panel">
    <div class="container">
        <div class="search-panel">
            <form action="#" method="get" id="history-search" class="has-validation-callback">
                <div class="form-group">
                    <div class="input-group">
                        <input type="hidden" name="status" value="all">
                        <input type="text" name="user_search_keyword" class="form-control" value="" placeholder="Search orders">
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-green"><i class="fa fa-search" aria-hidden="true"></i></button>
                        </span>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
    <section>
       <div class="container-fluid">
            <div class="card my-orders-panel">
               <div class="card-body">
                  <div class="tabs-wrapper">
                     <ul class="nav nav-justified nav-tabs dragscroll horizontal">
                        <li class="nav-item  {{ !request()->has('query') && request()->is('orders') ? 'filter-active' : '' }}">
                            <a class="nav-link" href="#"><i class="fa fa-list-ul"></i> All</a>
                        </li>
                        <li class="nav-item  {{ request()->has('query') &&  request()->query()['query'] == 'PENDING' ? 'filter-active' : '' }}">
                        <a class="nav-link" href="#"> <i class="fa fa-clock"></i> Pending  </a>
                        </li>
                        <li class="nav-item  {{ request()->has('query') &&  request()->query()['query'] == 'PROCESSING' ? 'filter-active' : '' }}">
                            <a class="nav-link" href="#"><i class="fa fa-chart-line"></i> Processing</a>
                        </li>
                        <li class="nav-item  {{ request()->has('query') &&  request()->query()['query'] == 'INPROGRESS' ? 'filter-active' : '' }}">
                            <a class="nav-link" href="#"><i class="fa fa-spinner"></i> In progress</a>
                        </li>
                        <li class="nav-item  {{ request()->has('query') &&  request()->query()['query'] == 'COMPLETED' ? 'filter-active' : '' }}">
                            <a class="nav-link"  href="#">
                                <i class="fa fa-check"></i> Completed
                            </a>
                        </li>
                        <li class="nav-item  {{ request()->has('query') &&  request()->query()['query'] == 'PARTIAL' ? 'filter-active' : '' }}">
                            <a class="nav-link"  href="#">
                                <i class="fa fa-hourglass-half"></i> Partial
                            </a>
                        </li>
                        <li class="nav-item  {{ request()->has('query') &&  request()->query()['query'] == 'CANCELLED' ? 'filter-active' : '' }}">
                            <a class="nav-link"  href="#">
                                <i class="fa fa-times-circle"></i> Canceled
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="tab-content table-responsive-xl">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Service</th>
                                <th scope="col" width="400">Link</th>
                                <th scope="col">Quantity</th>
                                <th scope="col" width="105">Start count</th>
                                <th class="text-center" scope="col" width="100">Date</th>
                                <th class="text-center" scope="col">Charge</th>
                                <th scope="col">Remains</th>
                                <th class="text-center" scope="col" width="120">Status</th>
                                <!-- <th scope="col" width="0"></th> -->
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($orders as $order)
                            <tr @if ($order->status == 'completed' && $order->refill_order_status == null) class="refill-button-enabled" @endif>
                                <td>{{$order->order_id}}</td>
                                <td>
                                    @if (isset($order->service) &&  $order->service!=null)
                                        {{ $order->service->name}}
                                    @endif
                                </td>
                                <td>
                                    <a href="{{$order->link}}" target="_blank"><i class="fa fa-link"></i>  {{$order->link}}</a>
                                    @if ($order->service_type == 'SEO')
                                        <a  class="service_type_tags" @click="modalVIsible('text_area_1', {{$order}} )">Keywords</a>
                                    @elseif ($order->service_type == 'SEO2')
                                        <a  class="service_type_tags" @click="modalVIsible('text_area_1', {{$order}} )">Keywords</a>
                                        <a  class="service_type_tags" @click="modalVIsible('additional_comment_owner_username_visible', {{$order}} )">Email</a>
                                    @elseif ($order->service_type == 'Custom Comments' || $order->service_type == 'Custom Comments Package')
                                        <a  class="service_type_tags" @click="modalVIsible('text_area_1', {{$order}} )">Comments</a>
                                    @elseif ($order->service_type == 'Comment Likes' || $order->service_type == 'Mentions Users Followers' )
                                        <a  class="service_type_tags" @click="modalVIsible('additional_comment_owner_username_visible', {{$order}} )">Username</a>
                                    @elseif ($order->service_type == 'Mentions Custom List' || $order->service_type == 'Mentions')
                                        <a  class="service_type_tags" @click="modalVIsible('text_area_1', {{$order}} )">Username</a>
                                    @elseif ($order->service_type == 'Mentions with Hashtags')
                                        <a  class="service_type_tags" @click="modalVIsible('text_area_1', {{$order}} )">Username</a>
                                        <a  class="service_type_tags" @click="modalVIsible('text_area_2', {{$order}} )">Hastags</a>
                                    @elseif ($order->service_type == 'Comment Replies')
                                        <a  class="service_type_tags" @click="modalVIsible('additional_comment_owner_username_visible', {{$order}} )">Username</a>
                                        <a  class="service_type_tags" @click="modalVIsible('text_area_1', {{$order}} )">Comments</a>
                                    @elseif ($order->service_type == 'Mentions Hashtag')
                                        <a  class="service_type_tags" @click="modalVIsible('additional_comment_owner_username_visible', {{$order}} )">Hastags</a>
                                    @elseif ($order->service_type == 'Mentions Media Likers')
                                        <a  class="service_type_tags" @click="modalVIsible('additional_comment_owner_username_visible', {{$order}} )">Mediua URLs</a>
                                    @endif
                                </td>
                                <td class="text-center"> {{$order->quantity}}</td>
                                <td class="text-center">{{$order->start_counter}}</td>
                                
                                <td class="text-center">{{$order->created_at}}</td>
                                <td class="text-center">${{sprintf('%0.3f',$order->charges) }}</td>
                                <td class="text-left">{{$order->remains}}</td>
                                <td class="status-value text-center">
                                    <div class="d-flex justify-content-between">

                                        <span class="status">
                                            {{  strtolower(str_replace(" ","",$order->status))}}
                                        </span>

                                       
                                    </div>
                                    @if ($role=='user')
                                    
                                        @if ($order->status == 'completed' && $order->refill_order_status == null)
                                        <div class="action bg-green order-actions">
                                            <form action="{{route('user.changeRefillStatus')}}" method="post" class="refiller-button p-0">
                                                @csrf
                                                <input type="hidden" name="order_table_id" value="{{$order->id}}">
                                                <input type="hidden" name="order_id" value="{{$order->order_id}}">
                                                <input type="hidden" name="refill_order_status" value="processing">
                                                <button type="submit">♻ Refill</button>
                                            </form>
                                        </div>
                                        @elseif (($order->status == 'completed' || $order->status == 'COMPLETED') && ($order->refill_order_status == 'success' || $order->refill_order_status == 'pending'))
                                            <div class="action bg-green order-actions">    
                                                <div class="refiller-button p-0">
                                                    <span class="btn btn-xs"><i class="fa fa-drivers-license-o"></i> Refilling</span>
                                                </div>
                                            </div>
                                        @endif
                                    @endif 
                                </td>
                               
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>

    <div class="clearfix mb-5">
        
    </div> 

            <div class="row mb-2 mt-1 mb-5">
                <div class="col-12 mx-auto text-center">
                    <div class="d-inline-block mx-auto pagination-mobile">
                    {{ $orders->links() }}
                    </div>
                </div>
                <div class="col-0"></div>
            </div>
        </div>
    </section>
</div> 
 
@endsection
