@extends('layouts.app')
@section('title')
{{ isset($query) ? $query : 'Search'}}
@endsection
@section('breadcrumb')
    <?php
        $headlines = App\Headline::all();
        $count = 0;
        if(request()->consortium){
            $consortium_search = App\Consortia::where('id','=',request()->consortium)->first()->short_name;
        }
        $user = auth()->user();
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

@include('pages.modals.landingPage')
@include('layouts.messages')
<?php 
    $landing_page = App\LandingPageElement::find(1);
    $aanrPage = App\AANRPage::first();
    $content_search_query = ''; 
    if(request()->content_type == 'all' || request()->content_type == null){
        $content_search_query = 'All Content Types';
    } else {
        $content_search_query = App\Content::where('id', request()->content_type)->first()->type;
    }
    $consortia_search_query = '';
    if(request()->consortia){
        $consortia_search_query = App\Consortia::where('id', request()->consortia)->first()->short_name;
    }
    $year_search_query = '';
    if(request()->year_published){
        $year_search_query = request()->year_published;
    }
?>
@if($user != null && $user->role == 5)
<div class="edit-bar">
    <nav class="navbar navbar-expand-lg shadow rounded" style="background-color:{{request()->edit == 1 ? '#53ade9' : '#05b52c'}}; height:52px">
        <div class="col-auto text-white font-weight-bold">
            You are viewing in {{request()->edit == 1 ? 'EDIT' : 'LIVE'}} mode
        </div>
        @if(request()->edit == 1)
            <a href="{{route('search')}}" class="btn btn-success">View Live</a>
        @else
            <a href="{{route('search', ['edit' => '1'])}}" class="btn btn-light">Edit</a>
        @endif
    </nav>
</div> 
@endif
<div class="text-center {{request()->edit == '1' ? 'overlay-container' : ''}}" style="height:auto !important">
    <img src="/storage/page_images/{{$landing_page->top_banner}}" class="" style="height:550px;width:100%;margin-bottom:1rem; object-fit: cover;background-repeat: no-repeat">
    @if(request()->edit == 1)
        <div class="hover-overlay" style="width:100%">    
            <button type="button" class="btn btn-xs btn-primary" data-target="#editSearchBannerModal" data-toggle="modal"><i class="far fa-edit"></i></button>      
        </div>
    @endif
</div>
<a id="search-anchor" style="bottom:12rem;position:relative"></a>

<div class="container" style="margin-bottom:5rem; margin-top:2rem">
    <form action="/search#search-anchor" method="GET" role="search" class="mb-4 w-80">
        {{ csrf_field() }}
        <div class="input-group">
            <input type="text" class="form-control" style="font-size:1.25rem;height:4rem" name="search" placeholder="Input keywords or topics on {{request()->consortium ? $consortium_search : 'AANR'}}" value="{{ isset($results) ? $query : ''}}"> 
            <span class="input-group-append">
                <button type="submit" class="btn btn-outline-secondary" style="font-size:1.25rem;color:white;#ced4da;height:100%;background-color:rgb(33,109,158)">
                    <i class="fas fa-search" style="color:white;width:3rem"></i>
                    Search
                </button>
            </span>
        </div>
    </form>   
</div>

<?php
    function highlight($text, $words) {
        preg_match_all('~\w+~', $words, $m);
        if(!$m)
            return $text;
        $re = '~\\b(' . implode('|', $m[0]) . ')\\b~i';
        return preg_replace($re, '<b>$0</b>', $text);
    }
?>

