<?php $nav_pushnotification = 'active'; ?>

@extends('layouts.app')

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Send Push Notification
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="{{ url('/admin/push-notification') }}"><i class="fa fa-bell"></i> Sent Notifications</a></li>
            <li class="active">Send Push Notification</li>
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
                    <form role="form" method="post" class="add_push_notification" id="add_push_notification" action="{{url('/admin/push-notification')}}">  
                        {{ csrf_field() }}
                        <div class="box-body">
                            <div class="form-group">
                                <label>Title <small>(Only 100 characters are allowed)</small></label>
                                <input id="notification_title" name="notification_title" class="form-control notification_title" placeholder="Enter title" type="text" >
                            </div>

                            <div class="form-group">
                                <label>Message <small>(Only 250 characters are allowed)</small></label>
                                <textarea class="form-control textarea-control notification_message" id="notification_message" placeholder="Type a message..." data-emojiable="true" name="notification_message"></textarea>
                            </div>                            
                            
                            <div class="form-group">
                                <label>Send To</label>
                                <div class="radio">   
                                    <label>
                                        <input type="radio" name="send_to_radios" class="select_to_radio" id="all_users" value="all"> All Users
                                    </label>
                                </div>
                                <div class="radio">   
                                    <label>
                                        <input type="radio" name="send_to_radios" class="select_to_radio" id="male_users" value="male"> Only Males 
                                    </label>
                                </div>
                                <div class="radio">   
                                    <label>
                                        <input type="radio" name="send_to_radios" class="select_to_radio" id="female_users" value="female"> Only Females
                                    </label>
                                </div>
                                <div class="radio">   
                                    <label>
                                        <input type="radio" name="send_to_radios" value="selected_users" class="select_to_radio" id="specific_users"> Specific Users
                                    </label>
                                    <div class="s2-example select-user hide" >
                                        <p>
                                            <select class="js-user-multiple js-users form-control" multiple="multiple" name="js_users[]"></select>
                                        </p>
                                    </div>
                                </div>
                                <div class="radio">   
                                    <label>
                                        <input type="radio" name="send_to_radios" class="select_to_radio" id="specific_location" value="selected_location"> Specific Location
                                    </label>
                                    <div class="s2-example select-location hide" >
                                        <p>
                                            <select class="js-location-multiple js-location form-control" multiple="multiple" name="js_location[]"></select>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>    
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">Send</button>
                        </div>
                    </form>
                </div>    
            </div>
        </div>
    </section>
</div>
<!-- Emoji JS -->
<script src="{{asset('admin/js/nanoscroller.min.js')}}"></script>
<script src="{{asset('admin/js/tether.min.js')}}"></script>
<script src="{{asset('admin/js/config.js')}}"></script>
<script src="{{asset('admin/js/util.js')}}"></script>
<script src="{{asset('admin/js/jquery.emojiarea.js')}}"></script>
<script src="{{asset('admin/js/emoji-picker.js')}}"></script>

<!-- Custom JS -->
<script src="{{asset('admin/js/push-notification.js?v='.Config::get('cache.js_version_number')) }}"></script>
@endsection
