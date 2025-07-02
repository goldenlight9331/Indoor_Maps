<meta charset="UTF-8">
<meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
<title>@yield('admin-title')</title>
<!-- General CSS Files -->
<link rel="stylesheet" href={{ asset('assets/css/app.min.css') }}>
<link rel="stylesheet" href={{ asset('assets/bundles/summernote/summernote-bs4.css') }}>
<link rel="stylesheet" href={{ asset('assets/bundles/jquery-selectric/selectric.css') }}>
<link rel="stylesheet" href={{ asset('assets/bundles/bootstrap-tagsinput/dist/bootstrap-tagsinput.css') }}>
<!-- Template CSS -->
<link rel="stylesheet" href={{ asset('assets/css/style.css') }}>
<link rel="stylesheet" href={{ asset('assets/css/components.css') }}>
<link rel="stylesheet" href={{ asset('assets/bundles/datatables/datatables.min.css') }}>
<link rel="stylesheet"
    href={{ asset('assets/bundles/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}>
<!-- Custom style CSS -->
<!-- JS Libraies -->
<script src={{ asset('assets/bundles/summernote/summernote-bs4.js') }}></script>
<script src={{ asset('assets/bundles/jquery-selectric/jquery.selectric.min.js') }}></script>
<script src={{ asset('assets/bundles/upload-preview/assets/js/jquery.uploadPreview.min.js') }}></script>
<script src={{ asset('assets/bundles/bootstrap-tagsinput/dist/bootstrap-tagsinput.min.js') }}></script>
<link rel="stylesheet" href={{ asset('assets/css/custom.css') }}>
<script src={{ asset('assets/js/page/create-post.js') }}></script>
<link rel='shortcut icon' type='image/x-icon' href={{ asset('assets/img/favicon.ico') }} />
{{-- Leftlet css --}}
@yield('leaflet-css')

</head>

<body>
    <div class="loader"></div>
    <div id="app">
        <div class="main-wrapper main-wrapper-1">Pinned
            <div class="navbar-bg"></div>
            <nav class="navbar navbar-expand-lg main-navbar sticky">
                <div class="form-inline mr-auto">
                    <ul class="navbar-nav mr-3">
                        <li><a href="#" data-toggle="sidebar"
                                class="nav-link nav-link-lg
                                      collapse-btn"> <i
                                    data-feather="align-justify"></i></a></li>
                        <li><a href="#" class="nav-link nav-link-lg fullscreen-btn">
                                <i data-feather="maximize"></i>
                            </a>
                        </li>
                    </ul>
                </div>
                <ul class="navbar-nav navbar-right">
                    @php
                        use Illuminate\Support\Facades\Request;
                        $currentUrl = Request::url();
                        if (strpos($currentUrl, 'profile') === false) {
                            $user = Auth::user()->image;
                            // if (Auth::user()->image) {
                            //     Auth::user()->image = Auth::user()->image
                            //         ? stream_get_contents(Auth::user()->image)
                            //         : null;
                            // }
                        }
                    @endphp
                    <li class="dropdown">
                        <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                            <img alt="image" src="data:image/png;base64,{{ Auth::user()->image }}">
                        </a>

                        <div class="dropdown-menu dropdown-menu-right pullDown">

                            <div class="dropdown-title">Hello, {{ ucfirst(Auth::user()->name) }}!</div>
                            <a href="{{ route('profile') }}" class="dropdown-item has-icon"> <i class="far fa-user"></i> Profile</a>
                            <a href="{{ route('setting') }}" class="dropdown-item has-icon"> <i class="fas fa-cog"></i>
                                Settings
                            </a>
                            @php
                                $users = Auth()->user();
                            @endphp
                            <a href="{{ route('changePasswordUser', $users->id) }}" class="dropdown-item has-icon"> <i
                                    class="fa fa-lock" aria-hidden="true"></i>
                                Change Password
                            </a>
                            <div class="dropdown-divider"></div>
                            <a onclick="event.preventDefault();document.getElementById('logout-form').submit();"
                                href="{{ route('logout') }}" class="dropdown-item has-icon text-danger"><i
                                    class="fa fa-sign-out"></i>
                                Logout</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none">
                                @csrf
                            </form>
                        </div>
                    </li>
                </ul>
            </nav>
            <div class="main-sidebar sidebar-style-2" style="display: flex; flex-direction: column; height: 100vh;">
                <aside id="sidebar-wrapper" style="flex-grow: 1; display: flex; flex-direction: column;">
                    <div class="sidebar-brand">
                        <a href="{{ route('dashboard') }}">
                            @php
                                use App\Models\setting;
                                $settings = setting::findOrFail(1);
                                // if ($settings->image) {
                                //     $settings->image = $settings->image ? stream_get_contents($settings->image) : null;
                                // }
                                $setting_image = $settings->image;
                                $setting_name = $settings->name;
                            @endphp
                            <img alt="image" src="data:image/png;base64,{{ $setting_image }}" class="header-logo" />
                            {{-- <img alt="image" src=" $setting_image }}" class="header-logo" /> --}}
                            <span class="logo-name">{{ ucfirst($setting_name) }}</span>
                        </a>
                    </div>
                    <ul class="sidebar-menu" style="flex-grow: 1;">
                        {{-- <li class="menu-header">Main</li> --}}
                        <li class="dropdown">
                            <a href="{{ route('dashboard') }}" class="nav-link"><i
                                    class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="menu-toggle nav-link has-dropdown"><i
                                    class="fas fa-user-secret"></i><span>Admin</span></a>
                            <ul class="dropdown-menu">
                                <li><a class="nav-link" href="{{ route('allCompanies') }}">Companies</a></li>
                                <li><a class="nav-link" href="{{ route('allUser') }}">User</a></li>
                                <li><a class="nav-link" href="{{ route('kiosk') }}">Kiosk</a></li>
                                <li><a class="nav-link" href="{{ route('viewLogs') }}">Logs</a></li>
                            </ul>
                        </li>
                        {{-- <li class="menu-header">UI Elements</li> --}}
                        <li class="dropdown">
                            <a href="#" class="menu-toggle nav-link has-dropdown"><i
                                    class="fas fa-info-circle"></i><span>Theme Settings</span></a>
                            <ul class="dropdown-menu">
                                <li><a href="{{ route('ads') }}" class="nav-link"><i class="fas fa-ad"></i><span>Ads</span></a></li>
                                <li><a href="{{ route('banners') }}" class="nav-link"><i class="fas fa-flag"></i><span>Banners</span></a></li>
                                <li><a href="{{ route('videos') }}" class="nav-link"><i class="fas fa-video"></i><span>Video</span></a></li>
                                <li><a href="{{ route('themes.index') }}" class="nav-link"><i class="fas fa-palette"></i><span>Theme Color</span></a></li>
                                <li><a href="{{ route('forntSetting') }}" class="nav-link"><i class="fas fa-laptop-code"></i><span>Front Images</span></a></li>
                                <li><a href="{{ route('misc') }}" class="nav-link"><i class="fas fa-flask"></i><span>Miscellaneous</span></a></li>
                            </ul>
                        </li>

                        {{-- <li class="dropdown">
                            <a href="#" class="nav-link dropdown-toggle" id="navbarDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="far fa-file"></i><span>Export & Download</span>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <form action="{{ route('sqlite') }}" method="GET">
                                    <button type="submit" class="dropdown-item">SQLite</button>
                                </form>
                                <!-- Add more export options here if needed -->
                            </div>
                        </li> --}}

                        <li class="dropdown">
                            <a href="{{ route('setting') }}" class="nav-link"><i
                                    class="fas fa-cog"></i><span>Settings</span></a>
                        </li>

                        <li class="dropdown">
                            <a href="{{ route('profile') }}" class="nav-link"><i
                                    class="far
                                fa-user"></i><span>Profile</span></a>
                        </li>

                        <li class="dropdown">
                            <a onclick="event.preventDefault();document.getElementById('logout-form').submit();"
                                href="{{ route('logout') }}" class="nav-link"><i class="fas fa-sign-out-alt"></i>
                                Logout</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                style="display: none">
                                @csrf
                            </form>
                        </li>

                    </ul>

                    <footer style="padding: 20px; text-align: center;">
                        <a href="https://navigosmarttech.co.uk/" target='_blank'>
                            <img src="assets/img/navigo-logo.jpg" height='50'>
                        </a>
                        <br><br>
                        <a href="mailto:info@navigosmarttech.co.uk">info@navigosmarttech.co.uk</a>
                    </footer>
                </aside>
            </div>
            <!-- Main Content -->
            <div class="main-content">
                @if (Session::has('success'))
                    <div class="alert alert-success">
                        {{ Session::get('success') }}
                    </div>
                @endif
                @yield('admin-content')
                <div class="settingSidebar">
                    <a href="javascript:void(0)" class="settingPanelToggle"> <i class="fa fa-spin fa-cog"></i>
                    </a>
                    <div class="settingSidebar-body ps-container ps-theme-default">
                        <div class=" fade show active">
                            <div class="setting-panel-header">Setting Panel
                            </div>
                            <div class="p-15 border-bottom">
                                <h6 class="font-medium m-b-10">Select Layout</h6>
                                <div class="selectgroup layout-color w-50">
                                    <label class="selectgroup-item">
                                        <input type="radio" name="value" value="1"
                                            class="selectgroup-input-radio select-layout" checked>
                                        <span class="selectgroup-button">Light</span>
                                    </label>
                                    <label class="selectgroup-item">
                                        <input type="radio" name="value" value="2"
                                            class="selectgroup-input-radio select-layout">
                                        <span class="selectgroup-button">Dark</span>
                                    </label>
                                </div>
                            </div>
                            <div class="p-15 border-bottom">
                                <h6 class="font-medium m-b-10">Sidebar Color</h6>
                                <div class="selectgroup selectgroup-pills sidebar-color">
                                    <label class="selectgroup-item">
                                        <input type="radio" name="icon-input" value="1"
                                            class="selectgroup-input select-sidebar">
                                        <span class="selectgroup-button selectgroup-button-icon" data-toggle="tooltip"
                                            data-original-title="Light Sidebar"><i class="fas fa-sun"></i></span>
                                    </label>
                                    <label class="selectgroup-item">
                                        <input type="radio" name="icon-input" value="2"
                                            class="selectgroup-input select-sidebar" checked>
                                        <span class="selectgroup-button selectgroup-button-icon" data-toggle="tooltip"
                                            data-original-title="Dark Sidebar"><i class="fas fa-moon"></i></span>
                                    </label>
                                </div>
                            </div>
                            <div class="p-15 border-bottom">
                                <h6 class="font-medium m-b-10">Color Theme</h6>
                                <div class="theme-setting-options">
                                    <ul class="choose-theme list-unstyled mb-0">
                                        <li title="white" class="active">
                                            <div class="white"></div>
                                        </li>
                                        <li title="cyan">
                                            <div class="cyan"></div>
                                        </li>
                                        <li title="black">
                                            <div class="black"></div>
                                        </li>
                                        <li title="purple">
                                            <div class="purple"></div>
                                        </li>
                                        <li title="orange">
                                            <div class="orange"></div>
                                        </li>
                                        <li title="green">
                                            <div class="green"></div>
                                        </li>
                                        <li title="red">
                                            <div class="red"></div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="p-15 border-bottom">
                                <div class="theme-setting-options">
                                    <label class="m-b-0">
                                        <input type="checkbox" name="custom-switch-checkbox"
                                            class="custom-switch-input" id="mini_sidebar_setting">
                                        <span class="custom-switch-indicator"></span>
                                        <span class="control-label p-l-10">Mini Sidebar</span>
                                    </label>
                                </div>
                            </div>
                            <div class="p-15 border-bottom">
                                <div class="theme-setting-options">
                                    <label class="m-b-0">
                                        <input type="checkbox" name="custom-switch-checkbox"
                                            class="custom-switch-input" id="sticky_header_setting">
                                        <span class="custom-switch-indicator"></span>
                                        <span class="control-label p-l-10">Sticky Header</span>
                                    </label>
                                </div>
                            </div>
                            <div class="mt-4 mb-4 p-3 align-center rt-sidebar-last-ele">
                                <a href="#" class="btn btn-icon icon-left btn-primary btn-restore-theme">
                                    <i class="fas fa-undo"></i> Restore Default
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- leftlet js --}}
    @yield('leaflet-js')

    <!-- General JS Scripts -->
    <script src={{ asset('assets/js/app.min.js') }}></script>
    <!-- JS Libraies -->
    <script src={{ asset('assets/bundles/apexcharts/apexcharts.min.js') }}></script>
    <!-- Page Specific JS File -->
    <script src={{ asset('assets/js/page/index.js') }}></script>
    <script src={{ asset('assets/bundles/datatables/datatables.min.js') }}></script>
    <script src={{ asset('assets/bundles/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}></script>
    <script src={{ asset('assets/bundles/datatables/export-tables/dataTables.buttons.min.js') }}></script>
    <script src={{ asset('assets/bundles/datatables/export-tables/buttons.flash.min.js') }}></script>
    <script src={{ asset('assets/bundles/datatables/export-tables/jszip.min.js') }}></script>
    <script src={{ asset('assets/bundles/datatables/export-tables/pdfmake.min.js') }}></script>
    <script src={{ asset('assets/bundles/datatables/export-tables/vfs_fonts.js') }}></script>
    <script src={{ asset('assets/bundles/datatables/export-tables/buttons.print.min.js') }}></script>
    <script src={{ asset('assets/js/page/datatables.js') }}></script>
    <!-- Template JS File -->
    <script src={{ asset('assets/js/scripts.js') }}></script>
    <!-- Custom JS File -->
    <script src={{ asset('assets/js/custom.js') }}></script>
</body>

</html>
