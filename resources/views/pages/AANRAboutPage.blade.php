@extends('layouts.app')
@section('title', 'About AANR')
@section('breadcrumb')
    <?php
        $headlines = App\Headline::all();
        $count = 0;
        if(request()->consortia){
            $consortium = App\Consortia::where('short_name','=',request()->consortia)->first();
        } else {
            $consortium = App\Consortia::first();
        }
        $aanrPage = App\AANRPage::first();
        $user = auth()->user();
        $pcaarrdPage = App\PCAARRDPage::first();
    ?>
    <div id="carouselContent" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner" role="listbox">
            @foreach($headlines as $headline)
                <div class="carousel-item {{$count == 0 ? 'active' : ''}} text-center p-4">
                    <a href="{{$headline->link}}" target="_blank" style="text-decoration: none; color:white; font-size:15px">{{$headline->title}}</a>
                </div>
                <?php $count++ ?>
            @endforeach
        </div>
    </div>
@endsection

    
@section('content')
@include('layouts.messages')

@if($user != null && ($user->consortia_admin_id == $consortium->id || $user->role == 5))
<div class="edit-bar">
    <nav class="navbar navbar-expand-lg shadow rounded" style="background-color:{{request()->edit == 1 ? '#53ade9' : '#05b52c'}}; height:52px">
        <div class="col-auto text-white font-weight-bold">
            You are viewing in {{request()->edit == 1 ? 'EDIT' : 'LIVE'}} mode
        </div>
        @if(request()->edit == 1)
            <a href="{{route('AANRAboutPage')}}" class="btn btn-success">View Live</a>
        @else
            <a href="{{route('AANRAboutPage', ['edit' => '1'])}}" class="btn btn-light">Edit</a>
        @endif
    </nav>
</div>
@endif

@if($aanrPage->is_gradient == 0)
<div class="w-100" style="height:450px;background-color:{{$aanrPage->banner_color}};">
@else
<div class="w-100" style="height:450px;background-image: linear-gradient({{$aanrPage->gradient_direction != null ? $aanrPage->gradient_direction : 'to right'}}, {{$aanrPage->gradient_first != null ? $aanrPage->gradient_first : '#ffffff'}} , {{$aanrPage->gradient_second != null ? $aanrPage->gradient_second : '#f89c0e'}});">
@endif
    <div class="{{request()->edit == '1' ? 'overlay-container' : ''}} " style="padding-left:10rem; padding-right:10rem">
        <div class="row">
            <div class="col-sm-3 flex-center-vertically">
                <div class="vertical-center" style="text-align:right;">
                    <img src="/storage/page_images/{{$aanrPage->thumbnail}}" style="width:75%">
                </div>  
            </div>
            <div class="col-sm-9 flex-center-vertically text-center">
                <div class="container text-center vertical-center pt-5">
                    <h1 style="text-transform: uppercase;"><b>{{$aanrPage->full_name}}</b></h1>
                    <div class="card" style="background-color:rgba(0, 0, 0,0.6);">
                        <div class="card-body">
                        <h3 style="color:white">{{$aanrPage->welcome_message}}</h3>
                        <a href="{{$aanrPage->link}}" target="_blank"><button type="button" class="btn btn-primary mt-3" style=";margin:auto">{{$aanrPage->button_text == null ? 'Visit website' : $consortium->button_text}}</button></a>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
        @if(request()->edit == 1)
        <div class="hover-overlay" style="width:100%; height:450px">    
            <button type="button" class="btn btn-xs btn-primary" data-target="#editAANRBanner" data-toggle="modal"><i class="far fa-edit"></i></button>      
        </div>
        @endif
    </div>
</div>

<style>

    .flex-center-vertically {
        display: flex;
        justify-content: center;
        flex-direction: column;
        height: 400px;
    }
    .consortia-header {
      position: relative;
    }
    
    .vertical-center {
      margin: 0;
      top: 50%;
    }
    .hover-overlay {
        transition: .5s ease;
        height:100%;
        opacity: 0;
        position: absolute;
        z-index:1000;
        text-align: right;
    }

    .overlay-container{
        position: relative;
        background-color:rgba(0,0,0,0);
        height:100%
    }

    .overlay-container:hover .bottom-overlay{
        opacity: 0.5;
    }

    .overlay-container:hover{
        background-color:rgba(0,0,0,.15);
        transition: .5s ease;
    }

    .overlay-container:hover .hover-overlay, .overlay-container:hover .hover-overlay-text{
        opacity: 1;
    }
    </style>