<div class="container section-margin results">
    <div class="row">
        <div class="col-lg-9 col-sm-12">
            <h3 class="font-weight-bold">Total Results: <b>{{$results->total()}}</b></h3>
                @if($results->count() == 0)
                    <span class="text-muted">Sorry, there are no results found for <b>'{{$query}}'</b> in <b>{{$content_search_query}}</b>
                        @if($consortia_search_query)
                        | <b>{{$consortia_search_query}}</b>
                        @endif
                        @if($year_search_query)
                        | <b>{{$year_search_query}}</b>
                        @endif<br>
                @else
                    <p> 
                        Showing <b>{{($results->currentpage()-1)*$results->perpage()+1}} to {{(($results->currentpage()-1)*$results->perpage())+$results->count()}}</b> of <b>{{$results->total()}}</b> entries from <b>{{$content_search_query}}</b>
                            @if($consortia_search_query)
                            | <b>{{$consortia_search_query}}</b>
                            @endif
                            @if($year_search_query)
                            | <b>{{$year_search_query}}</b>
                            @endif<br>
                        Search results for<b> '{{ $query }}' </b> <i class="fas fa-info-circle" style="color:rgb(25,123,255)" title="Nearest search results based on searched keywords"></i>
                    
                    </p>
                    @foreach($results as $result)
                        <?php 
                            $titleHighlighted = highlight($result->title, $query);
                            $descriptionHighlighted = highlight($result->description, $query);
                        ?>
                        @if($result->is_agrisyunaryo == 0)
                        <a class="result-modal" data-toggle="modal" data-id="{{$result->id}}" data-target="#resultModal-{{$result->id}}" style="text-decoration: none;
                            color: black;
                            cursor: pointer;">
                            <div class="card front-card rounded">
                                <div class="card-horizontal">
                                    <div class="card-body">
                                        <div class="card-pills mb-1">
                                            <span class="badge badge-info text-white">{{$result->consortia->short_name}}</span>
                                            <span class="badge badge-success">{{$result->content->type}}</span>
                                            <span class="badge badge-secondary text-white">{{date('M d, Y', strtotime($result->date_published))}}</span>
                                        </div>
                                        <h4 class="card-title" style="">{!!$titleHighlighted!!}</h4>
                                        <p class="card-text" style="
                                            text-overflow:ellipsis;
                                            overflow:hidden;
                                            display: -webkit-box;
                                            -webkit-line-clamp: 3; /* number of lines to show */
                                            -webkit-box-orient: vertical;
                                            line-height:1.4;
                                            color: #382f2f !important;
                                            ">
                                            {!!$descriptionHighlighted!!}<br/>
                                        </p>
                                        <small class="text-muted">
                                            {{$result->author}} - {{$result->author_institution}}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </a>
                        @else
                        <div class="card shadow rounded">
                            <div class="card-body">
                                <h3 class="card-title">{!!$titleHighlighted!!}</h3>
                                <h5 class="card-subtitle">{!!$descriptionHighlighted!!}</h5>
                            </div>
                            <img class="card-img-top" src="/storage/page_images/{{$result->imglink}}" alt="{{$result->title}}" style=" height: auto; 
                                                                width: 100%; 
                                                                max-height: 450px;
                                                                object-fit: cover;
                                                                object-position: top">
                            <a href="{{$result->link}}" target="_blank" class="stretched-link"></a> 
                        </div>
                        @endif
                    @endforeach
                @endif
        <div class="pagination mt-5">
            {{$results->appends(request()->input())->links("pagination::bootstrap-4")}}
        </div>
        </div>
        <div class="col-lg-3 col-sm-12">
            <form action="/search#search-anchor" method="GET" role="search" class="">
            <h4>Advanced Search Options</h4>
            <div class="form-group">
                {{Form::label('search', 'Include keyword/s', ['class' => 'col-form-label required'])}}
                {{Form::text('search',  isset($results) ? $query : '', ['class' => 'form-control', 'placeholder' => 'Add keyword/s'])}}
            </div>
            <div class="form-group">
                {{Form::label('content_type', 'Content Type', ['class' => 'col-form-label'])}}
                {{Form::select('content_type', App\Content::pluck('type', 'id')->all(), isset($content_search_query) ? request()->content_type : '',['class' => 'form-control', 'placeholder' => '------------']) }}
            </div>
            <div class="form-group">
                {{Form::label('consortia', 'Consortia', ['class' => 'col-form-label'])}}
                {{Form::select('consortia', App\Consortia::pluck('short_name', 'id')->all(), isset($consortia_search_query) ? request()->consortia : '',['class' => 'form-control', 'placeholder' => '------------']) }}
            </div>
            <!--
            <div class="form-group">
                {{Form::label('6ps', '6 Ps', ['class' => 'col-form-label'])}}
                {{Form::select('6ps', ['Product' => 'Product', 
                                    'People' => 'People and Services', 
                                    'Policy' => 'Policy', 
                                    ], '',['class' => 'form-control', 'placeholder' => '------------']) }}
            </div>-->
            <div class="form-group">
                {{Form::label('year_published', 'Year Published', ['class' => 'col-form-label mb-3'])}}
                <div class="px-3">
                    <div class="slider-styled slider-round mt-4" id="slider"></div>
                </div>
                {{ Form::hidden('start', '', array('id' => 'form-year-start')) }}
                {{ Form::hidden('end', '', array('id' => 'form-year-end')) }}
            </div>
            <div class="form-group">
                {{Form::label('gad', 'GAD Focus', ['class' => 'col-form-label' ])}}
                {{Form::select('gad', ['Yes' => 'Yes', 
                                    'No' => 'No', 
                                    ], 'No',['class' => 'form-control']) }}
            </div>
            <button type="submit" class="btn btn-outline-secondary" style="font-size:1rem;color:white;#ced4da;background-color:rgb(23,162,184)">
                <i class="fas fa-search" style="color:white;width:1.5rem"></i>
                Advanced Search
            </button>
            </form>
            <?php $searchRegions = App\SearchQuery::where('query', '=', $query)->where('location', '!=', null)->select('location', DB::raw('count(*) as total'))->groupBy('location')->orderByDesc('total')->get()->take(5); ?>
            <h4 class="mt-5">Search Analytics</h4>
            <div class="mb-3">
                <b>Interest over time for <i>"{{$query}}"</i></b>
                <canvas id="interest_over_time"></canvas>
             </div>
            @if($searchRegions == null)
            <table class="table data-table tech-table table-hover" style="width:100%">
                <thead>
                    <b>Interest by region for <i>"{{$query}}"</i></b>
                    <tr>
                        <td></td>
                        <td>Region Name</td>
                        <td>Hits</td>
                    </tr>
                </thead>
                <tbody>
                    @foreach($searchRegions as $searchRegion)
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>{{$searchRegion->location}}</td>
                        <td>{{$searchRegion->total}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
            <div class="mb-3 mt-4">
                <b>Trending topics</b> 
                <canvas id="most_popular_topics"></canvas>
            </div>
            <div class="card">
                <div class="card-body">
                  <h5 class="card-title">Discover what people are searching for</h5>
                  <p class="card-text">Get instant, raw search insights, direct from the minds of your customers. Retype keyword to search additional insights.</p>
                  <a target="_blank" href="https://answerthepublic.com/" class="btn btn-primary">Click Here</a>
                </div>
              </div>
            <div class="w-100 mt-4" style="text-align:center">
               <a href="/analytics/search" class=""><button type="button" class="btn btn-info text-white">See more analytics</button></a>
            </div>
             
        </div> 
    </div>
</div>

<?php
    if($user != null){
        $compiled_featured_artifacts = collect();
        if($user->organization != null && $user->is_organization_other != 1){
            $user_consortia_id = App\Consortia::where('short_name', '=', $user->organization)->first()->id;
            $organization_artifacts = App\ArtifactAANR::where('consortia_id', '=', $user_consortia_id)->get();
            foreach($organization_artifacts as $organization_artifact){
                $compiled_featured_artifacts->push($organization_artifact);
            }
        }
        if(!empty($user->interest) && $user->interest != "null" && $user->interest != "NULL"){
            //get all relevant consortia from the user's interest
            $all_consortia_interest = App\Consortia::whereIn('short_name', json_decode($user->interest))->get();
            $consortia_interest_array = array();
            foreach($all_consortia_interest as $consortium_interest){
                array_push($consortia_interest_array, $consortium_interest->id);
            }
            $consortia_interest_artifacts = App\ArtifactAANR::whereIn('consortia_id',$consortia_interest_array)->get();
            foreach($consortia_interest_artifacts as $consortia_interest_artifact){
                $compiled_featured_artifacts->push($consortia_interest_artifact);
            }

            //get all relevant commodities from the user's interest
            $all_commodities_interest = App\Commodity::whereIn('name', json_decode($user->interest))->get();
            $commodity_interest_array = array();
            foreach($all_commodities_interest as $commodity_interest){
                array_push($commodity_interest_array, $commodity_interest->id);
            }
            $all_artifactaanr_commodity_query = DB::table('artifactaanr_commodity')->whereIn('commodity_id', $commodity_interest_array)->get();
            $all_artifactaanr_commodity_idarray = array();
            foreach($all_artifactaanr_commodity_query as $artifactaanr_commodity_query){
                array_push($all_artifactaanr_commodity_idarray, $artifactaanr_commodity_query->artifactaanr_id);
            }
            $commodity_interest_artifacts = App\ArtifactAANR::whereIn('id',$all_artifactaanr_commodity_idarray)->get();
            foreach($commodity_interest_artifacts as $commodity_interest_artifact){
                $compiled_featured_artifacts->push($commodity_interest_artifact);
            }

            //get all relevant ISP from the user's interest
            $all_isps_interest = App\ISP::whereIn('name', json_decode($user->interest))->get();
            $isp_interest_array = array();
            foreach($all_isps_interest as $isp_interest){
                array_push($isp_interest_array, $isp_interest->id);
            }
            $all_artifactaanr_isp_query = DB::table('artifactaanr_isp')->whereIn('isp_id', $isp_interest_array)->get();
            $all_artifactaanr_isp_idarray = array();
            foreach($all_artifactaanr_isp_query as $artifactaanr_isp_query){
                array_push($all_artifactaanr_isp_idarray, $artifactaanr_isp_query->artifactaanr_id);
            }
            $isp_interest_artifacts = App\ArtifactAANR::whereIn('id',$all_artifactaanr_isp_idarray)->get();
            foreach($isp_interest_artifacts as $isp_interest_artifact){
                $compiled_featured_artifacts->push($isp_interest_artifact);
            }
        }

        $compiled_featured_artifacts = $compiled_featured_artifacts->shuffle()->take(3)->unique();
        if($compiled_featured_artifacts == null){
            $compiled_featured_artifacts = App\ArtifactAANR::inRandomOrder()->limit(3)->get();
        }
    } else {
        $recommended_artifacts_not_logged_in = App\ArtifactAANR::inRandomOrder()->limit(3)->get();
    }
?>

<!-- RECOMMENDED SECTION -->
@if($landing_page->recommended_for_you_bg_type == 1)
<div class="recommended-section {{request()->edit != '1' && $landing_page->recommended_for_you_visibility == 0 ? 'section-none' : ''}} {{request()->edit == '1' && $user != null ? 'overlay-container' : ''}}" style="background: {{$landing_page->recommended_for_you_bg}};">
@else
<div class="recommended-section {{request()->edit != '1' && $landing_page->recommended_for_you_visibility == 0 ? 'section-none' : ''}} parallax-section {{request()->edit == '1' && $user != null ? 'overlay-container' : ''}}" style="background-image: url('/storage/page_images/{{$landing_page->recommended_for_you_bg}}');">
@endif
    <div class="container section-margin">
        <h2 class="mb-2 font-weight-bold" style="color:white">{{$landing_page->recommended_for_you_header}}</h2>
        <h5 class="mb-0" style="color:rgb(48, 152, 197)">{{$landing_page->recommended_for_you_subheader}}</h5>
        <div id="techCards" class="row">
            @if($user!=null)
                @foreach($compiled_featured_artifacts as $compiled_featured_artifact)
                    <div class="col-sm-4 tech-card-container">
                        <div class="card front-card h-auto shadow rounded">
                            @if($compiled_featured_artifact->imglink == null)
                            <div class="card-img-top center-vertically px-3 tech-card-color" style="height:200px">
                                <span class="font-weight-bold" style="font-size: 17px;line-height: 1.5em;color: #2b2b2b;">
                                    {{$compiled_featured_artifact->title}}
                                </span>
                            </div>
                            @else
                            <img src="{{$compiled_featured_artifact->imglink}}" class="card-img-top" height="175" style="object-fit: cover;">
                            @endif
                            <div class="card-body">
                                <h4 class="card-title trail-end">{{$compiled_featured_artifact->title}}</h4>
                                <div class="card-text trail-end" style="line-height: 120%;">
                                    <p class="mb-2"><b>{{$compiled_featured_artifact->author}}</b></p>
                                    <small>{{isset($compiled_featured_artifact->consortia->short_name) ? $compiled_featured_artifact->consortia->short_name : '--'}}<br>           
                                                {{$compiled_featured_artifact->content->type}} <br> </small>
                                </div>
                            </div>
                            <a href="{{$compiled_featured_artifact->link != null ? $compiled_featured_artifact->link : '/search?search='.$compiled_featured_artifact->title.'#search-anchor'}}" target="_blank" class="stretched-link"></a>
                        </div>
                    </div>
                @endforeach
            @else
                @foreach($recommended_artifacts_not_logged_in as $recommended_artifact_not_logged_in)
                    <div class="col-sm-4 tech-card-container">
                        <div class="card front-card h-auto shadow rounded">
                            @if($recommended_artifact_not_logged_in->imglink == null)
                            <div class="card-img-top center-vertically px-3 tech-card-color" style="height:200px">
                                <span class="font-weight-bold" style="font-size: 17px;line-height: 1.5em;color: #2b2b2b;">
                                    {{$recommended_artifact_not_logged_in->title}}
                                </span>
                            </div>
                            @else
                            <img src="{{$recommended_artifact_not_logged_in->imglink}}" class="card-img-top" height="200" style="object-fit: cover;">
                            @endif
                            <div class="card-body">
                                <h4 class="card-title trail-end">{{$recommended_artifact_not_logged_in->title}}</h4>
                                <div class="card-text trail-end" style="line-height: 120%;">
                                    <p class="mb-2"><b>{{$recommended_artifact_not_logged_in->author}}</b></p>
                                    <small>{{isset($recommended_artifact_not_logged_in->consortia->short_name) ? $recommended_artifact_not_logged_in->consortia->short_name : '--'}}<br>           
                                                {{isset($recommended_artifact_not_logged_in->content->type) ? $recommended_artifact_not_logged_in->content->type : '--'}} <br> </small>
                                </div>
                            </div>
                            <a href="{{$recommended_artifact_not_logged_in->link != null ? $recommended_artifact_not_logged_in->link : '/search?search='.$recommended_artifact_not_logged_in->title.'#search-anchor'}}" target="_blank" class="stretched-link"></a>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
        @if(request()->edit == 1)
            <div class="hover-overlay" style="width:100%">    
                <button type="button" class="btn btn-xs btn-primary" data-target="#editRecommendedForYouSectionModal" data-toggle="modal"><i class="far fa-edit"></i></button>      
            </div>
        @endif
    </div>
</div>

<div class="consortia-section container section-margin text-center" id="consortiaGroup">
    <h1 class="mb-2 font-weight-bold">Consortia Members</h1>
    <h5 class="mb-4" style="color:rgb(23, 135, 184)">Kilalanin ang mga miyembro ng consortium sa bawat rehiyon.</h5>
    @foreach(App\Consortia::all() as $consortium)
    <span data-toggle="collapse" data-target="#{{$consortium->short_name}}">
        <a data-toggle="tooltip" title="{{$consortium->short_name}}"><img src="/storage/page_images/{{$consortium->thumbnail}}" style="object-fit: cover;background-repeat: no-repeat;height:55px; width:55px"></a>
    </span>
    @endforeach
    @if($aanrPage->thumbnail != null)
    <span data-toggle="collapse" data-target="#{{$aanrPage->short_name}}">
        <a data-toggle="tooltip" title="{{$aanrPage->short_name}}"><img src="/storage/page_images/{{$aanrPage->thumbnail}}" style="object-fit: cover;background-repeat: no-repeat;height:55px; width:55px"></a>
    </span>
    @endif
        
    <div class="accordion-group">

    @foreach(App\Consortia::all() as $consortium)
        <div class="collapse" id="{{$consortium->short_name}}" data-parent="#consortiaGroup">
            <div class="container">
                <div class="card card-body">
                    <h3>{{$consortium->short_name}}</h3>
                    <span style="text-align: left">
                        {!!$consortium->profile!!}
                    </span>
                    <div class="btn-group">
                        <a href="{{route('consortiaAboutPage', ['consortia' => $consortium->short_name])}}" class="btn btn-primary mt-3" role="button" aria-disabled="true">More info about this consortia</a>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
    @if($aanrPage->thumbnail != null)
        <div class="collapse" id="{{$aanrPage->short_name}}" data-parent="#consortiaGroup">
            <div class="container">
                <div class="card card-body">
                    <h3>{{$aanrPage->short_name}}</h3>
                    <span style="text-align: left">
                        {!!$aanrPage->profile!!}
                    </span>
                    <div class="btn-group">
                        <a href="{{route('AANRAboutPage')}}" class="btn btn-primary mt-3" role="button" aria-disabled="true">Link to page</a>
                        <a target="_blank" href="{{$aanrPage->link}}" class="btn btn-secondary mt-3" role="button" aria-disabled="true">Link to website</a>
                    </div>
                </div>
            </div>
        </div>
    @endif
    </div>
</div>
<style>
    #fullscreeniframe {
        opacity: 0.3;
        position:relative;
        float: right;
        right:25px;
        bottom:60px;
        transition: 0.5s;
    }
    #fullscreeniframe {
        opacity: 1;
    }  
