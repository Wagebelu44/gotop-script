@extends('layouts.web')

@section('content')
<section class="banner">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-3 text-center border-0">
                    <div class="card-body">
                        <p class="card-text mb-3"><span class="firstLine">{{ $msg }} </p>
                        <a href="http://gopanelshop.com" class="btn btn-success btn-lg">Order your panel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection