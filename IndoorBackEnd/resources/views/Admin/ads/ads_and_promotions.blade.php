@extends('layouts.adminLayouts')
@section('admin-title', 'Ads and Promotions')
@section('admin-content')
    <section class="section">
        <div class="row">
            <div class="col-12">

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

                <div class="card m-2">
                    <div class="card-header">
                        <h4 class="text-center">Upload Ads</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <center>
                                <form action="{{ route('uploadAds') }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <label for="ads-image-upload" class="font-weight-bold">Ad 1</label>
                                        <div id="ads-image-preview" class="image-preview mb-2">
                                            @if(isset($ads->image))
                                                <img class="image-preview" src="data:image/png;base64,{{ $ads->image }}" alt="Ads Image">
                                            @else
                                                <img class="image-preview" src="path/to/default-image.png" alt="Default Image">
                                            @endif
                                            <label for="ads-image-upload" class="btn btn-outline-secondary mt-2">Choose File</label>
                                            <input type="file" name="image" id="ads-image-upload" class="d-none" required />
                                        </div>
                                        @error('image')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <button class="btn btn-primary" type="submit">Upload</button>
                                </form>
                                </center>
                            </div>

                            <div class="col-md-6">
                                <center>
                                <form action="{{ route('uploadPromotions') }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <label for="promotions-image-upload" class="font-weight-bold">Ad 2</label>
                                        <div id="promotions-image-preview" class="image-preview mb-2">
                                            @if(isset($promotion->image))
                                                <img class="image-preview" src="data:image/png;base64,{{ $promotion->image }}" alt="Promotion Image">
                                            @else
                                                <img class="image-preview" src="path/to/default-image.png" alt="Default Image">
                                            @endif
                                            <label for="promotions-image-upload" class="btn btn-outline-secondary mt-2">Choose File</label>
                                            <input type="file" name="image" id="promotions-image-upload" class="d-none" required />
                                        </div>
                                        @error('image')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <button class="btn btn-primary" type="submit">Upload</button>
                                </form>
                                </center>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection
