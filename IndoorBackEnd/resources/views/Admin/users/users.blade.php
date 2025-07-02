@extends('layouts.adminLayouts')
@section('admin-title', 'All Users')
@section('admin-content')
<section class="section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
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
                    @if (session()->has('DeleteMessage'))
                        <div class="alert alert-danger alert-dismissible show fade">
                            <div class="alert-body">
                                <button class="close" data-dismiss="alert">
                                    <span>&times;</span>
                                </button>
                                {{ session('DeleteMessage') }}
                            </div>
                        </div>
                    @endif
                    <div class="card-header">
                        <div style="display: flex; flex-direction:row; justify-content:space-between; width:100%">
                            <h4>Export Table</h4>
                            <a href="{{ route('createUsers') }}" class="btn btn-primary text-white">Create User</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $data)
                                        <tr>
                                            <td>{{ $data->name }}</td>
                                            <td>{{ $data->email }}</td>
                                            <td>{{ $data->role }}</td>
                                            <td class="d-flex">
                                                <a href="{{ route('editUsers', $data->id) }}" class="btn btn-success"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                                <a href="{{ route('viewUser', $data->id) }}" class="btn btn-warning"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                                @if (!$data->isSuperAdmin() && auth()->user()->id !== $data->id)
                                                    <a href="{{ route('deleteUser', $data->id) }}" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?');"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                                @else
                                                    <button class="btn btn-danger" disabled><i class="fa fa-trash" aria-hidden="true"></i></button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
