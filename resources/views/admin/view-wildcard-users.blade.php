<?php $nav_viewwildcarduser = 'active'; ?>

@extends('layouts.app')

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Wildcard Daters
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('/admin/home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Manage Daters</a></li>
            <li class="active">Wildcard Daters</li>
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
                                <button type="button" class="btn btn-primary pull-right wildcardsearch-btn">Search</button>
                            </div>    
                        </div>
                        <table id="example3" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Profile Image</th>
                                    <th>Dater's Bucket</th>
                                    <th>Date And Time</th>
                                    <th>No. of times dater became a wildcard</th>
                                    <th>No. of Daters rated</th>
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
<script src="{{asset('admin/js/wildcarduser.js?v='.Config::get('cache.js_version_number')) }}"></script>
@endsection
