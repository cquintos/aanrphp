@extends('layouts.app')
@section('title', 'Home')
@section('breadcrumb')
<?php
    use Illuminate\Support\Facades\Auth;
        $headlines = App\Headline::all();
        $count = 0;
        $user = Auth::user();
    ?>
    <div id="carouselContent" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner" role="listbox">
            @foreach($headlines as $headline)
                <div class="wrap-ellipsis carousel-item {{$count == 0 ? 'active' : ''}} text-center p-4" style="color:white">
                    <a href="{{$headline->link}}" target="_blank" style="text-decoration: none; color:white; font-size:15px">{{$headline->title}}</a>
                </div>
                <?php $count++ ?>
            @endforeach
        </div>
    </div>
@endsection
@section('content')

@include('layouts.messages')
@include('pages.modals.landingPage')
<?php 
    $landing_page = App\LandingPageElement::findOrFail(1);
    $aanrPage = App\AANRPage::first();
?>

@if($user != null && $user->role == 5)
    <div class="edit-bar">
        <nav class="navbar navbar-expand-lg shadow rounded" style="background-color:{{request()->edit == 1 ? '#53ade9' : '#05b52c'}}; height:52px">
            <div class="col-auto text-white font-weight-bold">
                You are viewing in {{request()->edit == 1 ? 'EDIT' : 'LIVE'}} mode
            </div>
            @if(request()->edit == 1)
                <a href="{{route('getLandingPage')}}" class="btn btn-success">View Live</a>
            @else
                <a href="{{route('getLandingPage', ['edit' => '1'])}}" class="btn btn-light">Edit</a>
            @endif
        </nav>
    </div> 
@endif
@if(auth()->user() != null)
    @if(!auth()->user()->hasVerifiedEmail())
        <div class="verification-header  px-5">
            <span class="text-black">Please verify your email within 24 hours. If you did not receive the email,</span>
            <form action="{{ route('verification.resend') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="d-inline btn btn-link p-0">
                    <span class="text-black" style="font-weight: 600; font-size:1rem">click here to request another</span>
                </button>.
            </form>
        </div>
    @endif
@endif
@if (session('resent'))
    <div class="alert alert-success px-5" role="alert">
        A new verification link has been sent to your email address.
    </div>
@endif
<!-- CAROUSEL SLIDER SECTION -->
<div class="container{{$landing_page->slider_container_toggle == 1 ? '-fluid' : ''}} pb-1 px-0" style="z-index:0">
    <div id="featuredBanner" style="" class="carousel slide" data-ride="carousel">
            <?php 
                $sliders = App\LandingPageSlider::all()->sortBy('weight');
                $count = 0;
            ?>
            <ol class="carousel-indicators">
                @foreach($sliders as $slider)
                    <li data-target="#featuredBanner" data-slide-to={{$loop->index}} class={{$loop->first ? "active" : ""}}></li>
                @endforeach
            </ol>
            <style>
                @media only screen and (max-width: 600px) {
                    .mobile-50 {
                        height:40vh !important
                    }
                    .section-margin {
                        margin-top:2.5rem !important;
                    }
                }
            </style>
            <div class="carousel-inner text-center mobile-50" style="height:550px">
                @foreach($sliders as $slider)
                    @if($slider->is_video == 0)
                        <div class="carousel-item mobile-50 {{$count == 0 ? 'active' : ''}}" style="height:550px;">
                            <img src="/storage/cover_images/{{$slider->image}}" class="d-block w-100 fill-background" alt="Carousel Image">
                            <img src="/storage/cover_images/{{$slider->image}}" class="d-block w-100 mobile-50" alt="Carousel Image" width="100%" style="object-fit: contain; height:550px; z-index:10;">
                            @if($slider->textcard_enable == 'yes')
                            <div class="carousel-caption d-none d-md-block px-4 carousel-caption-align-{{$slider->caption_align}}" style="bottom:6%">
                                <h1 style="font-weight:600">{{$slider->title}}</h1>
                                <h4>{{$slider->description}}</h4>
                                <a href="{{$slider->link}}"><button type="button" class="btn btn-primary">{{$slider->button_text}}</button></a>
                            </div>
                            @endif
                        </div>
                    @else
                        <div class="carousel-item mobile-50 {{$count == 0 ? 'active' : ''}}" style="height:550px;">
                            <iframe src="{{$slider->video_link}}" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen height="100%" width="100%">
                            </iframe>
                        </div>
                    @endif
                    <?php $count++ ?>
                @endforeach
            </div>
            <style>
                .carousel-control-prev, .carousel-control-next{
                    z-index:50 !important;
                }
                .fill-background{
                    position: absolute;
                    filter: blur(2em);
                    height:100%;
                    z-index:-1;
                    box-shadow: inset 0 0 30px 15px #212121;
                    transform: scale(1.2);
                    object-fit:fill;
                }
                .carousel-caption-align-left{
                    left:5% !important;
                    text-align:left !important;
                }
                .carousel-caption-align-center{
                    left:25% !important;
                    text-align:left !important;
                }
                .carousel-caption-align-right{
                    left: 45% !important;
                    text-align:right !important;
                }
            </style>
            <a class="carousel-control-prev" href="#featuredBanner" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#featuredBanner" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
    </div>
</div>

<!-- SEARCH BAR SECTION -->
<div class="container section-margin">
   <!-- <div class="text-center">
        <img src="/storage/page_images/{{$landing_page->top_banner}}" class="w-80" style="width:80%; margin-bottom:1rem; object-fit: contain;background-repeat: no-repeat">
    </div>
    -->
    <form action="/search" method="GET" role="search" class="mb-4 w-80">
        {{ csrf_field() }}
        <div class="input-group">
            <label class="mr-2 radio-inline"><input type="radio" name="content_type" value="all" checked> All Content Types</label>
            <span class="ml-2 mr-3" style="color:rgba(124, 124, 124, 0.788)">|</span>
            @foreach(App\Content::orderBy('type', 'asc')->get() as $content_type)
                <label class="mr-3 radio-inline"><input type="radio" name="content_type" value="{{$content_type->id}}"> {{$content_type->type}}</label>
            @endforeach
        </div>
        <div class="input-group">
            <input type="text" class="form-control" style="font-size:1.25rem;height:4rem" name="search" placeholder="Input keywords or topics on AANR" value={{ isset($results) ? $query : ''}}> 
            <span class="input-group-append">
                <button type="submit" class="btn btn-outline-secondary" style="font-size:1.25rem;color:white;#ced4da;height:100%;background-color:rgb(33,109,158)">
                    <i class="fas fa-search" style="color:white;width:3rem"></i>
                    Search
                </button>
            </span>
        </div>
        <div class="input-group aanr-input mt-3">
            <label class="checkbox-inline mr-2"><input type="checkbox" value="1" name="aanr-agri" checked> Agriculture</label>
            <label class="checkbox-inline mx-2"><input type="checkbox" value="1" name="aanr-aqua" checked> Aquatic</label>
            <label class="checkbox-inline mx-2"><input type="checkbox" value="1" name="aanr-natu" checked> Natural Resources</label> 
            <span class="mx-2" style="color:rgba(124, 124, 124, 0.788)">|</span>
            <span class="ml-4">
                <label class="form-check-label" for="flexCheckChecked" ><input type="checkbox" class="form-check-input" id="flexCheckChecked" name="is_gad" value="1">Gender and Development Focus</label>
            </span>
            <div class="media-input">
                <span class="mx-2" style="color:rgba(124, 124, 124, 0.788)">|</span>
                <label class="checkbox-inline mr-2"><input type="checkbox" value="1" name="media-pdf" checked> PDF</label>
                <label class="checkbox-inline mx-2"><input type="checkbox" value="1" name="media-videos" checked> Videos</label> 
                <label class="checkbox-inline mx-2"><input type="checkbox" value="1" name="media-images" checked> Podcasts</label>
                <label class="checkbox-inline mx-2"><input type="checkbox" value="1" name="media-videos" checked> Graphics</label> 
            </div>
        </div>
        
    </form>   
</div>

<!-- INDUSTRY PROFILE SECTION -->
<div class="container section-margin {{request()->edit != '1' && $landing_page->industry_profile_visibility == 0 ? 'section-none' : ''}} {{request()->edit == '1' && $user != null ? 'overlay-container' : ''}}">
    <h2 class="mb-2 font-weight-bold">{{$landing_page->industry_profile_header}}</h2>
    <h5 class="mb-0" style="color:rgb(23, 135, 184)">{{$landing_page->industry_profile_subheader}}</h5>
    <div class="row">
        <div class="col-sm-4">
            <div class="card h-auto text-white">
                <img src="/storage/page_images/{{$landing_page->industry_profile_agri_bg}}" class="card-img-top" height="250" style="object-fit: cover;">
                <div class="card-image-overlay" style="background-color:rgba(0,0,0,0.5)">
                    <img src="/storage/page_images/{{$landing_page->industry_profile_agri_icon}}" class="card-img-top" height="160" style="object-fit:contain">
                    <h4 class="card-title text-center">Agriculture</h4>
                </div>
                <a href="/aanr-industry-profile?industry=1" class="stretched-link"></a>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card h-auto text-white">
                <img src="/storage/page_images/{{$landing_page->industry_profile_aqua_bg}}" class="card-img-top" height="250" style="object-fit: cover;">
                <div class="card-image-overlay" style="background-color:rgba(0,0,0,0.5)">
                    <img src="/storage/page_images/{{$landing_page->industry_profile_aqua_icon}}" class="card-img-top" height="160" style="object-fit:contain">
                    <h4 class="card-title text-center">Aquatic Resources</h4>
                </div>
                <a href="/aanr-industry-profile?industry=2" class="stretched-link"></a>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card h-auto text-white">
                <img src="/storage/page_images/{{$landing_page->industry_profile_natural_bg}}" class="card-img-top" height="250" style="object-fit: cover;">
                <div class="card-image-overlay" style="background-color:rgba(0,0,0,0.5)">
                    <img src="/storage/page_images/{{$landing_page->industry_profile_natural_icon}}" class="card-img-top" height="160" style="object-fit:contain">
                    <h4 class="card-title text-center">Environmental and Natural Resources</h4>
                </div>
                <a href="/aanr-industry-profile?industry=3" class="stretched-link"></a>
            </div>
        </div>
    </div>
    @if(request()->edit == 1)
        <div class="hover-overlay" style="width:100%">    
            <button type="button" class="btn btn-xs btn-primary" data-target="#editIndustryProfileSectionModal" data-toggle="modal"><i class="far fa-edit"></i></button>      
        </div>
    @endif
</div>

