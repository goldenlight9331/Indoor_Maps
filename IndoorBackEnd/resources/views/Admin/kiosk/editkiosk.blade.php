@extends('layouts.adminLayouts')
@section('admin-title', 'Edit Kiosk')
@section('admin-content')
    <section class="section">
        <div class="row">
            <div class="col-3">
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
                        <h4>Edit Kioks</h4>
                    </div>
                    <div class="card-body">

                        <form action="{{ route('UpdateKiosk', $editKiosk->id) }}" method="post">
                            @csrf
                            <div class="form-group">
                                <div class="col-md-12 col-sm-12 mt-1">
                                    <label class="col-12 col-md-12 col-lg-12" for="kioksname"><b>Kioks Name</b></label>
                                    <input type="text" class="form-control col-md-12 col-12"
                                        value="{{ $editKiosk->name }}" name="kioksname" id="kioksname">
                                    <font color="red">
                                        <b> @error('kioksname')
                                                <span class="error">{{ $message }}</span>
                                            @enderror </b>
                                    </font>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12 col-sm-12 mt-1">
                                    <label class="col-12 col-md-12 col-lg-12" for="latitude"><b>Latitude</b></label>
                                    <input type="text" class="form-control col-md-12 col-12"
                                        value="{{ $editKiosk->Latitude }}" name="latitude" id="latitude">
                                </div>
                                <font color="red">
                                    <b> @error('latitude')
                                            <span class="error">{{ $message }}</span>
                                        @enderror </b>
                                </font>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12 col-sm-12 mt-1">
                                    <label class="col-12 col-md-12 col-lg-12" for="longitude"><b>Longitude </b></label>
                                    <input type="text" class="form-control col-md-12 col-12" name="longitude"
                                        value="{{ $editKiosk->Longitude }}" id="longitude">
                                </div>
                                <font color="red">
                                    <b> @error('longitude')
                                            <span class="error">{{ $message }}</span>
                                        @enderror </b>
                                </font>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12 col-sm-12 mt-1">
                                    <label><b>Level ID</b></label>
                                    <select class="form-control form-control-sm" name="levelID">>
                                        <option value="">Select Level-ID</option>
                                        @foreach ($distinctLevelIds as $levelid)
                                            <option value="{{ $levelid }}"
                                                {{ $levelid == $editKiosk->levelid ? 'selected' : '' }}>
                                                {{ $levelid }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <font color="red">
                                    <b> @error('levelID')
                                            <span class="error">{{ $message }}</span>
                                        @enderror </b>
                                </font>
                            </div>
                            <center><button class="btn btn-primary" style="submit">Submit</button></center>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-8">
                <div class="card ">
                    <div class="card-header">
                        <h4>All Kioks</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
                                <thead>
                                    <tr>
                                        <th>Kiosk Name</th>
                                        <th>Latitude</th>
                                        <th>Longitude</th>
                                        <th>Level ID</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($kiosks as $data)
                                        <tr>
                                            <td>{{ $data->name }}</td>
                                            <td>{{ $data->Latitude }}</td>
                                            <td>{{ $data->Longitude }}</td>
                                            <td>{{ $data->levelid }}</td>
                                            <td class="d-flex">
                                                <a href="{{ route('EditKiosk', $data->id) }}" class="btn btn-success"><i
                                                        class="fa fa-edit" aria-hidden="true"></i></a>
                                                {{-- <a href="{{ route('ViewKiosk', $data->id) }}" class="btn btn-warning"><i
                                                        class="fa fa-eye" aria-hidden="true"></i></a> --}}
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
    </section>
@endsection