</style>
@foreach($results as $result)
    <?php 
    $titleHighlighted = highlight($result->title, $query);
    $descriptionHighlighted = highlight($result->description, $query);
    ?>
    @if($result->is_agrisyunaryo == 0)
    <div class="modal fade" id="resultModal-{{$result->id}}" aria-hidden="true">
        <div class="modal-dialog {{$result->embed_link ? 'modal-xl' : 'modal-lg'}}">
            <div class="modal-content pl-0 pr-0 pl-0">
                <div class="inner-modal pl-3 pr-3"> 
                    <div class="modal-header" style="padding-bottom:8px">
                        <span style="width:100%" class="mt-2">
                            <h4>{!!$titleHighlighted!!} </h4>
                        <span>
                        <small class="text-muted">
                            <i class="far fa-sticky-note"></i> {{$result->content->type}} | 
                            <i class="fas fa-calendar"></i> {{date('d-m-Y', strtotime($result->date_published))}}
                        </small>
                    </div>
                    <div class="modal-body">
                        @if($result->description)
                        <b>Description</b><br>
                        <span>{!!$descriptionHighlighted!!}</span>
                        @endif

                        @if($result->imglink != null)
                        <div class="dropdown-divider mt-3"></div>
                        <b>Image</b><br>
                        <span style=''>
                            <img src="{{$result->imglink}}" style="object-fit: contain; width:100%; height:300px">
                        </span>
                        @endif
                            

                        @if($result->consortia)
                        <div class="dropdown-divider mt-3"></div>
                        <b>Consortia Resource</b><br>
                        <span>{{$result->consortia->short_name}}</span>
                        @endif
                        
                        @if($result->author)
                        <div class="dropdown-divider mt-3"></div>
                        <b>Author</b><br>
                        <span>{{$result->author}}</span>
                        @endif

                        @if($result->author_institution)
                        <div class="dropdown-divider mt-3"></div>
                        <b>Author Institution</b><br>
                        <span>{{$result->author_institution}}</span>
                        @endif

                        @if($result->embed_link)
                        <div class="dropdown-divider mt-3"></div>
                        <b>Content Website</b><br>
                        <iframe allowfullscreen src="{{$result->embed_link}}" width="100%" height="500"></iframe>
                        <button id="fullscreeniframe" title="view in full screen" class="button btn btn-light"><i class="fas fa-expand"></i> View in fullscreen</button>
                        @endif
                        
                        
                        @if($result->file)
                        <div class="dropdown-divider mt-3"></div>
                        <b>PDF Preview</b><br>
                        <iframe 
                            class="mt-2"
                            src="{{$result->file_type == 'pdf_link' ? $result->file : asset('/storage/files/' . $result->file)}}" 
                            style="width:100%; height:500px;" 
                            frameborder="0">
                        </iframe>
                        @endif

                        <div class="dropdown-divider mt-3"></div>
                        <b>Search Keywords</b><br>
                        <span>{{$result->keywords}}</span>
                    </div>
                    <div class="modal-footer">
                        @if($result->link != null)
                        @php
                            $http_pattern = "/^http[s]*:\/\/[\w]+/i";
                            $url = $result->link;
                            if (!preg_match($http_pattern, $url, $match)){  
                                $url = 'http://' .$url;
                            }
                        @endphp
                        <a target="_blank" href="{{$url}}"><button type="button" class="btn btn-primary">Go to link</button></a>
                        @endif
                        <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
