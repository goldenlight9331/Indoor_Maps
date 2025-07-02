@extends('layouts.adminLayouts')
@section('admin-title', 'Banners')
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
                        <h4>Upload Banners</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('uploadBanners') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"><b>Banners</b></label>
                                <div class="col-sm-12 col-md-7">
                                    <div id="image-preview" class="image-preview">
                                        @if(isset($banners) && $banners->image)
                                        <img class="image-preview" src="data:image/png;base64,{{ $banners->image }}" alt="Banner Image">
                                        @else
                                            <img class="image-preview" src="path/to/default-image.png" alt="Default Image">
                                        @endif
                                        
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
                            <center><button class="btn btn-primary" style="submit">Upload</button></center>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
