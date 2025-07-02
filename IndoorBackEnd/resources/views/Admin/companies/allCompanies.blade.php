@extends('layouts.adminLayouts')
@section('admin-title', 'All Companies')
@section('admin-content')
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>All Companies Data</h4>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th>Image</th>
                                            <th>Store Name</th>
                                            <th>Phone</th>
                                            <th>Days</th>
                                            <th>Time</th>
                                            {{-- <th>Website</th> --}}
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($companies as $data)
                                            <tr>
                                                <td>
                                                    <img style="width: 50px; height:50px" class="image-preview"
                                                        src="data:image/png;base64,{{ $data->image_data }}"
                                                        alt="Company Image">
                                                </td>
                                                <td>{{ $data->storename }}</td>
                                                <td>{{ $data->phone }}</td>
                                                <td>{{ $data->day }}</td>
                                                <td>{{ $data->time }}</td>
                                                {{-- <td>{{ $data->WEBSITE }}</td> --}}
                                                <td class="d-flex">
                                                    <a href="{{ route('editCompanies', $data->id) }}"
                                                        class="btn btn-success"><i class="fa fa-edit"
                                                            aria-hidden="true"></i>
                                                    </a>
                                                    {{-- <a href="{{ route('viewCompanies', $data->id) }}"
                                                        class="btn btn-warning"><i class="fa fa-eye" aria-hidden="true"></i>
                                                    </a> --}}
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