@endforeach

<!--
<div class="px-5 mt-5">
    <img src="/storage/page_images/KM4AANR Footer_sample.png" class="card-img-top" style="object-fit: cover;">
</div>
-->
<div class="modal fade" id="editSearchBannerModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {{ Form::open(['action' => ['LandingPageElementsController@updateTopBanner'], 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
            <div class="modal-header">
                <h6 class="modal-title" id="exampleModalLabel">Edit Search Banner</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                        {{Form::label('image', 'Search Banner', ['class' => 'col-form-label required'])}}
                        <br>
                        @if($landing_page->top_banner!=null)
                        <img src="/storage/page_images/{{$landing_page->top_banner}}" class="card-img-top" style="object-fit: cover;overflow:hidden;width:250px;border:1px solid rgba(100,100,100,0.25)" >
                        @else
                        <div class="card-img-top center-vertically px-3" style="height:250px; width:1110px; background-color: rgb(227, 227, 227);">
                            <span class="font-weight-bold" style="font-size: 17px;line-height: 1.5em;color: #2b2b2b;">
                                Upload a 1110x315px logo for the #editSearchBannerModal banner.
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                {{Form::submit('Save Changes', ['class' => 'btn btn-success'])}}
            </div>
            {{Form::close()}}
        </div>
    </div>
