@extends('layouts.adminLayouts')
@section('admin-title', 'Themes')
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
                        <h4>Pick Theme Color</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('themes.store') }}" method="post">
                            @csrf
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"><b>Map Container Color</b></label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="color" name="map_container_color" class="form-control" value="{{ $theme->map_container_color ?? '#ffffff' }}" required>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"><b>Lots color</b></label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="color" name="lots_color" class="form-control" value="{{ $theme->lots_color ?? '#ffffff' }}" required>
                                </div>
                            </div>
                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"><b>Clock Text Color</b></label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="color" name="color" class="form-control" value="{{ $theme->color ?? '#ffffff' }}" required>
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"><b>Button Color</b></label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="color" name="button_color" class="form-control" value="{{ $theme->button_color ?? '#ffffff' }}" required>
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"><b>Options Ribbon Color</b></label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="color" name="options_ribbon_color" class="form-control" value="{{ $theme->options_ribbon_color ?? '#ffffff' }}" required>
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"><b>Browse Venue Ribbon Color</b></label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="color" name="browse_venue_ribbon_color" class="form-control" value="{{ $theme->browse_venue_ribbon_color ?? '#ffffff' }}" required>
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"><b>Browse Venue Text Color</b></label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="color" name="browse_venue_text_color" class="form-control" value="{{ $theme->browse_venue_text_color ?? '#ffffff' }}" required>
                                </div>
                            </div>

                            <div class="form-group row mb-4">
                                <label class="col-form-label text-md-right col-12 col-md-3 col-lg-3"><b>Side Drawer Background Color</b></label>
                                <div class="col-sm-12 col-md-7">
                                    <input type="color" name="categories_background_color" class="form-control" value="{{ $theme->categories_background_color ?? '#ffffff' }}" required>
                                </div>
                            </div>

                            <center><button class="btn btn-primary" type="submit">Save</button></center>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection
