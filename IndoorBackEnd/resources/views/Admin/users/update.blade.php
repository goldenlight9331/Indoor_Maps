@extends('layouts.adminLayouts')
@section('admin-title', 'Edit Users')
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
                <h4>Update User</h4>
            </div>
            <div class="card-body">
            <form action="{{ route('updateUsers', $user->id) }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="name"><b>Name</b></label>
                            <input type="text" value="{{ $user->name }}"
                                class="form-control" name="name" id="name" 
                                {{ $user->name === 'NAVISupport' ? 'readonly' : '' }}>
                            @error('name')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <div class="col-md-12 col-sm-12 mt-1">
                                <label class="col-12 col-md-6 col-lg-6" for="email"><b>Email</b></label>
                                <input type="email" value="{{ $user->email }}"
                                    class="form-control col-md-12 col-12" name="email" id="email" 
                                    {{ $user->name === 'NAVISupport' ? 'readonly' : '' }}>
                                <font color="red">
                                    <b> @error('email')
                                            <span class="error">{{ $message }}</span>
                                        @enderror </b>
                                </font>
                            </div>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="form-group">
                            <label for="role"><b>Role</b></label>
                            <select class="form-control" name="role" id="role" {{ $user->name === 'NAVISupport' ? 'disabled' : '' }}>
                                <option value="User" {{ $user->role === 'User' ? 'selected' : '' }}>User</option>
                                <option value="Admin" {{ $user->role === 'Admin' ? 'selected' : '' }}>Admin</option>
                                <option value="Super Admin" {{ $user->role === 'Super Admin' ? 'selected' : '' }}>Super Admin</option>
                            </select>
                            @error('role')
                                <span class="error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">Submit</button>
            </form>
            </div>
        </div>
    </div>
</section>
@endsection