<!-- LATEST AANR SECTION -->
@if(App\ArtifactAANR::where('imglink', '!=', null)->count() != 0)
@if($landing_page->latest_aanr_bg_type == 1)
<div class="parallax-section {{request()->edit != '1' && $landing_page->latest_aanr_visibility == 0 ? 'section-none' : ''}} pb-2 pt-1 {{request()->edit == '1' && $user != null ? 'overlay-container' : ''}}" style="background: {{$landing_page->latest_aanr_bg}});">
@else
<div class="parallax-section {{request()->edit != '1' && $landing_page->latest_aanr_visibility == 0 ? 'section-none' : ''}} pb-2 pt-1 {{request()->edit == '1' && $user != null ? 'overlay-container' : ''}}" style="background-image: url('/storage/page_images/{{$landing_page->latest_aanr_bg}}');">
@endif
    <div class="container section-margin">
        <h2 class="mb-2 font-weight-bold" style="color:rgb(220,220,220)">{{$landing_page->latest_aanr_header}}</h2>
        <h5 class="mb-0" style="color:rgb(48, 152, 197)">{{$landing_page->latest_aanr_subheader}}</h5>
        <div class="row">
            @forelse(App\ArtifactAANR::where('is_agrisyunaryo', '!=', 1)->orderBy('date_published')->take(3)->get() as $artifact)
            <div class="col-sm-4">
                <div class="card front-card h-auto shadow rounded">
                    @if($artifact->imglink != null)
                        <img src="{{$artifact->imglink}}" class="card-img-top" height="200" style="object-fit: cover;">
                    @else
                        <div class="card-img-top center-vertically px-3" style="height:200; background-color:rgb(150,150,150)">
                                <span class="font-weight-bold" style="font-size: 17px;line-height: 1.5em;color: white;">
                                    {{$artifact->title}}
                                </span>
                            </div>
                    @endif
                    <div class="card-body">
                        <h4 class="card-title trail-end">{{$artifact->title}}</h4>
                        <div class="card-text trail-end" style="line-height: 120%;">
                            <p class="mb-2"><b>{{$artifact->author}}</b></p>
                            <small>{{isset($artifact->consortia->short_name) ? $artifact->consortia->short_name : ''}}<br>           
                                        {{isset($artifact->content) ? $artifact->content->type : ''}} <br> 
                                    </small>
                        </div>
                    </div>
                    <a href="{{$artifact->link != null ? $artifact->link : '/search?search='.$artifact->title.'#search-anchor'}}" target="_blank" class="stretched-link"></a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @if(request()->edit == 1)
        <div class="hover-overlay" style="width:100%">    
            <button type="button" class="btn btn-xs btn-primary" data-target="#editLatestAANRSectionModal" data-toggle="modal"><i class="far fa-edit"></i></button>      
        </div>
    @endif
</div>
@endif

<!-- USER TYPE RECOMMENDATION SECTION -->
<div class="container section-margin {{request()->edit != '1' && $landing_page->user_type_recommendation_visibility == 0 ? 'section-none' : ''}} {{request()->edit == '1' && $user != null ? 'overlay-container' : ''}}">
    <h2 class="mb-2 font-weight-bold">{{$landing_page->user_type_recommendation_header}}</h2>
    <h5 class="mb-0" style="color:rgb(23, 135, 184)">{{$landing_page->user_type_recommendation_subheader}}</h5>
    <div class="row">
        <div class="col-sm-4">
            <div class="card">
                <img class="card-img-top" src="/storage/page_images/govpartner.jpg" alt="Card image cap">
                <div class="card-body">
                    <h5 class="card-title">A government partner</h5>
                    <p class="card-text">Get acquainted with the R & D work of the regions on AANR. Get to know the...</p>
                    <a href="#" class="btn btn-primary">Learn More</a>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card">
                <img class="card-img-top" src="/storage/page_images/student.jpg" alt="Card image cap">
                <div class="card-body">
                    <h5 class="card-title">A student</h5>
                    <p class="card-text">If you are a student looking for resources on AANR for your next class project or research...                    </p>
                    <a href="#" class="btn btn-primary">Learn More</a>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="card">
                <img class="card-img-top" src="/storage/page_images/researcher.jpg" alt="Card image cap">
                <div class="card-body">
                    <h5 class="card-title">A researcher</h5>
                    <p class="card-text">Looking for literature for your next study? You might be interested in these R & D...</p>
                    <a href="#" class="btn btn-primary">Learn More</a>
                </div>
            </div>
        </div>
    </div>
    @if(request()->edit == 1)
        <div class="hover-overlay" style="width:100%">    
            <button type="button" class="btn btn-xs btn-primary" data-target="#editUserTypeRecommendationSectionModal" data-toggle="modal"><i class="far fa-edit"></i></button>      
        </div>
    @endif
