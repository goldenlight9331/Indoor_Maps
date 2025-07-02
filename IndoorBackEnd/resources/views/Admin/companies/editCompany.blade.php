@extends('layouts.adminLayouts')
@section('admin-title', 'Edit Company')
@section('admin-content')
    <section class="section">

        <div class="card">
            <div class="card-header">
                <h4>Edit {{ $company->storename }} Data</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('updateCompanies', $company->id) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group row mb-4">
                        <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"><b>Store Image</b></label>
                        <div class="col-sm-12 col-md-7">
                            <div id="image-preview" class="image-preview">
                                {{-- <img class="image-preview" src="{{ asset($company->images) }}" alt="Company Image"> --}}
                                <img class="image-preview" src="data:image/png;base64,{{ $company->image_data }}"
                                    alt="Company Image">
                                <label for="image-upload" id="image-label">Choose File</label>
                                <input type="file" name="image" id="image-upload" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row mb-3">
                            <div class="col-md-6 col-sm-12 mt-1">
                                <label class="col-12 col-md-12 col-lg-12" for="storeName"><b>Store Name</b></label>
                                <input type="text" value="{{ $company->storename }}"
                                    class="form-control col-md-12 col-12" name="storename" id="storeName">
                            </div>

                            <div class="col-md-6 col-sm-12 mt-1">
                                <label class="col-12 col-md-12 col-lg-12" for="phone"><b>Phone</b></label>
                                <input type="text" value="{{ $company->phone }}" class="form-control col-md-12 col-12"
                                    name="phonenumber" id="phone">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row mb-3">
                            <div class="col-md-6 col-sm-12 mt-1">
                                <label class="col-12 col-md-12 col-lg-12" for="Category"><b>Category</b></label>
                                <input type="text" class="form-control col-md-12 col-12" value="{{ $company->category }}"
                                    name="category" id="Category">
                            </div>
                            <div class="col-md-6 col-sm-12 mt-1">
                                <label class="col-12 col-md-12 col-lg-12" for="sub_cat"><b>Sub Category</b></label>
                                <input type="text" class="form-control col-md-12 col-12" value="{{ $company->sub_cat }}"
                                    name="sub_cat" id="sub_cat">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row mb-3">
                            <div class="col-md-6 col-sm-12 mt-1">
                                <label class="col-12 col-md-12 col-lg-12" for="day"><b>Day</b></label>
                                <input type="text" class="form-control col-md-12 col-12" value="{{ $company->day }}"
                                    name="day" id="day">
                            </div>
                            <div class="col-md-6 col-sm-12 mt-1">
                                <label class="col-12 col-md-12 col-lg-12" for="time"><b>Time</b></label>
                                <input type="text" class="form-control col-md-12 col-12" value="{{ $company->time }}"
                                    name="time" id="time">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row mb-3">
                            <div class="col-md-6 col-sm-12 mt-1">
                                <label class="col-12 col-md-12 col-lg-12" for="day_1"><b>Day 1</b></label>
                                <input type="text" class="form-control col-md-12 col-12" value="{{ $company->day_1 }}"
                                    name="day_1" id="day_1">
                            </div>
                            <div class="col-md-6 col-sm-12 mt-1">
                                <label class="col-12 col-md-12 col-lg-12" for="time_1"><b>Time 1</b></label>
                                <input type="text" class="form-control col-md-12 col-12" value="{{ $company->time_1 }}"
                                    name="time_1" id="time_1">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row mb-3">
                            <div class="col-md-12 col-sm-12 mt-1">
                                <label class="col-12 col-md-12 col-lg-12" for="day_1"><b>Website</b></label>
                                <input type="text" class="form-control col-md-12 col-12" value="{{ $company->website }}"
                                    name="website" id="day_1">
                            </div>
                        </div>
                    </div>
                    <center><button class="btn btn-primary" style="submit">Update</button></center>
                </form>
            </div>
        </div>
    </section>
@endsection
