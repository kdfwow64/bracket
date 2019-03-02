<?php $nav_viewuser = 'active'; ?>

@extends('layouts.app')

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Dater Profile
        </h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('/admin/home') }}"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Manage Daters</a></li>
            <li><a href="{{ url('/admin/user') }}">View All Daters</a></li>
            <li class="active">Dater Profile</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#user_details">Dater Details</a></li>
                    <li><a class="get_ratings" userid='{{ $projects['id'] }}' href="#user_ratings">Ratings Received</a></li>
                </ul>
                <div class="tab-content box">
                    <div id="user_details" class="tab-pane fade in active box-body">
                        <div class="details-div">&nbsp;
                            <?php
                            if ($projects['profile_picture'] == "") {
                                $imgsrc = asset('admin/img/default-img.png');
                            } else {
                                $imgsrc = $projects['profile_picture'];
                            }
                            if($projects['age'] == 0){
                                $projects['age'] = 'Not Shared';
                            }  
                            if($projects['height'] == 0){
                                $projects['height'] = "Not Shared";
                            }else{
                                $ft = floor($projects['height'] / 12);
                                $inches = $projects['height'] % 12;
                                $projects['height'] = $ft."' ".$inches."''";
                            }
                            if($projects['average_rating'] == 1){
                                $bucket = '1 and 2';
                            }else if($projects['average_rating'] == 5){
                                $bucket = '4 and 5';
                            }else{
                                $bucket = ($projects['average_rating'] - 1).' , '.($projects['average_rating']).' and '.($projects['average_rating'] + 1);
                            }
                            ?>
                            <div class="detail-row"><p>Name : <span>{{ $projects['first_name'] }} {{ substr($projects['last_name'] ,0, 1) }}</span></p></div>
                            <div class="detail-row"><p>Email : <span>{{ $projects['email'] }}</span></p></div>
                            <div class="detail-row"><p>Profile Image : </p>
                                <div class="row"><div class="col-lg-3 col-md-4 col-xs-6 thumb"><a class="thumbnail" href="#"><img class="img-responsive" src="{{ $imgsrc }}" alt=""></a></div></div>
                            </div>
                            <div class="detail-row"><p>Age : <span>{{ $projects['age'] }}</span></p></div>
                            <div class="detail-row"><p>Gender : <span>{{ Config::get('constants.gender_db.'.$projects['gender']) }}</span></p></div>
                            <div class="detail-row"><p>Location Radius Preference : <span>{{ $projects['end_radius'] }} miles</span></p></div>
                            <div class="detail-row"><p>Gender Preference : <span>{{ Config::get('constants.gender_db.'.$projects['prefer_gender']) }}</span></p></div>
                            <div class="detail-row"><p>Occupation : <span>{{ $projects['occupation'] }}</span></p></div>
                            <div class="detail-row"><p>Height : <span>{{ $projects['height'] }}</span></p></div>
                            <div class="detail-row"><p>Rating Level : </p>
                                <img class="img-responsive" src="{{ asset(Config::get('constants.img_rating.'.$projects['gender'].'.'.$projects['average_rating'].'.url')) }}" alt="">
                                <span>{{ Config::get('constants.img_rating.'.$projects['gender'].'.'.$projects['average_rating'].'.name') }}</span>
                            </div>
                            <div class="detail-row"><p>Bucket : <span>{{ $bucket }}</span></p>
                                
                            </div>
                            <div class="detail-row"><p>About Dater : <span>{{ $projects['about_me'] }}</span></p></div>
                            <div class="detail-row"><p>Question 1 : <span class="non-bold">{{ $projects['user_question1']['question'] }}</span></p><p>Answer : <span class="non-bold">{{ $projects['question_1_answer'] }}</span></p></div>
                            <div class="detail-row"><p>Question 2 : <span class="non-bold">{{ $projects['user_question2']['question'] }}</span></p><p>Answer : <span class="non-bold">{{ $projects['question_2_answer'] }}</span></p></div>
                            <div class="detail-row"><p>Question 3 : <span class="non-bold">{{ $projects['user_question3']['question'] }}</span></p><p>Answer : <span class="non-bold">{{ $projects['question_3_answer'] }}</span></p></div>

                        </div>
                    </div>
                    <div id="user_ratings" class="tab-pane fade table-responsive box-body">
                        <div class="col-md-3 col-md-offset-8 col-sm-5 col-sm-offset-6 col-xs-8 col-xs-offset-3">
                            <div class="form-group pull-right fullwidth input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control pull-right" id="daterange_search" value="">
                            </div>    
                        </div>
                        <div class="col-md-1 col-xs-1 nopaddingright">
                            <div class="form-group">
                                <button type="button" user_id="{{ $projects['id'] }}" class="btn btn-primary pull-right ratingsearch-btn">Search</button>
                            </div>    
                        </div>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Profile Image</th>
                                    <th>Rating Given</th>
                                    <th>Date and Time</th>
                                </tr>
                            </thead>
                            <tbody id="users-rating-list">
                                
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
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
<script src="{{asset('admin/js/user.js?v='.Config::get('cache.js_version_number')) }}"></script>
@endsection
