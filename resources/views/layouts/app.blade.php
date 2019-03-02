<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="{{ asset('admin/img/favicon.ico?v='.Config::get('cache.favicon_version_number')) }}">
    <title>Bracket Dating Admin Panel</title>
    <link href="{{ asset('admin/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('admin/css/font-awesome.min.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('admin/css/style.css?v='.Config::get('cache.css_version_number')) }}">
    <link rel="stylesheet" href="{{ asset('admin/css/jquery-confirm.min.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/css/daterangepicker.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/css/select2.min.css') }}">
    
    <!-- Emoji CSS -->
    <link rel="stylesheet" href="{{asset('admin/css/nanoscroller.css') }}">
    <link rel="stylesheet" href="{{asset('admin/css/emoji.css') }}">
    
    <!-- Loading Default JS files -->
    <script src="{{asset('admin/js/app.js?v='.Config::get('cache.js_version_number')) }}"></script>
    <script src="{{asset('admin/js/admin.js?v='.Config::get('cache.js_version_number')) }}"></script>
    <script src="{{asset('admin/js/jquery-confirm.min.js') }}"></script>
    <script src='{{asset('admin/js/jquery.blockUI.js') }}'></script>
    <script src='{{asset('admin/js/select2.full.min.js') }}'></script>
    
    <!-- Jquery Validation -->
    <script src="{{asset('admin/js/jquery-validation/dist/jquery.validate.js')}}"></script>
    <script src="{{asset('admin/js/jquery-validation/dist/additional-methods.min.js')}}"></script>
    
    <!-- Custom js -->
    <script src="{{asset('admin/js/custom.js?v='.Config::get('cache.js_version_number')) }}"></script>
    
    @if (Auth::guest())
        
    @else
        <link rel="stylesheet" href="{{ asset('admin/css/skin-red.css') }}">
    @endif
   
</head>
<body id="app-layout" class="hold-transition login-page hold-transition skin-red fixed sidebar-mini">
    <div class="wrapper">
    @if (Auth::guest())
        <div class="login-logo">
          <a href="{{ url('/admin/login') }}"><b>Bracket Dating</b></a>
        </div>
    @else
    <header class="main-header">
        
            <!-- Logo -->
            <a href="{{ url('/admin/home') }}" class="logo">
                <!-- mini logo for sidebar mini 50x50 pixels -->
                <span class="logo-mini"><img src="{{ asset('admin/img/logo.png') }}" /></span>
                <!-- logo for regular state and mobile devices -->
                <span class="logo-lg"><img src="{{ asset('admin/img/logo.png') }}" /><b>Bracket Dating</b></span>
            </a>
            <!-- Header Navbar: style can be found in header.less -->
            <nav class="navbar navbar-static-top">
                <!-- Sidebar toggle button-->
                <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>

                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <!-- User Account: style can be found in dropdown.less -->
                        <li class="dropdown user user-menu">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                
                                @if( Auth::user()->profile_img == "" )
                                <img src="{{ asset('admin/img/default-img.png') }}" class="user-image" alt="User Image">
                                @else
                                <img src="{{ asset('img/upload/'.Auth::user()->profile_img) }}" class="user-image" alt="User Image">
                                @endif
                                <span class="hidden-xs">{{ Auth::user()->first_name }}</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <ul class="menu">
                                        <li><a href="{{ url('/admin/change-password') }}"> Change Password</a></li>
                                        <li>
                                        <a href="{{ url('/admin/logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>

                                        <form id="logout-form" action="{{ url('/admin/logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                    </ul>
                                </li>

                            </ul>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
        <aside class="main-sidebar">
            <!-- sidebar: style can be found in sidebar.less -->
            <section class="sidebar">

                <!-- sidebar menu: : style can be found in sidebar.less -->
                <ul class="sidebar-menu">
                    <li class="{{ $nav_viewdashboard or ''  }}">
                        <a href="{{ url('admin/home') }}">
                            <i class="fa fa-dashboard"></i> <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="treeview {{ $nav_viewuser or ''  }} {{ $nav_viewblockeduser or ''  }} {{ $nav_viewunblockeduser or ''  }} {{ $nav_viewuserprofile or ''  }} {{ $nav_viewwildcarduser or '' }} ">
                        <a href="#">
                            <i class="fa fa-users"></i> <span>Manage Daters</span>
                            <span class="pull-right-container">
                                <i class="fa fa-angle-left pull-right"></i>
                            </span>
                        </a>
                        <ul class="treeview-menu">
                            <li class="{{ $nav_viewuser or ''  }}"><a href="{{ url('/admin/user/') }}"><i class="fa fa-circle-o"></i> View All Daters</a></li>
                            <li class="{{ $nav_viewblockeduser or ''  }}"><a href="{{ url('/admin/blocked-user/') }}"><i class="fa fa-circle-o"></i> View Blocked Daters</a></li>
                            <li class="{{ $nav_viewunblockeduser or ''  }}"><a href="{{ url('/admin/unblocked-user/') }}"><i class="fa fa-circle-o"></i> View Unblocked Daters</a></li>
                            <li class="{{ $nav_viewwildcarduser or ''  }}"><a href="{{ url('/admin/wildcard-user/') }}"><i class="fa fa-circle-o"></i> View Wildcard Daters</a></li>
                        </ul>
                    </li>
                    <li class="{{ $nav_pushnotification or ''  }} {{ $nav_viewpushnotification or ''  }}">
                        <a href="{{ url('admin/push-notification') }}">
                            <i class="fa fa-bell"></i> <span>Sent Notifications</span>
                        </a>
                    </li>
                    <li class="{{ $nav_in_app_purchase or ''  }}">
                        <a href="{{ url('admin/in-app-purchase') }}">
                            <i class="fa fa-credit-card"></i> <span>In-app Purchase</span>
                        </a>
                    </li>
                </ul>
            </section>
            <!-- /.sidebar -->
        </aside>
        
    @endif
    @yield('content')
    </div>

    <!-- Include Date Range Picker -->
    <script src="{{asset('admin/js/moment.min.js')}}"></script>
    <script src="{{asset('admin/js/daterangepicker.js')}}"></script>
    
</body>
</html>
