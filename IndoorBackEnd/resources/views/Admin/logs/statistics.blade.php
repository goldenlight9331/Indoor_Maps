@extends('layouts.adminLayouts')
@section('admin-title', 'Log Statistics')
@section('admin-content')
<section class="section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">

                <div class="card">
                    <div class="card-header">
                        <h4>Log Statistics</h4>
                    </div>
                    <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('viewLogStatistics') }}" method="GET">
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
                                <select name="store_id" class="form-control">  <!-- New: Store dropdown -->
                                    <option value="">All Stores</option>
                                    @foreach ($companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->storename }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-auto">
                                <label for="start_date" class="sr-only">Start Date</label>
                                <input type="date" name="start_date" class="form-control" placeholder="Start Date" />
                            </div>
                            <div class="col-auto">
                                <label for="end_date" class="sr-only">End Date</label>
                                <input type="date" name="end_date" class="form-control" placeholder="End Date" />
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                    </form>

                    <a href="{{ route('viewLogStatisticsChart') }}"><button class="btn btn-primary">View Pie Chart</button></a>

                    <br>
                    <br>

                        <!-- Statistics Data -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h5>Total Logs</h5>
                                        <p>{{ $totalLogs }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h5>Logs in Last 24 Hours</h5>
                                        <p>{{ $logsLast24Hours }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Logs per Kiosk Table -->
                        <h4>Logs per Kiosk</h4>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Kiosk Name</th>
                                    <th>Total Logs</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($logsPerKiosk as $log)
                                    <tr>
                                        <td>{{ $log->kiosk_name }}</td> <!-- Display the kiosk name -->
                                        <td>{{ $log->total }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Logs per Company Table -->
                        <h4>Most Popular Companies</h4>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Company Name</th>
                                    <th>Total Logs</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($logsPerCompany as $log)
                                    <tr>
                                        <td>{{ $log->company_name }}</td> <!-- Display the company name -->
                                        <td>{{ $log->total }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection