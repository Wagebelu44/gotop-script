@extends('layouts.panel')

@section('content')
    <div class="row">
        @include('panel.settings.navbar')

        <div class="col-md-8">
            <button class="btn btn-sm btn-primary">Add Faq</button>
            <div class="card panel-default" style="margin-top: 10px;">
                <div class="card-body">
                    <div class="col-md-12">
                        <table class="setting-table">
                            <tbody>
                            <tr>
                                <td>
                                    <div class="settings-menu-drag">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Drag-Handle</title><path d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z"></path></svg>
                                    </div>
                                    Lorem Ipsum has been the industry's standard dummy text ever since the 1500s Lorem Ipsum has ?
                                </td>
                                <td width="10%" class="text-center">Active</td>
                                <td width="10%">
                                    <a href="" class="btn btn-default btn-xs" style="margin-left: 10px;">Edit</a>
                                </td>
                            </tr>
                            <tr style="border-bottom: 1px solid lightgray">
                                <td>
                                    <div class="settings-menu-drag">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Drag-Handle</title><path d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z"></path></svg>
                                    </div>
                                    Lorem Ipsum has been the industry's standard dummy text ever since the 1500s Lorem Ipsum has ?
                                </td>
                                <td width="10%" class="text-center">Active</td>
                                <td width="10%">
                                    <a href="" class="btn btn-default btn-xs" style="margin-left: 10px;">Edit</a>
                                </td>
                            </tr>
                            <tr style="border-bottom: 1px solid lightgray">
                                <td>
                                    <div class="settings-menu-drag">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Drag-Handle</title><path d="M7 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm6-8c1.104 0 2-.896 2-2s-.896-2-2-2-2 .896-2 2 .896 2 2 2zm0 2c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2zm0 6c-1.104 0-2 .896-2 2s.896 2 2 2 2-.896 2-2-.896-2-2-2z"></path></svg>
                                    </div>
                                    Lorem Ipsum has been the industry's standard dummy text ever since the 1500s Lorem Ipsum has ?
                                </td>
                                <td style="width: 10%" class="text-center">Active</td>
                                <td style="width: 10%">
                                    <a href="" class="btn btn-default btn-xs" style="margin-left: 10px;">Edit</a>
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
