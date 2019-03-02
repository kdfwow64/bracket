<?php 
    if(isset($flag) && $flag == 1){
        $nav_viewunblockeduser = 'active'; 
        $page_name = "Unblocked";
        $link = 'admin/unblocked-user/';
    }else{
        $nav_viewblockeduser = 'active'; 
        $page_name = "Blocked";
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
            {{ $page_name }} Daters
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('/admin/home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Manage Daters</a></li>
            <li class="active">{{ $page_name }} Daters</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        @if(Session::has('status'))
            <p class="alert {{ Session::get('alert-class', 'alert-danger') }}">{{ Session::get('status') }}</p>
        @endif
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <!-- /.box-header -->
                    <div class="box-body table-responsive">
                        <table id="example3" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Profile Picture</th>
                                    <th>{{ $page_name }} By</th>
                                </tr>
                            </thead>
                            <tbody id="users-list">
                                <?php
                                if(count($blocked_users['data']) == 0){
                                echo "<tr>
                                        <td colspan='4'>No records found</td>
                                      </tr>";
                                }
                                foreach ($blocked_users['data'] as $blockedUser):
                                    if($blockedUser['blocked_to']['email'] == "") $blockedUser['blocked_to']['email'] = "Not Shared";
                                    ?>
                                    <tr>
                                        <td>{{ $blockedUser['blocked_to']['first_name'] }} {{ substr($blockedUser['blocked_to']['last_name'] ,0, 1) }}</td>
                                        <td>{{ $blockedUser['blocked_to']['email'] }}</td>
                                        <td>
                                            <?php if ($blockedUser['blocked_to']['profile_picture'] == "" || $blockedUser['blocked_to']['profile_picture'] == NULL) { ?>
                                                <img class="roundimg img-responsive" src="{{ asset('admin/img/default-img.png') }}" />
                                            <?php } else { ?>
                                                <img class="roundimg img-responsive" src="{{ $blockedUser['blocked_to']['profile_picture'] }}" />
                                            <?php } ?>
                                        </td>
                                        <td><a href="{{ url($link.$blockedUser['blocked_user_id']) }}">{{ $blockedUser['total'] }} dater(s)</a></td>
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
                <?php if($blocked_users['last_page'] == 0) $blocked_users['last_page'] = 1; ?>
                Page {{ $blocked_users['current_page'] }} of {{ $blocked_users['last_page'] }}
            </div>
            <div class="col col-xs-8">
                <ul class="pagination pull-right">
                    <?php
                    if($blocked_users['current_page'] != 1){
                        echo "<li><a href='javascript:;' class='blocked_user_page_dec'><<</a></li>";
                    }
                    $fromloop = 1;
                    $toloop = 10;
                    if($blocked_users['current_page'] > 5){
                        $fromloop = $blocked_users['current_page'] - 4;
                        $toloop = $blocked_users['current_page'] + 5;
                    }
                    if($toloop > $blocked_users['last_page']){
                        $toloop = $blocked_users['last_page'];
                    }
                    if($blocked_users['current_page'] == $blocked_users['last_page'] && $blocked_users['last_page'] > 10){
                        $fromloop = $blocked_users['current_page'] - 10;
                    }
                    for ($i = $fromloop; $i <= $toloop; $i++) {
                        if ($blocked_users['current_page'] == 1 && $i == 1) {
                            $act_class = 'page_active';
                        } else {
                            $act_class = '';
                        }
                        ?>
                        <li><a href='javascript:;' id='pagenum_{{ $i }}' class='blocked_user_page_num {{$act_class}}'>{{ $i }}</a></li>
                        <?php }
                        if($blocked_users['current_page'] != $blocked_users['last_page']){
                            echo "<li><a href='javascript:;' class='blocked_user_page_inc'>>></a></li>";
                        }
                        ?>
                </ul>
            </div>
        </div>
        <!-- /.row -->
    </section>
</div>
<?php
if(isset($flag) && $flag == 1){ ?>
    <script src="{{asset('admin/js/unblockeduser.js?v='.Config::get('cache.js_version_number')) }}"></script>
<?php } else { ?>
    <script src="{{asset('admin/js/blockeduser.js?v='.Config::get('cache.js_version_number')) }}"></script>
<?php } ?>
@endsection
