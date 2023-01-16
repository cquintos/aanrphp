@extends('layouts.app')
@section('title', 'Edit Artifact')
@section('breadcrumb')
    <ol class="breadcrumb pb-0" style="background-color:transparent">
        <li class="breadcrumb-item"><a class="breadcrumb-link" href="/">KM4AANR</a></li>
        @if(auth()->user()->role == 5)
            <li class="breadcrumb-item"><a class="breadcrumb-link" href="{{ route('dashboardAdmin') }}">Admin Dashboard</a></li>
            <li class="breadcrumb-item"><a class="breadcrumb-link" href="{{ route('dashboardAdmin') }}?asset=Artifacts">Artifact</a></li>
        @else
            <li class="breadcrumb-item"><a class="breadcrumb-link" href="{{ route('userDashboard') }}">Consortia Admin Dashboard</a></li>
            <li class="breadcrumb-item"><a class="breadcrumb-link" href="{{ route('userDashboard') }}?asset=Artifacts">Artifact</a></li>
        @endif
        <li class="breadcrumb-item active" aria-current="page">Edit</li>
    </ol>
@endsection

<?php
    use Illuminate\Support\Collection;

    $commodity_subtypes = new Collection();
    foreach($artifact->commodities as $entry) {
        $commodity_subtypes = $commodity_subtypes->mergeRecursive($entry->subtypes->sortBy('name')->pluck('id', 'name'));
    }

    $commodity_subtypes = array_combine($commodity_subtypes->values()->toArray(), $commodity_subtypes->keys()->toArray());
?>

@section('content')
    <!-- Modal Includes -->
