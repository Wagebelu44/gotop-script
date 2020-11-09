@extends('layouts.panel')
@section('content')
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-uppercase p-3 bg-info text-white">Ticket details</h5>

                    <table class="table table-bordered">
                        <tr>
                            <th width="15%">Client name</th>
                            <td>{{ $ticket->user->username }}</td>
                        </tr>
                        <tr>
                            <th width="15%">Subject</th>
                            <td>{{ $ticket->subject }}</td>
                        </tr>
                        <tr>
                            <th width="15%">Message</th>
                            <td>{!! nl2br($ticket->description) !!}</td>
                        </tr>
                        <tr>
                            <th width="15%">Status</th>
                            <td>
                                {{$ticket->status}}
                            </td>
                        </tr>
                        <tr>
                            <th width="15%">Created at</th>
                            <td>{{ date('M d, Y h:iA', strtotime($ticket->created_at))}}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-uppercase p-3 bg-info text-white">Give a reply</h5>
                    <form class="form-material" method="post" action="{{ route('admin.tickets.comment', $ticket->id) }}" enctype="multipart/form-data" novalidate>
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="controls">
                                        <input type="hidden" name="ticket_id" value="{{$ticket->id}}" >
                                        <textarea style="height: 200px" name="content"
                                                  class="form-control summernote @error('comment') is-invalid @enderror"
                                                  placeholder="Message" required data-validation-required-comment="This field is required">{{ old('comment') }}</textarea>
                                        @error('comment')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-success"> <i class="fa fa-check"></i> Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-uppercase p-3 bg-info text-white">Comments</h5>

                    <div class="chat-box scrollable" style="height:434px;">
                        <!--chat Row -->
                        <ul class="chat-list">
                        @foreach($ticket->comments as $comment)
                            @if ($comment->commentor_role == 'user')
                                <!--chat Row -->
                                    <li class="chat-item">
                                        <div class="chat-img"><img src="{{ asset('/panel-assets/images/avatar.png') }}" alt="user"></div>
                                        <div class="chat-content">
                                            <div class="box bg-light-success">
                                                <h5 class="font-medium">{{$comment->user->name}}</h5>
                                                <p class="font-light mb-0">{!! $comment->message !!}</p>
                                                <div class="chat-time">{{ date('M d, Y h:iA', strtotime($comment->created_at)) }}</div>
                                            </div>
                                        </div>
                                    </li>
                            @else
                                <!--chat Row -->
                                    <li class="odd chat-item">
                                        <div class="chat-content">
                                            <div class="box bg-light-success">
                                                <h5 class="font-medium">Admin</h5>
                                                {{-- <p class="font-light mb-0">{{ nl2br($comment->message) }}</p> --}}
                                                <p class="font-light mb-0">{!! $comment->message !!}</p>
                                                <div class="chat-time">{{ date('M d, Y h:iA', strtotime($comment->created_at)) }}</div>
                                            </div>
                                        </div>
                                        <div class="chat-img"><img src="{{ asset('/panel-assets/images/avatar.png') }}" alt="user"></div>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