</div>

<!-- FEATURED PUBLICATION SECTION -->
<div class="container section-margin {{request()->edit != '1' && $landing_page->featured_publications_visibility == 0 ? 'section-none' : ''}} {{request()->edit == '1' && $user != null ? 'overlay-container' : ''}}">
    <?php
        if ($landing_page->featured_artifact_id_1 != null) {
            $featured_publication_1 = App\ArtifactAANR::find($landing_page->featured_artifact_id_1);    
        } else {
            
        }
        if ($landing_page->featured_artifact_id_2 != null) {
            $featured_publication_2 = App\ArtifactAANR::find($landing_page->featured_artifact_id_2);    
        }
    ?>
    <h2 class="mb-2 font-weight-bold">{{$landing_page->featured_publications_header}}</h2>
    <h5 class="mb-0" style="color:rgb(23, 135, 184)">{{$landing_page->featured_publications_subheader}}</h5>
    <div class="row">
        @isset($featured_publication_1)
        <div class="col-sm-6">
            <div class="card">
                <div class="row no-gutters">
                    <div class="col-sm-4">
                        @if($featured_publication_1->imglink != null)
                            <img class="card-img-top" style="width:100%" src="{{$featured_publication_1->imglink}}" alt="Card image cap">
                        @else
                        <div class="card-img-top center-vertically px-3" style="height:100%; background-color:rgb(150,150,150)">
                                <span class="font-weight-bold" style="font-size: 17px;line-height: 1.5em;color: white;">
                                    {{$featured_publication_1->title}}
                                </span>
                            </div>
                        @endif
                    </div>
                    <div class="col-sm-8">
                        <div class="card-body">
                            <h5 class="card-title">{{$featured_publication_1->title}}</h5>
                            <span class="card-text featured-publication-text">{!!$featured_publication_1->description!!}</span><br>
                            <small>{{$featured_publication_1->author}} <br> {{$featured_publication_1->date_published}}</small>
                            <br>
                            <a href="#" class="btn btn-primary">Learn More</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endisset
        @isset($featured_publication_2)
        <div class="col-sm-6">
            <div class="card">
                <div class="row no-gutters">
                    <div class="col-sm-4">
                        @if($featured_publication_2->imglink != null)
                            <img class="card-img-top" src="{{$featured_publication_2->imglink}}" alt="Card image cap">
                        @else
                            <div class="card-img-top center-vertically px-3" style="height:100%; background-color:rgb(150,150,150)">
                                <span class="font-weight-bold" style="font-size: 17px;line-height: 1.5em;color: white;">
                                    {{$featured_publication_2->title}}
                                </span>
                            </div>
                        @endif
                    </div>
                    <div class="col-sm-8">
                        <div class="card-body">
                            <h5 class="card-title">{{$featured_publication_2->title}}</h5>
                            <span class="card-text featured-publication-text">{!!$featured_publication_2->description!!}</span><br>
                            <small>{{$featured_publication_2->author}} <br> {{$featured_publication_1->date_published}}</small>
                            <br>
                            <a href="#" class="btn btn-primary mt-2">Learn More</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endisset
    </div>
    @if(request()->edit == 1)
        <div class="hover-overlay" style="width:100%">    
            <button type="button" class="btn btn-xs btn-primary" data-target="#editFeaturedPublicationsSectionModal" data-toggle="modal"><i class="far fa-edit"></i></button>      
        </div>
    @endif
</div>