<body data-spy="scroll" data-target="#nav_items" data-offset="550" style="position: relative">
    <div class="container-fluid">
        <div class="row" id="main_row">
            <div class="col-xl-2 col-md-3 col-sm-12 px-0 pt-4" id="side_bar">
                <div id="nav_items" class="list-group">
                    <a class="list-group-item list-group-item-action" href="#basic_info"><i class="fas fa-info-circle side_panel_icon"></i> Basic Info</a>
                    <a class="list-group-item list-group-item-action" href="#file_and_links"><i class="fas fa-file-pdf side_panel_icon"></i> Files</a>
                    <a class="list-group-item list-group-item-action" href="#keywords"><i class="fas fa-search side_panel_icon"></i> Keywords</a>
                    <a class="list-group-item" href="{{ url()->previous() }}"><i class="fas fa-angle-left side_panel_icon"></i> Back</a>
                    @include('layouts.messages')
                </div>
            </div>
            <div class="col-sm-1"></div> 
            <div class="col-xl-8 col-md-9 col-sm-12 py-0">
                <div class="tab-content">
                    <div class="tab-pane fade show active">
                        <div class="card shadow-lg my-4">
                            <div class="card-header px-5 pt-2">
                                <span class="title">EDIT ARTIFACT</span>
                            </div>
                            <div class="card-body">
                                {{ Form::open(['action' => ['ArtifactAANRController@editArtifact', $artifact->id], 'id' => 'info_table', 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                <div class="form-group row d-flex justify-content-around">
                                    <div class="col-sm-12">
                                        <h3 class="mt-3 mb-2 font-weight-bold">Basic Information</h3>
                                        <div class="dropdown-divider col-sm-12" id="basic_info"></div>
                                    </div>
                                    <div class="col-sm-5">
                                        <h5>{{Form::label('consortia', 'Consortia', ['class' => 'col-form-label required'])}}</h5>
                                            {{Form::select('consortia', $artifact->consortia->pluck('short_name', 'id'), $artifact->consortia_id,['id' => 'consortia', 'class' => 'dynamic_consortia_member form-control', 'placeholder' => 'Select Consortia']) }}
                                        </select> 
                                        <h5>{{Form::label('consortia_member', 'SUC/Unit/Institution', ['class' => 'col-form-label'])}}</h5>
                                            {{Form::select('consortia_member', $artifact->consortia->consortia_members->pluck('name', 'id'), $artifact->consortia_member_id,['id' => 'consortia-member-edit', 'class' => 'form-control', 'placeholder' => 'Select Consortia Member']) }}
                                        <br><br>
                                            <div class="dropdown-divider mb-3"></div>
                                        <br>
                                        <h5>{{Form::label('title', 'Content Title', ['class' => 'col-form-label required'])}}</h5>
                                            {{Form::text('title', $artifact->title, ['class' => 'form-control', 'placeholder' => 'Add a title', 'maxlength' => 200])}}
                                        <h5>{{Form::label('author', 'Author', ['class' => 'col-form-label'])}}</h5>
                                            {{Form::text('author', $artifact->author, ['class' => 'form-control', 'placeholder' => 'e.g. Mae Santos', 'maxlength' => 200])}}
                                        <h5>{{Form::label('author_affiliation', 'Author Affilitation', ['class' => 'col-form-label'])}}</h5>
                                            {{Form::text('author_affiliation', $artifact->author_affiliation, ['class' => 'form-control', 'placeholder' => 'e.g. DOST-PCAARRD S&T Media Service', 'maxlength' => 200])}}
                                        <h5>{{Form::label('date_published', 'Date Published', ['class' => 'col-form-label'])}}</h5>
                                            {{Form::date('date_published', $artifact->date_published,['class' => 'form-control']) }}
                                    </div>
                                    <div class="col-sm-5">
                                        <h5>{{Form::label('content', 'Content Type', ['class' => 'col-form-label required'])}}</h5>
                                            {{Form::select('content', $content, $artifact->content_id,['class' => 'dynamic_content_subtype form-control', 'placeholder' => 'Select Content Type']) }}
                                        <h5>{{Form::label('content_subtype', 'Subcontent Type', ['class' => 'col-form-label'])}}</h5>
                                            {{Form::select('content_subtype', $artifact->content->content_subtypes->pluck('name', 'id'), $artifact->contentsubtype_id,['id' => 'content-subtype-edit', 'class' => 'form-control', 'placeholder' => 'Select Content Subtype']) }}
                                        <br><br>
                                            <div class="dropdown-divider mb-3"></div>
                                        <br>
                                        <h5>{{Form::label('isp', 'ISPs', ['class' => 'col-form-label'])}}</h5>
                                            {{Form::select('isp[]', $isp, null, ['class' => 'form-control multi-isp-edit w-100', 'multiple' => 'multiple'])}}
                                        <h5>{{Form::label('commodity', 'Commodities', ['class' => 'col-form-label'])}}</h5>
                                            {{Form::select('commodities[]', $commodities, null, ['class' => 'form-control multi-commodity-edit w-100', 'multiple' => 'multiple'])}}
                                        <h5>{{Form::label('commodity_subtype', 'Commodity Subtype', ['class' => 'col-form-label'])}}</h5>
                                            {{Form::select('commodity_subtypes[]', $commodity_subtypes, null, ['class' => 'form-control multi-subcommodity-edit w-100', 'multiple' => 'multiple'])}}
                                        <h5>{{Form::label('is_gad', 'GAD Focus?', ['class' => 'col-form-label mb-1'])}}</h5>
                                        <label class="mr-2 radio-inline"><input type="radio" name="is_gad" value="1" {{$artifact->is_gad == 1 ? 'checked': ''}}> Yes</label>
                                        <label class="mx-2 radio-inline"><input type="radio" name="is_gad" value="0" {{$artifact->is_gad == 0 ? 'checked': ''}}> No</label>
                                    </div>
                                    <div class="col-sm-11">
                                        <br>
                                        <div class="dropdown-divider col-sm-12 mt-3" id="file_and_links"></div>
                                        <br>
                                        <h5>{{Form::label('description', 'Description', ['class' => 'col-form-label'])}}</h5>
                                            {{Form::textarea('description', $artifact->description, ['id'=>'description_box', 'class' => 'form-control', 'placeholder' => 'Add a description', 'maxlength' => 2000])}}
                                    </div>
                                </div>
                                <div class="form-group row d-flex justify-content-aroundd-flex justify-content-around">
                                    <div class="col-sm-12">
                                        <h3 class="mt-5 mb-3 font-weight-bold">File and Links</h3>
                                        <div class="dropdown-divider mb-3"></div>
                                    </div>
                                    <div class="col-sm-5">
                                        @if($artifact->file)
                                            <h5>PDF Preview</h5>
                                            <iframe src="{{$artifact->file_type == 'pdf_link' ? $artifact->file : asset('/storage/files/' . $artifact->file)}}"></iframe>
                                        @endif
                                        <h5>{{Form::label('file', 'File Upload (PDF, JPEG, PNG)', ['class' => 'col-form-label'])}}</h5>
                                            {{ Form::file('file', ['class' => 'form-control mb-3 pt-1'])}}
                                    </div>
                                    <div class="col-sm-5">
                                        @if($artifact->embed_link)
                                            <h5>Embed Link Preview</h5><iframe src="{{$artifact->embed_link}}"></iframe>
                                        @endif
                                        <h5>{{Form::label('link', 'Redirect Link', ['class' => 'col-form-label'])}}</h5>
                                            {{Form::text('link', $artifact->link, ['class' => 'form-control mb-3 pt-1', 'placeholder' => 'Add an external link to redirect to'])}}
                                        <h5>{{Form::label('embed_link', 'Embed Link', ['class' => 'col-form-label'])}}</h5>
                                            {{Form::text('embed_link', $artifact->embed_link, ['class' => 'form-control', 'placeholder' => 'Add a link to embed to the content modal'])}}
                                    </div>
                                </div>
                                <div class="form-group row d-flex justify-content-around" id="keywords">
                                    <div class="col-sm-12">    
                                        <h3 class="mt-5 mb-3 font-weight-bold">Keywords</h3>
                                        <div class="dropdown-divider mb-3"></div>
                                    </div>
                                    <div class="col-sm-11">
                                        <h5>{{Form::label('keywords', 'Search keywords', ['class' => 'col-form-label'])}}</h5>
                                            {{Form::text('keywords', $artifact->keywords, ['class' => 'form-control', 'placeholder' => 'Separate keywords with commas (,)'])}}
                                    </div>
                                </div>
                                <div class="col-sm-12 card-footer row d-flex justify-content-around px-0">
                                    <div class='col-sm-4'></div>
                                    <div class='col-sm-2'><i class="fas fa-angle-down d-flex justify-content-center"></i></div>
                                    <div class="col-sm-4 d-flex justify-content-end">
                                        {{Form::submit('Save changes', ['class' => 'btn btn-success'])}}
                                        {{ Form::close() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<style>
    .fa-angle-down{
        color: white;
        font-size: 2rem;
    }

    .description_box {
        min-height: 500px;
    }

    iframe{
        width:100%;
        height:500px;
        margin-top:1rem;
        border:0px;
    }

    h3 {
        color: white;
    }
    
    h5 {
        margin-bottom: 0;
        margin-top: 1rem;
        color: white;
        font-weight: 600;
    }

    h6 {
        margin-top: 0.5rem;
        color: white;
        font-weight: 600;
    }

    .list-group-item:first-child {
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }

    .shadow-lg {
        border: 0px;
        border-radius: 10px !important;
        background-color: lightgray;
    }

    #nav_items{
        position: sticky;
        top:10.4rem;
    }

    .btn-success{
        background-color: #30A662;
        color: #fff;
        font-weight: bold;
    }

    .card-footer {
        margin:0px !important;
        position: sticky; 
        bottom:0rem; 
        background-color: rgb(115, 134, 141);
        border-top: 0px;
    }

    .list-group-item.active, a.list-group-item:hover {
        background-color: lightgray;
        border-color: rgb(71,87,102) !important;
        color:rgb(40, 40, 40);
    }

    #side_bar {
        background-color: rgb(40, 40, 40);
    }

    #main_row {
        max-height:inherit; 
        background-color: lightgray;
    }

    .title {
        font-weight: bold;
        font-size: 2.5rem;
    }

    .card-body {
        position: relative;
        padding-right: 3rem;
        padding-left: 3rem;
        background-color: rgb(115, 134, 141);
        border-bottom-left-radius: 10px !important;
        border-bottom-right-radius: 10px !important;
    }

    .card-header:first-child {
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
        height: 4.5rem !important;
        background-color: #30A662;
        color: #fff;
        z-index:100;
    }

    .nav{
        position: fixed;
        border-bottom-width: 0px;
    }

    .side_panel_icon{
        margin-right:0.8rem;
    }

    #submit_btn{
        background-color: #30A662;
        color: white;
    }
    
    .select2-container {
        width: 100% !important;
        padding: 0;
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

    .list-group-item{
        width:100%;
        font-size: 1.125rem;
        font-weight: 500;
        height:4.5rem;
        background-color: inherit;
        border-style: solid !important;
        border:0;
        color:rgb(207, 207, 207);
        border: 0px;
        border-top-right-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
        color:rgb(207, 207, 207);
        padding-top:23px; 
        padding-left:32px;
        padding-right: 0;
    }
</style>                  
@endsection
@section('scripts')
    <script>
        
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
                $.ajax({
                    url:"{{ route('fetchContentSubtypeDependent') }}",
                    method:"POST",
                    data:{value:$(this).val(), _token:$('input[name="_token"]').val()},
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

        $('.multi-subcommodity-edit').select2({
            placeholder: " Select sub commodities"
        }).val({!! json_encode($artifact->commodity_subtypes()->allRelatedIds()) !!}).trigger('change')

        $('.multi-commodity-edit').change(function(){
            $ids = $('.multi-commodity-edit').val();
            $.ajax({
                url:"{{ route('fetchCommoditySubtypeDependent') }}",
                method:"GET",
                data:{ids:$ids, _token: '{{csrf_token()}}'},
                success:function(result) {
                    $currentVal = $('.multi-subcommodity-edit').val();
                    $('.multi-subcommodity-edit').select2({ placeholder: " Select sub commodity" }).html(result);
                    $('.multi-subcommodity-edit').val($currentVal).trigger("change");
                }
            }) 
        });
    </script>
@endsection