<div class="container mb-5 pt-5">
    <div class="row">
        <div class="col-sm-12">
            <a href="/" style="font-size:20px"><i class="fas fa-arrow-left"></i> Back to Home</a><br><br>
        </div>
        <div class="col-sm-12 {{request()->edit == '1' ? 'overlay-container' : ''}}">
            <div class="header">
                <h1><span class="title">{{$aanrPage->short_name}}</span></h1>
                <h3><span class="subtitle">{{$aanrPage->full_name}}</span></h3>
                <h5><span class="text-muted">{{$aanrPage->contact_name}} | {{$aanrPage->contact_details}}</span></h5>
            </div>
            <div class="dropdown-divider" style="border-top: 0.5px solid black"></div>
            <span class="pt-3" style="font-size:1rem">{!! $aanrPage->profile !!}</span>

            @if(request()->edit == 1)
            <div class="hover-overlay" style="width:100%">    
                <button type="button" class="btn btn-xs btn-primary" data-target="#editAANRDetails" data-toggle="modal"><i class="far fa-edit"></i></button>      
            </div>
            @endif
        </div>
        <div class="col-sm-12">
            <div class="card p-3" style="width:100%;">
                <h3 class="mt-3 mb-3">AANR Consortia and PCAARRD</h3>
                <div class="dropdown-divider mb-3"></div>
                <div class="card-body row">
                        @foreach(App\Consortia::all() as $consortia)
                            <div class="col-lg-3" >
                                <div class="card inside-card front-card shadow rounded mb-3" style="margin-top:0px !important">
                                    <div class="{{request()->edit == '1' ? 'card-hover' : ''}}">
                                        <img src="/storage/page_images/{{$consortia->thumbnail}}" class="card-img-top" height="175" style="object-fit: contain;background-color:rgb(224, 224, 235)">
                                        <div class="card-body">
                                            <h4 class="card-title trail-end mb-0">{{$consortia->short_name}}</h4>
                                            <div class="card-text trail-end" style="line-height: 120%;">
                                                <p class="mb-0"><b>{{$consortia->full_name}}</b></p>
                                                <small>
                                                    {{$consortia->region}}<br>
                                                    {{$consortia->contact_name}}<br>
                                                    {{$consortia->contact_details}}
                                                    <br></small>
                                            </div>
                                        </div>
                                        <a href="{{route('consortiaAboutPage', ['consortia' => $consortia->short_name])}}" class="stretched-link"></a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <div class="col-lg-3" >
                            <div class="card inside-card front-card shadow rounded mb-3" style="margin-top:0px !important">
                                <div class="{{request()->edit == '1' ? 'card-hover' : ''}}">
                                    <img src="https://upload.wikimedia.org/wikipedia/en/thumb/5/59/PCAARRD.svg/1200px-PCAARRD.svg.png" class="card-img-top" height="175" style="object-fit: contain;background-color:rgb(224, 224, 235)">
                                    <div class="card-body">
                                        <h4 class="card-title trail-end mb-0">{{$pcaarrdPage->short_name}}</h4>
                                        <div class="card-text trail-end" style="line-height: 120%;">
                                            <p class="mb-0"><b>{{$pcaarrdPage->full_name}}</b></p>
                                            <small>
                                                {{$pcaarrdPage->contact_name}}<br>
                                                {{$pcaarrdPage->contact_details}}
                                                <br></small>
                                        </div>
                                    </div>
                                    <a href="{{route('PCAARRDAboutPage')}}" class="stretched-link"></a>
                                </div>
                            </div>
                        </div>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editAANRBanner" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {{ Form::open(['action' => ['AANRPageController@editAANRPageBanner', $aanrPage->id], 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">Edit AANR Banner</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="mt-2">
                        {{Form::label('image', 'AANR Logo')}}
                        <br>
                        @if($aanrPage->thumbnail!=null)
                        <img src="/storage/page_images/{{$aanrPage->thumbnail}}" class="card-img-top" style="object-fit: cover;overflow:hidden;height:250px;width:250px;border:1px solid rgba(100,100,100,0.25)" >
                        @else
                        <div class="card-img-top center-vertically px-3" style="height:250px; width:250px; background-color: rgb(227, 227, 227);">
                            <span class="font-weight-bold" style="font-size: 17px;line-height: 1.5em;color: #2b2b2b;">
                                Upload a 250x250px logo for the AANR.
                            </span>
                        </div>
                        @endif 
                        {{ Form::file('image', ['class' => 'form-control mt-2 mb-3 pt-1'])}}
                        <style>
                            .center-vertically{
                                display: flex;
                                justify-content: center;
                                align-items: center;
                            }
                        </style>
                    </div> 
                </div>
                <div class="form-group">
                    {{Form::label('welcome', 'Welcome Message', ['class' => 'col-form-label'])}}
                    {{Form::textarea('welcome', $aanrPage->welcome_message, ['class' => 'form-control', 'placeholder' => 'Add a welcome message', 'rows' => '4'])}}
                </div>
                {{Form::label('banner_color', 'Change banner color', ['class' => 'col-form-label'])}}
                <div class="input-group">
                    <label class="mr-2 radio-inline"><input type="radio" name="banner_color_radio" value="0" {{$aanrPage->is_gradient == 0 ? 'checked': ''}}> Block color</label>
                    <label class="mx-2 radio-inline"><input type="radio" name="banner_color_radio" value="1" {{$aanrPage->is_gradient != 0 ? 'checked': ''}}> Gradient</label>
                </div>
                <div class="form-group block-color-form" style="{{$aanrPage->is_gradient != 0 ? 'display:none': ''}}">
                    {{Form::label('banner_color', 'Change block color', ['class' => 'col-form-label'])}}
                    {{Form::text('banner_color', $aanrPage->banner_color, ['class' => 'form-control', 'placeholder' => 'Add a hex'])}}
                </div>
                <div class="form-group gradient-color-form row" style="{{$aanrPage->is_gradient == 0 ? 'display:none': ''}}">
                    <div class="col-sm-6">
                        {{Form::label('gradient_first', 'Set first color', ['class' => 'col-form-label'])}}
                        {{Form::text('gradient_first', $aanrPage->gradient_first, ['class' => 'form-control', 'placeholder' => 'Add a hex'])}}
                    </div>
                    <div class="col-sm-6-">
                        {{Form::label('gradient_second', 'Set second color', ['class' => 'col-form-label'])}}
                        {{Form::text('gradient_second', $aanrPage->gradient_second, ['class' => 'form-control', 'placeholder' => 'Add a hex'])}}
                    </div>
                    <div class="col-sm-12">
                        {{Form::label('gradient_direction', 'Gradient direction', ['class' => 'col-form-label'])}}
                        {{Form::select('gradient_direction', ['to right' => 'Left to Right', 
                                                    'to left' => 'Right to Left', 
                                                    'to bottom' => 'Top to Bottom', 
                                                    'to top' => 'Bottom to Top', 
                                                    ], $aanrPage->gradient_direction,['class' => 'form-control', 'placeholder' => '------------']) }}
                    </div>
                </div>
                <div class="form-group">
                    {{Form::label('button_text', 'Change button text', ['class' => 'col-form-label'])}}
                    {{Form::text('button_text', $aanrPage->button_text, ['class' => 'form-control', 'placeholder' => 'Add button text'])}}
                </div>

                <div class="form-group">
                    {{Form::label('link', 'Add website link', ['class' => 'col-form-label'])}}
                    {{Form::text('link', $aanrPage->link, ['class' => 'form-control', 'placeholder' => 'Add a link'])}}
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

