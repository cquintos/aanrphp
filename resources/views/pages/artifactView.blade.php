@extends('layouts.app')
@section('title', 'View Artifact')
@section('breadcrumb')
    <ol class="breadcrumb pb-0" style="background-color:transparent">
        <li class="breadcrumb-item"><a class="breadcrumb-link" href="/">km4aanr</a></li>
        <li class="breadcrumb-item"><a class="breadcrumb-link" href="/">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Content</li>
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
    $content_type = App\Content::pluck('type', 'id')->all();
    $consortia_name = App\Consortia::pluck('short_name', 'id')->all();
    $content_sub_type = App\ContentSubtype::pluck('name', 'id')->all();
    $isp = App\ISP::pluck('name', 'id')->all();
?>

@section('content')
    <!-- Modal Includes -->
    <div class="container-fluid">
        <div class="row" style="max-height:inherit; min-height:52.5rem">
            <div class="col-xl-2 col-md-3 pl-0 pr-0" style="background-image: linear-gradient(to right, rgb(118,128,138) , rgb(79, 94, 109));">
                <div class="nav nav-tabs" style="border-bottom-width: 0px;">
                   
                    <a class="list-group-item" href="/dashboard/manage?asset=Artifacts" style="padding-top:23px; padding-left:32px">
                        <span><i class="fas fa-angle-left" style="margin-right:0.8rem"></i> Back</span>
                    </a>
                </div>
            </div>


            <div class="col-xl-10 col-md-9 pl-0 pr-0">
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="edit">
                        <div class="card shadow mb-5" style="margin-top:0px !important">
                            <div class="card-header px-10 pt-0 pb-0" >
                                <h4 class="mt-3 mb-3 font-weight-bold">{{$consortia_name[$artifact->consortia_id]}}
                                || {{$content_type[$artifact->content_id]}} 
                                {{$artifact->contentsubtype_id != null ? "- ".$content_sub_type[$artifact->contentsubtype_id] : ""}} </h4>
                            </div>
                            <div class="card-body">
                                <h2 class="mt-0 mb-3 font-weight-bold">{{$artifact->title}}</h2>
                                <div class="dropdown-divider mb-3"></div>
                                <div class="form-group row">
                                    <div class="col-sm-11">
                                        {{$artifact->description}}
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        {{Form::label('isp', 'ISPs', ['class' => 'col-form-label'])}} 
                                        <br>
                                        {{Form::select('isp[]', $isp, null, ['class' => 'form-control multi-isp-edit w-100', 'multiple' => 'multiple', 'disabled', 'readonly'])}}
                                    </div>
                                    <div class="col-sm-4">
                                        {{Form::label('commodity', 'Commodities', ['class' => 'col-form-label'])}} 
                                        <br>
                                        {{Form::select('commodities[]', $commodities, null, ['class' => 'form-control multi-commodity-edit w-100', 'multiple' => 'multiple', 'disabled', 'readonly'])}}
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        {{Form::label('author', 'Author', ['class' => 'col-form-label'])}}
                                        {{Form::text('author', $artifact->author, ['class' => 'form-control', 'disabled', 'readonly'])}}
                                    </div>
                                    <div class="col-sm-4">
                                        {{Form::label('author_affiliation', 'Author Affilitation', ['class' => 'col-form-label'])}}
                                        {{Form::text('author_affiliation', $artifact->author_affiliation, ['class' => 'form-control', 'disabled', 'readonly'])}}
                                    </div>
                                    <div class="col-sm-3">
                                        {{Form::label('date_published', 'Date Published', ['class' => 'col-form-label'])}}
                                        {{ Form::date('date_published', $artifact->date_published,['class' => 'form-control', 'disabled', 'readonly']) }}
                                    </div>
                                </div>
                                <div class="form-group mt-4">
                                    <h3 class="mt-5 mb-3 font-weight-bold">File and Links</h3>
                                    <div class="dropdown-divider mb-3"></div>
                                    @if($artifact->file)
                                        <div class="form-group row">
                                            <div class="col-sm-6">
                                                <b>PDF Preview</b><br>
                                                <iframe 
                                                    class="mt-2"
                                                    src="{{$artifact->file_type == 'pdf_link' ? $artifact->file : asset('/storage/files/' . $artifact->file)}}" 
                                                    style="width:180%; height:1200px;" 
                                                    frameborder="0">
                                                </iframe>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                
                            </div>
                            <div class="card-footer">
                                <h4 class="font-weight-bold">Search Keywords</h4>
                                    <div class="dropdown-divider mb-3"></div>
                                    <div class="form-group row">
                                        <div class="col-sm-6">
                                            {{$artifact->keywords}}
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .select2-container {
            width: 100% !important;
            padding: 0;
        }
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
@endsection
@section('scripts')
    <script>
        
        $('.dynamic_consortia_member').each(function(){
            if($(this).val() != ''){
                var consortia_member = $(this).attr('id');
                var consortia_member = consortia_member+'_id';
                var value = $(this).val();
                var dependent = $(this).data('dependent');
                var consortia_member_id = $(this).data('consortiamemberid');
                var _token = $('input[name="_token"]').val();
                $.ajax({
                    url:"{{ route('fetchConsortiaMemberDependent') }}",
                    method:"POST",
                    data:{consortia_member:consortia_member, value:value, _token:_token, dependent:dependent, consortia_member_id:consortia_member_id},
                    success:function(result){
                        $('#consortia-member-edit').html(result);
                    }
                })
            }
        });
        $('.dynamic_consortia_member').change(function(){
            if($(this).val() != ''){
                var consortia_member = $(this).attr('id');
                var consortia_member = consortia_member+'_id';
                var value = $(this).val();
                var dependent = $(this).data('dependent');
                var consortia_member_id = $(this).data('consortiamemberid');
                var _token = $('input[name="_token"]').val();
                $.ajax({
                    url:"{{ route('fetchConsortiaMemberDependent') }}",
                    method:"POST",
                    data:{consortia_member:consortia_member, value:value, _token:_token, dependent:dependent, consortia_member_id:consortia_member_id},
                    success:function(result){
                        $('#consortia-member-edit').html(result);
                    }
                })
            }
        });
        $('.dynamic_content_subtype').change(function(){
            if($(this).val() != ''){
                var content_subtype = $(this).attr('id');
                var content_subtype = content_subtype+'_id';
                var value = $(this).val();
                var dependent = $(this).data('dependent');
                var contentsubtype_id = $(this).data('contentsubtypeid');
                var _token = $('input[name="_token"]').val();
                $.ajax({
                    url:"{{ route('fetchContentSubtypeDependent') }}",
                    method:"POST",
                    data:{content_subtype:content_subtype, value:value, _token:_token, dependent:dependent, contentsubtype_id:contentsubtype_id},
                    success:function(result){
                        $('#content-subtype-edit').html(result);
                    }
                })
            }
        });
        $('.dynamic_content_subtype').each(function(){
            if($(this).val() != ''){
                var content_subtype = $(this).attr('id');
                var content_subtype = content_subtype+'_id';
                var value = $(this).val();
                var dependent = $(this).data('dependent');
                var contentsubtype_id = $(this).data('contentsubtypeid');
                var _token = $('input[name="_token"]').val();
                $.ajax({
                    url:"{{ route('fetchContentSubtypeDependent') }}",
                    method:"POST",
                    data:{content_subtype:content_subtype, value:value, _token:_token, dependent:dependent, contentsubtype_id:contentsubtype_id},
                    success:function(result){
                        $('#content-subtype-edit').html(result);
                    }
                })
            }
        });
        $('.multi-commodity-edit').select2({
            placeholder: " Select commodity"
        }).val({!! json_encode($artifact->commodities()->allRelatedIds()) !!}).trigger('change');
        $('.multi-isp-edit').select2({
            placeholder: " Select ISP"
        }).val({!! json_encode($artifact->isp()->allRelatedIds()) !!}).trigger('change');
    </script>
@endsection