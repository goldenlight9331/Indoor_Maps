@extends('layouts.adminLayouts')
@section('admin-title', 'Profile')
@section('admin-content')
    <section class="section">
        <div class="row">
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
                        <h4>Update Profile</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('updateProfile') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"><b>
                                        Profile</b></label>
                                <div class="col-sm-12 col-md-7">
                                    <div id="image-preview" class="image-preview">
                                        <img class="image-preview" src="data:image/png;base64,{{ $users->image }}"
                                            alt="Company Image">
                                        <label for="image-upload" id="image-label">Choose File</label>
                                        <input type="file" name="image" id="image-upload" />

                                    </div>
                                    <font color="red">
                                        <b> @error('image')
                                                <span class="error">{{ $message }}</span>
                                            @enderror </b>
                                    </font>
                                </div>
                            </div>
                            {{-- <div class="form-group row mb-4"> --}}
                            <div class="form-group">
                                <div class="col-md-12 col-sm-12 mt-1">
                                    <label class="col-12 col-md-12 col-lg-12" for="name"><b>Name</b></label>
                                    <input type="text" class="form-control col-md-12 col-12" 
                                        value="{{ $users->name }}" 
                                        name="name" 
                                        id="name"
                                        {{ $users->name === 'NAVISupport' ? 'readonly' : '' }}>

                                </div>
                                <font color="red">
                                    <b> @error('name')
                                            <span class="error">{{ $message }}</span>
                                        @enderror </b>
                                </font>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12 col-sm-12 mt-1">
                                    <label class="col-12 col-md-12 col-lg-12" for="email"><b>Email </b></label>
                                    <input type="email" class="form-control col-md-12 col-12" value="{{ $users->email }}"
                                        name="email" id="email">
                                </div>
                                <font color="red">
                                    <b> @error('email')
                                            <span class="error">{{ $message }}</span>
                                        @enderror </b>
                                </font>
                            </div>
                            {{-- </div> --}}

                            <center>
                                <a href="{{ route('changePasswordUser', $users->id) }}" class="btn btn-warning"
                                    style="submit">Change
                                    Password
                                </a>
                                <button class="btn btn-primary" style="submit">Update</button>
                            </center>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection
