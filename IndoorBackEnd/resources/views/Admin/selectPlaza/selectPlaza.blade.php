<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- General CSS Files -->
    <link rel="stylesheet" href={{ asset('assets/css/app.min.css') }}>
    <!-- Template CSS -->
    <link rel="stylesheet" href={{ asset('assets/css/style.css') }}>
    <!-- Custom style CSS -->
    <link rel="stylesheet" href={{ asset('assets/css/custom.css') }}>
    <title>Select Plaza</title>
</head>

<body>
    <div class="loader"></div>
    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            <div class="navbar-bg"></div>
            <div class="main-content">
                <section class="section">
                    <div class="section-body">
                        <div class="row">
                            <div class="col-12 col-md-6 col-sm-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>SELECT PLAZA</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="empty-state" data-height="400">
                                            <div class="empty-state-icon">
                                                {{-- <i class="fas fa-question"></i> --}}
                                            </div>
                                            <h2>Select Plaza</h2>
                                            <div class="form-group">
                                                <select class="form-control form-control-sm">
                                                    <option>Option 1</option>
                                                    <option>Option 2</option>
                                                    <option>Option 3</option>
                                                </select>
                                            </div>
                                            <center> <a href="#" class="btn btn-primary mt-4">Create new One</a>
                                            </center>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <script src={{ asset('assets/js/app.min.js') }}></script>
    <!-- Template JS File -->
    <script src={{ asset('assets/js/scripts.js') }}></script>
    <!-- Custom JS File -->
    <script src={{ asset('assets/js/custom.js') }}></script>
</body>

</html>
