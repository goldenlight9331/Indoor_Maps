@extends('layouts.adminLayouts')
@section('admin-title', 'Background Images')
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
                        <h4>Update Background Images
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('UpdateforntSetting') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"><b>
                                        Categories Page</b></label>
                                <div class="col-sm-12 col-md-7">
                                    <div id="image-preview" class="image-preview">

                                        {{-- <img src="{{ asset($fornt->home) }}" alt="Home Image"> --}}
                                        <img class="image-preview" src="data:image/png;base64,{{ $fornt->home }}"
                                            alt="Company Image">
                                        {{-- <img class="image-preview"
                                            src="{{ asset(str_replace('public', '', $fornt->home)) }}" alt="Company Image"> --}}
                                        <label for="image-upload-home" id="image-label">Choose File</label>
                                        <input type="file" name="home" id="image-upload-home" />
                                    </div>
                                    <font color="red">
                                        <b> @error('home')
                                                <span class="error">{{ $message }}</span>
                                            @enderror </b>
                                    </font>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"><b>
                                        Navigation Page</b></label>
                                <div class="col-sm-12 col-md-7">
                                    <div id="image-preview" class="image-preview">
                                        {{-- <img class="image-preview"
                                            src="{{ asset(str_replace('public', '', $fornt->navigation)) }}"
                                            alt="Company Image"> --}}
                                        <img class="image-preview" src="data:image/png;base64,{{ $fornt->navigation }}"
                                            alt="Company Image">
                                        <label for="image-upload-nav" id="image-label">Choose File</label>
                                        <input type="file" name="navigation" id="image-upload-nav" />

                                    </div>
                                    <font color="red">
                                        <b> @error('navigation')
                                                <span class="error">{{ $message }}</span>
                                            @enderror </b>
                                    </font>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"><b>
                                        More Page</b></label>
                                <div class="col-sm-12 col-md-7">
                                    <div id="image-preview" class="image-preview">
                                        <img class="image-preview" src="data:image/png;base64,{{ $fornt->more }}"
                                            alt="Company Image">
                                        <label for="image-upload-more" id="image-label">Choose File</label>
                                        <input type="file" name="more" id="image-upload-more" />
                                    </div>
                                    <font color="red">
                                        <b> @error('more')
                                                <span class="error">{{ $message }}</span>
                                            @enderror </b>
                                    </font>
                                </div>
                            </div>


                            <center><button class="btn btn-primary" style="submit">Upload</button></center>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </section>
@endsection
