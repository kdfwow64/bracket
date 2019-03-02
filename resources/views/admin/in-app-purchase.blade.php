<?php $nav_in_app_purchase = 'active'; ?>

@extends('layouts.app')

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            In-App Purchase
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('/admin/home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">In-App Purchase</li>
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
                        <div class="pull-left heading_font">Total in-app purchases in this month : <strong>{{ $total_purchases }}</strong></div>
                    </div>    
                    <div class="box-body table-responsive">
                        <table id="example3" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Type</th>
                                    <th>No. of purchases</th>
                                    <th>Date And Time</th>
                                    <th>Total Amount Received</th>
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
        
    </section>
</div>
<script src="{{asset('admin/js/inapp-users.js?v='.Config::get('cache.js_version_number'))}}"></script>
@endsection
