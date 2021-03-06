@extends('layouts.app')
@section('title', 'Search Analytics')
@section('breadcrumb')
    <?php
        $headlines = App\Headline::all();
        $count = 0;
        if(request()->consortium){
            $consortium_search = App\Consortia::where('id','=',request()->consortium)->first()->short_name;
        }
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
<?php 
    $landing_page = App\LandingPageElement::findOrFail(1);
    $aanrPage = App\AANRPage::first();
    $totalContent = App\ArtifactAANR::where('is_agrisyunaryo', '=', 0)->get();

    //Total search for the month
    $totalSearchCurrentMonth = App\SearchQuery::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->count();
    if($totalSearchCurrentMonth <= 0){
        $totalSearchCurrentMonth = 1;
    }
    $totalSearchWithFilter = 0;
    if(request()->filter == 'yes'){
        $totalSearchWithFilter = App\SearchQuery::whereBetween('created_at', [request()->from, request()->to])->count();
    }
    $totalSearchLastMonth = App\SearchQuery::whereMonth('created_at', Carbon::now()->subMonth()->month)->whereYear('created_at', date('Y'))->count();
    if($totalSearchLastMonth == 0){
        $totalSearchIncreasePercent = 100;
    } else {
        $totalSearchIncreasePercent = (1 - $totalSearchLastMonth/$totalSearchCurrentMonth) * 100;
        $totalSearchIncreasePercent = sprintf("%.2f", $totalSearchIncreasePercent);
    }

    //Average search for the last 15 days
    $averageSearchCurrentHalfMonth = sprintf("%.2f", App\SearchQuery::whereBetween('created_at', [Carbon::now()->subdays(15),Carbon::now()])->count()/15);
    if($averageSearchCurrentHalfMonth <= 0){
        $averageSearchCurrentHalfMonth = 1;
    }

    $averageSearchWithFilter = null;
    if(request()->from){
        $averageSearchWithFilter = sprintf("%.2f", App\SearchQuery::whereBetween('created_at', [request()->from, request()->to])->count()/15);
    }

    $averageSearchLastHalfMonth = sprintf("%.2f", App\SearchQuery::whereBetween('created_at', [Carbon::now()->subdays(30),Carbon::now()->subdays(15)])->count()/15);
    if($averageSearchLastHalfMonth == 0){
        $averageSearchIncreasePercent = 100;
    } else {
        $averageSearchIncreasePercent = (1 - $averageSearchLastHalfMonth/$averageSearchCurrentHalfMonth) * 100;
        $averageSearchIncreasePercent = sprintf("%.2f", $averageSearchIncreasePercent);
    }

    //Most search topics in the last 15 days
    $search_query_freq_array = array();
    $search_query_freq_array[0] = array();
    $search_query_freq_array[1] = array();
    foreach(App\SearchQuery::whereBetween('created_at', [Carbon::now()->subdays(15),Carbon::now()])->select('query', DB::raw('count(*) as total'))->groupBy('query')->orderByDesc('total')->get()->take(5) as $item){
        array_push($search_query_freq_array[0], $item->query);
        array_push($search_query_freq_array[1], $item->total);
    }

    //AANR content with the most total views
    $contentMostViews = App\ArtifactAANRViews::select('title', DB::raw('count(*) as total'))->groupBy('title')->orderByDesc('total')->get()->take(5);

    //Commodities with the most views
    $commodity_views_freq_array = array();
    $commodity_views_freq_array[0] = array();
    $commodity_views_freq_array[1] = array();
    foreach(App\CommodityViews::select('id_commodity', DB::raw('count(*) as total'))->groupBy('id_commodity')->orderByDesc('total')->get()->take(5) as $item){
        array_push($commodity_views_freq_array[0], App\Commodity::find($item->id_commodity)->name);
        array_push($commodity_views_freq_array[1], $item->total);
    }

    //ISP with the most views
    $isp_views_freq_array = array();
    $isp_views_freq_array[0] = array();
    $isp_views_freq_array[1] = array();
    foreach(App\ISPViews::select('id_isp', DB::raw('count(*) as total'))->groupBy('id_isp')->orderByDesc('total')->get()->take(5) as $item){
        array_push($isp_views_freq_array[0], App\ISP::find($item->id_isp)->name);
        array_push($isp_views_freq_array[1], $item->total);
    }

     //Number of daily users for the last 15 days
    $page_visitors_freq_array = array();
    $page_visitors_freq_array[0] = array();
    $page_visitors_freq_array[1] = array();
    $totalVisitorsWithFilter = 0;
    if(request()->filter == 'yes'){
        for ($i = 14; $i >= 0; $i--) {
            array_push($page_visitors_freq_array[1], App\PageViews::whereDate('created_at', Carbon::parse(request()->to)->subDays($i))->count());
            array_push($page_visitors_freq_array[0], Carbon::parse(request()->to)->subDays($i)->format('F d'));
        }

        $totalVisitorsWithFilter = App\PageViews::whereBetween('created_at', [request()->from, request()->to])->count();
    } else {
        for ($i = 14; $i >= 0; $i--) {
            array_push($page_visitors_freq_array[1], App\PageViews::whereDate('created_at', Carbon::now()->subDays($i))->count());
            array_push($page_visitors_freq_array[0], Carbon::now()->subDays($i)->format('F d'));
        }
    }
