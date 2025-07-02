@extends('layouts.adminLayouts')
@section('admin-title', 'Setting')
@section('admin-content')
    <section class="section">
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
                            <h4>Update Setting</h4>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('settingUpdate') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group row mb-4">
                                    <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"><b>
                                            Logo</b></label>
                                    <div class="col-sm-12 col-md-7">
                                        <div id="image-preview" class="image-preview">

                                            <img class="image-preview" src="data:image/png;base64,{{ $setting->image }}"
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
                                <div class="form-group">
                                    <div class="col-md-12 col-sm-12 mt-1">
                                        <label class="col-12 col-md-12 col-lg-12" for="name"><b>Name</b></label>
                                        <input type="text" value="{{ $setting->name }}"
                                            class="form-control col-md-12 col-12" name="name" id="name">
                                        <font color="red">
                                            <b> @error('name')
                                                    <span class="error">{{ $message }}</span>
                                                @enderror </b>
                                        </font>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12 col-sm-12 mt-1">
                                        <label class="col-12 col-md-12 col-lg-12" for="et"><b>Expiry Date</b></label>
                                        <input type="date" value="{{ $setting->et }}"
                                            class="form-control col-md-12 col-12" name="et" id="et">
                                        <font color="red">
                                            <b> @error('et')
                                                    <span class="error">{{ $message }}</span>
                                                @enderror </b>
                                        </font>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12 col-sm-12 mt-1">
                                        <label class="col-12 col-md-12 col-lg-12" for="nor_ang"><b>North Angle</b></label>
                                        <input type="number" value="{{ $setting->nor_ang }}"
                                            class="form-control col-md-12 col-12" name="nor_ang" id="nor_ang">
                                        <font color="red">
                                            <b> @error('nor_ang')
                                                    <span class="error">{{ $message }}</span>
                                                @enderror </b>
                                        </font>
                                    </div>
                                </div>

                                <center><button class="btn btn-primary" style="submit">Submit</button></center>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </section>
@endsection
