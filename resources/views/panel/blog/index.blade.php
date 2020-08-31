@extends('layouts.panel')

@section('content')
    <div class="container all-mt-30">
        <div class="row">
            <!--navbar-->
            @include('panel.blog.nav')
            <!--navbar-->
            <div class="col-md-8">
                <div class="card panel-default">
                    <div class="card-body">
                        <a class="btn btn-default m-b add-page" href="">Add New Blog</a>

                        <table class="table">
                            <thead>
                            <tr>
                                <th width="45%" class="p-l">Post Title</th>
                                <th>Created</th>
                                <th>Visibility</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <a href="#" class="link" target="_blank">
                                        Blog title
                                        <span class="someicon">
                                            <i class="fa fa-link" aria-hidden="true"></i>
                                        </span>
                                    </a>
                                </td>
                                <td>20.20.2020</td>
                                <td>published</td>
                                <td class="p-r text-right">
                                    <a class="btn btn-default btn-xs" href="">Edit</a>
                                </td>
                            </tr>
                            <tr>
                                <td>No Data found.</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