<?php 
    $url_1 = $landing_page->featured_video_link_1;
    $url_2 = $landing_page->featured_video_link_2;
    $url_3 = $landing_page->featured_video_link_3;
    $shortUrlRegex = '/youtu.be\/([a-zA-Z0-9_]+)\??/i';
    $longUrlRegex = '/youtube.com\/((?:embed)|(?:watch))((?:\?v\=)|(?:\/))(\w+)/i';
    $youtube_id = '';
          
    if (preg_match($longUrlRegex, $url_1, $matches)) {
        $youtube_id = $matches[count($matches) - 1];
    } 
    if (preg_match($shortUrlRegex, $url_1, $matches)) {
        $youtube_id = $matches[count($matches) - 1];
    }
    $fullEmbedUrl_1 = 'https://www.youtube.com/embed/' . $youtube_id ;

    if($url_2 != null){
        $youtube_id = '';
        if (preg_match($longUrlRegex, $url_2, $matches)) {
            $youtube_id = $matches[count($matches) - 1];
        } 
        if (preg_match($shortUrlRegex, $url_2, $matches)) {
            $youtube_id = $matches[count($matches) - 1];
        }
        $fullEmbedUrl_2 = 'https://www.youtube.com/embed/' . $youtube_id ;
    }

    if($url_3 != null){
        $youtube_id = '';
        if (preg_match($longUrlRegex, $url_2, $matches)) {
            $youtube_id = $matches[count($matches) - 1];
        } 
        if (preg_match($shortUrlRegex, $url_2, $matches)) {
            $youtube_id = $matches[count($matches) - 1];
        }
        $fullEmbedUrl_3 = 'https://www.youtube.com/embed/' . $youtube_id ;
    }
?>

<!-- FEATURED VIDEOS SECTION -->
<div class="container section-margin {{request()->edit != '1' && $landing_page->featured_videos_visibility == 0 ? 'section-none' : ''}} {{request()->edit == '1' && $user != null ? 'overlay-container' : ''}}">
    <h2 class="mb-2 font-weight-bold">{{$landing_page->featured_videos_header}}</h2>
    <h5 class="mb-4" style="color:rgb(23, 135, 184)">{{$landing_page->featured_videos_subheader}}</h5>
    <div id="featuredVideo" style="" class="carousel slide" data-interval="false">
        <div class="carousel-inner text-center" style="height:550px">
            <div class="carousel-item active" style="height:550px;">
                <iframe src="{{$fullEmbedUrl_1}}" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen height="500px" width="100%">
                </iframe>
            </div>
            @if($landing_page->featured_video_link_2 != null)
            <div class="carousel-item" style="height:550px;">
                <iframe src="{{$fullEmbedUrl_2}}" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen height="500px" width="100%">
                </iframe>
            </div>
            @endif
            @if($landing_page->featured_video_link_3 != null)
            <div class="carousel-item" style="height:550px;">
                <iframe src="{{$fullEmbedUrl_3}}" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen height="500px" width="100%">
                </iframe>
            </div>
            @endif
        </div>

        @if($landing_page->featured_video_link_2 || $landing_page->featured_video_link_2 != null)
        <a class="carousel-control-prev" href="#featuredVideo" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#featuredVideo" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
        @endif
    </div>
    @if(request()->edit == 1)
        <div class="hover-overlay" style="width:100%">    
            <button type="button" class="btn btn-xs btn-primary" data-target="#editFeaturedVideosSectionModal" data-toggle="modal"><i class="far fa-edit"></i></button>      
        </div>
    @endif
</div>

