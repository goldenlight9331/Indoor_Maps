@extends('layouts.adminLayouts')
@section('admin-title', 'Log Statistics with Pie Chart')
@section('admin-content')
<section class="section">
    <div class="section-body">
        <div class="row">
            <div class="col-12">

                <div class="card">
                    <div class="card-header">
                        <h4>Log Statistics with Pie Chart</h4>
                    </div>
                    <div class="card-body">

                    <!-- Filter Form -->
                    <form action="{{ route('viewLogStatisticsChart') }}" method="GET">
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

                    <!-- Pie Chart -->
                    <canvas id="logStatisticsPieChart"></canvas>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('logStatisticsPieChart').getContext('2d');
        const logData = @json($logsPerCompany); // Use the data passed from the controller

        const labels = logData.map(log => log.company_name);
        const data = logData.map(log => log.total);

        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Log Statistics by Company',
                    data: data,
                    backgroundColor: ['#ff6384', '#36a2eb', '#cc65fe', '#ffce56', '#4bc0c0'],
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                }
            }
        });
    });
</script>
@endsection
