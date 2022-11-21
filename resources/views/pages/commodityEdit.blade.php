@extends('layouts.app')
@section('title', 'Edit Commodity')
@section('breadcrumb')
    <ol class="breadcrumb pb-0" style="background-color:transparent">
        <li class="breadcrumb-item"><a class="breadcrumb-link" href="/">KM4AANR</a></li>
        <li class="breadcrumb-item"><a class="breadcrumb-link" href="{{ route('dashboardAdmin') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a class="breadcrumb-link" href="{{ route('dashboardAdmin') }}?asset=Commodities">Commodity</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit</li>
    </ol>
@endsection

<?php
    $commodity = App\Commodity::findOrFail(request()->id);
    $industries = App\Industry::all()->pluck('name', 'id');
?>

@section('content')
<body data-spy="scroll" data-target="#nav_items" data-offset="350" style="position: relative">
    <div class="container-fluid">
        <div id="main_row" class="row">
            <div class="col-xl-2 col-lg-12 col-md-12 pl-0 pr-0" id="side_bar">
                <div id="nav_items" class="list-group">
                    <a class="list-group-item list-group-item-action" href="#basic_info"><i class="fas fa-info-circle side_panel_icon"></i> Basic Information</a>
                    <a class="list-group-item list-group-item-action" href="{{ route('dashboardAdmin') }}?asset=Commodities"><i class="fas fa-angle-left side_panel_icon "></i> Back</a>
                    @include('layouts.messages')
                </div>
            </div>
            <div class="col-sm-1">
            </div>
            <div class="col-xl-6 col-lg-12 col-md-12 pl-0 pr-0">
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="edit">
                        <div class="card shadow-lg my-4">
                            <div class="card-header px-5 pt-2" ><span class="title">EDIT COMMODITY</span></div>
                            <div class="card-body">
                                {{ Form::open(['action' => ['CommoditiesController@edit', request()->id], 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
                                <div class="form-group row d-flex justify-content-around" id="basic_info">
                                    <div class="col-sm-12">
                                        <h3 class="mt-3 mb-2 font-weight-bold">Basic Information</h5>
                                        <div class="dropdown-divider col-sm-12"></div>
                                    </div>
                                    <div class="col-sm-5">
                                        <h5>{{Form::label('name', 'Commodity Name', ['class' => 'col-form-label required'])}}</h5>
                                        {{Form::text('name', $commodity->name, ['class' => 'form-control', 'placeholder' => 'Add a name', 'maxlength' => '40'])}}
                                        <h5>{{Form::label('industry_name', 'Industry', ['class' => 'col-form-label required'])}}</h5>
                                        {{Form::select('industry', $industries, $commodity->industry_id,['class' => 'form-control', 'placeholder' => 'Select Industry']) }}
                                    </div>
                                    <div class="col-sm-5">
                                        <h5> {{Form::label('name', 'Add sub-commodity', ['class' => 'col-form-label'])}} </h5>
                                        <div class="input-group">
                                            <input type="text" class="form-control" placeholder="Sub-commodity name" name="new-commodity" id="new-commodity" maxlength="40">
                                            <button class="btn btn-success" type="button" id="button-add"><i class="fas fa-plus-circle"></i></button>
                                        </div>
                                        <div class="btn-group-toggle" data-toggle="buttons">
                                            <h5>{{Form::label('subcommodities', 'Sub-commodities:', ['class' => 'col-form-label'])}}</h5>
                                            @php $i=0 @endphp
                                            @forelse($commodity->subtypes->all() as $entry)
                                                <label class="btn btn-outline-danger mt-1 ml-1">
                                                    <input type="checkbox"  id="subtype-btn_{{$i}}" class="btn-check" name="subtypes[]" autocomplete="off" checked value="{{$entry->name}}"> 
                                                    <i class="fas fa-trash" id="subtype-icon{{$i}}"></i> {{$entry->name}} 
                                                </label>
                                                @php $i++ @endphp
                                            @empty
                                            @endforelse
                                        </div>
                                        @empty($commodity->subtypes->all())
                                        @else 
                                            <h6>(click to delete sub-commodity)<h6>
                                        @endempty
                                    </div>
                                </div>
                                <div class="form-group row d-flex justify-content-around">
                                    <div class="col-sm-11">
                                        <h5> {{ Form::label('description', 'Description', ['class' => 'col-form-label']) }} </h5>
                                        {{ Form::textarea('description', $commodity->description, ['class' => 'form-control', 'placeholder' => 'Add a description', 'maxlength' => 2000]) }}
                                    </div>
                                </div> 
                                <div class="col-sm-12 card-footer row d-flex justify-content-around px-0">
                                    <div class='col-sm-5'></div>
                                    <div class="col-sm-5 d-flex justify-content-end">
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
    h3 {
        color: white;
    }

    h6 {
        margin-top: 0.5rem;
        color: white;
        font-weight: 600;
    }

    h5 {
        margin-bottom: 0;
        margin-top: 1rem;
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

    .side_panel_icon{
        margin-right:0.8rem;
    }

    #nav_items{
        position: sticky;
        top:10.4rem;
    }
    
    .title {
        font-weight: bold;
        font-size: 2.5rem;
    }
    
    #main_row {
        max-height:inherit; 
        background-color: lightgray;
    }

    .btn.active{
        background-color: #30A662;
    }

    .btn-outline-danger {
        color: #fff;
        background-color: #30A662;
        border-color: #30A662;
        box-shadow: #30A662;
    }

    .btn-success{
        background-color: #30A662;
        color: #fff;
    }

    .btn{
        font-weight: bold;
    }

    .card-header{
        height: 4.5rem;
        background-color: #30A662;
        color: #fff;
        border-top-left-radius: 10px !important;
        border-top-right-radius: 10px !important;
    }

    .card-body {
        padding-right: 3rem;
        padding-left: 3rem;
        background-color: rgb(115, 134, 141);
        border-bottom-left-radius: 10px;
        border-bottom-right-radius: 10px;
    }

    .card-footer {
        position: sticky; 
        bottom:0rem; 
        background-color: rgb(115, 134, 141) !important;

        border-top: 0;
    }

    .nav-link.active{
        color:black !important;
    }

    #side_bar {
        background-color: rgb(40, 40, 40);
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

    .form-switch input:checked + i { background-color: #30A662; }

    .form-switch input:checked + i::before { transform: translate3d(18px, 2px, 0) scale3d(0, 0, 0); }

    .form-switch input:checked + i::after { transform: translate3d(22px, 2px, 0); }

    .form-check-input {
        margin-left:0px !important;
    }

    .list-group-item.active, a.list-group-item:hover {
        background-color: lightgray;
        border-color: rgb(71,87,102) !important;
        color:rgb(40, 40, 40);
    }

    .list-group-item{
        width:100%;
        border: 0px;
        font-size: 1.125rem;
        font-weight: 500;
        height:4.5rem;
        background-color: inherit;
        border-width: 2px 0px;
        border-top-left-radius: 10px !important; 
        border-bottom-left-radius: 10px !important; 
        border-top-right-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
        color:rgb(207, 207, 207);
        padding-top:23px; 
        padding-left:32px;
        padding-right: 0;
    }

    .list-group-item:last-child {

    }

</style>                  
@endsection
@section('scripts')
    <script>
        var n = <?php echo $i?>;

        $(function(){
            $(document).on("click", ".btn-check", (function() {
                var number = $(this).attr('id').split('_').pop();
                $("#subtype-icon"+number).toggleClass("fa-trash fa-plus-circle");
                return true;
                })
            );
        });
        
        $(function(){
            $("#button-add").click(function(){
                var value = $("#new-commodity").val();
                $("#new-commodity").val('');
                n++;

                if(value == '') {
                    alert('Please input a name');
                    return;
                }

                $(".btn-group-toggle").append(
                    $(document.createElement('label'))
                        .addClass("btn btn-outline-danger active mt-1 ml-1")
                        .append(
                            $(document.createElement('input'))
                                .attr({
                                    type: 'checkbox',
                                    id: 'subtype-btn_'+n,
                                    class: 'btn-check',
                                    name: 'subtypes[]',
                                    autocomplete: 'off',
                                    value: value,
                                    checked: 'true',
                                })
                        ).append(
                            $(document.createElement('i'))
                                .attr({
                                    class: 'fas fa-trash',
                                    id: 'subtype-icon'+n
                                })
                        ).append(
                            " "+value
                        )
                )
                return true;
            });
        });
    </script>
@endsection