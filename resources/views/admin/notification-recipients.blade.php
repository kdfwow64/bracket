<?php $nav_viewpushnotification = 'active'; ?>

@extends('layouts.app')

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Notification Recipients
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('/admin/home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="{{ url('/admin/push-notification') }}"><i class="fa fa-bell"></i> Sent Notification</a></li>
            <li class="active">Notification Recipients</li>
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
                                    <th>Gender</th>
                                    <th>Location</th>
                                </tr>
                            </thead>
                            <tbody id="users-list">
                                <?php
                                if (count($notification_recipients['data']) == 0) {
                                    echo "<tr>
                                        <td colspan='5'>No records found</td>
                                      </tr>";
                                }
                                foreach ($notification_recipients['data'] as $notification_recipient):
                                    if($notification_recipient['user_details']['email'] == ""){
                                        $notification_recipient['user_details']['email'] = "Not Shared";
                                    }
                                    if($notification_recipient['user_details']['country'] == ""){
                                        $notification_recipient['user_details']['country'] = "Not Shared";
                                    }
                                    ?>
                                    <tr>
                                        <td>{{ $notification_recipient['user_details']['first_name'] }} {{ substr($notification_recipient['user_details']['last_name'],0, 1) }}</td>
                                        <td>{{ $notification_recipient['user_details']['email'] }}</td>
                                        <td>
                                            <?php if ($notification_recipient['user_details']['profile_picture'] == "" || $notification_recipient['user_details']['profile_picture'] == NULL) { ?>
                                                <img class="roundimg img-responsive" src="{{ asset('admin/img/default-img.png') }}" />
                                            <?php } else { ?>
                                                <img class="roundimg img-responsive" src="{{ $notification_recipient['user_details']['profile_picture'] }}" />
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php if($notification_recipient['user_details']['gender'] == 0){
                                                echo "Female";
                                            }else{
                                                echo "Male";
                                            } ?>
                                        </td>
                                        <td>{{ $notification_recipient['user_details']['country'] }}</td>
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
                <?php if ($notification_recipients['last_page'] == 0) $notification_recipients['last_page'] = 1; ?>
                Page {{ $notification_recipients['current_page'] }} of {{ $notification_recipients['last_page'] }}
            </div>
            <div class="col col-xs-8">
                <ul class="pagination pull-right">
                    <?php
                    if ($notification_recipients['current_page'] != 1) {
                        echo "<li><a href='javascript:;' user_id=".$notification_recipients['data'][0]['id']." class='recipient_page_dec'><<</a></li>";
                    }
                    $fromloop = 1;
                    $toloop = 10;
                    if ($notification_recipients['current_page'] > 5) {
                        $fromloop = $notification_recipients['current_page'] - 4;
                        $toloop = $notification_recipients['current_page'] + 5;
                    }
                    if ($toloop > $notification_recipients['last_page']) {
                        $toloop = $notification_recipients['last_page'];
                    }
                    if ($notification_recipients['current_page'] == $notification_recipients['last_page'] && $notification_recipients['last_page'] > 10) {
                        $fromloop = $notification_recipients['current_page'] - 10;
                    }
                    for ($i = $fromloop; $i <= $toloop; $i++) {
                        if ($notification_recipients['current_page'] == 1 && $i == 1) {
                            $act_class = 'page_active';
                        } else {
                            $act_class = '';
                        }
                        ?>
                        <li><a href='javascript:;' id='pagenum_{{ $i }}' user_id='{{ $notification_recipients['data'][0]['id'] }}' class='recipient_page_num {{$act_class}}'>{{ $i }}</a></li>
                            <?php
                        }
                        if ($notification_recipients['current_page'] != $notification_recipients['last_page']) {
                            echo "<li><a href='javascript:;' user_id=".$notification_recipients['data'][0]['id']." class='recipient_page_inc'>>></a></li>";
                        }
                        ?>
                </ul>
            </div>
        </div>
        <!-- /.row -->
    </section>
</div>
<script src="{{asset('admin/js/push-notification.js?v='.Config::get('cache.js_version_number')) }}"></script>
@endsection
