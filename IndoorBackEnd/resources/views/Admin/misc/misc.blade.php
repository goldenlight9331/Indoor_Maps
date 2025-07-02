@extends('layouts.adminLayouts')
@section('admin-title', 'Miscellaneous')
@section('admin-content')
<div class="container">
    <h1>Miscellaneous Settings</h1>

    <form action="{{ route('misc.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Screensaver Time Field -->
        <div class="form-group">
            <label for="screensaver_time">Screensaver Time (in seconds)</label>
            <input type="number" name="screensaver_time" class="form-control" value="{{ $miscSettings->screensaver_time }}" min="30" required>
        </div>

        <!-- City Field -->
        <div class="form-group">
            <label for="city">City</label>
            <input type="text" name="city" class="form-control" value="{{ $miscSettings->city }}" required>
        </div>

        <!-- State Field -->
        <div class="form-group">
            <label for="state">State</label>
            <input type="text" name="state" class="form-control" value="{{ $miscSettings->state }}" required>
        </div>

        <!-- Log Interval Field -->
        <div class="form-group">
            <label for="log_interval">Log Interval (in seconds)</label>
            <input type="number" name="log_interval" class="form-control" value="{{ $miscSettings->log_interval }}" required>
        </div>

        <!-- Day Image Upload Field -->
        <div class="form-group">
            <label for="day_image">Day Image</label>
            <div class="image-preview">
                @if($miscSettings->day_image)
                    <img class="image-preview" src="data:image/png;base64,{{ $miscSettings->day_image }}" alt="Day Image">
                @else
                    <img class="image-preview" src="path/to/default-image.png" alt="Default Day Image">
                @endif
                <label for="day_image-upload" id="day_image-label">Choose File</label>
                <input type="file" name="day_image" id="day_image-upload" />
            </div>
        </div>

        <!-- Night Image Upload Field -->
        <div class="form-group">
            <label for="night_image">Night Image</label>
            <div class="image-preview">
                @if($miscSettings->night_image)
                    <img class="image-preview" src="data:image/png;base64,{{ $miscSettings->night_image }}" alt="Night Image">
                @else
                    <img class="image-preview" src="path/to/default-image.png" alt="Default Night Image">
                @endif
                <label for="night_image-upload" id="night_image-label">Choose File</label>
                <input type="file" name="night_image" id="night_image-upload" />
            </div>
        </div>

        <!-- Save Button -->
        <button type="submit" class="btn btn-primary mt-3">Save Settings</button>
    </form>
</div>
@endsection
