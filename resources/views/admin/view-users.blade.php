<?php $nav_viewuser = 'active'; ?>

@extends('layouts.app')

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Daters
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('/admin/home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Manage Daters</a></li>
            <li class="active">Daters</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        @if(Session::has('status'))
            <p class="alert {{ Session::get('alert-class', 'alert-danger') }}">{{ Session::get('status') }}</p>
        @endif
        @if(Session::has('status_sent'))
            <p class="alert {{ Session::get('alert-class', 'alert-success') }}">{{ Session::get('status_sent') }}</p>
        @endif
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body table-responsive">
                        <div class="row">
                        <div class="col-md-2 col-sm-2 col-xs-1">
                            <a class="pull-left" href="{{ URL::to('admin/downloadDatersExcel') }}"><button class="btn btn-success pull-right">Download CSV</button></a> 
                        </div>
                        <div class="col-md-3 col-md-offset-6 col-sm-5 col-sm-offset-4 col-xs-6 col-xs-offset-3">
                            <div class="form-group pull-right fullwidth">
                                <input type="search" id="usersearch" value="" class="form-control" placeholder="Enter Name/Email/Location...">
                            </div>    
                        </div>
                        <div class="col-md-1 col-xs-1">
                            <div class="form-group">
                                <button type="button" class="btn btn-primary pull-right usersearch-btn">Search</button>
                            </div>    
                        </div>
                        </div>
                        <table id="example3" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Profile Image</th>
                                    <th>Age</th>
                                    <th>Current Location</th>
                                    <th>Gender</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="users-list">
                                <?php
                                if(count($projects['data']) == 0){
                                echo "<tr>
                                        <td colspan='7'>No records found</td>
                                      </tr>";
                                }
                                foreach ($projects['data'] as $project):
                                    if($project['email'] == ""){
                                        $project['email'] = "Not Shared";
                                    }
                                    if($project['age'] == 0){
                                        $project['age'] = "Not Shared";
                                    }
                                    if($project['country'] == ""){
                                        $project['country'] = "Not Shared";
                                    }
                                    ?>
                                    <tr>
                                        <td>{{ $project['first_name'] }} {{ substr($project['last_name'] ,0, 1) }}</td>
                                        <td>{{ $project['email'] }}</td>
                                        <td>
                                            <?php if ($project['profile_picture'] == "" || $project['profile_picture'] == NULL) { ?>
                                                <img class="roundimg img-responsive" src="{{ asset('admin/img/default-img.png') }}" />
                                            <?php } else { ?>
                                                <img class="roundimg img-responsive" src="{{ $project['profile_picture'] }}" />
                                            <?php } ?>
                                        </td>
                                        <td>{{ $project['age'] }}</td>
                                        <td>{{ $project['country'] }}</td>
                                        <td>
                                            <?php if($project['gender'] == 0){
                                                echo "Female";
                                            }else{
                                                echo "Male";
                                            } ?>
                                        </td>
                                        <td>
                                            <a href="{{ url('admin/user/'.$project['id']) }}"><button type="button" class="btn btn-sm btn-success view viewbtn">View Profile</button></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->
        </div>
        <div class="row">
            <div class="col col-xs-4 totalList">
                <?php if($projects['last_page'] == 0) $projects['last_page'] = 1; ?>
                Page {{ $projects['current_page'] }} of {{ $projects['last_page'] }}
            </div>
            <div class="col col-xs-8">
                <ul class="pagination pull-right">
                    <?php
                    if($projects['current_page'] != 1){
                        echo "<li><a href='javascript:;' class='search_page_dec'><<</a></li>";
                    }
                    $fromloop = 1;
                    $toloop = 10;
                    if($projects['current_page'] > 5){
                        $fromloop = $projects['current_page'] - 4;
                        $toloop = $projects['current_page'] + 5;
                    }
                    if($toloop > $projects['last_page']){
                        $toloop = $projects['last_page'];
                    }
                    if($projects['current_page'] == $projects['last_page'] && $projects['last_page'] > 10){
                        $fromloop = $projects['current_page'] - 10;
                    }
                    for ($i = $fromloop; $i <= $toloop; $i++) {
                        if ($projects['current_page'] == 1 && $i == 1) {
                            $act_class = 'page_active';
                        } else {
                            $act_class = '';
                        }
                        ?>
                        <li><a href='javascript:;' id='pagenum_{{ $i }}' class='user_page_num {{$act_class}}'>{{ $i }}</a></li>
                        <?php }
                        if($projects['current_page'] != $projects['last_page']){
                            echo "<li><a href='javascript:;' class='search_page_inc'>>></a></li>";
                        }
                        ?>
                </ul>
            </div>
        </div>
        <!-- /.row -->
    </section>
</div>
<script src="{{asset('admin/js/user.js?v='.Config::get('cache.js_version_number')) }}"></script>
@endsection