</div>

<style>
    .section-margin{
        margin-top:5rem;
        margin-bottom:5rem;
    }

    .parallax-section{
         /* The image used */
        background-image: url(/storage/page_images/new-commodities.jpg);

        /* Create the parallax scrolling effect */
        background-attachment: fixed;
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
        box-shadow: inset 0 0 0 1000px rgba(0,0,0,.75);
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
    }


    .overlay-container:hover .bottom-overlay{
        opacity: 0.5;
    }

    .overlay-container:hover{
        background-color:rgba(0,0,0,.15);
        transition: .5s ease;
    }

    }
    .last-section{
        background: rgb(33,109,158);
        color:white;
        padding-top:7rem;
        padding-bottom:7rem;
    }

    .recommended-section{
        background: rgb(40,40,45);
        padding-top:5rem;
        padding-bottom:5rem;
    }

    .consortia-section{
        padding-top:5rem;
        padding-bottom:5rem;
    }
    
</style>
@endsection
@section('scripts')

<?php
    //$search_queries = App\SearchQuery::select('query', DB::raw('count(*) as total'))->groupBy('query')->orderBy('total', 'DESC');
    $fromDate_1 = Carbon::now()->subMonths(2)->startOfMonth()->toDateString();
    $tillDate_1 = Carbon::now()->subMonths(2)->endOfMonth()->toDateString();
    $fromDate_2 = Carbon::now()->subMonth()->startOfMonth()->toDateString();
    $tillDate_2 = Carbon::now()->subMonth()->endOfMonth()->toDateString();
    $search_query_date_1 = App\SearchQuery::where('query', '=', $query)->whereBetween(DB::raw('date(created_at)'), [$fromDate_1, $tillDate_1])->whereYear('created_at', Carbon::now()->year)->count();
    $search_query_date_2 = App\SearchQuery::where('query', '=', $query)->whereBetween(DB::raw('date(created_at)'), [$fromDate_2, $tillDate_2])->whereYear('created_at', Carbon::now()->year)->count();
    $search_query_date_3 = App\SearchQuery::where('query', '=', $query)->where('created_at', '>=', Carbon::now()->startOfMonth())->whereYear('created_at', Carbon::now()->year)->count();
    
    $search_query_freq_array = array();
    $search_query_freq_array[0] = array();
    $search_query_freq_array[1] = array();
    foreach(App\SearchQuery::select('query', DB::raw('count(*) as total'))->groupBy('query')->orderByDesc('total')->get()->take(5) as $item){
        array_push($search_query_freq_array[0], $item->query);
        array_push($search_query_freq_array[1], $item->total);
    }
    //$search_query_freq_compiled = "'" . $search_query_freq_1->query . "','" . $search_query_freq_2->query . "','" . $search_query_freq_3->query . "','" . $search_query_freq_4->query . "','" . $search_query_freq_5->query . "'";
