@extends('layouts.adminLayouts')
@section('admin-title', 'Change Users Password')
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
                    <h4>Change User Password</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('UpdatePasswordUser', $user->id) }}" method="post">
                        @csrf
                        {{-- <div class="col-6"> --}}
                        <div class="form-group">
                            <div class="col-md-12 col-sm-12 mt-1">
                                <label class="col-12 col-md-12 col-lg-12" for="current_password"><b>Current
                                        Password</b></label>
                                <input type="password" class="form-control col-md-12 col-12" name="current_password"
                                    id="current_password">
                                <font color="red">
                                    <b> @error('current_password')
                                            <span class="error">{{ $message }}</span>
                                        @enderror </b>
                                </font>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-12 col-sm-12 mt-1">
                                <label class="col-12 col-md-12 col-lg-12" for="new_password"><b>Password</b></label>
                                <input type="password" class="form-control col-md-12 col-12" name="new_password"
                                    id="new_password">
                                <font color="red">
                                    <b> @error('new_password')
                                            <span class="error">{{ $message }}</span>
                                        @enderror </b>
                                </font>
                            </div>
                        </div>
                        {{-- </div> --}}
                        {{-- <div class="col-6"> --}}
                        <div class="form-group">
                            <div class="col-md-12 col-sm-12 mt-1">
                                <label class="col-12 col-md-12 col-lg-12" for="password_confirmation"><b>Confirm
                                        Password</b></label>
                                <input type="password" class="form-control col-md-12 col-12" name="password_confirmation"
                                    id="password_confirmation">
                                <font color="red">
                                    <b> @error('password_confirmation')
                                            <span class="error">{{ $message }}</span>
                                        @enderror </b>
                                </font>
                            </div>
                        </div>
                        {{-- </div> --}}
                        <center>
                            <button class="btn btn-primary" style="submit">Save</button>
                        </center>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
