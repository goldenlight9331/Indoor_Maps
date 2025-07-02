@extends('layouts.adminLayouts')
@section('admin-title', 'Create Users')
@section('admin-content')
    <section class="section">
        <div class="col-12">
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
                    <h4>Create User</h4>
                </div>
                <div class="card-body">

                    <form action="{{ route('saveUsers') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <div class="col-md-12 col-sm-12 mt-1">
                                        <label class="col-12 col-md-6 col-lg-6" for="name"><b>Name</b></label>
                                        <input type="text" value="{{ old('name') }}"
                                            class="form-control col-md-12 col-12" name="name" id="name">
                                        <font color="red">
                                            <b> @error('name')
                                                    <span class="error">{{ $message }}</span>
                                                @enderror </b>
                                        </font>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <div class="col-md-12 col-sm-12 mt-1">
                                        <label class="col-12 col-md-6 col-lg-6" for="email"><b>Email</b></label>
                                        <input type="email" value="{{ old('email') }}"
                                            class="form-control col-md-12 col-12" name="email" id="email">
                                        <font color="red">
                                            <b> @error('email')
                                                    <span class="error">{{ $message }}</span>
                                                @enderror </b>
                                        </font>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <div class="col-md-12 col-sm-12 mt-1">
                                        <label class="col-12 col-md-6 col-lg-6" for="password"><b>Password</b></label>
                                        <input type="password" class="form-control col-md-12 col-12" name="password"
                                            id="name">
                                        <font color="red">
                                            <b> @error('password')
                                                    <span class="error">{{ $message }}</span>
                                                @enderror </b>
                                        </font>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <div class="col-md-12 col-sm-12 mt-1">
                                        <label class="col-12 col-md-6 col-lg-6" for="password_confirmation"><b>Confirm
                                                Password</b></label>
                                        <input type="password" class="form-control col-md-12 col-12"
                                            name="password_confirmation" id="password_confirmation">
                                        <font color="red">
                                            <b> @error('password_confirmation')
                                                    <span class="error">{{ $message }}</span>
                                                @enderror </b>
                                        </font>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <center><button class="btn btn-primary" style="submit">Submit</button></center>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