<?php
    $compiled_artifacts = getInterests($user);

    function getInterests($user) {
        $compiled_artifacts = collect();

        if(!$user) {
            return App\ArtifactAANR::inRandomOrder()->limit(3)->get();
        }

        if($user->is_organization_other != 1){
            $user_consortia_id = App\Consortia::where('short_name', '=', $user->organization)->first()->id;
            $compiled_artifacts = App\ArtifactAANR::where('consortia_id', '=', $user_consortia_id)->get();
        }

        if($user->interest == null){
            return $compiled_artifacts;
        }

        //get all relevant consortia from the user's interest
        $consortia_ids = App\Consortia::select('id')->whereIn('short_name', json_decode($user->interest))->get();
        $compiled_artifacts = $compiled_artifacts->merge(App\ArtifactAANR::whereIn('consortia_id',$consortia_ids->pluck('id'))->get());
        
        //get all relevant commodities from the user's interest
        $commodities_ids = App\Commodity::select('id')->whereIn('name', json_decode($user->interest))->get();
        $artifact_ids = DB::table('artifactaanr_commodity')->select('artifactaanr_id')->whereIn('commodity_id', $commodities_ids->pluck('id'))->get();
        
        //get all relevant ISP from the user's interest
        $isp_ids = App\ISP::select('id')->whereIn('name', json_decode($user->interest))->get();
        $artifact_ids = $artifact_ids->merge(DB::table('artifactaanr_isp')->select('artifactaanr_id')->whereIn('isp_id', $isp_ids->pluck('id'))->get());
        
        $compiled_artifacts = $compiled_artifacts->merge(App\ArtifactAANR::whereIn('id',$artifact_ids->pluck('artifactaanr_id'))->get());
        
        if($compiled_artifacts == null){
            return App\ArtifactAANR::inRandomOrder()->limit(3)->get();
        }

        return $compiled_artifacts->shuffle()->take(3)->all();
    }
?>

<!-- RECOMMENDED SECTION -->
@if($landing_page->recommended_for_you_bg_type == 1)
<div class="recommended-section {{request()->edit != '1' && $landing_page->recommended_for_you_visibility == 0 
    ? 'section-none' 
    : ''}} {{request()->edit == '1' && $user != null 
    ? 'overlay-container' 
    : ''}}" style="background: {{$landing_page->recommended_for_you_bg}};">
@else
<div class="recommended-section {{request()->edit != '1' && $landing_page->recommended_for_you_visibility == 0 
    ? 'section-none' 
    : ''}} parallax-section {{request()->edit == '1' && $user != null 
    ? 'overlay-container' 
    : ''}}" style="background-image: url('/storage/page_images/{{$landing_page->recommended_for_you_bg}}');">
@endif
    <div class="container section-margin">
        <h2 class="mb-2 font-weight-bold" style="color:white">{{$landing_page->recommended_for_you_header}}</h2>
        <h5 class="mb-0" style="color:rgb(48, 152, 197)">{{$landing_page->recommended_for_you_subheader}}</h5>
        <div id="techCards" class="row">
            @foreach($compiled_artifacts as $artifact)
                <div class="col-sm-4 tech-card-container">
                    <div class="card front-card h-auto shadow rounded">
                        @if($artifact->imglink == null)
                        <div class="card-img-top center-vertically px-3 tech-card-color" style="height:200px">
                            <span class="font-weight-bold" style="font-size: 17px;line-height: 1.5em;color: #2b2b2b;">
                                {{$artifact->title}}
                            </span>
                        </div>
                        @else
                        <img src="{{$artifact->imglink}}" class="card-img-top" height="175" style="object-fit: cover;">
                        @endif
                        <div class="card-body">
                            <h4 class="card-title trail-end">{{$artifact->title}}</h4>
                            <div class="card-text trail-end" style="line-height: 120%;">
                                <p class="mb-2"><b>{{$artifact->author}}</b></p>
                                <small>{{isset($artifact->consortia->short_name) ? $artifact->consortia->short_name : '--'}}<br>           
                                            {{isset($artifact->content) ? $artifact->content->type : ""}} <br> 
                                        </small>
                            </div>
                        </div>
                        <a href="{{$artifact->link != null ? $artifact->link : '/search?search='.$artifact->title.'#search-anchor'}}" target="_blank" class="stretched-link"></a>
                    </div>
                </div>
            @endforeach
        </div>
        @if(request()->edit == 1)
            <div class="hover-overlay" style="width:100%">    
                <button type="button" class="btn btn-xs btn-primary" data-target="#editRecommendedForYouSectionModal" data-toggle="modal"><i class="far fa-edit"></i></button>      
            </div>
        @endif
    </div>
</div>

