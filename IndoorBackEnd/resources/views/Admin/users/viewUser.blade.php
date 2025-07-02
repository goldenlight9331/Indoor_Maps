@extends('layouts.adminLayouts')
@section('admin-title', $user->name)
@section('admin-content')
    <section class="section">
        <center>
            <div class="col-6">
                <div class="card m-1">
                    @if (session()->has('message'))
                        <div class="alert alert-primary alert-dismissible show fade">
                            <div class="alert-body">
                                <button class="close" data-dismiss="alert">
                                    <span>&times;</span>
                                </button>
                                {{ session('message') }}
                            </div>
                        </div>
                    @endif
                    <div class="card-header">
                        <center>
                            <h4> View {{ $user->name }} Details</h4>
                        </center>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <h5 class="col-12"><b>Name</b></h5>
                            <p>{{ $user->name }}</p>
                        </div>
                        <div class="form-group">
                            <h5 class="col-12"><b>Email</b></h5>
                            <p>{{ $user->email }}</p>
                        </div>
                        <div class="form-group">
                            <h5 class="col-12"><b>Created at</b></h5>
                            <p>
                                {{ $user->created_at->diffForHumans() }}
                            </p>
                        </div>
                        <div class="form-group">
                            <a href="{{ route('allUser') }}" class="btn btn-danger"><i class="fa fa-backward"
                                    aria-hidden="true"></i></a>
                            <a href="{{ route('editUsers', $user->id) }}" class="btn btn-success"><i class="fa fa-edit"
                                    aria-hidden="true"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </center>
    </section>
@endsection