<div class="modal fade" id="editAANRDetails" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {{ Form::open(['action' => ['AANRPageController@editAANRPageDetails', $aanrPage->id], 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">Edit AANR Details</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    {{Form::label('full_name', 'Consortia Full Name', ['class' => 'col-form-label'])}}
                    {{Form::text('full_name', $aanrPage->full_name, ['class' => 'form-control', 'placeholder' => 'Add full name', 'disabled'])}}
                </div>
                <div class="form-group">
                    {{Form::label('short_name', 'Consortia Short Name', ['class' => 'col-form-label'])}}
                    {{Form::text('short_name', $aanrPage->short_name, ['class' => 'form-control', 'placeholder' => 'Add acronym/short name', 'disabled'])}}
                </div>
                <div class="form-group">
                    {{Form::label('profile', 'Profile', ['class' => 'col-form-label'])}}
                    {{Form::textarea('profile', $aanrPage->profile, ['class' => 'form-control', 'placeholder' => 'Add a profile', 'rows' => '4'])}}
                </div>
                <div class="form-group">
                    {{Form::label('contact_name', 'Contact Name', ['class' => 'col-form-label'])}}
                    {{Form::text('contact_name', $aanrPage->contact_name, ['class' => 'form-control', 'placeholder' => 'Add contact name'])}}
                </div>
                <div class="form-group">
                    {{Form::label('contact_details', 'Contact Details', ['class' => 'col-form-label'])}}
                    {{Form::text('contact_details', $aanrPage->contact_details, ['class' => 'form-control', 'placeholder' => 'Add contact details'])}}
                </div>

                <input type="hidden" id="link" name="link" value="{{$aanrPage->link}}">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                {{Form::submit('Save Changes', ['class' => 'btn btn-success'])}}
            </div>
            {{Form::close()}}
        </div>
    </div>
</div>

@endsection
<style>
    .title{
        color:rgb(74,130,185);
        margin-bottom: 2rem;
    }
    .header{
        line-height:5px;
    }
    .card-image-overlay{
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        padding: 1.25rem;
    }
    .card-hover {
        opacity: 1;
        display: block;
        width: 100%;
        height: auto;
        transition: .5s ease;
        backface-visibility: hidden;
    }

    .card-middle {
        transition: .5s ease;
        opacity: 0;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        -ms-transform: translate(-50%, -50%);
        text-align: center;
    }

    .inside-card:hover .card-hover{
        opacity: 0.3;
    }

    .inside-card:hover .card-middle{
        opacity: 1;
    }
    .edit-bar {
        position: fixed;
        bottom: 0;
        width: 100%;
        transform: translate(0%);
        z-index: 10000;
    }
  
</style>
@section('scripts')
    <script>
        $(document).ready(function() {
            $('input[name$="banner_color_radio"]').click(function() {
                if($(this).val() == '0') {
                    $('.gradient-color-form').hide();  
                    $('.block-color-form').show();            
                }
                else {
                    $('.block-color-form').hide();  
                    $('.gradient-color-form').show();   
                }
            });
            ClassicEditor
                .create(document.querySelector('#profile'))
                .catch(error => {
                    console.error(error);
            });
        });
    </script>
@endsection