?>
<script>
        var slider = document.getElementById('slider');
        function mergeTooltips(slider, threshold, separator) {
            var textIsRtl = getComputedStyle(slider).direction === 'rtl';
            var isRtl = slider.noUiSlider.options.direction === 'rtl';
            var isVertical = slider.noUiSlider.options.orientation === 'vertical';
            var tooltips = slider.noUiSlider.getTooltips();
            var origins = slider.noUiSlider.getOrigins();

            // Move tooltips into the origin element. The default stylesheet handles this.
            tooltips.forEach(function (tooltip, index) {
                if (tooltip) {
                    origins[index].appendChild(tooltip);
                }
            });

            slider.noUiSlider.on('update', function (values, handle, unencoded, tap, positions) {
                document.getElementById('form-year-start').setAttribute('value', values[0]);
                document.getElementById('form-year-end').setAttribute('value', values[1]);

                var pools = [[]];
                var poolPositions = [[]];
                var poolValues = [[]];
                var atPool = 0;

                // Assign the first tooltip to the first pool, if the tooltip is configured
                if (tooltips[0]) {
                    pools[0][0] = 0;
                    poolPositions[0][0] = positions[0];
                    poolValues[0][0] = values[0];
                }

                for (var i = 1; i < positions.length; i++) {
                    if (!tooltips[i] || (positions[i] - positions[i - 1]) > threshold) {
                        atPool++;
                        pools[atPool] = [];
                        poolValues[atPool] = [];
                        poolPositions[atPool] = [];
                    }

                    if (tooltips[i]) {
                        pools[atPool].push(i);
                        poolValues[atPool].push(values[i]);
                        poolPositions[atPool].push(positions[i]);
                    }
                }

                pools.forEach(function (pool, poolIndex) {
                    var handlesInPool = pool.length;

                    for (var j = 0; j < handlesInPool; j++) {
                        var handleNumber = pool[j];

                        if (j === handlesInPool - 1) {
                            var offset = 0;

                            poolPositions[poolIndex].forEach(function (value) {
                                offset += 1000 - value;
                            });

                            var direction = isVertical ? 'bottom' : 'right';
                            var last = isRtl ? 0 : handlesInPool - 1;
                            var lastOffset = 1000 - poolPositions[poolIndex][last];
                            offset = (textIsRtl && !isVertical ? 100 : 0) + (offset / handlesInPool) - lastOffset;

                            // Center this tooltip over the affected handles
                            tooltips[handleNumber].innerHTML = poolValues[poolIndex].join(separator);
                            tooltips[handleNumber].style.display = 'block';
                            tooltips[handleNumber].style[direction] = offset + '%';
                        } else {
                            // Hide this tooltip
                            tooltips[handleNumber].style.display = 'none';
                        }
                    }
                });
            });
        }
        noUiSlider.create(slider, {
            range: {
                'min': [1970],
                'max': [2021]
            },
            start: ['1970', '2021'],
            format: {
                from: function(value) {
                    return parseInt(value);
                },
                to: function(value) {
                    return parseInt(value);
                }
            },
            connect: true,
            tooltips: [true, true],
        });
        var url = new URL(window.location.href);
        var start = url.searchParams.get("start");
        var end = url.searchParams.get("end");
        slider.noUiSlider.set([start, end]);
        mergeTooltips(slider, 15, ' - ');
    $(document).on("click", ".result-modal", function (){
        var content_id = $(this).data('id');
        var _token = $('input[name="_token"]').val();
        $.ajax({
            url:"{{ route('createArtifactViewLog') }}",
            method:"POST",
            data:{content_id:content_id, _token:_token},
            success: function (data) {
                //console.log('success:', content_id);
            },
            error: function(xhr, status, error,data) {
                //console.log('error:', content_id);
                //alert(xhr.responseText);
            }
        })
        $.ajax({
            url:"{{ route('createISPViewLog') }}",
            method:"POST",
            data:{content_id:content_id, _token:_token},
            success: function (data) {
                //console.log('success:', content_id);
            },
            error: function(xhr, status, error,data) {
                //console.log('error:', content_id);
                //alert(xhr.responseText);
            }
        })
        $.ajax({
            url:"{{ route('createCommodityViewLog') }}",
            method:"POST",
            data:{content_id:content_id, _token:_token},
            success: function (data) {
                //console.log('success:', content_id);
            },
            error: function(xhr, status, error,data) {
                //console.log('error:', content_id);
                //alert(xhr.responseText);
            }
        })
    });

    let interest_over_time = new Chart(document.getElementById('interest_over_time').getContext('2d'), {
        type:'line',
        data:{
            labels: [@php echo "'" . Carbon::now()->subMonths(2)->format('F') . "','" . Carbon::now()->subMonths()->format('F') . "','" . Carbon::now()->format('F') . "'";@endphp],
            datasets:[{
                label: 'No. of times searched',
                data: [@php echo $search_query_date_1 . ',' . $search_query_date_2 . ',' . $search_query_date_3;@endphp],
                backgroundColor:[
                    'rgba(20,99,20,1)'
                ],
                hoverBorderWidth:3,
                hoverBorderColor:'rgb(0,0,0)'
            }]
        },
        options:{
            legend: {
                display: false
            },
            responsive:true,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero:true
                    }
                }]
            }

        }
    });
    let most_popular_topics = new Chart(document.getElementById('most_popular_topics').getContext('2d'), {
        type:'doughnut',
        data:{
            labels: @php echo json_encode($search_query_freq_array[0]);@endphp,
            datasets:[{
                data: @php echo json_encode($search_query_freq_array[1]);@endphp,
                backgroundColor: [
                    'rgba(89, 233, 112, 1)',
                    'rgba(123, 155, 76, 1)',
                    'rgba(154, 125, 98, 1)',
                    'rgba(95, 104, 222, 1)',
                    'rgba(150, 216, 2, 1)',
                ],
                hoverBorderWidth:3,
                hoverBorderColor:'rgb(0,0,0)'
            }]
        },
        options:{
            legend: {
                display: false
            },
            plugins: {
                legend: {
                    display: true,
                    align: 'start'
                },
            },
            responsive:true,
        }
    });
    (function(window, document){
        var $ = function(selector,context){return(context||document).querySelector(selector)};

        var iframe = $("iframe"),
            domPrefixes = 'Webkit Moz O ms Khtml'.split(' ');

        var fullscreen = function(elem) {
            var prefix;
            // Mozilla and webkit intialise fullscreen slightly differently
            for ( var i = -1, len = domPrefixes.length; ++i < len; ) {
              prefix = domPrefixes[i].toLowerCase();

              if ( elem[prefix + 'EnterFullScreen'] ) {
                // Webkit uses EnterFullScreen for video
                return prefix + 'EnterFullScreen';
                break;
              } else if( elem[prefix + 'RequestFullScreen'] ) {
                // Mozilla uses RequestFullScreen for all elements and webkit uses it for non video elements
                return prefix + 'RequestFullScreen';
                break;
              }
            }

            return false;
        };              
        // Webkit uses "requestFullScreen" for non video elements
        var fullscreenother = fullscreen(document.createElement("iframe"));

        if(!fullscreen) {
            alert("Fullscreen won't work, please make sure you're using a browser that supports it and you have enabled the feature");
            return;
        }

        $("#fullscreeniframe").addEventListener("click", function(){
            // iframe fullscreen and non video elements in webkit use request over enter
            iframe[fullscreenother]();
        }, false);
    })(this, this.document);  
</script>
@endsection