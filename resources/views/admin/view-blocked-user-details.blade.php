<?php 
    if(isset($flag) && $flag == 1){
        $nav_viewunblockeduser = 'active'; 
        $page_name = "Unblocked";
        $instance_time = 'deleted';
        $link = 'admin/unblocked-user/';
    }else{
        $nav_viewblockeduser = 'active'; 
        $page_name = "Blocked";
        $instance_time = 'created';
        $link = 'admin/blocked-user/';
    }    
?>

@extends('layouts.app')

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            {{ $page_name }} By Daters
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('/admin/home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Manage Daters</a></li>
            <li><a href="{{ url($link) }}">View {{ $page_name }} Daters</a></li>
            <li class="active">Dater {{ $page_name }} By</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body table-responsive">
                        <div class="col-md-3 col-md-offset-8 col-sm-5 col-sm-offset-5 col-xs-8 col-xs-offset-2">
                            <div class="form-group pull-right fullwidth input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right" id="daterange_search" value="">
                            </div>    
                        </div>
                        <div class="col-md-1 col-xs-1 nopaddingright pull-right">
                            <div class="form-group">
                                <button type="button" user_id="{{ $blocked_by_users['data'][0]['blocked_user_id'] }}" class="btn btn-primary pull-right blockedusersearch-btn">Search</button>
                            </div>    
                        </div>
                        <table id="example3" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Profile Picture</th>
                                    <th>Date and Time</th>
                                    <?php if(isset($flag) && $flag == 1){}else{ ?>
                                    <th>Reason</th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody id="users-list">
                                
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
                <?php if($blocked_by_users['last_page'] == 0) $blocked_by_users['last_page'] = 1; ?>
                Page {{ $blocked_by_users['current_page'] }} of {{ $blocked_by_users['last_page'] }}
            </div>
            <div class="col col-xs-8">
                <ul class="pagination pull-right">
                    <?php
                    if($blocked_by_users['current_page'] != 1){
                        echo "<li><a href='javascript:' userid='".$blocked_by_users['data'][0]['blocked_user_id']."' class='blockedby_user_page_dec'><<</a></li>";
                    }
                    $fromloop = 1;
                    $toloop = 10;
                    if($blocked_by_users['current_page'] > 5){
                        $fromloop = $blocked_by_users['current_page'] - 4;
                        $toloop = $blocked_by_users['current_page'] + 5;
                    }
                    if($toloop > $blocked_by_users['last_page']){
                        $toloop = $blocked_by_users['last_page'];
                    }
                    if($blocked_by_users['current_page'] == $blocked_by_users['last_page'] && $blocked_by_users['last_page'] > 10){
                        $fromloop = $blocked_by_users['current_page'] - 10;
                    }
                    for ($i = $fromloop; $i <= $toloop; $i++) {
                        if ($blocked_by_users['current_page'] == 1 && $i == 1) {
                            $act_class = 'page_active';
                        } else {
                            $act_class = '';
                        }
                        ?>
                        <li><a href='javascript:;' id='pagenum_{{ $i }}' userid="{{ $blocked_by_users['data'][0]['blocked_user_id'] }}" class='blockedby_user_page_num {{$act_class}}'>{{ $i }}</a></li>
                        <?php }
                        if($blocked_by_users['current_page'] != $blocked_by_users['last_page']){
                            echo "<li><a href='javascript:;' userid='".$blocked_by_users['data'][0]['blocked_user_id']."' class='blockedby_user_page_inc'>>></a></li>";
                        }
                        ?>
                </ul>
            </div>
        </div>
        <!-- /.row -->
    </section>
</div>
<?php if(isset($flag) && $flag == 1){ ?>
    <script src="{{asset('admin/js/unblockeduser.js?v='.Config::get('cache.js_version_number')) }}"></script>
<?php } else { ?>
    <script src="{{asset('admin/js/blockeduser.js?v='.Config::get('cache.js_version_number')) }}"></script>
<?php } ?>
@endsection