<!-- CONSORTIA SECTION -->
<div class="consortia-section {{request()->edit != '1' && $landing_page->consortia_members_visibility == 0 ? 'section-none' : ''}} container section-margin text-center {{request()->edit == '1' && $user != null ? 'overlay-container' : ''}}" id="consortiaGroup">
    <h1 class="mb-2 font-weight-bold">{{$landing_page->consortia_members_header}}</h1>
    <h5 class="mb-4" style="color:rgb(23, 135, 184)">{{$landing_page->consortia_members_subheader}}</h5>
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
    @if(request()->edit == 1)
        <div class="hover-overlay" style="width:100%">    
            <button type="button" class="btn btn-xs btn-primary" data-target="#editConsortiaMembersSectionModal" data-toggle="modal"><i class="far fa-edit"></i></button>      
        </div>
    @endif
</div>

<!--
<div class="px-5 mt-5">
    <img src="/storage/page_images/KM4AANR Footer_sample.png" class="card-img-top" style="object-fit: cover;">
</div>
-->

@if(Session::has('modal_message'))
    <script type="text/javascript">
        $(document).ready(function() {
            $('#new_user_modal').modal();
        });
    </script>

    <div id="new_user_modal" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Welcome to KM4AANR, {{$user->first_name}}!</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>To fully access our portal, please check your email for the verification.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endif

@endsection
<style>
    .featured-publication-text{
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 4; /* number of lines to show */
                line-clamp: 24; 
        -webkit-box-orient: vertical;
    }
    .section-none{
        display:none;
    }

    .section-margin{
        margin-top:5rem;
        margin-bottom:5rem;
    }

    .parallax-section{
         /* The image used */

        /* Create the parallax scrolling effect */
        background-position: center;
        background-repeat: no-repeat;
        background-size: cover;
        box-shadow: inset 0 0 0 1000px rgba(0,0,0,.75);
        -webkit-transform: translate3d(0,0,0);
    }

    .last-section{
        background: rgb(33,109,158);
        color:white;
        padding-top:7rem;
        padding-bottom:7rem;
    }

    .recommended-section{
        padding-top:5rem;
        padding-bottom:5rem;
    }

    .consortia-section{
        padding-top:5rem;
        padding-bottom:5rem;
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

    .overlay-container:hover .hover-overlay, .overlay-container:hover .hover-overlay-text{
        opacity: 1;
    }
    
    .card-image-overlay{
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        padding: 1.25rem;
    }
    .verification-header{
        height:2.5rem;
        padding-top: 10px;
        font-size: 1rem;
        font-weight: 600;
        background-color:gold;
    }

    #techCards .tech-card-container:nth-child(3n+1) div .tech-card-color{
        background-color: rgb(1, 197, 237);
    }
    #techCards .tech-card-container:nth-child(3n+2) div .tech-card-color {
        background-color: rgb(255, 206, 16);
    }
    #techCards .tech-card-container:nth-child(3n+3) div .tech-card-color {
        background-color: rgb(241, 86, 64);
    }

    .sixp-input{
        display:none;
    }
    .media-input{
        display:none;
    }
</style>
@section('scripts')
<script>  
    $(document).ready(function(){
        $('[data-toggle="popover"]').popover('show');
        $('[data-toggle="popover"]').on('shown.bs.popover', function () {
            var $pop = $(this);
            setTimeout(function () {
                $pop.popover('hide');
            }, 10000);
        });

        $('input[type="radio"]').click(function() {
            if($(this).attr('value') == 'sixp-radio' || $(this).attr('name') == 'sixp-input') {
                $('.sixp-input').show();  
                $('.media-input').hide();       
            }
            else if($(this).attr('value') == 'media-radio' || $(this).attr('name') == 'media-input'){
                $('.media-input').show();  
                $('.sixp-input').hide();
            }
            else {
                $('.sixp-input').hide();  
                $('.media-input').hide();
            }
        });
    });
</script>
@endsection