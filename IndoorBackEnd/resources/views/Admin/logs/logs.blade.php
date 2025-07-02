@extends('layouts.adminLayouts')
@section('admin-title', 'Logs')
@section('admin-content')
    <section class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3>Log Data</h3>
                        </div>

                        <div class="card-body">
                            {{-- Display validation errors --}}
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif


                            <form action="{{ route('viewLogs') }}" method="GET">
                                <div class="form-row align-items-center mb-3">
                                    <div class="col-auto">
                                        <select name="kiosk_id" class="form-control">
                                            <option value="">All Kiosks</option>
                                            @foreach ($kiosks as $kiosk)
                                                <option value="{{ $kiosk->id }}">{{ $kiosk->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-auto">
                                        <label for="start_date" class="sr-only">Start Date</label>
                                        <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $startDate) }}" />
                                    </div>
                                    <div class="col-auto">
                                        <label for="end_date" class="sr-only">End Date</label>
                                        <input type="date" name="end_date" class="form-control" value="{{ old('end_date', $endDate) }}" />
                                    </div>
                                    <div class="col-auto">
                                        <button type="submit" class="btn btn-success">Filter</button>
                                    </div>

                                </div>
                            </form>

                            <a href="{{ route('viewLogStatistics') }}"><button class="btn btn-primary">View Statistics</button></a>


                            <br>
                            <br>

                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="tableExport" style="width:100%;">
                                    <thead>
                                        <tr>
                                            <th>Kiosk ID</th>
                                            <th>Kiosk Name</th>
                                            <th>Store ID</th>
                                            <th>Store Name</th>
                                            <th>Timestamp</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($logs as $log)
                                            <tr>
                                                <td>{{ $log->kiosk_id }}</td>
                                                <td>{{ $log->kiosk_name }}</td>
                                                <td>{{ $log->store_id }}</td>
                                                <td>{{ $log->company_name }}</td>
                                                <td>{{ $log->timestamp }}</td>
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

    @section('scripts')
    <script>
        $(document).ready(function() {
            $('#tableExport').DataTable({
                pageLength: 20,  // Set the default number of rows per page
                lengthMenu: [ [10, 20, 50, 100], [10, 20, 50, 100] ],  // Add dropdown options for pagination
                responsive: true,  // Make the table responsive for different screen sizes
                dom: 'lfrtip',  // 'l' shows the length changing dropdown
                // Additional configuration options can be added here
            });
        });
    </script>
    @endsection
@endsection