?>
<div class="container-fluid mb-5 px-5">
    {{ Form::open(['action' => ['PagesController@searchAnalyticsWithFilter'], 'method' => 'POST', 'enctype' => 'multipart/form-data']) }}
    {{ method_field('GET') }}
    <div class="card">
        <div class="card-header" style="text-align:left;background-color:white !important" >
            <div class="float-left">
                <span><i class="fas fa-search"></i> FILTER</span><br>
                <small class="text-muted">Filter analytics by date.</small>
            </div>
            <div class="float-right">
                {{Form::submit('Apply Filter', ['class' => 'btn btn-primary'])}}
                <a href="/analytics/search" class="btn btn-success">Clear Filter</a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-xl-2">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-2">
                                {{Form::label('year_from', 'From: ', ['class' => 'col-form-label'])}}
                            </div>
                            <div class="col-9">
                                {{ Form::date('year_from_filter', Carbon::now()->startOfMonth(),['class' => 'form-control']) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-2">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-2">
                                {{Form::label('year_to', 'To: ', ['class' => 'col-form-label'])}}
                            </div>
                            <div class="col-9">
                                {{ Form::date('year_to_filter', Carbon::now()->addDay(),['class' => 'form-control']) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{ Form::close() }}
    <div class="row">
        <div class="col-xl-3 col-lg-4 col-sm-12">
            <div class="card text-center">
                <div class="card-header" style="text-align:left;background-color:white !important" >
                    <span><i class="fas fa-search"></i> TOTAL SEARCH</span><br>
                    <small class="text-muted">Total number of searches made <span class="{{request()->filter == 'yes' ? 'font-weight-bold' : ''}}">{{request()->filter == 'yes' ? 'from '.Carbon::parse(request()->from)->format('M d, Y').' to '.Carbon::parse(request()->to)->format('M d, Y') : 'this month'}}</span></small>
                </div>
                <div class="card-body" style="height:150px">
                    <span style="font-size:3.5rem; color:rgb(59,155,207)">
                        {{request()->filter == 'yes' ? $totalSearchWithFilter : $totalSearchCurrentMonth}}
                    </span><br>
                    <h5 style="{{request()->filter != 'yes' ? 'display:none;' : ''}}; color:rgb(83,186,139)">
                        Total Searches
                    </h5>
                    <h5 class="" style="{{request()->filter == 'yes' ? 'display:none;' : ''}} {{$totalSearchIncreasePercent >= 0 ? 'color:rgb(83,186,139)' : 'color:rgb(243,23,0)'}}">
                        <i class="fas {{$totalSearchIncreasePercent >= 0 ? 'fa-caret-up' : 'fa-caret-down'}}"></i> {{$totalSearchCurrentMonth - $totalSearchLastMonth}} / {{$totalSearchIncreasePercent}}% 
                    </h5>
                </div>
            </div>
            <div class="card text-center">
              <div class="card-header" style="text-align:left;background-color:white !important" >
                  <span><i class="fas fa-search"></i> SEARCH PER DAY</span><br>
                  <small class="text-muted">Average daily search <span class="{{request()->filter == 'yes' ? 'font-weight-bold' : ''}}">{{request()->filter == 'yes' ?  'from '.Carbon::parse(request()->from)->format('M d, Y').' to '.Carbon::parse(request()->to)->format('M d, Y') : 'for the last 15 days'}}</span></small>
              </div>
              <div class="card-body" style="height:150px">
                  <span style="font-size:3.5rem; color:rgb(59,155,207)">
                      {{request()->filter == 'yes' ? ceil($averageSearchWithFilter) : ceil($averageSearchCurrentHalfMonth)}}
                  </span><br>
                  <h5 style="{{request()->filter != 'yes' ? 'display:none;' : ''}}; color:rgb(83,186,139)">
                      Searches per day
                  </h5>
                  <h5 class="" style="{{request()->filter == 'yes' ? 'display:none;' : ''}}{{$averageSearchIncreasePercent >= 0 ? 'color:rgb(83,186,139)' : 'color:rgb(243,23,0)'}}">
                      <i class="fas {{$averageSearchIncreasePercent >= 0 ? 'fa-caret-up' : 'fa-caret-down'}}"></i> {{$averageSearchCurrentHalfMonth - $averageSearchLastHalfMonth}} / {{$averageSearchIncreasePercent}}% 
                  </h5>
              </div>
            </div>
        </div>
        <div class="col-xl-9 col-lg-8 col-sm-12">
            <div class="card">
                <div class="card-header" style="background-color:white !important">
                    <i class="fas fa-chart-line"></i> USERS <br>
                    <small class="text-muted">Number of {{request()->filter == 'yes' ? '' : 'daily'}} users <span class="{{request()->filter == 'yes' ? 'font-weight-bold' : ''}}">{{request()->filter == 'yes' ? 'from '.Carbon::parse(request()->to)->subDays(14)->format('M d, Y').' to '.Carbon::parse(request()->to)->format('M d, Y') : 'for the last 15 days'}}</span></small>
                </div>
                <div class="card-body text-center">
                    <div style="{{request()->filter != 'yes' ? 'display:none;' : ''}}" class="py-4">
                        <span style="font-size:10rem; color:rgb(59,155,207)">
                            {{$totalVisitorsWithFilter}}
                        </span><br>
                        <h2 style="{{request()->filter != 'yes' ? 'display:none;' : ''}}; color:rgb(83,186,139)">
                            Users visited the site from {{Carbon::parse(request()->from)->format('M d, Y')}} to {{Carbon::parse(request()->to)->format('M d, Y')}}
                        </h2>
                    </div>
                    <canvas id="daily_visitors" style="height:355px !important;{{request()->filter == 'yes' ? 'display:none;' : ''}}"></canvas>
                </div>
             </div>
        </div>
        <div class="col-md-6 col-sm-12">
            <div class="card">
                <div class="card-header" style="background-color:white !important">
                    <i class="fas fa-chart-line"></i> CONTENT <br>
                    <small class="text-muted">AANR content with the most total views</small>
                </div>
                <div class="card-body">
                    <table class="table data-table tech-table table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <td></td>
                                <td>Title</td>
                                <td>Views</td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($contentMostViews as $contentMostView)
                            <tr>
                                <td>{{$loop->iteration}}</td>
                                <td>{{$contentMostView->title}}</td>
                                <td>{{$contentMostView->total}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-12">
            <div class="card">
                <div class="card-header" style="background-color:white !important">
                    <i class="fas fa-chart-line"></i> COMMODITIES <br>
                    <small class="text-muted">Commodities with the most views</small>
                </div>
                <div class="card-body">
                    <canvas id="most_popular_commodities" style="height:310px !important;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-12">
            <div class="card">
                <div class="card-header" style="background-color:white !important">
                    <i class="fas fa-chart-line"></i> ISPs <br>
                    <small class="text-muted">ISPs with the most views</small>
                </div>
                <div class="card-body">
                    <canvas id="most_popular_isps" style="height:310px !important;"></canvas>
                </div>
            </div>
        </div>
        <?php
            $merged_artifact_industry = DB::table(DB::raw('(SELECT artifactaanr_id, industry_id FROM artifactaanr_isp UNION ALL SELECT artifactaanr_id, industry_id FROM artifactaanr_commodity GROUP BY artifactaanr_id, industry_id) as artifact_industries'))
                ->select(DB::raw('industry_id, count(artifactaanr_id) as total'))
                ->groupBy('industry_id')
                ->orderBy('industry_id')
                ->get();
        ?>
        <div class="col-sm-6">
            <div class="row">
                <div class="col-sm-6">
                    <div class="card text-center">
                        <div class="card-header" style="text-align:left;background-color:white !important" >
                            <span><i class="fas fa-search"></i> CONTENT</span><br>
                            <small class="text-muted">Total number of AANR Content</small>
                        </div>
                        <div class="card-body" style="height:150px; background-color:rgb(40,109,158)">
                            <span style="font-size:4.5rem; color:white; line-height:1">
                                <b>{{$totalContent->count()}}</b>
                            </span>
                            <h4 class="text-white" style="">
                                AANR Content
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card text-center">
                        <div class="card-header" style="text-align:left;background-color:white !important" >
                            <span><i class="fas fa-search"></i> CONTENT</span><br>
                            <small class="text-muted">Total number of Agricultural Technologies</small>
                        </div>
                        <div class="card-body" style="height:150px; background-color:rgb(247,186,6)">
                            <span style="font-size:4.5rem;color:white; line-height:1">
                                <b>{{$merged_artifact_industry[0]->total}}</b>
                            </span>
                            <h4 class="text-white">
                                Agricultural Technologies
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card text-center">
                        <div class="card-header" style="text-align:left;background-color:white !important" >
                            <span><i class="fas fa-search"></i> CONTENT</span><br>
                            <small class="text-muted">Total number of Aquatic Resources</small>
                        </div>
                        <div class="card-body" style="height:150px; background-color:rgb(58,136,235)">
                            <span style="font-size:4.5rem;color:white; line-height:1">
                                <b>{{$merged_artifact_industry[0]->total}}</b>
                            </span>
                            <h4 class="text-white">
                                Aquatic Resources
                            </h4>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card text-center">
                        <div class="card-header" style="text-align:left;background-color:white !important" >
                            <span><i class="fas fa-search"></i> CONTENT</span><br>
                            <small class="text-muted">Total number of Natural Resources</small>
                        </div>
                        <div class="card-body" style="height:150px; background-color:rgb(60,193,114)">
                            <span style="font-size:5.5rem; color:white; line-height:1">
                                <b>{{$merged_artifact_industry[1]->total}}</b>
                            </span>
                            <h4 class="text-white">
                                Natural Resources
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6">
            <div class="card">
                <div class="card-header" style="background-color:white !important">
                    <i class="fas fa-search"></i> TOPICS <br>
                    <small class="text-muted">Most searched topics in the last 15 days</small>
                </div>
                <div class="card-body">
                    <canvas id="most_popular_topics" style="height:355px !important;"></canvas>
                </div>
             </div>
        </div>
    </div>
    <div class="text-center mt-3">
        <a href="{{ url('/analytics/search/save') }}" class="btn btn-info">Save Page as PDF</a>
    </div>  
</div>

@endsection
@section('scripts')
<script>
    let daily_visitors = new Chart(document.getElementById('daily_visitors').getContext('2d'), {
        type:'bar',
        data:{
            labels: @php echo json_encode($page_visitors_freq_array[0]); @endphp,
            datasets:[{
                label: 'No. of site visitors',
                data: @php echo json_encode($page_visitors_freq_array[1]); @endphp,
                backgroundColor:[
                    'rgb(59,155,207)'
                ],
                hoverBorderWidth:3,
                hoverBorderColor:'rgb(0,0,0)'
            }]
        },
        options:{
            legend: {
                display: false
            },
            maintainAspectRatio: false,
            responsive:true,
            scales: {
                yAxes:{
                    display: true,
                    ticks: {
                        beginAtZero: true,
                        stepSize: 1
                    }
                }
            }
        }
    }); 
    let most_popular_commodities = new Chart(document.getElementById('most_popular_commodities').getContext('2d'), {
        type:'pie',
        data:{
            labels: @php echo json_encode($commodity_views_freq_array[0]); @endphp,
            datasets:[{
                data: @php echo json_encode($commodity_views_freq_array[1]); @endphp,
                backgroundColor:[
                    'rgba(20,99,20,1)',
                    'rgba(54,38,195,1)',
                    'rgba(108,21,105,1)',
                    'rgba(169,201,51,1)',
                    'rgba(20,21,20,1)',
                ],
                hoverBorderWidth:3,
                hoverBorderColor:'rgb(0,0,0)'
            }]
        },
        options:{
            legend: {
                display: false
            },
            maintainAspectRatio: false,
            responsive:true,
        }
    });
    let most_popular_isps = new Chart(document.getElementById('most_popular_isps').getContext('2d'), {
        type:'pie',
        data:{
            labels: @php echo json_encode($isp_views_freq_array[0]); @endphp,
            datasets:[{
                data:  @php echo json_encode($isp_views_freq_array[1]); @endphp,
                backgroundColor:[
                    'rgba(8,99,132,1)',
                    'rgba(54,38,8,1)',
                    'rgba(9,21,5,1)',
                    'rgba(3,201,51,1)',
                    'rgba(210,7,100,1)',
                ],
                hoverBorderWidth:3,
                hoverBorderColor:'rgb(0,0,0)'
            }]
        },
        options:{
            legend: {
                display: false
            },
            maintainAspectRatio: false,
            responsive:true,
        }
    });
    let most_popular_topics = new Chart(document.getElementById('most_popular_topics').getContext('2d'), {
        type:'bar',
        data:{
            labels: @php echo json_encode($search_query_freq_array[0]); @endphp,
            datasets:[{
                data: <?php echo json_encode($search_query_freq_array[1]);?>,
                backgroundColor:[
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
        axisX: {
            labelMaxWidth: 100,
        },
        options:{
            indexAxis: 'y',
            legend: {
                display: false
            },
            elements: {
                bar: {
                    borderWidth: 2,
                }
            },
            maintainAspectRatio: false,
            responsive:true,
            scales: {         
                yAxes: {
                    ticks: {
                        callback: function(value, index) {
                            value = @php echo json_encode($search_query_freq_array[0]); @endphp;
                            const vw = Math.max(document.documentElement.clientWidth || 0, window.innerWidth || 0)
                            console.log(vw/30);
                            if ((vw/30) < value[index].length) {
                                return value[index].substr(0, (vw/30)) + " ...";
                            }
                            return value[index];
                        },
                    },
                },
            },
            plugins: {
                legend: {
                    display: false,
                },
                title: {
                    display: true,
                    text: 'Most searched terms'
                }
            }
        }
    });
</script>
@endsection
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
    body{
        background-color:rgb(245,245,245) !important;
    }

</style>