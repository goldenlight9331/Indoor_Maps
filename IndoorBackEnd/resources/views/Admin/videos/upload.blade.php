@extends('layouts.adminLayouts')
@section('admin-title', 'Upload Videos')
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
                @if (session()->has('error'))
                    <div class="alert alert-danger alert-dismissible show fade">
                        <div class="alert-body">
                            <button class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                            {{ session('error') }}
                        </div>
                    </div>
                @endif
                <div class="card-header">
                    <h4>Upload Videos</h4>
                </div>
                <div class="card-body">
                    <!-- Form for Landscape Video -->
                    <form action="{{ route('uploadVideos') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="video_type" value="landscape">
                        <div class="form-group row mb-4">
                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"><b>Landscape Video</b></label>
                            <div class="col-sm-12 col-md-7">
                                <input type="file" name="video" class="form-control" required>
                                <font color="red"><b> @error('video') <span class="error">{{ $message }}</span> @enderror </b></font>
                            </div>
                        </div>
                        <center><button class="btn btn-primary" type="submit">Upload Landscape</button></center>
                    </form>

                    <!-- Form for Portrait Video -->
                    <form action="{{ route('uploadVideos') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="video_type" value="portrait">
                        <div class="form-group row mb-4">
                            <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"><b>Portrait Video</b></label>
                            <div class="col-sm-12 col-md-7">
                                <input type="file" name="video" class="form-control" required>
                                <font color="red"><b> @error('video') <span class="error">{{ $message }}</span> @enderror </b></font>
                            </div>
                        </div>
                        <center><button class="btn btn-primary" type="submit">Upload Portrait</button></center>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
