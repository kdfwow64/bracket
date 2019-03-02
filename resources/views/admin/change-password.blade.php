@extends('layouts.app')

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Change Password
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Profile</a></li>
            <li class="active">Change Password</li>
        </ol>
    </section>
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if(Session::has('status'))
                    <p class="alert {{ Session::get('alert-class', 'alert-success') }}">{{ Session::get('status') }}</p>
                @endif
                
                @if(Session::has('status_fail'))
                    <p class="alert {{ Session::get('alert-class', 'alert-danger') }}">{{ Session::get('status_fail') }}</p>
                @endif
                
               
                <div class="box box-primary">
                    <form role="form" method="post" class="change_password" id="change_password" action="{{url('/admin/change-password')}}">  
                        {{ csrf_field() }}
                        <div class="box-body">
                            <div class="form-group">
                                <label>Current Password</label>
                                <input id="old_password" name="old_password" class="form-control" placeholder="Enter current password" type="password" >
                            </div>

                            <div class="form-group">
                                <label>New Password</label>
                                <input id="new_password" class="form-control" name="new_password" placeholder="Enter new password" type="password" >
                            </div>

                            <div class="form-group">
                                <label>Confirm Password</label>
                                <input id="confirm_password" class="form-control" name="confirm_password" placeholder="Confirm new password" type="password" >
                            </div>  
                        </div>    
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>    
            </div>
        </div>
    </section>
</div>
<script src="{{asset('admin/js/change-password.js?v='.Config::get('cache.js_version_number')) }}"></script>
@endsection
