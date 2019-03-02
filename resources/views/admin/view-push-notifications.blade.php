<?php $nav_viewpushnotification = 'active'; ?>

@extends('layouts.app')

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Sent Notifications
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('/admin/home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">Sent Notifications</li>
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
                        <div class="col-md-1 col-md-offset-11 col-sm-2 col-sm-offset-10 col-xs-2 col-xs-offset-10 nopaddingright padding-bottom">
                            <a href="{{ url('admin/push-notification/create') }}" /><button type="button" class="btn btn-primary pull-right addnew-btn">New Notification </button></a>
                        </div>
                        <table id="example3" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Message</th>
                                    <th>Sent On</th>
                                    <th>Recipients</th>
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
                
            </div>
            <div class="col col-xs-8">
                <ul class="pagination pull-right">
                    
                </ul>
            </div>
        </div>
        <!-- /.row -->
    </section>
</div>
<script src="{{asset('admin/js/push-notification.js?v='.Config::get('cache.js_version_number')) }}"></script>
@endsection
