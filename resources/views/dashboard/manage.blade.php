@extends('layouts.app')


@section('breadcrumb')
    <ol class="breadcrumb pb-0" style="background-color:transparent">
        <li class="breadcrumb-item"><a class="breadcrumb-link" href="/">km4aanr</a></li>
        <li class="breadcrumb-item"><a class="breadcrumb-link" href="/">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Manage</li>
    </ol>
@endsection

<?php
    $consortiaAdminRequests = App\User::where('consortia_admin_request', '=', 1)->count();
    $aanrPage = App\AANRPage::first();
    $pcaarrdPage = App\PCAARRDPage::first();
    $headlines = App\Headline::all(); 
    $landing_page = App\LandingPageElement::find(1);
    $sliders = App\LandingPageSlider::all(); 
    $social_media = App\SocialMediaSticky::all();
    $header_links = App\HeaderLink::all(); 
?>

<style>
    .center-td{
        vertical-align:inherit !important;
    }
    .form-switch {
        display: inline-block;
        cursor: pointer;
        -webkit-tap-highlight-color: transparent;
    }

    .form-switch i {
        position: relative;
        display: inline-block;
        margin-right: .5rem;
        width: 46px;
        height: 26px;
        background-color: #e6e6e6;
        border-radius: 23px;
        vertical-align: text-bottom;
        transition: all 0.3s linear;
    }

    .form-switch i::before {
        content: "";
        position: absolute;
        left: 0;
        width: 42px;
        height: 22px;
        background-color: #fff;
        border-radius: 11px;
        transform: translate3d(2px, 2px, 0) scale3d(1, 1, 1);
        transition: all 0.25s linear;
    }

    .form-switch i::after {
        content: "";
        position: absolute;
        left: 0;
        width: 22px;
        height: 22px;
        background-color: #fff;
        border-radius: 11px;
        box-shadow: 0 2px 2px rgba(0, 0, 0, 0.24);
        transform: translate3d(2px, 2px, 0);
        transition: all 0.2s ease-in-out;
    }

    .form-switch:active i::after {
        width: 28px;
        transform: translate3d(2px, 2px, 0);
    }

    .form-switch:active input:checked + i::after { transform: translate3d(16px, 2px, 0); }

    .form-switch input { display: none; }

    .form-switch input:checked + i { background-color: #4BD763; }

    .form-switch input:checked + i::before { transform: translate3d(18px, 2px, 0) scale3d(0, 0, 0); }

    .form-switch input:checked + i::after { transform: translate3d(22px, 2px, 0); }

    .landing-page-image{
        max-height:310px;
        max-width:590px;
    }
   .form-check-input {
       margin-left:0px !important;
   }
   .section-header{
        height:4.5rem;
        background-image: linear-gradient(to bottom, rgb(95,189,226) , rgb(77,171,214));
        padding-top: 20px;
        font-size: 1.125rem;
        font-weight: 900;
        box-shadow: inset 0px 0px 15px 5px #6dbddd !important;
   }
   .list-group-item{
        width:100%;
        border: 0px;
        font-size: 1.125rem;
        font-weight: 500;
        height:4.5rem;
        background-color: inherit !important;
        border-top-color: rgb(83,98,114) !important;
        border-bottom-color: rgb(123, 138, 155) !important;
        border-style: solid !important;
        border-width: 2px 0px;
        color:rgb(207, 207, 207);
    }
    .center {
        margin: auto;
        padding: 10px;
    }
    .list-group-item.active {
        background-color: rgb(71,87,102) !important;
        border-color: rgb(71,87,102) !important;
    }
    a.list-group-item:hover {
        text-decoration: none !important;
        color: white;
    }
    .tech-table{
        overflow-y:scroll;
        overflow-x:scroll;
        height:100%;
    }
</style>
@section('content')
    <!-- Modal Includes -->
    @include('dashboard.modals.artifact')
    @include('dashboard.modals.aanrpage')
    @include('dashboard.modals.agrisyunaryos')
    @include('dashboard.modals.pcaarrdpage')
    @include('dashboard.modals.industry')
    @include('dashboard.modals.sector')
    @include('dashboard.modals.isp')
    @include('dashboard.modals.commodity')
    @include('dashboard.modals.consortia')
    @include('dashboard.modals.consortiaMembers')
    @include('dashboard.modals.contenttype')
    @include('dashboard.modals.contentSubtype')
    @include('dashboard.modals.advertisement')
    @include('dashboard.modals.agendas')
    @include('dashboard.modals.apientries')
    @include('dashboard.modals.announcements')
    @include('dashboard.modals.users')
    <div class="container-fluid">
        <div class="row" style="max-height:inherit; min-height:52.5rem">
            <div class="col-xl-2 col-md-3 pl-0 pr-0" style="background-image: linear-gradient(to right, rgb(118,128,138) , rgb(79, 94, 109));">
                <div class="nav nav-tabs" style="border-bottom-width: 0px;">
                    <a class="list-group-item active" data-toggle="tab" href="#user_profile" style="padding-top:23px; padding-left:32px">
                        <span><i class="fas fa-user" style="margin-right:0.8rem"></i> User Profile</span>
                    </a>
                    <a class="list-group-item" data-toggle="tab" href="#landing_page" style="padding-top:23px; padding-left:32px">
                        <span><i class="fas fa-home" style="margin-right:0.8rem"></i> Manage Landing Page</span>
                    </a>
                    <a class="list-group-item" data-toggle="tab" href="#artifacts" style="padding-top:23px; padding-left:32px">
                        <span><i class="fas fa-database" style="margin-right:0.8rem"></i> Manage Resources</span>
                    </a>
                    <a class="list-group-item" data-toggle="tab" href="#users" style="padding-top:23px; padding-left:32px">
                        <span><i class="fas fa-user-friends" style="margin-right:0.8rem"></i> Manage Users <span class="badge badge-warning" style="{{$consortiaAdminRequests != 0 ? '' : 'display:none'}}">!</span></span>
                    </a>
                    <a class="list-group-item" data-toggle="tab" href="#logs" style="padding-top:23px; padding-left:32px">
                        <span><i class="fas fa-clipboard-list" style="margin-right:0.8rem"></i> Activity Logs</span>
                    </a>
                    <a class="list-group-item" href="/analytics/search" style="padding-top:23px; padding-left:32px">
                        <span><i class="fas fa-chart-line" style="margin-right:0.8rem"></i> Dashboard</span>
                    </a>
                </div>
            </div>

            <script>
                $(function() {
                    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                        localStorage.setItem('lastTab', $(this).attr('href'));
                    });
                    var lastTab = localStorage.getItem('lastTab');
                    if (lastTab) {
                        $('[href="' + lastTab + '"]').tab('show');
                    }
                });
            </script>
            <div class="col-xl-10 col-md-9 pl-0 pr-0">
                <div class="tab-content">
                    <div class="tab-pane fade  active show" id="user_profile">
                        <div class="section-header shadow px-5">
                            <span class="text-white mr-3">Manage Profile </span>
                        </div>
                        
                        @include('layouts.messages')
                        <div class="card shadow mb-5 mt-0 ml-0">
                            <div class="card-header px-5 pt-4" >
                                <h2 class="text-primary" >
                                    Edit Account Details
                                </h2>
                            </div>

                            {{ Form::open(['action' => ['UsersController@editUser', auth()->user()->id], 'method' => 'POST']) }}
                            <div class="row card-body px-5">
                                <div class="col-sm-6">
                                    @csrf
                                    <div class="form-group" style="margin-bottom:0.2rem">
                                        <label for="name" class="col-form-label font-weight-bold required">{{ __('Full Name') }}</label>
                                        <div class="row">
                                            <div class="col-md-6 pr-0">
                                                <input id="first_name" type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" value="{{auth()->user()->first_name}}" required autocomplete="first_name" autofocus>
                                                @error('first_name')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                                <small class="ml-1"><label for="first_name" class="col-form-label text-muted">{{ __('First Name') }}</label></small>
                                            </div>
                                            <div class="col-md-6">
                                                {{Form::text('last_name', auth()->user()->last_name, ['class' => 'form-control', 'required',])}}
                                                @error('last_name')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                                <small class="ml-1"><label for="last_name" class="col-form-label text-muted">{{ __('Last Name') }}</label></small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="email" class="col-form-label font-weight-bold required">{{ __('E-Mail Address') }}</label>
                                        <input id="email" type="email" placeholder="example@email.com" class="form-control @error('email') is-invalid @enderror" name="email" value="{{auth()->user()->email}}" required autocomplete="email">
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                        <label class="checkbox-inline mt-2"><input type="checkbox" name="subscribe" {{auth()->user()->subscribed == 1 ? 'checked' : ''}} value="1"> Get emails and latest updates from us</label>
                                    </div>
                                    <div class="form-group">
                                        {{Form::label('gender', 'Gender', ['class' => 'col-form-label font-weight-bold'])}}
                                        <br>
                                        {{ Form::radio('gender', 'male' , auth()->user()->gender == 'male' ? 'checked' : '') }} Male
                                        {{ Form::radio('gender', 'female' , auth()->user()->gender == 'female' ? 'checked' : '', ['class' => 'ml-3']) }} Female
                                    </div>
                                    <div class="form-group">
                                        {{Form::label('age_range', 'Age Range', ['class' => 'col-form-label'])}}
                                        {{Form::select('age_range', ['1' => '15-18', 
                                                                    '2' => '19-22', 
                                                                    '3' => '23-30', 
                                                                    '4' => '31-40', 
                                                                    '5' => '41-50', 
                                                                    '6' => '51-60', 
                                                                    '7' => '61 Onwards', 
                                                                    ], auth()->user()->age_range,['class' => 'form-control', 'placeholder' => '------------']) }}

                                    </div>
                                    <div class="form-group">
                                        {{Form::label('organization', 'Organization', ['class' => 'col-form-label font-weight-bold required'])}}   
                                        
                                        <select class="form-control" data-live-search="true" name="select_org" id="select_org">
                                            <option disabled selected>Select Organization</option>
                                            @foreach(App\Consortia::all() as $consortium_account_details)
                                                <option value="{{$consortium_account_details->short_name}}" {{auth()->user()->organization == $consortium_account_details->short_name  ? 'selected' : ''}}>{{$consortium_account_details->short_name}}</option>
                                            @endforeach
                                            <option value="PCAARRD" {{auth()->user()->organization == 'PCAARRD'  ? 'selected' : ''}}>PCAARRD</option>
                                            <option value='other' {{auth()->user()->is_organization_other == 1  ? 'selected' : ''}}>Other</option>
                                        </select> 
                                        <div class="form-group consortia-input" >
                                            {{Form::label('others_org', 'If Other, please specify', ['class' => 'col-form-label'])}}
                                            {{Form::text('others_org', auth()->user()->is_organization_other == 1 ? auth()->user()->organization : '', ['class' => 'form-control'])}}
                                        </div>
                                        
                                    </div>
                                    <div class="form-group">
                                        {{Form::label('account_type', 'User Account Type', ['class' => 'col-form-label font-weight-bold'])}}
                                        <br>  
                                        @if(auth()->user()->role == 5)
                                            <span class="badge bg-success px-3 pt-2 "><h5 class="text-white">Superadmin</h5></span>
                                        @elseif(auth()->user()->role == 2)
                                            <span class="badge bg-success px-3 pt-2"><h5 class="text-white">Consortia Admin</h5></span>
                                        @else
                                            <span class="badge bg-success px-3 pt-2"><h5 class="text-white">Regular User</h5></span>
                                        @endif
                                    </div>
                                    <script>
                                        $(document).ready(function() {
                                            $("#select_org").change(function(){
                                                $(this).find("option:selected").each(function(){
                                                    if($(this).attr("value")=="other"){
                                                        $(".ask-consortia-admin").hide();
                                                        $(".consortia-input").show();
                                                    }
                                                    else{
                                                        $(".ask-consortia-admin").show();
                                                        $(".consortia-input").hide();
                                                    }
                                                });
                                            }).change();
                                        });
                                    </script>
                                    <div class="form-group">
                                        {{Form::label('contact_number', 'Contact Number', ['class' => 'col-form-label font-weight-bold'])}}
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                              <span class="input-group-text">+63</span>
                                            </div>
                                            {{ Form::text('contact_number', auth()->user()->contact_number,['class' => 'form-control']) }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group" style="margin-bottom:0.2rem">
                                        <?php 
                                            $user_interests = '[]';
                                            if(auth()->user()->interest){
                                                $user_interests = auth()->user()->interest;
                                            }
                                        ?>
                                        {{Form::label('interests', 'Topics of Interest', ['class' => 'col-form-label font-weight-bold'])}}
                                        <div class="btn-group-toggle" data-toggle="buttons">
                                            @foreach(App\Consortia::all() as $consortium)
                                            <label class="btn btn-outline-primary {{in_array($consortium->short_name, json_decode($user_interests)) == true  ? 'active' : ''  }}">
                                                <input type="checkbox" name="interest[]" autocomplete="off" {{in_array($consortium->short_name, json_decode($user_interests)) == true ? 'checked' : ''  }}  value="{{$consortium->short_name}}"> {{$consortium->short_name}}
                                            </label>
                                            @endforeach
                                        </div>
                                        <div class="btn-group-toggle mt-3" data-toggle="buttons">
                                            @foreach(App\ISP::groupBy('name')->get() as $isp)
                                            <label class="btn btn-outline-primary" {{in_array($isp->name, json_decode($user_interests)) == true  ? 'active' : ''  }}>
                                                <input type="checkbox" name="interest[]" autocomplete="off" {{in_array($isp->name, json_decode($user_interests)) == true ? 'checked' : ''  }}  value="{{$isp->name}}"> {{$isp->name}}
                                            </label>
                                            @endforeach
                                        </div>
                                        <div class="btn-group-toggle mt-3" data-toggle="buttons">
                                            @foreach(App\Commodity::groupBy('name')->get() as $commodity)
                                            <label class="btn btn-outline-primary" {{in_array($commodity->name, json_decode($user_interests)) == true  ? 'active' : ''  }}>
                                                <input type="checkbox" name="interest[]" autocomplete="off" {{in_array($commodity->name, json_decode($user_interests)) == true ? 'checked' : ''  }} value="{{$commodity->name}}"> {{$commodity->name}}
                                            </label>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="form-group mt-3 float-right">
                                        {{Form::submit('Save Changes', ['class' => 'btn btn-primary'])}}
                                    </div>
                                </div>
                            </div>
                            {{Form::close()}}
                            
                        </div>
                    </div>
                    <div class="tab-pane fade" id="artifacts">
                        <div class="section-header shadow px-5">
                            <span class="text-white mr-3">Manage Resources: </span>
                            <div class="dropdown" style="display:initial">
                                <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <b>{!!request()->asset ? str_replace('_',' ',request()->asset) : 'Industries'!!}</b>
                                </button>
                                <div class="dropdown-menu">
                                    <h6 class="dropdown-header">ISPs</h6>
                                    <a class="dropdown-item" href="{{route('dashboardManage', ['asset' => 'Industries'])}}">Industries</a>
                                    <a class="dropdown-item" href="{{route('dashboardManage', ['asset' => 'Sectors'])}}">Sectors</a>
                                    <a class="dropdown-item" href="{{route('dashboardManage', ['asset' => 'ISP'])}}">ISP</a>
                                    <a class="dropdown-item" href="{{route('dashboardManage', ['asset' => 'Commodities'])}}">Commodities</a>
                                    <div class="dropdown-divider"></div>
                                    <h6 class="dropdown-header">Consortia</h6>
                                    <a class="dropdown-item" href="{{route('dashboardManage', ['asset' => 'Consortia'])}}">Consortia</a>
                                    <a class="dropdown-item" href="{{route('dashboardManage', ['asset' => 'Consortia_Members'])}}">Consortia Members</a>
                                    <div class="dropdown-divider"></div>
                                    <h6 class="dropdown-header">Advertisments</h6>
                                    <a class="dropdown-item" href="{{route('dashboardManage', ['asset' => 'Advertisements'])}}">Advertisements</a>
                                    <a class="dropdown-item" href="{{route('dashboardManage', ['asset' => 'Agenda'])}}">Agenda</a>
                                    <a class="dropdown-item" href="{{route('dashboardManage', ['asset' => 'Announcements'])}}">Announcements</a>
                                    <div class="dropdown-divider"></div>
                                    <h6 class="dropdown-header">Artifact</h6>
                                    <a class="dropdown-item" href="{{route('dashboardManage', ['asset' => 'Artifacts'])}}">AANR Content</a>
                                    <a class="dropdown-item" href="{{route('dashboardManage', ['asset' => 'API'])}}">API Upload</a>
                                    <a class="dropdown-item" href="{{route('dashboardManage', ['asset' => 'Content'])}}">Content Type</a>
                                    <a class="dropdown-item" href="{{route('dashboardManage', ['asset' => 'Content_Subtype'])}}">Content Subtype</a>
                                    <div class="dropdown-divider"></div>
                                    <h6 class="dropdown-header">Others</h6>
                                    <a class="dropdown-item" href="{{route('dashboardManage', ['asset' => 'Contributors'])}}">Contributors</a>
                                    <a class="dropdown-item" href="{{route('dashboardManage', ['asset' => 'Subscribers'])}}">Subscribers</a>
                                    <a class="dropdown-item" href="{{route('dashboardManage', ['asset' => 'Agrisyunaryo'])}}">Agrisyunaryo</a>
                                </div>
                            </div>
                        </div>
                        @include('layouts.messages')
                        @if(request()->asset == 'Industries' || !request()->asset)
                            <div class="card shadow mb-5 mt-0 ml-0">
                                <div class="card-header px-5 pt-4">
                                    <h2 class="text-primary" >
                                        Industries
                                    <span class="float-right">
                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#createIndustryModal"><i class="fas fa-plus"></i> Add Industry</button>
                                    </span></h2>
                                </div>
                                <div class="card-body px-5">
                                    <table class="table data-table tech-table table-hover" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th width="5%">ID</th>
                                                    <th width="85%">Name</th>
                                                    <th width="10%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @foreach(App\Industry::all() as $industry)
                                                <tr>
                                                    <td>{{$industry->id}}</td>
                                                    <td>{{$industry->name}}</td>
                                                    <td>
                                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#editIndustryModal-{{$industry->id}}"><i class="fas fa-edit"></i>  Edit Details</button>
                                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#deleteIndustryModal-{{$industry->id}}"><i class="fas fa-trash"></i> Delete Entry</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                    </table>
                                </div>
                            </div>
                        @elseif(request()->asset == 'Sectors')
                            <div class="card shadow mb-5 mt-0 ml-0">
                                <div class="card-header px-5 pt-4">
                                    <h2 class="text-primary" >
                                        Sectors
                                    <span class="float-right">
                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#createSectorModal"><i class="fas fa-plus"></i> Add Sector</button>
                                    </span></h2>
                                </div>
                                <div class="card-body px-5">
                                    <table class="table data-table tech-table table-hover" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th width="5%">ID</th>
                                                    <th width="60%">Name</th>
                                                    <th width="25%">Industry</th>
                                                    <th width="10%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach(App\Sector::all() as $sector)
                                                <tr>
                                                    <td>{{$sector->id}}</td>
                                                    <td>{{$sector->name}}</td>
                                                    <td>{{$sector->industry->name}}</td>
                                                    <td>
                                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#editSectorModal-{{$sector->id}}"><i class="fas fa-edit"></i> Edit Details</button>
                                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#deleteSectorModal-{{$sector->id}}"><i class="fas fa-trash"></i> Delete Entry</button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                    </table>
                                </div>
                            </div>
                        @elseif(request()->asset == 'ISP')
                            <div class="card shadow mb-5 mt-0 ml-0">
                                <div class="card-header px-5 pt-4">
                                    <h2 class="text-primary" >
                                        ISP
                                    <span class="float-right">
                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#createISPModal"><i class="fas fa-plus"></i> Add ISP</button>
                                    </span></h2>
                                </div>
                                <div class="card-body px-5">
                                    <table class="table data-table tech-table table-hover" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th width="5%">ID</th>
                                                    <th width="55%">Name</th>
                                                    <th width="25%">Sector</th>
                                                    <th width="10%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach(App\ISP::all() as $isp_each)
                                                <tr>
                                                    <td>{{$isp_each->id}}</td>
                                                    <td>{{$isp_each->name}}</td>
                                                    <td>{{$isp_each->sector->name}}</td>
                                                    <td>
                                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#editISPModal-{{$isp_each->id}}"><i class="fas fa-edit"></i> Edit Details</button>
                                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#deleteISPModal-{{$isp_each->id}}"><i class="fas fa-trash"></i> Delete Entry</button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                    </table>
                                </div>
                            </div>
                        @elseif(request()->asset == 'Commodities')
                            <div class="card shadow mb-5 mt-0 ml-0">
                                <div class="card-header px-5 pt-4">
                                    <h2 class="text-primary" >
                                        Commodities
                                    <span class="float-right">
                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#createCommodityModal"><i class="fas fa-plus"></i> Add Commoddity</button>
                                    </span></h2>
                                </div>
                                <div class="card-body px-5">
                                    <table class="table data-table tech-table table-hover" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th width="5%">ID</th>
                                                    <th width="60%">Name</th>
                                                    <th width="25%">ISP</th>
                                                    <th width="10%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach(App\Commodity::all() as $commodity)
                                                    <tr>
                                                        <td>{{$commodity->id}}</td>
                                                        <td>{{$commodity->name}}</td>
                                                        <td>{{$commodity->isp->name}}</td>
                                                        <td>
                                                            <button type="button" class="btn btn-default" data-toggle="modal" data-target="#editCommodityModal-{{$commodity->id}}"><i class="fas fa-edit"></i> Edit Details</button>
                                                            <button type="button" class="btn btn-default" data-toggle="modal" data-target="#deleteCommodityModal-{{$commodity->id}}"><i class="fas fa-trash"></i> Delete Entry</button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                    </table>
                                </div>
                            </div>
                        @elseif(request()->asset == 'Consortia')
                            <div class="card shadow mb-5 mt-0 ml-0">
                                <div class="card-header px-5 pt-4">
                                    <h2 class="text-primary" >
                                        Consortia
                                    <span class="float-right">
                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#createConsortiaModal"><i class="fas fa-plus"></i> Add Consortia</button>
                                    </span></h2>
                                </div>
                                <div class="card-body px-5">
                                    <table class="table data-table tech-table table-hover" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th width="5%">ID</th>
                                                    <th width="20%">Acronym</th>
                                                    <th width="40%">Full Name</th>
                                                    <th width="25%">Region</th>
                                                    <th width="10%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>{{$aanrPage->id}}</td>
                                                    <td>{{$aanrPage->short_name}}</td>
                                                    <td>{{$aanrPage->full_name}}</td>
                                                    <td>----------------</td>
                                                    <td>
                                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#editAANRPageModal"><i class="fas fa-edit"></i> Edit Details</button>
                                                    </td>
                                                </tr>
                                            @foreach(App\Consortia::all() as $consortium)
                                                <tr>
                                                    <td>{{$consortium->id}}</td>
                                                    <td>{{$consortium->short_name}}</td>
                                                    <td>{{$consortium->full_name}}</td>
                                                    <td>{{$consortium->region}}</td>
                                                    <td>
                                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#editConsortiaModal-{{$consortium->id}}"><i class="fas fa-edit"></i> Edit Details</button>
                                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#deleteConsortiaModal-{{$consortium->id}}"><i class="fas fa-trash"></i> Delete Entry</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                    </table>
                                </div>
                            </div>
                        @elseif(request()->asset == 'Consortia_Members')
                            <div class="card shadow mb-5 mt-0 ml-0">
                                <div class="card-header px-5 pt-4">
                                    <h2 class="text-primary" >
                                        Consortia Members
                                    <span class="float-right">
                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#createConsortiaMemberModal"><i class="fas fa-plus"></i> Add</button>
                                    </span></h2>
                                </div>
                                <div class="card-body px-5">
                                    <table class="table data-table tech-table table-hover" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th width="5%">ID</th>
                                                    <th width="20%">Acronym</th>
                                                    <th width="40%">Name</th>
                                                    <th width="25%">Contact Name</th>
                                                    <th width="10%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>{{$pcaarrdPage->id}}</td>
                                                    <td>{{$pcaarrdPage->short_name}}</td>
                                                    <td>{{$pcaarrdPage->full_name}}</td>
                                                    <td>----------------</td>
                                                    <td>
                                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#editPCAARRDPageModal"><i class="fas fa-edit"></i> Edit Details</button>
                                                    </td>
                                                </tr>
                                            @foreach(App\ConsortiaMember::all() as $consortia_member)
                                                <tr>
                                                    <td>{{$consortia_member->id}}</td>
                                                    <td>{{$consortia_member->acronym}}</td>
                                                    <td>{{$consortia_member->name}}</td>
                                                    <td>{{$consortia_member->contact_name}}</td>
                                                    <td>
                                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#editConsortiaMemberModal-{{$consortia_member->id}}"><i class="fas fa-edit"></i> Edit Details</button>
                                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#deleteConsortiaMemberModal-{{$consortia_member->id}}"><i class="fas fa-trash"></i> Delete Entry</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                    </table>
                                </div>
                            </div>
                        @elseif(request()->asset == 'Advertisements')
                            <div class="card shadow mb-5 mt-0 ml-0">
                                <div class="card-header px-5 pt-4">
                                    <h2 class="text-primary" >
                                        Advertisements
                                    <span class="float-right">
                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#createAdvertisementModal"><i class="fas fa-plus"></i> Add Advertisement</button>
                                    </span></h2>
                                </div>
                                <div class="card-body px-5">
                                    <table class="table data-table tech-table table-hover" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th width="5%"></th>
                                                    <th width="5%">ID</th>
                                                    <th width="35%">Title</th>
                                                    <th width="45%">Ad Overview</th>
                                                    <th width="10%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                    </table>
                                </div>
                            </div>
                        @elseif(request()->asset == 'Agenda')
                            <div class="card shadow mb-5 mt-0 ml-0">
                                <div class="card-header px-5 pt-4">
                                    <h2 class="text-primary" >
                                        Agenda
                                    <span class="float-right">
                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#createAgendaModal"><i class="fas fa-plus"></i> Add Agenda</button>
                                    </span></h2>
                                </div>
                                <div class="card-body px-5">
                                    <table class="table data-table tech-table table-hover" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th width="5%"></th>
                                                    <th width="5%">ID</th>
                                                    <th width="45%">Agenda</th>
                                                    <th width="20%">Agenda Type</th>
                                                    <th width="15%">Sector</th>
                                                    <th width="10%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @foreach(App\Agenda::all() as $agenda)
                                                <tr>
                                                    <td style="text-align:center"><input class="form-check-input" type="checkbox" value="" id="flexCheckDefault"></td>
                                                    <td>{{$agenda->id}}</td>
                                                    <td>{{$agenda->agenda}}</td>
                                                    <td>{{$agenda->agenda_types}}</td>
                                                    <td>{{$agenda->sector_id}}</td>
                                                    <td>
                                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#editAgendaModal-{{$agenda->id}}"><i class="fas fa-edit"></i>  Edit Details</button>
                                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#deleteAgendaModal-{{$agenda->id}}"><i class="fas fa-trash"></i> Delete</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                    </table>
                                </div>
                            </div>
                        @elseif(request()->asset == 'Announcements')
                            <div class="card shadow mb-5 mt-0 ml-0">
                                <div class="card-header px-5 pt-4">
                                    <h2 class="text-primary" >
                                        Announcements
                                    <span class="float-right">
                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#createAnnouncementModal"><i class="fas fa-plus"></i> Add</button>
                                    </span></h2>
                                </div>
                                <div class="card-body px-5">
                                    <table class="table data-table tech-table table-hover" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th width="5%"></th>
                                                    <th width="5%">ID</th>
                                                    <th width="45%">Title</th>
                                                    <th width="20%">Feature</th>
                                                    <th width="15%">Link</th>
                                                    <th width="10%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                    </table>
                                </div>
                            </div>
                        @elseif(request()->asset == 'Artifacts')
                            <div class="card shadow mb-5 mt-0 ml-0">
                                <form action="{{ route('deleteArtifact')}}" id="deleteForm" method="POST">
                                {{ csrf_field() }}
                                <input type="hidden" name="_method" value="delete">
                                <div class="card-header px-5 pt-4">
                                    <h2 class="text-primary" >
                                        AANR Content
                                    <span class="float-right">
                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#createArtifactModal"><i class="fas fa-plus"></i> Add Content</button>
                                        <input type="submit" class="btn btn-default" value="Delete Checked">
                                    </span></h2>
                                </div>
                                <div class="card-body px-5">
                                    <table class="table data-table-options tech-table table-hover" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th style="width:5%"></th>
                                                    <th style="width:5%">ID</th>
                                                    <th style="width:45%">Title</th>
                                                    <th style="width:5%">Content Type</th>
                                                    <th style="width:5%">Date Published</th>
                                                    <th style="width:25%">Author</th>
                                                    <th style="width:10%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach(App\ArtifactAANR::where('is_agrisyunaryo', '=', 0)->get() as $artifact)
                                                <tr>
                                                    <td style="text-align:center"><input class="form-check-input" type="checkbox" name="artifactaanr_check[]" value="{{$artifact->id}}" id="flexCheckDefault"></td>
                                                    <td>{{$artifact->id}}</td>
                                                    <td>{{$artifact->title}}</td>
                                                    <td>{{$artifact->content ? $artifact->content->type : ''}}</td>
                                                    <td>{{$artifact->date_published}}</td>
                                                    <td>{{$artifact->author}}</td>
                                                    <td>
                                                        <!--<button type="button" class="btn btn-default" 
                                                            data-toggle="modal" 
                                                            data-id="{{$artifact->id}}" 
                                                            data-consortia="{{$artifact->consortia_id}}"
                                                            data-consortia-member="{{$artifact->consortia_member_id}}"
                                                            data-title="{{$artifact->title}}"
                                                            data-content="{{$artifact->content_id}}"
                                                            data-content-subtype="{{$artifact->contentsubtype_id}}"
                                                            data-date-published="{{$artifact->date_published}}"
                                                            data-author="{{$artifact->author}}"
                                                            data-author-affiliation="{{$artifact->author_institution}}"
                                                            data-description="{{$artifact->description}}"
                                                            data-link="{{$artifact->link}}"
                                                            data-keywords="{{$artifact->keywords}}"
                                                            data-commodities="{{$artifact->commodities()->allRelatedIds()}}"
                                                            data-target="#editArtifactModal">
                                                            <i class="fas fa-edit"></i> Edit Details
                                                        </button>-->
                                                        <a class="btn btn-default" href="/dashboard/manage/content/{{$artifact->id}}/edit" role="button"><i class="fas fa-edit"></i> Edit Details</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                    </table>
                                </div>
                                </form>
                            </div>
                            @elseif(request()->asset == 'API')
                            <div class="card shadow mb-5 mt-0 ml-0">
                                <form action="{{ route('deleteContent')}}" id="deleteForm" method="POST">
                                {{ csrf_field() }}
                                <input type="hidden" name="_method" value="delete">
                                <div class="card-header px-5 pt-4">
                                    <h2 class="text-primary" >
                                        API Upload
                                    <span class="float-right">
                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#createAPIEntryModal"><i class="fas fa-plus"></i> Add API</button>
                                    </span></h2>
                                </div>
                                <div class="card-body px-5">
                                    <table class="table data-table tech-table table-hover" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th width="5%">ID</th>
                                                    <th width="20%">Description</th>
                                                    <th width="25%">Link</th>
                                                    <th width="10%">Frequency</th>
                                                    <th width="15%">Time</th>
                                                    <th width="15%">Next Upload</th>
                                                    <th width="10%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach(App\APIEntries::all() as $api_entry)
                                                    <tr>
                                                        <td>{{$api_entry->id}}</td>
                                                        <td>{{$api_entry->description}}</td>
                                                        <td>{{$api_entry->link}}</td>
                                                        <td>Every {{$api_entry->frequency}} hours</td>
                                                        <td>{{Carbon::parse($api_entry->time)->format('h:i A')}} </td>
                                                        <td>-</td>
                                                        <td>
                                                            <button type="button" class="btn btn-default" data-toggle="modal" data-target="#editAPIEntryModal-{{$api_entry->id}}"><i class="fas fa-edit"></i> Edit Details</button>
                                                            <button type="button" class="btn btn-default" data-toggle="modal" data-target="#deleteAPIEntryModal-{{$api_entry->id}}"><i class="fas fa-trash"></i> Delete Entry</button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                    </table>
                                </div>
                                </form>
                            </div>
                            @elseif(request()->asset == 'Content')
                            <div class="card shadow mb-5 mt-0 ml-0">
                                <form action="{{ route('deleteContent')}}" id="deleteForm" method="POST">
                                {{ csrf_field() }}
                                <input type="hidden" name="_method" value="delete">
                                <div class="card-header px-5 pt-4">
                                    <h2 class="text-primary" >
                                        Content Type
                                    <span class="float-right">
                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#createContentTypeModal"><i class="fas fa-plus"></i> Add Content Type</button>
                                    </span></h2>
                                </div>
                                <div class="card-body px-5">
                                    <table class="table data-table tech-table table-hover" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th width="5%">ID</th>
                                                    <th width="80%">Type</th>
                                                    <th width="10%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach(App\Content::all() as $content)
                                                    <tr>
                                                        <td>{{$content->id}}</td>
                                                        <td>{{$content->type}}</td>
                                                        <td>
                                                            <button type="button" class="btn btn-default" data-toggle="modal" data-target="#editContentTypeModal-{{$content->id}}"><i class="fas fa-edit"></i> Edit Details</button>
                                                            <button type="button" class="btn btn-default" data-toggle="modal" data-target="#deleteContentTypeModal-{{$content->id}}"><i class="fas fa-trash"></i> Delete Entry</button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                    </table>
                                </div>
                                </form>
                            </div>
                        @elseif(request()->asset == 'Content_Subtype')
                            <div class="card shadow mb-5 mt-0 ml-0">
                                <form action="{{ route('deleteContentSubtype')}}" id="deleteForm" method="POST">
                                {{ csrf_field() }}
                                <input type="hidden" name="_method" value="delete">
                                <div class="card-header px-5 pt-4">
                                    <h2 class="text-primary" >
                                        Content Subtype
                                    <span class="float-right">
                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#createContentSubtypeModal"><i class="fas fa-plus"></i> Add Content Subtype</button>
                                    </span></h2>
                                </div>
                                <div class="card-body px-5">
                                    <table class="table data-table tech-table table-hover" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th width="5%">ID</th>
                                                    <th width="40%">Name</th>
                                                    <th width="40%">Content</th>
                                                    <th width="10%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach(App\ContentSubtype::all() as $content_subtype)
                                                    <tr>
                                                        <td>{{$content_subtype->id}}</td>
                                                        <td>{{$content_subtype->name}}</td>
                                                        <td>{{$content_subtype->content->type}}</td>
                                                        <td>
                                                            <button type="button" class="btn btn-default" data-toggle="modal" data-target="#editContentSubtypeModal-{{$content_subtype->id}}"><i class="fas fa-edit"></i> Edit Details</button>
                                                            <button type="button" class="btn btn-default" data-toggle="modal" data-target="#deleteContentSubtypeModal-{{$content_subtype->id}}"><i class="fas fa-trash"></i> Delete Entry</button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                    </table>
                                </div>
                                </form>
                            </div>
                        @elseif(request()->asset == 'Contributors')
                            <div class="card shadow mb-5 mt-0 ml-0">
                                <div class="card-header px-5 pt-4">
                                    <h2 class="text-primary" >
                                        Contributors
                                    <span class="float-right">
                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#createTechnologyCategoryModal"><i class="fas fa-plus"></i> Add</button>
                                    </span></h2>
                                </div>
                                <div class="card-body px-5">
                                    <table class="table data-table tech-table table-hover" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th width="5%"></th>
                                                    <th width="5%">ID</th>
                                                    <th width="20%">First Name</th>
                                                    <th width="20%">Last Name</th>
                                                    <th width="10%">Email</th>
                                                    <th width="30%">Feedback</th>
                                                    <th width="10%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                    </table>
                                </div>
                            </div>
                        @elseif(request()->asset == 'Subscribers')
                            <div class="card shadow mb-5 mt-0 ml-0">
                                <div class="card-header px-5 pt-4">
                                    <h2 class="text-primary" >
                                        Subscribers
                                    <span class="float-right">
                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#createTechnologyCategoryModal"><i class="fas fa-plus"></i> Add</button>
                                    </span></h2>
                                </div>
                                <div class="card-body px-5">
                                    <table class="table data-table tech-table table-hover" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th width="5%"></th>
                                                    <th width="5%">ID</th>
                                                    <th width="30%">First Name</th>
                                                    <th width="30%">Last Name</th>
                                                    <th width="20%">Email</th>
                                                    <th width="10%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                    </table>
                                </div>
                            </div>
                        @elseif(request()->asset == 'Agrisyunaryo')
                            <div class="card shadow mb-5 mt-0 ml-0">
                                <form action="{{ route('deleteAgrisyunaryo')}}" id="deleteForm" method="POST">
                                {{ csrf_field() }}
                                <input type="hidden" name="_method" value="delete">
                                <div class="card-header px-5 pt-4">
                                    <h2 class="text-primary" >
                                        Agrisyunaryos
                                    <span class="float-right">
                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#createAgrisyunaryoModal"><i class="fas fa-plus"></i> Add Agrisyunaryo</button>
                                        <input type="submit" class="btn btn-default" value="Delete Checked">
                                    </span></h2>
                                </div>
                                <div class="card-body px-5">
                                    <table class="table data-table-options tech-table table-hover" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th width="5%"></th>
                                                    <th width="5%">ID</th>
                                                    <th width="40%">Title</th>
                                                    <th width="40%">Thumbnail</th>
                                                    <th width="10%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @foreach(App\Agrisyunaryo::all() as $agrisyunaryo)
                                                <tr>
                                                    <td style="text-align:center"><input class="form-check-input" type="checkbox" name="agrisyunaryo_check[]" value="{{$agrisyunaryo->id}}" id="flexCheckDefault"></td>
                                                    <td>{{$agrisyunaryo->id}}</td>
                                                    <td>{{$agrisyunaryo->title}}</td>
                                                    <td>
                                                        <img src="/storage/page_images/{{$agrisyunaryo->image}}" alt="{{$agrisyunaryo->title}}" style=" height: auto; 
                                                        width: auto; 
                                                        max-width: 150px; 
                                                        max-height: 150px;"></td>
                                                    <td>
                                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#editAgrisyunaryoModal-{{$agrisyunaryo->id}}"><i class="fas fa-edit"></i>  Edit Details</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                    </table>
                                </div>
                            </div>
                        </form>
                        @endif

                    </div>
                    <div class="tab-pane fade" id="landing_page">
                        <div class="section-header shadow px-5" style="padding-top:23px">
                            <span class="text-white mr-3">Manage Landing Page Items</span>
                            <div class="dropdown" style="display:initial">
                                <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <b>{!!request()->landing_page ? str_replace('_',' ',request()->landing_page) : 'Header Logo'!!}</b>
                                </button>
                                <div class="dropdown-menu">
                                    <h6 class="dropdown-header">Header</h6>
                                    <a class="dropdown-item" href="{{route('dashboardManage', ['landing_page' => 'Header_Logo'])}}">Header Logo</a>
                                    <a class="dropdown-item" href="{{route('dashboardManage', ['landing_page' => 'Header_Links'])}}">Header Links</a>
                                    <div class="dropdown-divider"></div>
                                    <h6 class="dropdown-header">Others</h6>
                                    <a class="dropdown-item" href="{{route('dashboardManage', ['landing_page' => 'Search'])}}">Search Banner</a>
                                    <a class="dropdown-item" href="{{route('dashboardManage', ['landing_page' => 'Headlines'])}}">Headlines</a>
                                    <a class="dropdown-item" href="{{route('dashboardManage', ['landing_page' => 'Social_Media_Icons'])}}">Social Media Icons</a>
                                    <div class="dropdown-divider"></div>
                                    <h6 class="dropdown-header">Landing Page</h6>
                                    <a class="dropdown-item" href="{{route('dashboardManage', ['landing_page' => 'Sliders'])}}">Sliders</a>
                                   <!-- <a class="dropdown-item" href="{{route('dashboardManage', ['landing_page' => 'Featured_Videos'])}}">Featured Videos</a>
                                    <a class="dropdown-item" href="{{route('dashboardManage', ['landing_page' => 'Featured_Publications'])}}">Featured Publications</a>
                                    <a class="dropdown-item" href="{{route('dashboardManage', ['landing_page' => 'Industry_Profile'])}}">Industry Profile</a>
                                    <a class="dropdown-item" href="{{route('dashboardManage', ['landing_page' => 'AANR_Latest'])}}">AANR Latest</a>
                                    <a class="dropdown-item" href="{{route('dashboardManage', ['landing_page' => 'User_Type_Recommendation'])}}">User Type Recommendation</a>
                                    <a class="dropdown-item" href="{{route('dashboardManage', ['landing_page' => 'Recommended_For_You'])}}">Recommended For You</a> -->
                                </div>
                            </div>
                        </div>
                        @include('layouts.messages')
                        @if(request()->landing_page == 'Search')
                            <div class="card shadow mb-5 mt-0 ml-0">
                                <div class="card-header px-5 pt-4">
                                    <div class="text-primary" style="margin-bottom: 0.5rem;font-weight: 500;line-height: 1.2;">
                                        <span style="font-size:1.8rem;"> Top Banner for Search Page </span>
                                        <span class="text-muted"><i>Add banner to your page.</i></span>
                                        <span class="float-right">
                                            <button class="btn btn-primary mr-2" data-toggle="modal" data-target="#addTopBannerModal"><i>Update image</i></button>
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body px-5">
                                    <div class="image-contain text-center">
                                        <img src="/storage/page_images/{{$landing_page->top_banner}}" class="manage-image">
                                    </div>
                                </div>
                            </div>
                        @elseif(request()->landing_page == 'Header_Logo' || !request()->landing_page)
                            <div class="card shadow mb-5 mt-0 ml-0">
                                <div class="card-header px-5 pt-4">
                                    <div class="text-primary" style="margin-bottom: 0.5rem;font-weight: 500;line-height: 1.2;">
                                        <span style="font-size:1.8rem;"> Header Logo </span>
                                        <span class="text-muted"><i>Add a photo for the header logo.</i></span>
                                        <span class="float-right">
                                            <button class="btn btn-primary mr-2" style="margin-bottom:-12.5px" data-toggle="modal" data-target="#updateHeaderLogo"><i>Update image</i></button>
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body px-5">
                                    <div class="image-contain text-center">
                                        <img src="/storage/page_images/{{$landing_page->header_logo}}" class="manage-image">
                                    </div>
                                </div>
                            </div>
                        @elseif(request()->landing_page == 'Header_Links')
                            <div class="card shadow mb-5 mt-0 ml-0">
                                <div class="card-header px-5 pt-4">
                                    <div class="text-primary" style="margin-bottom: 0.5rem;font-weight: 500;line-height: 1.2;">
                                        <span style="font-size:1.8rem;"> Header Links </span>
                                        <span class="text-muted"><i>Edit header links.</i></span>

                                        <span class="float-right">
                                            <button type="button" class="btn btn-default" data-toggle="modal" data-target="#addHeaderLinkModal"><i class="fas fa-plus"></i> Add Header Link</button>
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body px-5">
                                    <table class="table data-table tech-table table-hover" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th width="45%">Name</th>
                                                <th width="15%">Position</th>
                                                <th width="25%">Link</th>
                                                <th width="15%">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach(App\HeaderLink::all() as $header_link)
                                            <tr>
                                                <td><span class="text-muted">{{$header_link->name}}</span></td>
                                                <td><span class="text-muted">{{$header_link->position}}</span></td>
                                                <td><span class="text-muted">{{$header_link->link}}</span></td>
                                                <td class="">
                                                    <button class="btn btn-primary pl-1 pr-1 pt-0 pb-0" data-toggle="modal" data-target="#editHeaderLinkModal-{{$header_link->id}}"><i class="fas fa-edit"></i> Edit Details</button>
                                                    <button class="btn btn-danger pl-1 pr-1 pt-0 pb-0" data-toggle="modal" data-target="#deleteHeaderLinkModal-{{$header_link->id}}"><i class="fas fa-trash"></i> Delete</button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @elseif(request()->landing_page == 'Headlines')
                            <div class="card shadow mb-5 mt-0 ml-0">
                                <div class="card-header px-5 pt-4">
                                    <div class="text-primary" style="margin-bottom: 0.5rem;font-weight: 500;line-height: 1.2;">
                                        <span style="font-size:1.8rem;"> Headlines </span>
                                        <span class="text-muted"><i>Add a headline topic.</i></span>

                                        <span class="float-right">
                                            <button type="button" class="btn btn-default" data-toggle="modal" data-target="#addHeadlineModal"><i class="fas fa-plus"></i> Add Headline</button>
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body px-5">
                                    <table class="table data-table tech-table table-hover" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th width="10%">#</th>
                                                <th width="60%">Title</th>
                                                <th width="15%">Link</th>
                                                <th width="15%">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                $count = 1;
                                            ?>
                                            @foreach($headlines as $headline)
                                                <tr>
                                                    <td>{{$count++}}</td>
                                                    <td>{{$headline->title}}</td>
                                                    <td><a href="{{$headline->link}}">Link</a></td>
                                                    <td class="text-center">
                                                        <button class="btn btn-primary pl-1 pr-1 pt-0 pb-0" data-toggle="modal" data-target="#editHeadlineModal-{{$headline->id}}"><i class="fas fa-edit"></i> Edit Details</button>
                                                        <button class="btn btn-danger pl-1 pr-1 pt-0 pb-0" data-toggle="modal" data-target="#deleteHeadlineModal-{{$headline->id}}"><i class="fas fa-trash"></i> Delete</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @elseif(request()->landing_page == 'Sliders')
                            <div class="card shadow mb-5 mt-0 ml-0">
                                <div class="card-header px-5 pt-4">
                                    <div class="text-primary" style="margin-bottom: 0.5rem;font-weight: 500;line-height: 1.2;">
                                        <span style="font-size:1.8rem;"> Slider Content </span>
                                        <span class="text-muted"><i>Add a carousel entry.</i></span>

                                        <span class="float-right">
                                            <button type="button" class="btn btn-default" data-toggle="modal" data-target="#addSliderModal"><i class="fas fa-plus"></i> Add Slider</button>
                                        </span>
                                    </div>
                                </div>
                                <div class="card-body px-5">
                                    <table class="table data-table tech-table table-hover" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th width="10%">#</th>
                                                <th width="50%">Title</th>
                                                <th width="25%">Consortia</th>
                                                <th width="15%">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                                $count = 1;
                                            ?>
                                            @foreach(App\LandingPageSlider::all()->sortBy('id') as $slider)
                                                <tr>
                                                    <td>{{$count++}}</td>
                                                    <td>{{$slider->title}}</td>
                                                    <td>{{$slider->is_consortia == 0 ? 'AANR' : $slider->consortia->short_name}}</td>
                                                    <td class="text-center">
                                                        <button class="btn btn-primary pl-1 pr-1 pt-0 pb-0" data-toggle="modal" data-target="#editSliderModal-{{$slider->id}}"><i class="fas fa-edit"></i> Edit Details</button>
                                                        <button class="btn btn-danger pl-1 pr-1 pt-0 pb-0" data-toggle="modal" data-target="#deleteSliderModal-{{$slider->id}}"><i class="fas fa-trash"></i> Delete</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @elseif(request()->landing_page == 'Social_Media_Icons')
                            <div class="card shadow mb-5 mt-0 ml-0">
                                <div class="card-header px-5 pt-4">
                                    <div class="text-primary" style="margin-bottom: 0.5rem;font-weight: 500;line-height: 1.2;">
                                        <span style="font-size:1.8rem;"> Social Media Icons </span>
                                        <span class="text-muted"><i>Edit sticky social media icons.</i></span>
                                    </div>
                                </div>
                                <div class="card-body px-5">
                                    <table class="table data-table tech-table table-hover" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th width="10%">#</th>
                                                <th width="45%">Name</th>
                                                <th width="30%">Link</th>
                                                <th width="15%">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($social_media as $social)
                                            <tr>
                                                <td><span class="text-muted">{{$social->id}}</span></td>
                                                <td><span class="text-muted">{{$social->name}}</span></td>
                                                <td><span class="text-muted">{{$social->link}}</span></td>
                                                <td class="">
                                                    <button class="btn btn-primary pl-1 pr-1 pt-0 pb-0" data-toggle="modal" data-target="#editStickyModal-{{$social->id}}"><i class="fas fa-edit"></i> Edit Details</button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @elseif(request()->landing_page == 'Featured_Videos')
                            <div class="card shadow mb-5 mt-0 ml-0">
                                <div class="card-header px-5 pt-4">
                                    <h2 class="text-primary" >
                                        Featured Videos
                                    <span class="float-right">
                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#createTechnologyCategoryModal"><i class="fas fa-plus"></i> Add</button>
                                    </span></h2>
                                </div>
                                <div class="card-body px-5">
                                    <table class="table data-table tech-table table-hover" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th width="5%"></th>
                                                    <th width="5%">ID</th>
                                                    <th width="80%">Title</th>
                                                    <th width="10%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                    </table>
                                </div>
                            </div>
                        @elseif(request()->landing_page == 'Featured_Publications')
                            <div class="card shadow mb-5 mt-0 ml-0">
                                <div class="card-header px-5 pt-4">
                                    <h2 class="text-primary" >
                                        Featured Publications
                                    <span class="float-right">
                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#createTechnologyCategoryModal"><i class="fas fa-plus"></i> Add</button>
                                    </span></h2>
                                </div>
                                <div class="card-body px-5">
                                    <table class="table data-table tech-table table-hover" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th width="5%"></th>
                                                    <th width="5%">ID</th>
                                                    <th width="50%">Title</th>
                                                    <th width="30%">Consortia</th>
                                                    <th width="10%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                    </table>
                                </div>
                            </div>
                        @elseif(request()->landing_page == 'Industry_Profile') 
                            <div class="card shadow mb-5 mt-0 ml-0">
                                <div class="card-header px-5 pt-4">
                                    <h2 class="text-primary" >
                                        Industry Profile
                                    <span class="float-right">
                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#editIndustryProfileFieldsModal"><i class="fas fa-edit"></i> Edit Fields</button>
                                    </span></h2>
                                </div>
                            </div>   
                        @elseif(request()->landing_page == 'AANR_Latest')
                            <div class="card shadow mb-5 mt-0 ml-0">
                                <div class="card-header px-5 pt-4">
                                    <h2 class="text-primary" >
                                        AANR Latest
                                    <span class="float-right">
                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#editAANRLatestFieldsModal"><i class="fas fa-edit"></i> Edit Fields</button>
                                    </span></h2>
                                </div>
                            </div>  
                        @elseif(request()->landing_page == 'User_Type_Recommendation')
                            <div class="card shadow mb-5 mt-0 ml-0">
                                <div class="card-header px-5 pt-4">
                                    <h2 class="text-primary" >
                                        User Type Recommendation
                                    <span class="float-right">
                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#editUserTypeRecommendationFieldsModal"><i class="fas fa-edit"></i> Edit Fields</button>
                                    </span></h2>
                                </div>
                            </div>    
                        @elseif(request()->landing_page == 'Recommended_For_You')
                            <div class="card shadow mb-5 mt-0 ml-0">
                                <div class="card-header px-5 pt-4">
                                    <h2 class="text-primary" >
                                        Recommended For You
                                    <span class="float-right">
                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#editRecommendedForYouFieldsModal"><i class="fas fa-edit"></i> Edit Fields</button>
                                    </span></h2>
                                </div>
                            </div>       
                        @endif                  
                        <!-- Modal for updateTopBannerModal -->
                            <div class="modal fade" id="addTopBannerModal" tabindex="-1" role="dialog" aria-labelledby="imageLabel" aria-hidden="true" style="z-index:9999">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h6 class="modal-title" id="exampleModalLabel">Upload new Top Banner</h6>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        {{ Form::open(['action' => ['LandingPageElementsController@updateTopBanner'], 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                                
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <div class="custom-file">
                                                    {{ Form::file('image', ['class' => 'custom-file-input', 'id' => 'customFile'])}}
                                                    <label class="custom-file-label" for="customFile">Choose file</label>
                                                    {{ csrf_field() }}
                                                </div>
                                                <script>
                                                    // Add the following code if you want the name of the file appear on select
                                                    $(".custom-file-input").on("change", function() {
                                                    var fileName = $(this).val().split("\\").pop();
                                                    $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
                                                    });
                                                </script>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                            <button class="btn btn-success"><i class="fas fa-save"></i> Save</button>
                                        </div>
                                        {{ Form::close() }}
                                    </div>
                                </div>
                            </div>
                        <!-- END modal for updateTopBannerModal -->
                    
                        <!-- Modal for update consortia banner -->
                            <div class="modal fade" id="updateConsortiaBanner" tabindex="-1" role="dialog" aria-labelledby="imageLabel" aria-hidden="true" style="z-index:9999">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h6 class="modal-title" id="exampleModalLabel">Upload Consortia Banner</h6>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        {{ Form::open(['action' => ['LandingPageElementsController@updateConsortiaBanner'], 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                                
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <div class="custom-file">
                                                    {{ Form::file('image', ['class' => 'custom-file-input', 'id' => 'customFile'])}}
                                                    <label class="custom-file-label" for="customFile">Choose file</label>
                                                    {{ csrf_field() }}
                                                </div>
                                                <script>
                                                    // Add the following code if you want the name of the file appear on select
                                                    $(".custom-file-input").on("change", function() {
                                                    var fileName = $(this).val().split("\\").pop();
                                                    $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
                                                    });
                                                </script>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                            <button class="btn btn-success"><i class="fas fa-save"></i> Save</button>
                                        </div>
                                        {{ Form::close() }}
                                    </div>
                                </div>
                            </div>
                        <!-- END modal for update consortia banner -->
                    
                        <!-- Modal for update Header Logo -->
                            <div class="modal fade" id="updateHeaderLogo" tabindex="-1" role="dialog" aria-labelledby="imageLabel" aria-hidden="true" style="z-index:9999">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h6 class="modal-title" id="exampleModalLabel">Upload Header Logo</h6>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        {{ Form::open(['action' => ['LandingPageElementsController@updateHeaderLogo'], 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                                
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <div class="custom-file">
                                                    {{ Form::file('image', ['class' => 'custom-file-input', 'id' => 'customFile'])}}
                                                    <label class="custom-file-label" for="customFile">Choose file</label>
                                                    {{ csrf_field() }}
                                                </div>
                                                <script>
                                                    // Add the following code if you want the name of the file appear on select
                                                    $(".custom-file-input").on("change", function() {
                                                    var fileName = $(this).val().split("\\").pop();
                                                    $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
                                                    });
                                                </script>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                            <button class="btn btn-success"><i class="fas fa-save"></i> Save</button>
                                        </div>
                                        {{ Form::close() }}
                                    </div>
                                </div>
                            </div>
                        <!-- END modal for update Header Logo -->
                    
                        <!-- Headline modals-->
                            <!-- Modal for add Headline -->
                                <div class="modal fade" id="addHeadlineModal" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-l" role="document">
                                        <div class="modal-content">
                                            {{ Form::open(['action' => ['HeadlinesController@addHeadline'], 'method' => 'POST']) }}
                                            <div class="modal-header">
                                                <h6 class="modal-title" id="exampleModalLabel">Add Headline</h6>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    {{Form::label('title', 'Title', ['class' => 'col-form-label'])}}
                                                    {{Form::text('title', '', ['class' => 'form-control', 'placeholder' => 'Add a title'])}}
                                                </div>
                                                <div class="form-group">
                                                    {{Form::label('link', 'Link to article', ['class' => 'col-form-label'])}}
                                                    {{Form::text('link', '', ['class' => 'form-control', 'placeholder' => 'Add a link'])}}
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                {{Form::submit('Add Headline', ['class' => 'btn btn-success'])}}
                                            </div>
                                            {{Form::close()}}
                                        </div>
                                    </div>
                                </div>
                            <!-- END modal for Headline -->
                    
                            @foreach($headlines as $headline)
                                <!-- Modal for EDIT Headline -->
                                    <div class="modal fade" id="editHeadlineModal-{{$headline->id}}" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog modal-l" role="document">
                                            <div class="modal-content">
                                                {{ Form::open(['action' => ['HeadlinesController@editHeadline', $headline->id], 'method' => 'POST']) }}
                                                <div class="modal-header">
                                                    <h6 class="modal-title" id="exampleModalLabel">Add Headline</h6>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        {{Form::label('title', 'Title', ['class' => 'col-form-label'])}}
                                                        {{Form::text('title', $headline->title, ['class' => 'form-control', 'placeholder' => 'Add a title'])}}
                                                    </div>
                                                    <div class="form-group">
                                                        {{Form::label('link', 'Link to article', ['class' => 'col-form-label'])}}
                                                        {{Form::text('link', $headline->link, ['class' => 'form-control', 'placeholder' => 'Add a link'])}}
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                    {{Form::submit('Add Headline', ['class' => 'btn btn-success'])}}
                                                </div>
                                                {{Form::close()}}
                                            </div>
                                        </div>
                                    </div>
                                <!-- END modal for Headline -->
                                <!-- Modal for add Headline -->
                                    <div class="modal fade" id="deleteHeadlineModal-{{$headline->id}}" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <form action="{{ route('deleteHeadline', $headline->id) }}" id="deleteForm" method="POST">
                                                <div class="modal-header">
                                                    <h6 class="modal-title" id="exampleModalLabel">Confirm Delete</h6>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    {{ csrf_field() }}
                                                    {{ method_field('DELETE') }}
                                                    <span>
                                                        Are you sure you want to delete: <b>{{$headline->title}}</b>
                                                    </span>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>
                                                    <input class="btn btn-danger" type="submit" value="Yes, Delete">
                                                </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <!-- END modal for Headline -->
                            @endforeach
                        <!-- END of Headline modals-->
                    
                        <!-- Social Media Modals -->
                            <div class="modal fade" id="addStickyModal" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog modal-l" role="document">
                                    <div class="modal-content">
                                        {{ Form::open(['action' => ['SocialMediaStickyController@addSocial'], 'method' => 'POST']) }}
                                        <div class="modal-header">
                                            <h6 class="modal-title" id="exampleModalLabel">Edit Sticky</h6>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group">
                                                {{Form::label('name', 'Name', ['class' => 'col-form-label'])}}
                                                {{Form::text('name', '', ['class' => 'form-control', 'placeholder' => 'Add a name'])}}
                                            </div>
                                            <div class="form-group">
                                                {{Form::label('link', 'Link to social', ['class' => 'col-form-label'])}}
                                                {{Form::text('link', '', ['class' => 'form-control', 'placeholder' => 'Add a link'])}}
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                            {{Form::submit('Save Changes', ['class' => 'btn btn-success'])}}
                                        </div>
                                        {{Form::close()}}
                                    </div>
                                </div>
                            </div>
                            @foreach($social_media as $social)
                                <!-- Modal for EDIT Headline -->
                                    <div class="modal fade" id="editStickyModal-{{$social->id}}" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog modal-l" role="document">
                                            <div class="modal-content">
                                                {{ Form::open(['action' => ['SocialMediaStickyController@editSocial', $social->id], 'method' => 'POST']) }}
                                                <div class="modal-header">
                                                    <h6 class="modal-title" id="exampleModalLabel">Edit Sticky</h6>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        {{Form::label('name', 'Name', ['class' => 'col-form-label'])}}
                                                        {{Form::text('name', $social->name, ['class' => 'form-control', 'disabled' => true, 'placeholder' => 'Add a name'])}}
                                                    </div>
                                                    <div class="form-group">
                                                        {{Form::label('link', 'Link to social', ['class' => 'col-form-label'])}}
                                                        {{Form::text('link', $social->link, ['class' => 'form-control', 'placeholder' => 'Add a link'])}}
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                    {{Form::submit('Save Changes', ['class' => 'btn btn-success'])}}
                                                </div>
                                                {{Form::close()}}
                                            </div>
                                        </div>
                                    </div>
                            @endforeach
                        <!-- END of Social Media Modals -->

                        <!-- Header Links Modals -->
                            <!-- Modal for ADD Header link -->
                                <div class="modal fade" id="addHeaderLinkModal" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-l" role="document">
                                        <div class="modal-content">
                                            {{ Form::open(['action' => ['HeaderLinksController@addHeaderLink'], 'method' => 'POST']) }}
                                            <div class="modal-header">
                                                <h6 class="modal-title" id="exampleModalLabel">Add Header Link</h6>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    {{Form::label('name', 'Name', ['class' => 'col-form-label'])}}
                                                    {{Form::text('name', '', ['class' => 'form-control', 'placeholder' => 'Add a name'])}}
                                                </div>
                                                <div class="form-group">
                                                    {{Form::label('link', 'Link to header', ['class' => 'col-form-label'])}}
                                                    {{Form::text('link', '', ['class' => 'form-control', 'placeholder' => 'Add a link'])}}
                                                </div>
                                                <div class="form-group">
                                                    {{Form::label('weight', 'Position weight (lowest to highest, from left to right)', ['class' => 'col-form-label'])}}
                                                    {{Form::text('weight', '', ['class' => 'form-control', 'placeholder' => 'Add a number'])}}
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                {{Form::submit('Save Changes', ['class' => 'btn btn-success'])}}
                                            </div>
                                            {{Form::close()}}
                                        </div>
                                    </div>
                                </div>
                            
                            @foreach($header_links as $header)
                            <!-- Modal for EDIT header link -->
                                <div class="modal fade" id="editHeaderLinkModal-{{$header->id}}" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-l" role="document">
                                        <div class="modal-content">
                                            {{ Form::open(['action' => ['HeaderLinksController@editHeaderLink', $header->id], 'method' => 'POST']) }}
                                            <div class="modal-header">
                                                <h6 class="modal-title" id="exampleModalLabel">Edit Header Links</h6>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    {{Form::label('name', 'Name', ['class' => 'col-form-label'])}}
                                                    {{Form::text('name', $header->name, ['class' => 'form-control', 'placeholder' => 'Add a name'])}}
                                                </div>
                                                <div class="form-group">
                                                    {{Form::label('link', 'Link to header', ['class' => 'col-form-label'])}}
                                                    {{Form::text('link', $header->link, ['class' => 'form-control', 'placeholder' => 'Add a link'])}}
                                                </div>
                                                <div class="form-group">
                                                    {{Form::label('weight', 'Position weight (lowest to highest, from left to right)', ['class' => 'col-form-label'])}}
                                                    {{Form::text('weight', $header->position, ['class' => 'form-control', 'placeholder' => 'Add a number'])}}
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                {{Form::submit('Save Changes', ['class' => 'btn btn-success'])}}
                                            </div>
                                            {{Form::close()}}
                                        </div>
                                    </div>
                                </div>

                                <!-- Modal for DELETE header link -->
                                <div class="modal fade" id="deleteHeaderLinkModal-{{$header->id}}" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="{{ route('deleteHeaderLink', $header->id) }}" id="deleteForm" method="POST">
                                            <div class="modal-header">
                                                <h6 class="modal-title" id="exampleModalLabel">Confirm Delete</h6>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                {{ csrf_field() }}
                                                {{ method_field('DELETE') }}
                                                <span>
                                                    Are you sure you want to delete: <b>{{$header->name}}</b>?</br></br>
                                                </span>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>
                                                <input class="btn btn-danger" type="submit" value="Yes, Delete">
                                            </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <!-- END of Header Links Modals -->

                        <!-- Slider modals-->
                            <!-- Modal for add Slider -->
                                <div class="modal fade" id="addSliderModal" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-l" role="document">
                                        <div class="modal-content">
                                            {{ Form::open(['action' => ['LandingPageSlidersController@addSlider'], 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                            <div class="modal-header">
                                                <h6 class="modal-title" id="exampleModalLabel">Add Slider</h6>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    {{Form::label('is_video_create', 'Slider Type')}}
                                                    {{Form::select('is_video_create', ['0' => 'Text/Image', 
                                                                                '1' => 'Video', 
                                                                                ], '',['class' => 'form-control', 'name' => 'is_video_create']) }}
                                                </div>
                                                <div class="form-group">
                                                    {{Form::label('consortia', 'Consortia', ['class' => 'col-form-label'])}}
                                                    <select name="consortia" class="form-control" id="consortia">
                                                        <option value="" disabled> Select Consortia </option>
                                                        <option value="0">AANR</option>
                                                        @foreach(App\Consortia::all() as $consortium_add)
                                                            <option value="{{$consortium_add->id}}">{{$consortium_add->short_name}}</option>
                                                        @endforeach
                                                    </select> 
                                                </div>
                                                <div class="form-group">
                                                    {{Form::label('title', 'Title', ['class' => 'col-form-label'])}}
                                                    {{Form::text('title', '', ['class' => 'form-control', 'placeholder' => 'Add a title'])}}
                                                </div>
                                                <div class="is-video-create-no">
                                                    <div class="form-group">
                                                        {{Form::label('description', 'Description', ['class' => 'col-form-label'])}}
                                                        {{Form::textarea('description', '', ['class' => 'form-control', 'placeholder' => 'Add a description', 'rows' => 4])}}
                                                    </div>
                                                    <div class="form-group">
                                                        {{Form::label('textcard_enable', 'Enable text card?')}}
                                                        {{Form::select('textcard_enable', ['yes' => 'Yes', 
                                                                                    'no' => 'No', 
                                                                                    ], '',['class' => 'form-control',]) }}
                                                    </div>
                                                    <div class="form-group">
                                                        {{Form::label('button_text', 'Button Text', ['class' => 'col-form-label'])}}
                                                        <a href="#" data-toggle="tooltip" data-placement="top" title="Text for the button"><i class="far fa-question-circle"></i></a>
                                                        {{Form::text('button_text', '', ['class' => 'form-control', 'placeholder' => 'Add a text for the button'])}}
                                                    </div>
                                                    <div class="form-group">
                                                        {{Form::label('link', 'Link to article', ['class' => 'col-form-label'])}}
                                                        <a href="#" data-toggle="tooltip" data-placement="top" title="Link for the button"><i class="far fa-question-circle"></i></a>
                                                        {{Form::text('link', '', ['class' => 'form-control', 'placeholder' => 'Add a link for the button'])}}
                                                    </div>
                                                    <div class="form-group">
                                                        {{Form::label('caption_align', 'Caption Align')}}
                                                        <a href="#" data-toggle="tooltip" data-placement="top" title="Aligns the caption to the option selected"><i class="far fa-question-circle"></i></a>
                                                        {{Form::select('caption_align', ['left' => 'Left', 
                                                                                    'center' => 'Center', 
                                                                                    'right' => 'Right'
                                                                                    ], '',['class' => 'form-control', 'placeholder' => 'Select Align']) }}
                                                    </div>
                                                    <div class="form-group">
                                                        {{ Form::label('image', 'Slider Image', ['class' => 'col-form-label']) }}
                                                        {{ Form::file('image', ['class' => 'form-control mt-2 mb-3 pt-1'])}}
                                                    </div>
                                                </div>
                                                <div class="is-video-create-yes" style="display:none">
                                                    <div class="form-group">
                                                        {{Form::label('video_link', 'Link to YouTube video', ['class' => 'col-form-label'])}}
                                                        {{Form::text('video_link', '', ['class' => 'form-control', 'placeholder' => 'Add a YouTube link for the slider'])}}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                {{Form::submit('Add Slider', ['class' => 'btn btn-success'])}}
                                            </div>
                                            {{Form::close()}}
                                        </div>
                                    </div>
                                </div>
                            <!-- END modal for Slider -->
                            
                    
                            @foreach($sliders as $slider)
                                <!-- Modal for EDIT Slider -->
                                    <div class="modal fade" id="editSliderModal-{{$slider->id}}" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog modal-l" role="document">
                                            <div class="modal-content">
                                                {{ Form::open(['action' => ['LandingPageSlidersController@editSlider', $slider->id], 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                                <div class="modal-header">
                                                    <h6 class="modal-title" id="exampleModalLabel">Edit Slider</h6>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        {{Form::label('is_video_edit', 'Slider Type')}}
                                                        {{Form::select('is_video_edit', ['0' => 'Text/Image', 
                                                                                    '1' => 'Video', 
                                                                                    ], $slider->is_video,['class' => 'form-control', 'name' => 'is_video_edit']) }}
                                                    </div>
                                                    <div class="form-group">
                                                        {{Form::label('consortia', 'Consortia', ['class' => 'col-form-label'])}}
                                                        <select name="consortia" class="form-control" id="consortia">
                                                            <option value="" disabled> Select Consortia </option>
                                                            <option value="0" {{$slider->consortia_id == null || $slider->consortia_id == 0 ? 'selected' : ''}}>AANR</option>
                                                            @foreach(App\Consortia::all() as $consortium_edit)
                                                                <option value="{{$consortium_edit->id}}" {{$slider->consortia_id == $consortium_edit->id ? 'selected' : ''}}>{{$consortium_edit->short_name}}</option>
                                                            @endforeach
                                                        </select> 
                                                    </div>
                                                    <div class="form-group">
                                                        {{Form::label('title', 'Title', ['class' => 'col-form-label'])}}
                                                        {{Form::text('title', $slider->title, ['class' => 'form-control', 'placeholder' => 'Add a title'])}}
                                                    </div>
                                                    <div class="is-video-edit-no" style="{{$slider->is_video == 1 ? 'display:none' : ''}}">
                                                        <div class="form-group">
                                                            {{Form::label('textcard_enable', 'Enable text card?')}}
                                                            {{Form::select('textcard_enable', ['yes' => 'Yes', 
                                                                                        'no' => 'No', 
                                                                                        ], $slider->textcard_enable,['class' => 'form-control', 'placeholder' => '------------']) }}
                                                        </div>
                                                        <div class="form-group">
                                                            {{Form::label('description', 'Description', ['class' => 'col-form-label'])}}
                                                            {{Form::textarea('description', $slider->description, ['class' => 'form-control', 'placeholder' => 'Add a description', 'rows' => 4])}}
                                                        </div>
                                                        <div class="form-group">
                                                            {{Form::label('button_text', 'Button Text', ['class' => 'col-form-label'])}}
                                                            {{Form::text('button_text', $slider->button_text, ['class' => 'form-control', 'placeholder' => 'Add a text for the button'])}}
                                                        </div>
                                                        <div class="form-group">
                                                            {{Form::label('link', 'Link to article', ['class' => 'col-form-label'])}}
                                                            {{Form::text('link', $slider->link, ['class' => 'form-control', 'placeholder' => 'Add a link'])}}
                                                        </div>
                                                        <div class="form-group">
                                                            {{Form::label('caption_align', 'Caption Align')}}
                                                            {{Form::select('caption_align', ['left' => 'Left', 
                                                                                        'center' => 'Center', 
                                                                                        'right' => 'Right'
                                                                                        ], $slider->caption_align,['class' => 'form-control', 'placeholder' => 'Select Align']) }}
                                                        </div>
                                                        <div class="form-group">
                                                            {{ Form::label('image', 'Replace Image', ['class' => 'col-form-label']) }}
                                                            <img src="/storage/cover_images/{{$slider->image}}" class="card-img-top" style="width:100%;border:1px solid rgba(100,100,100,0.25)" >
                                                            {{ Form::file('image', ['class' => 'form-control mt-2 mb-3 pt-1'])}}
                                                        </div>
                                                    </div>
                                                    <div class="is-video-edit-yes" style="{{$slider->is_video == 0 ? 'display:none' : ''}}">
                                                        <div class="form-group">
                                                            {{Form::label('video_link', 'Link to YouTube video', ['class' => 'col-form-label'])}}
                                                            {{Form::text('video_link', $slider->video_link, ['class' => 'form-control', 'placeholder' => 'Add a YouTube link for the slider'])}}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                                    {{Form::submit('Submit Changes', ['class' => 'btn btn-success'])}}
                                                </div>
                                                {{Form::close()}}
                                            </div>
                                        </div>
                                    </div>
                                <!-- END modal for edit Slider -->
                                <!-- Modal for delete Slider -->
                                    <div class="modal fade" id="deleteSliderModal-{{$slider->id}}" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <form action="{{ route('deleteSlider', $slider->id) }}" id="deleteForm" method="POST">
                                                <div class="modal-header">
                                                    <h6 class="modal-title" id="exampleModalLabel">Confirm Delete</h6>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    {{ csrf_field() }}
                                                    {{ method_field('DELETE') }}
                                                    <span>
                                                        Are you sure you want to delete: <b>{{$slider->title}}</b>
                                                    </span>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>
                                                    <input class="btn btn-danger" type="submit" value="Yes, Delete">
                                                </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <!-- END modal for delete Slider -->
                            @endforeach
                        <!-- END of Slider modals-->
                    </div>
                    <div class="tab-pane fade" id="users">
                        <div class="section-header shadow px-5" style="padding-top:23px">
                            <span class="text-white mr-3">Manage Users: </span>
                            <div class="dropdown" style="display:initial;">
                                <button class="btn btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="{{$consortiaAdminRequests != 0 ? 'background-color:rgb(255, 228, 156)' : ''}}">
                                    <b style="text-transform: capitalize">{!!request()->user ? str_replace('_',' ',request()->user) : 'All'!!}</b>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{route('dashboardManage', ['user' => 'all'])}}">All Users</a>
                                    <a class="dropdown-item" href="{{route('dashboardManage', ['user' => 'requests'])}}">User Requests <span class="badge badge-warning" style="{{$consortiaAdminRequests != 0 ? '' : 'display:none'}}">!</span></a>
                                </div>
                            </div>
                        </div>
                        @include('layouts.messages')
                        @if(request()->user == 'all' || !request()->user)
                        <div class="card shadow mb-5 mt-0 ml-0">
                                <div class="card-header px-5 pt-4">
                                    <h2 class="text-primary" >
                                        All Users
                                    <span class="float-right">
                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#createIndustryModal"><i class="fas fa-plus"></i> Add</button>
                                    </span></h2>
                                </div>
                                <div class="card-body px-5">
                                    <table class="table data-table tech-table table-hover" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th width="5%">ID</th>
                                                    <th width="25%">Email</th>
                                                    <th width="25%">First Name</th>
                                                    <th width="25%">Last Name</th>
                                                    <th width="10%">Role</th>
                                                    <th width="10%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @foreach(App\User::all() as $user)
                                                <tr>
                                                    <td>{{$user->id}}</td>
                                                    <td>{{$user->email}}</td>
                                                    <td>{{$user->first_name}}</td>
                                                    <td>{{$user->last_name}}</td>
                                                    <td>
                                                        @if($user->role == 5)
                                                            Superadmin
                                                        @elseif($user->role == 1 && $user->consortia_admin_id != null)
                                                            {{App\Consortia::find($user->consortia_admin_id)->short_name}} Manager
                                                        @else
                                                            Standard User
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#setConsortiaAdminModal-{{$user->id}}"><i class="fas fa-edit"></i> Set as Consortia Admin</button>
                                                        <button type="button" class="btn btn-default" data-toggle="modal" data-target="#deleteUserModal-{{$user->id}}"><i class="fas fa-trash"></i> Delete</button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                    </table>
                                </div>
                        </div>
                        @elseif(request()->user == 'requests')
                        <div class="card shadow mb-5 mt-0 ml-0">
                            <div class="card-header px-5 pt-4">
                                <h2 class="text-primary" >
                                    User Requests 
                                </h2>
                            </div>
                            <div class="card-body px-5">
                                <table class="table data-table tech-table table-hover" style="width:100%">
                                        <thead>
                                            <tr>
                                                <th width="5%"></th>
                                                <th width="5%">ID</th>
                                                <th width="15%">Email</th>
                                                <th width="25%">First Name</th>
                                                <th width="20%">Last Name</th>
                                                <th width="20%">Request</th>
                                                <th width="10%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach(App\User::where('consortia_admin_request', '=', 1)->get() as $user)
                                            <tr>
                                                <td style="text-align:center"><input class="form-check-input" type="checkbox" value="" id="flexCheckDefault"></td>
                                                <td>{{$user->id}}</td>
                                                <td>{{$user->email}}</td>
                                                <td>{{$user->first_name}}</td>
                                                <td>{{$user->last_name}}</td>
                                                <td>
                                                    @if($user->role == 5)
                                                        Administrator
                                                    @elseif($user->role == 1 && $user->consortia_admin_id != null)
                                                        {{App\Consortia::find($user->consortia_admin_id)->short_name}} Manager
                                                    @else
                                                        Standard User
                                                    @endif
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#consortiaAdminRequestApproveModal-{{$user->id}}">Approve</button>
                                                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#consortiaAdminRequestDeclineModal-{{$user->id}}">Reject</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                </table>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="tab-pane fade" id="logs">
                        <div class="section-header shadow px-5" style="padding-top:23px">
                            <span class="text-white mr-3">Activity Logs</span>
                        </div>
                        <div class="card shadow mb-5 mt-0">
                            <div class="card-header px-5 pt-4">
                                <h2 class="text-primary" >
                                    Activity Logs
                                <span class="float-right">
                                    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#createGeneratorModal"><i class="fas fa-plus"></i> Download Excel</button>
                                </span></h2>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                <table class="table data-table tech-table table-hover" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="15%">Timestamp</th>
                                            <th width="5%">User ID</th>
                                            <th width="10%">User Email</th>
                                            <th width="10%">IP Address</th>
                                            <th width="10%">Resource</th>
                                            <th width="40%">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                            @foreach(App\Log::orderBy('id', 'desc')->get(); as $log)
                                                <tr>
                                                    <td>{{$log->id}}</td>
                                                    <td>{{Carbon\Carbon::parse($log->created_at)->format('M d,Y g:i:s A')}}</td>
                                                    <td>{{$log->user_id}}</td>
                                                    <td>{{$log->user_email}}</td>
                                                    <td>{{$log->IP_address}}</td>
                                                    <td>{{$log->resource}}</td>
                                                    <td>{{$log->action}}</td>
                                                </tr>
                                            @endforeach
                                            
                                    </tbody>
                                </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


                                
@endsection
@section('scripts')
    <script type="text/javascript">
        $(".list-group-item-action").on('click', function() {
            $(".list-group-item-action").each(function(index) {
                $(this).removeClass("active show");
            });
        })
        $(document).ready(function() {
            // init datatable on #example table
            $('.data-table').DataTable({
                "order": [[ 0, "desc" ]],
            });
            $('.data-table-options').DataTable({
                "order": [[ 1, "desc" ]],
            });
            $('select[name$="is_video_create"]').click(function() {
                if($(this).val() == '0') {
                    $('.is-video-create-yes').hide();
                    $('.is-video-create-no').show();
                }
                else {
                    $('.is-video-create-no').hide();
                    $('.is-video-create-yes').show();   
                }
            });
            $('select[name$="is_video_edit"]').click(function() {
                if($(this).val() == '0') {
                    $('.is-video-edit-yes').hide();
                    $('.is-video-edit-no').show();
                }
                else {
                    $('.is-video-edit-no').hide();
                    $('.is-video-edit-yes').show();   
                }
            });
        });
    </script>
@endsection