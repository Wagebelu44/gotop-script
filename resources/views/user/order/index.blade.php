@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg">
            <div class="card bg-white">
                <div class="card-header custom-card-header" id="card-header-order">
                    <span  class="tab-bar order_active" data-id="new-order"> 
                        {{-- <img src="{{asset('frontend-assets/new_order.png')}}" width="20" height="20" alt="New Order"> --}}  New Order
                    </span>
                    <span  class="tab-bar" data-id="mass-order"> 
                       {{--  <img src="{{asset('frontend-assets/mass_order.png')}}" width="20" height="20" alt="New Order"> --}}  Mass Order
                    </span>
                </div>
                <div class="card-body">
                    @if (Session::has('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                <span class="sr-only">Close</span>
                            </button>
                            <strong>Error! </strong>{{Session::get('error')}}.
                        </div>
                    @elseif ($errors->any())
                        @foreach ($errors->all() as $error)
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                    <span class="sr-only">Close</span>
                                </button>
                                <strong>Warning! </strong>{{$error}}.
                            </div>
                        @endforeach
                    @endif
                    <div class="tab-content" style="padding-top: 0px !important">
                        <div  id="new_order">
                            @include('user.order.single-order-form')
                        </div>
                        <div  id="mass_order" style="display: none">
                            @include('user.order.mass-order')
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg p-0 news_layout-mobile">
            {{-- @include('frontend/orders/common/news_layout') --}}
        </div>
    </div>
</div>
@endsection
@section('script')
    <script src="{{asset('user-assets/vue-scripts/single-order.js')}}"></script>
@endsection