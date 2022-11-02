@extends('layouts.app')
@section('title', 'Edit Commodity')
@section('breadcrumb')
    <ol class="breadcrumb pb-0" style="background-color:transparent">
        <li class="breadcrumb-item"><a class="breadcrumb-link" href="/">km4aanr</a></li>
        <li class="breadcrumb-item"><a class="breadcrumb-link" href="/">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Manage</li>
    </ol>
@endsection

<?php
    $commodity = App\Commodity::findOrFail(request()->id)
?>

@section('content')
    <div class="container-fluid">
        <div id="main" class="row" style="max-height:inherit; min-height:52.5rem">

            <div class="col-xl-2 col-md-4 pl-0 pr-0" style="background-image: linear-gradient(to right, rgb(118,128,138) , rgb(79, 94, 109));">
                <div class="nav nav-tabs" style="border-bottom-width: 0px;">
                    <a class="list-group-item active" data-toggle="tab" href="#edit" style="padding-top:23px; padding-left:32px">
                        <span><i class="fas fa-database" style="margin-right:0.8rem"></i> Edit Commodity</span>
                    </a>
                    <a class="list-group-item" href="/dashboard/manage?asset=Commodities" style="padding-top:23px; padding-left:32px">
                        <span><i class="fas fa-angle-left" style="margin-right:0.8rem"></i> Back</span>
                    </a>
                </div>
            </div>

            <div class="col-xl-6 col-md-8 pl-0 pr-0">
            @include('layouts.messages')

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="edit">
                        <div class="card shadow" style="margin-top:0px !important">
                            <div class="card-header px-5 pt-4" >
                                <span class="title" >
                                    EDIT COMMODITY: {{$commodity->name}}                                 
                                </span>
                            </div>

                            <div class="card-body">
                                {{ Form::open(['action' => ['CommoditiesController@edit', request()->id], 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}

                                <h3 class="mt-3 mb-3 font-weight-bold">
                                    Basic Information
                                </h3>
                               
                                <div class="dropdown-divider mb-3"></div>
                               
                                <div class="form-group row">
                                    
                                    <div class="col-sm-5">
                                        <h3>
                                        {{Form::label('name', 'Commodity Name', ['class' => 'col-form-label required'])}}
                                        </h3>
                                        {{Form::text('name', $commodity->name, ['class' => 'form-control', 'placeholder' => 'Add a name'])}}
                                    </div>
                                    
                                    <div class="col-sm-5">
                                        <h3>
                                            {{Form::label('subcommodities', 'Sub-commodities', ['class' => 'col-form-label'])}}
                                        </h3>

                                        <div class="btn-group-toggle" data-toggle="buttons">
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
                                            (click to delete sub-commodity)
                                        @endempty
                                    </div>

                                </div>
                               
                                <div class="form-group row">

                                    <div class="col-sm-5">
                                        <h3> {{Form::label('name', 'Add sub-commodity', ['class' => 'col-form-label'])}} </h3>
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control" placeholder="Sub-commodity name" name="new-commodity" id="new-commodity">
                                            <button class="btn btn-success" type="button" id="button-add">
                                                <i class="fas fa-plus-circle"></i>
                                                Add
                                            </button>
                                        </div>
                                    </div>
                                    
                                </div>

                                <div class="form-group row">
                                    <div class="col-xl-5 col-md-10">
                                        <h3> {{ Form::label('description', 'Description', ['class' => 'col-form-label']) }} </h3>
                                        {{ Form::textarea('description', $commodity->description, ['class' => 'form-control', 'placeholder' => 'Add a description']) }}
                                </div>
                            
                            </div> 

                            <div class="card-footer form-group">
                                {{ Form::submit('Save Changes', ['class' => 'btn btn-success']) }}
                                {{ Form::close() }}
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .title {
            font-weight: bold;
            font-size: 2.5rem;
        }

        #main {
            height: 100%;
        }

        .btn.active{
            background-color: #52e991;
        }

        .btn-outline-danger {
            color: rgb(20, 20, 90);
            background-color: #52e991;
            border-color: #52e991;
            box-shadow: #52e991;
        }

        .btn-success{
            background-color: #52e991;
            color: rgb(20, 20, 90);
        }

        .btn{
            font-weight: bold;
        }

        .card-header{
            background-color: #52e991;
            color: rgb(20, 20, 90);
        }

        .card-body {
            padding-left: 3rem;
            background-color: #bfd1d8;
        }

        .card-footer {
            padding: 0.75rem 0rem;
            background-color: transparent;
            border-top: 2px solid white;
        }
        
        .nav-link.active{
            color:black !important;
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

        .form-check-input {
            margin-left:0px !important;
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