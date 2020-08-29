@extends('layouts.panel')

@section('content')
    <div class="container">
        <div class="row">
            @include('panel.appearance.nav')
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <a class="btn btn-default m-b add-page" href="#">Add page</a>

                        <table class="table">
                            <thead>
                            <tr>
                                <th width="45%" class="p-l">Name</th>
                                <th>Visibility</th>
                                <th>Public</th>
                                <th>Last modified</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr  class="disable-keystroke" >
                                <td class="p-l">Page Name</td>
                                <td>
                                    <div class="setting-switch setting-switch-table">
                                        <label class="switch">
                                            <input type="checkbox" class="toggle-page-visibility" name="page_status" id="page_status" value="">
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </td>
                                <td>Public</td>
                                <td>20.20.2020</td>
                                <td class="p-r text-right">
                                    <a class="btn btn-default btn-xs" href="#">Edit</a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
