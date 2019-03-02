<?php $nav_viewdashboard = 'active'; ?>

@extends('layouts.app')

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Dashboard Analytics
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Dashboard</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="row">
            <div class="col-md-12">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#dater_analytics">Dater Analytics</a></li>
                    <li><a href="#inapp_analytics" class="inapp_analytics_tab">In-App Analytics</a></li>
                </ul>
                <div class="tab-content box">
                    <div id="dater_analytics" class="tab-pane fade in active box-body">
                        <div class="col-md-3 col-md-offset-8 col-sm-5 col-sm-offset-5 col-xs-8 col-xs-offset-2">
                            <div class="form-group pull-right fullwidth input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right" class="daterange_search" id="daterange_search" value="">
                            </div>    
                        </div>
                        <div class="col-md-1 col-xs-1 nopaddingright pull-right">
                            <div class="form-group">
                                <button type="button" class="btn btn-primary pull-right analyticsfilter-btn">Search</button>
                            </div>    
                        </div>

                        <div class="col-md-4 col-sm-6 col-xs-12">
                            <div class="box">
                                <!-- /.box-header -->
                                <div class="box-body table-responsive">
                                    <table id="example3" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Gender Analytics</th>
                                                <th>No. of Daters</th>
                                            </tr>
                                        </thead>
                                        <tbody id="gender-analytics">
                                            <tr>
                                                <td>No. of Male Daters</td>
                                                <td>{{ $dashboard_data['gender_analytics']['male'] }}</td>
                                            </tr>
                                            <tr>
                                                <td>No. of Female Daters</td>
                                                <td>{{ $dashboard_data['gender_analytics']['female'] }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.box-body -->
                            </div>
                            <!-- /.box -->
                            <div class="box">
                                <!-- /.box-header -->
                                <div class="box-body table-responsive">
                                    <table id="example3" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Bracket Analytics</th>
                                                <th>Count</th>
                                            </tr>
                                        </thead>
                                        <tbody id="bracket-analytics">
                                            <tr>
                                                <td>No. of Brackets</td>
                                                <td>{{ $dashboard_data['bracket_analytics']['Brackets'] }}</td>
                                            </tr>
                                            <tr>
                                                <td>No. of Winners</td>
                                                <td>{{ $dashboard_data['bracket_analytics']['Winners'] }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.box-body -->
                            </div>
                            <!-- /.box -->
                        </div>

                        <div class="col-md-4 col-sm-6 col-xs-12">
                            <div class="box">
                                <!-- /.box-header -->
                                <div class="box-body table-responsive scroller-div">
                                    <table id="example3" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Age Group Analytics</th>
                                                <th>No. of Daters</th>
                                            </tr>
                                        </thead>
                                        <tbody id="age-analytics">
                                            <?php foreach ($dashboard_data['age_analytics'] as $age_group => $count_value): ?>
                                                <tr>
                                                    <td>{{ $age_group }}</td>
                                                    <td>{{ $count_value }}</td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.box-body -->
                            </div>
                            <!-- /.box -->
                        </div>

                        <div class="col-md-4 col-sm-6 col-xs-12">
                            <div class="box">
                                <!-- /.box-header -->
                                <div class="box-body table-responsive scroller-div">
                                    <table id="example3" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Occupation Analytics</th>
                                                <th>No. of Daters</th>
                                            </tr>
                                        </thead>
                                        <tbody id="occupation-analytics">
                                            <?php foreach ($dashboard_data['occupation_analytics'] as $occupation => $count_value): ?>
                                                <tr>
                                                    <td>{{ $occupation }}</td>
                                                    <td>{{ $count_value }}</td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.box-body -->
                            </div>
                            <!-- /.box -->
                        </div>

                        <div class="col-md-2 col-md-offset-10 col-sm-4 col-sm-offset-8 col-xs-6 col-xs-offset-6 download-xls-btn">
                            <a href="{{ URL::to('admin/downloadExcel') }}"><button class="btn btn-success pull-right">Download CSV</button></a>  
                        </div> 
                    </div>

                    <div id="inapp_analytics" class="tab-pane fade box-body">
                        <div class="col-md-3 col-md-offset-8 col-sm-5 col-sm-offset-5 col-xs-8 col-xs-offset-2">
                            <div class="form-group pull-right fullwidth input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right daterange_search_inapp" id="daterange_search_inapp" value="">
                            </div>    
                        </div>
                        <div class="col-md-1 col-xs-1 nopaddingright pull-right">
                            <div class="form-group">
                                <button type="button" class="btn btn-primary pull-right inapp-analyticsfilter-btn">Search</button>
                            </div>    
                        </div>
                        <div class="col-md-6 col-xs-12">
                            <div class="box">
                                <!-- /.box-header -->
                                <div class="box-body table-responsive scroller-div">
                                    <table id="example3" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>In-App Purchase</th>
                                                <th>No. of New Daters</th>
                                            </tr>
                                        </thead>
                                        <tbody id="inapp-analytics">
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.box-body -->
                            </div>
                            <!-- /.box -->
                        </div>
                        
                        <div class="col-md-6 col-xs-12">
                            <div class="box">
                                <!-- /.box-header -->
                                <div class="box-body table-responsive scroller-div">
                                    <table id="example3" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>In-App Purchase</th>
                                                <th>Total Amount Received</th>
                                            </tr>
                                        </thead>
                                        <tbody id="amount-analytics">
                                        </tbody>
                                    </table>
                                </div>
                                <!-- /.box-body -->
                            </div>
                            <!-- /.box -->
                        </div>
                        <div class="col-md-2 col-md-offset-10 col-sm-4 col-sm-offset-8 col-xs-6 col-xs-offset-6 inapp-download-xls-btn">
                            <a href="{{ URL::to('admin/downloadExcel') }}"><button class="btn btn-success pull-right">Download CSV</button></a>  
                        </div> 
                    </div>
                </div>
            </div>
        </div>    
        <!-- /.row -->

    </section>
    <!-- /.content -->
</div>
<script src="{{asset('admin/js/dashboard.js?v='.Config::get('cache.js_version_number')) }}"></script>
@endsection
