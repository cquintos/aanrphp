@extends('layouts.app')
@section('title', 'Search Analytics')
@section('breadcrumb')
    <?php
        $headlines = App\Headline::all();
        $isp = App\ISP::pluck('name', 'name')->all();
        asort($isp);
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

    //GOOGLE ANALYTICS
    use Spatie\Analytics\Period;
    Config::set('analytics.view_id', '273252945');
    
    //SET UP DATES, INTERVALS, AND PERIODS
    $begin = new DateTime(request()->from);
    $end = new DateTime(request()->to);
    $end->setTime(0,0,1);
    $interval = DateInterval::createFromDateString('1 day');

    if(request()->from == null) {
        $begin = Carbon::tomorrow()->subMonth();
        $end = Carbon::now();
    }
    
    $prevMonth = Carbon::tomorrow()->subMonth(2);
    $period_freq = new DatePeriod($begin, $interval, $end);
    $period = Period::create($begin, $end);
    $prevMonthPeriod = Period::create($prevMonth, $begin);

    // SET UP GOOGLE ANALYTICS QUERY PARAMETERS
    $metric_user = 'ga:users, ga:newUsers';
    $metric_session = 'ga:bounceRate, ga:avgSessionDuration, ga:pageviewsPerSession';
    $metric_search = 'ga:searchUniques';
    $filters_search ='ga:searchUniques!=0';

    if(request()->withISP != null) {
        $filters_search = 'ga:searchKeyword=@'.request()->withISP;
    }

    $dimensions_date = [
        'dimensions' => 'ga:date',
    ];

    $dimensions_search = [
        'dimensions' => 'ga:searchKeyword, ga:date',
        'filters' => $filters_search,
        'sort' => '-ga:searchUniques'
    ];  

    // GOOGLE ANALYTICS QUERY
    $analytics_query_sessions_result = Analytics::performQuery($period, $metric_session, $dimensions_date);
    $analytics_user_types = Analytics::performQuery($period, $metric_user, []);
    $analytics_most_visited_pages = Analytics::fetchMostVisitedPages($period);
    $prev_analytics_query_search_results = Analytics::performQuery($prevMonthPeriod, $metric_search, $dimensions_search);
    $analytics_query_user_results = Analytics::performQuery($period, $metric_user, $dimensions_date);
    $analytics_query_search_results = Analytics::performQuery($period, $metric_search, $dimensions_search);

    console_log($analytics_query_sessions_result);
    // console_log($analytics_query_user_results);

    // TOTAL SEARCH USING GOOGLE ANALYTICS
    $prev_total_search = $prev_analytics_query_search_results->totalsForAllResults['ga:searchUniques'];
    $total_search = $analytics_query_search_results->totalsForAllResults['ga:searchUniques'];
    // SEARCH THIS MONTH VS LAST MONTH
    $search_percentage = 0;

    if($prev_total_search != 0) {
        $search_percentage = (($total_search-$prev_total_search)/$prev_total_search) * 100;
    }

    $search_percentage = sprintf("%.2f", $search_percentage);

    // *NOT SHOWN IF WITH ISP FILTER*
    // AVERAGE SEARCH PER DAY 
    $date_diff = date_diff($begin, $end, true);
    $search_ave = sprintf("%.2f", $total_search/$date_diff->days);
    $prev_search_ave = sprintf("%.2f", $prev_total_search/$date_diff->days);
    $search_ave_percentage = 0;
    
    if($prev_search_ave != 0) {
        $search_ave_percentage = (($search_ave-$prev_search_ave)/$prev_search_ave) * 100;
    } 

    $search_ave_percentage = sprintf("%.2f", $search_ave_percentage);

    // USER TYPE PERCENTAGES
    $new_users_total = $analytics_user_types->totalsForAllResults['ga:newUsers'];
    $old_users_total = $analytics_user_types->totalsForAllResults['ga:users'];
    $total_users = $new_users_total + $old_users_total;
    $new_users_percentage = $new_users_total/$total_users*100;
    $old_users_percentage = $old_users_total/$total_users*100;

    
    // BAR GRAPH FOR ISP SEARCHES WITHIN DATE FILTER AND TOTAL VISITORS FOR 30 DAYS 
    $freq_index = 0;
    $freq_array = array();
    $new_user_freq_array = array();
    $freq_row = $analytics_query_user_results->rows;
    
    foreach ($period_freq as $dt) {
        $freq_array[$dt->format("M d")] = 0;
        $new_user_freq_array[$dt->format("M d")] = 0;
    }

    if(request()->withISP != null) {
        $freq_row = $analytics_query_search_results->rows;
        $freq_index = 1;
    }

    if($freq_row != null) {
        foreach ($freq_row as $key => $value) {
            $freq_array[date("M d",strtotime($value[$freq_index]))] += $value[$freq_index+1];

            if(request()->withISP == null) {
                $new_user_freq_array[date("M d",strtotime($value[$freq_index]))] += $value[$freq_index+2];
            }
        }
    }

    //  AANR CONTENT WITH THE MOST VIEWS
    $isp_content = App\ArtifactAANR::get();
    $total_content = App\ArtifactAANR::where('is_agrisyunaryo', '=', 0)->get();
    $content_most_views = App\ArtifactAANRViews::select('title', DB::raw('count(*) as total'))
                                            ->groupBy('title')
                                            ->orderByDesc('total')
                                            ->get()
                                            ->take(6);

    if(request()->withISP != null) {    
        $total_content = DB::table('artifactaanr')
                        ->join('artifactaanr_isp', 'artifactaanr.id', "=", 'artifactaanr_isp.artifactaanr_id')
                        ->join('isp', 'artifactaanr_isp.isp_id', "=", 'isp.id')
                        ->select(DB::raw('count(*)'))
                        ->where('isp.name', '=', request()->withISP);
        $content_most_views = DB::table('artifactaanr_views')
                            ->join('artifactaanr_isp', 'artifactaanr_views.id_artifact', "=", 'artifactaanr_isp.artifactaanr_id')
                            ->join('isp', 'artifactaanr_isp.isp_id', "=", 'isp.id')
                            ->select('artifactaanr_views.title', DB::raw('count(*) as total'))
                            ->where('isp.name', '=', request()->withISP)
                            ->groupBy('title')
                            ->orderByDesc('total')
                            ->get()
                            ->take(6);
    }

    // TABLE FOR CONTENT TYPE AND COUNT
    $isp_content_type_array = array();
    if($isp_content != null) {
        foreach($isp_content as $content) {
            if($content->isp->get('0')){
                if($content->isp->get('0')->name == request()->withISP){
                    if($content->content) {
                        if(array_key_exists($content->content->type, $isp_content_type_array)) {
                            $isp_content_type_array[$content->content->type] += 1;
                        } else {
                            $isp_content_type_array[$content->content->type] = 1;
                        }
                    }
                }
            }
        }
    }

    // *NOT SHOWN IF WITH ISP FILTER*
    //  COMMODITIES WITH THE MOST VIEWS
    $commodity_views_freq_array = array();
    $commodity_views_freq_array[0] = array();
    $commodity_views_freq_array[1] = array();
    foreach(App\CommodityViews::select('id_commodity', DB::raw('count(*) as total'))->groupBy('id_commodity')->orderByDesc('total')->get()->take(5) as $item){
        array_push($commodity_views_freq_array[0], App\Commodity::find($item->id_commodity)->name);
        array_push($commodity_views_freq_array[1], $item->total);
    }

    // *NOT SHOWN IF WITH ISP FILTER*
    // ISP WITH THE MOST VIEWS 
    $isp_views_freq_array = array();
    $isp_views_freq_array[0] = array();
    $isp_views_freq_array[1] = array();
    foreach(App\ISPViews::select('id_isp', DB::raw('count(*) as total'))->groupBy('id_isp')->orderByDesc('total')->get()->take(5) as $item){
        array_push($isp_views_freq_array[0], App\ISP::find($item->id_isp)->name);
        array_push($isp_views_freq_array[1], $item->total);
    }

    // MOST SEARCHED TERMS
    $search_query_freq_array = array(); 

    if ($analytics_query_search_results->rows != null) {
        foreach($analytics_query_search_results->rows as $key => $value) {
            if(array_key_exists($value[0], $search_query_freq_array)) {
                $search_query_freq_array[$value[0]] += $value[2];
            } else {
                $search_query_freq_array[$value[0]] = $value[2];
            } 
        }
    }

    arsort($search_query_freq_array);

    // SESSION BAR GRAPH
    $bounce_rate = array();
    $avg_duration_session = array();
    $pages_per_session = array();
    
    if ($analytics_query_sessions_result->rows != null) {
        foreach ($analytics_query_sessions_result->rows as $entry) {
            $mins = $entry[2]/60;
            array_push($bounce_rate, $entry[1]);
            array_push($avg_duration_session, $mins);
            array_push($pages_per_session, $entry[3]);
        }   
    }

    function console_log($output, $with_script_tags=true) { 
        $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) . ');';

        if ($with_script_tags) {
            $js_code = '<script>' . $js_code . '</script>';
        }

        echo $js_code;
    }

    $query_for_merge ='(SELECT artifactaanr_id, industry_id FROM artifactaanr_isp UNION ALL SELECT artifactaanr_id, industry_id FROM artifactaanr_commodity GROUP BY artifactaanr_id, industry_id) as artifact_industries';
    $merged_artifact_industry = DB::table(DB::raw($query_for_merge))
        ->select(DB::raw('industry_id, count(artifactaanr_id) as total'))
        ->groupBy('industry_id')
        ->orderBy('industry_id')
        ->get();
?>

<div class="container-fluid mb-5 px-5">

    {{-- TOP ROW --}}

    <div class="row">

        {{-- CALENDAR BOX --}}
        <div class="col-md-3 col-sm-12 d-flex align-items-stretch">
            <div class="card text-center" style="width:100%">
                <div class="card-header" style="text-align:left; background-color:lightgray">
                    <i class="fas fa-calendar-alt" aria-hidden="true" style="color: "></i> <b>DATE TODAY</b></h1>
                </div>
                <div class="card-body" style="background-color: brown">
                    <span style="font-size:4.5rem; color:white; line-height:1.25">
                        <b>{{date('j')}} </b>
                    </span>
                    <h3 class="text-white" style="">
                        {{strtoupper(date('M'))}}
                    </h3>
                </div>
                <div class="card-footer" style="background-color: ">
                    <b>{{strtoupper(date('l'))}}</b>

                </div>
            </div>
        </div>                                                                                                  

        {{-- FILTER OPTIONS --}}

        <div class="col-md-9 col-sm-12">
            {{ Form::open(['action' => ['PagesController@searchAnalyticsWithFilter'], 'method' => 'POST', 'enctype' => 'multipart/form-data', 'style' => 'margin:0; width:100%; height:100%'] ) }}
            {{ method_field('GET') }}
            <div class="card " style="">
                <div class="card-header" style="text-align:left;background-color:lightgray" >
                    <div class="float-left">
                        <span><i class="fa fa-filter"></i>  <b>FILTER OPTIONS</b></span><br>
                        <small class="font-weight-bold">Filter analytics by choosing a start date and end date or by selecting an ISP category.</small>
                    </div>
                </div>
                
                {{-- FILTER --}}
                
                <div class="card-body">
                    <div class="row">

                        {{-- DATE START FILTER --}}

                        <div class="col-xl-4">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-4">
                                        {{Form::label('year_from', 'Date Start: ', ['class' => 'col-form-label'])}}
                                    </div>
                                    <div class="col-6">
                                        {{ Form::date('year_from_filter', Carbon::now()->startOfMonth(),['class' => 'form-control']) }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ISP FILTER --}}

                        <div class="col-xl-4">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-3">
                                        {{Form::label('isp', 'ISP:', ['class' => 'col-form-label'])}}
                                    </div>
                                    <div class="col-9">
                                        {{Form::select('isp_filter', array_merge(['selected'=>'Select a category'],$isp), request()->withISP ? request()->withISP : 'Select a category', ['class' => 'form-control'])}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- DATE END FILTER --}}
    
                    <div class="row">
                        <div class="col-xl-4">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-4">
                                        {{Form::label('year_to', 'Date End: ', ['class' => 'col-form-label'])}}
                                    </div>
                                    <div class="col-6">
                                        {{ Form::date('year_to_filter', Carbon::now(),['class' => 'form-control']) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
    
                {{-- FILTER BUTTONS --}}
    
                <div class="card-footer">
                    <div class="float-left">
                        {{Form::submit('Apply Filter', ['class' => 'btn btn-primary'])}}
                        <a href="/analytics/search" class="btn btn-success">Clear Filter</a>
                    </div>
                </div>
            </div>
            {{ Form::close() }}
        </div>
    </div>

    {{-- MAIN ROW BODY --}}

    <div class="row">
        <div class="col-xl-3 col-lg-4 col-sm-12" style="{{request()->withISP == null ? 'display: none;' : ''}}">
            
            {{-- TOTAL SEARCH BOX --}}
            
            <div class="card text-center">

                {{-- TOTAL SEARCH HEADER --}}

                <div class="card-header" style="text-align:left;background-color:lightgray" >
                    <span>
                        <i class="fas fa-search"></i> 
                        <b>
                        {{request()->withISP == null 
                            ? 'TOTAL SEARCH' 
                            : 'TOTAL ' . strtoupper(request()->withISP)  . ' SEARCH'}}
                        </b>
                    </span><br>
                    <small class="font-weight-bold">
                        {{-- {{request()->withISP == null 
                            ? 'Total number of searches made' 
                            : "Total number of " . strtolower(request()->withISP)  . ' searches made'}} --}}
                        <span class="{{request()->filter == 'yes' ? 'font-weight-bold' : ''}}">
                            {{request()->filter == 'yes' 
                                ? 'from '.Carbon::parse(request()->from)->format('M d, Y').' to '.Carbon::parse(request()->to)->format('M d, Y') 
                                : 'for the last 30 days'}}
                        </span>
                    </small>
                </div>
                
                {{-- TOTAL SEARCH BODY --}}

                <div class="card-body" style="height:150px">
                    <span style="font-size:4.5rem; color:rgb(59,155,207); line-height:1.25">
                        <b>
                            {{$total_search}}
                        </b>
                    </span><br>
                    <h5 class="" style="{{$search_percentage >= 0 ? 'color:rgb(83,186,139)' : 'color:rgb(243,23,0)'}}">
                        <i class="fas {{$search_percentage >= 0 ? 'fa-caret-up' : 'fa-caret-down'}}"></i> 
                        {{$total_search - $prev_total_search}} / {{$search_percentage}}% 
                    </h5>
                </div>
            </div>

            {{-- AVERAGE SEARCH PER DAY BOX --}}
            
            <div class="card text-center" style="{{request()->withISP == null ? 'display: none;' : ''}}">

                {{-- AVERAGE SEARCH PER DAY HEADER --}}

                <div class="card-header" style="text-align:left;background-color:lightgray" >
                    <span>
                    <i class="fas fa-search"></i>
                    <b>
                        {{request()->withISP == null 
                        ? ' SEARCH PER DAY' 
                        : " " . strtoupper(request()->withISP)  . ' SEARCH PER DAY'}}</span><br>
                    </b>
                    <small class="font-weight-bold">
                    {{-- {{request()->withISP == null ? 'Average daily search' : "Average daily " . strtolower(request()->withISP)  . ' search'}} --}}
                    <span class="{{request()->filter == 'yes' ? 'font-weight-bold' : ''}}">
                        {{request()->filter == 'yes' 
                            ?  'from '.Carbon::parse(request()->from)->format('M d, Y').' to '.Carbon::parse(request()->to)->format('M d, Y') 
                            : 'for the last 30 days'}}
                    </span>
                </small>
                </div>

                {{-- AVERAGE SEARCH PER DAY BODY --}}

                <div class="card-body" style="height:150px">
                    <span style="font-size:4.5rem; color:rgb(59,155,207); line-height:1.25">
                        <b>{{number_format((float)$search_ave, 2, '.', '')}}</b>
                    </span><br>
                    <h5 class="" style="{{$search_ave_percentage >= 0 ? 'color:rgb(83,186,139)' : 'color:rgb(243,23,0)'}}">
                        <i class="fas {{$search_ave_percentage >= 0 ? 'fa-caret-up' : 'fa-caret-down'}}"></i> 
                        {{$search_ave - $prev_search_ave}} / {{$search_ave_percentage}}% 
                    </h5>
                </div>
            </div>
        </div>

        {{-- PIE CHART FOR RATIO OF NEW VS RETURNING USERS --}}

        <div class="col-md-3 col-sm-6  d-flex align-items-stretch" style="{{request()->withISP != null ? 'display: none !important;' : ''}}">
            <div class="card text-center" style="width:100%">
                <div class="card-header" style="text-align:left;background-color:lightgray">    
                    <i class="fas fa-chart-line"></i><b> USER TYPES </b><br>
                    <small class="font-weight-bold">RATIO OF NEW AND RETURNING USERS</small>
                </div>
                <div class="card-body">
                    <canvas id="pie_user_types" style="height:250px !important;"></canvas>
                </div>
                <div class="card-footer">
                    <b style="color: rgb(0,0,128)">New Visitors: {{$new_users_percentage}}%</b><br>

                    <b style="color: rgb(59,155,207)">Returning Visitors: {{$old_users_percentage}}%</b><br>
                </div>
            </div>
        </div>

        {{-- BAR GRAPH FOR NUMBER OF VISITORS --}}
        
        <div class="col-xl-9 col-lg-8 col-sm-12" style="{{request()->withISP != null ? 'display: none;' : ''}}">
            <div class="card">
                <div class="card-header" style="background-color:lightgray">
                    <i class="fas fa-chart-bar"></i> <b>USERS </b><br>
                    <small class="font-weight-bold">
                        Number of {{request()->filter == 'yes' 
                            ? '' 
                            : 'daily'}} users 
                        <span class="{{request()->filter == 'yes' ? 'font-weight-bold' : ''}}">
                            {{request()->filter == 'yes' 
                                ? 'from '.Carbon::parse(request()->from)->format('M d, Y').' to '.Carbon::parse(request()->to)->format('M d, Y') 
                                : 'for the last 30 days'}}
                        </span>
                    </small>
                </div>
                <div class="card-body text-center">
                    <canvas id="daily_visitors" style="height:355px;"></canvas>
                </div>
                <div class="card-footer text-center">
                    <small>
                        <b>**Data presented here does not show uniques users. A user may return several times and that user's session will be recorded per day.
                        <br>Also, a user can be both new and returning in a single instance.** </b>
                    </small>
                </div>
             </div>
        </div>

        {{-- ISP SPECIFIC ======  BAR GRAPH FOR ISP SPECIFIC NUMBER OF SEARCHES --}}

        <div class="col-xl-9 col-lg-8 col-sm-12" style="{{request()->withISP == null ? 'display: none;' : ''}}">
            <div class="card">
                <div class="card-header" style="background-color:lightgray">
                    <i class="fas fa-chart-bar"></i>  <b>{{strtoupper(request()->withISP)}} SEARCHES </b><br>
                    <small class="font-weight-bold">
                        Number of searches of ISP {{request()->withISP}} searches from {{Carbon::parse(request()->from)->format('M d, Y')}} to {{Carbon::parse(request()->to)->format('M d, Y')}}.
                    </small>
                </div>
                <div class="card-body text-center">
                    <canvas id="daily_searches" style="height:355px !important;"></canvas>
                </div>
             </div>
        </div>

        {{-- ISP SPECIFIC ====== TOTAL CONTENT BOX --}}
        
        <div class="col-md-3 col-sm-12 d-flex align-items-stretch" style="{{request()->withISP == null ? 'display: none !important;' : ''}}">
            <div class="card text-center">

                {{-- TOTAL CONTENT HEADER --}}

                <div class="card-header" style="text-align:left;background-color:lightgray" >
                    <span><i class="fas fa-database"></i><b> TOTAL CONTENT </b></span><br>
                    <small clas="font-weight-bold">
                        <span class="font-weight-bold">
                            <b>Total number of {{strtoupper(request()->withISP)}} Content</b>
                        </span>
                    </small>
                </div>

                {{-- TOTAL CONTENT BODY --}}

                <div class="card-body p0" style="padding: 0; height:150px; background-color:rgb(40,109,158); overflow:hidden">
                    <span style="font-size:2.5rem; color:white; line-height:1.25">
                        <b>
                            {{$total_content->count()}}
                        </b>
                    </span>
                    <h4 class="text-white" style="">
                        {{request()->withISP}} Content
                    </h4>
                    <div class="aanr-content" style="padding:0 !important">
                        <table class="table data-table-options tech-table table-hover" style="width:100%; color:whitesmoke">
                            <thead>
                                <tr>
                                    <th style="width:80%; color:black">Content Type</th>
                                    <th style="width:30%; color:black">Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><b>Publications</b></td>
                                    <td><b>86</b></td>
                                </tr>    
                                <tr>
                                    <td><b>Technologies</b></td>
                                    <td><b>57</b></td>
                                </tr>    
                                <tr>
                                    <td><b>Products</b></td>
                                    <td><b>42</b></td>
                                </tr>    
                                <tr>
                                    <td><b>Webinars</b></td>
                                    <td><b>40</b></td>
                                </tr>    
                                <tr>
                                    <td><b>Events</b></td>
                                    <td><b>15</b></td>
                                </tr>    
                                <tr>
                                    <td><b>Media</b></td>
                                    <td><b>11</b></td>
                                </tr>    
                                <tr>
                                    <td><b>Policies</b></td>
                                    <td><b>6</b></td>
                                </tr>    
                                @foreach($isp_content_type_array as $key => $value)
                                    <tr>
                                        <td><b>{{$key}}</b></td>
                                        <td><b>{{$value}}</b></td>
                                    </tr>            
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABLE FOR MOST VIEWED CONTENT --}}

        <div class="{{request()->withISP == null ? 'col-md-6 col-sm-12 d-flex align-items-stretch' : 'col-md-4 col-sm-12 d-flex align-items-stretch' }}">
            <div class="card" >
                <div class="card-header" style="background-color:lightgray">
                    <i class="fas fa-chart-line"></i> 
                    <b>
                    MOST VIEWED {{request()->withISP != null ? strtoupper(request()->withISP) . " RELATED" : "" }}
                    CONTENT <br>
                    </b>
                    <small class="font-weight-bold">{{request()->withISP != null ? request()->withISP . " related" : "" }} Content with the most total views</small>
                </div>
                <div class="card-body">
                    <table class="table data-table tech-table table-hover" style="width:100%; margin: 0">
                        <thead>
                            <tr>
                                <th style = "width:80%">Title</th>
                                <th style = "width:20%">Views</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($content_most_views as $contentMostView)
                            <tr>
                                <td>{{$contentMostView->title}}</td>
                                <td>{{$contentMostView->total}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- PIE CHART FOR COMMODITIES WITH MOST VIEWS --}}

        <div class="col-md-3 col-sm-12" style="{{request()->withISP != null ? 'display: none;' : ''}}">
            <div class="card">
                <div class="card-header" style="background-color:lightgray">
                    <i class="fas fa-chart-line"></i><b> COMMODITIES </b><br>
                    <small class="font-weight-bold">Commodities with the most views</small>
                </div>
                <div class="card-body">
                    <canvas id="most_popular_commodities" style="height:310px !important;"></canvas>
                </div>
            </div>
        </div>

        {{-- PIE CHART FOR FOR ISP WITH MOST VIEWS --}}

        <div class="col-md-3 col-sm-12" style="{{request()->withISP != null ? 'display: none;' : ''}}">
            <div class="card">
                <div class="card-header" style="background-color:lightgray">
                    <i class="fas fa-chart-line"></i><b> ISPs </b><br>
                    <small class="font-weight-bold">ISPs with the most views</small>
                </div>
                <div class="card-body">
                    <canvas id="most_popular_isps" style="height:310px !important;"></canvas>
                </div>
            </div>
        </div>

        {{-- 4 COLORED BOXES FOR CONTENTS TOTAL --}}

        <div class="col-sm-6" style="{{request()->withISP != null ? 'display: none;' : ''}}">
            <div class="row">

                {{-- AANR CONTENT BOX --}}

                <div class="col-sm-6">
                    <div class="card text-center">
                        <div class="card-header" style="text-align:left;background-color:lightgray" >
                            <span><i class="fas fa-search"></i><b> CONTENT </b></span><br>
                            <small class="font-weight-bold">Total number of AANR Content</small>
                        </div>
                        <div class="card-body" style="height:150px; background-color:rgb(40,109,158)">
                            <span style="font-size:4.5rem; color:white; line-height:1.25">
                                <b>{{$total_content->count()}}</b>
                            </span>
                            <h4 class="text-white" style="">
                                AANR Content
                            </h4>
                        </div>
                    </div>
                </div>

                {{-- AGRICULTURAL TECHNOLIGIES BOX --}}

                <div class="col-sm-6">
                    <div class="card text-center">
                        <div class="card-header" style="text-align:left;background-color:lightgray" >
                            <span><i class="fas fa-search"></i><b> CONTENT </b></span><br>
                            <small class="font-weight-bold">Total number of Agricultural Technologies</small>
                        </div>
                        <div class="card-body" style="height:150px; background-color:rgb(247,186,6)">
                            <span style="font-size:4.5rem;color:white; line-height:1.25">
                                <b>{{$merged_artifact_industry[0]->total}}</b>
                            </span>
                            <h4 class="text-white">
                                Agricultural Technologies
                            </h4>
                        </div>
                    </div>
                </div>

                {{-- AQUATIC RESOURCES BOX --}}

                <div class="col-sm-6">
                    <div class="card text-center">
                        <div class="card-header" style="text-align:left;background-color:lightgray" >
                            <span><i class="fas fa-search"></i><b> CONTENT </b></span><br>
                            <small class="font-weight-bold">Total number of Aquatic Resources</small>
                        </div>
                        <div class="card-body" style="height:150px; background-color:rgb(58,136,235)">
                            <span style="font-size:4.5rem;color:white; line-height:1.25">
                                <b>{{$merged_artifact_industry[0]->total}}</b>
                            </span>
                            <h4 class="text-white">
                                Aquatic Resources
                            </h4>
                        </div>
                    </div>
                </div>

                {{-- NATURAL RESOURCES BOX --}}

                <div class="col-sm-6">
                    <div class="card text-center">
                        <div class="card-header" style="text-align:left;background-color:lightgray" >
                            <span><i class="fas fa-search"></i><b> CONTENT </b></span><br>
                            <small class="font-weight-bold">Total number of Natural Resources</small>
                        </div>
                        <div class="card-body" style="height:150px; background-color:rgb(60,193,114)">
                            <span style="font-size:4.5rem; color:white; line-height:1.25">
                                <b>{{$merged_artifact_industry[0]->total}}</b>
                            </span>
                            <h4 class="text-white">
                                Natural Resources
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- VERTICAL GRAPH FOR MOST SEARCHED TERMS --}}

        <div class="{{request()->withISP == null ? 'col-md-6 col-sm-12 d-flex align-items-stretch' : 'col-md-5 col-sm-12 d-flex align-items-stretch' }}">
            <div class="card" style="width: 100%">
                <div class="card-header" style="background-color:lightgray">
                    <i class="fas fa-chart-bar"></i><b> TERMS </b><br>
                    <small class="font-weight-bold">
                        <span class="{{request()->filter == 'yes' ? 'font-weight-bold' : ''}}">
                            Most searched terms {{request()->filter=='yes' 
                            ? 'from '.Carbon::parse(request()->from)->format('M d, Y').' to '.Carbon::parse(request()->to)->format('M d, Y') 
                            : 'in the last 30 days.'
                            }}
                        </span>
                    </small>
                </div>
                <div class="card-body">
                    <canvas id="most_popular_topics" style="height:355px !important;"></canvas>
                </div>
             </div>
        </div>

        {{-- TABLE FOR ISP SPECIFIC CONTENT --}}

        <div class="col-sm-12" style="{{request()->withISP == null ? 'display: none;' : ''}} ">
            <div class="card">
                <div class="card-header" style="border-bottom: 0; height:67; background-color:lightgray">
                    <i class="fas fa-folder-open"></i>
                    <b>
                        AANR CONTENT LIST FOR ISP {{strtoupper(request()->withISP)}}
                    </b><br>
                    <small class="font-weight-bold">Every available contents related to ISP Cacao</small>
                </div>
                <div class="card-body aanr-content" style="padding-top:0">
                    <table class="table data-table-options tech-table table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th style="width:5%">ID</th>
                                <th style="width:35%">Title</th>
                                <th style="width:10%">Content Type</th>
                                <th style="width:10%">Date Published</th>
                                <th style="width:25%">Author</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($isp_content != null)
                                @foreach($isp_content as $artifact)
                                    @if($artifact->isp->get('0'))
                                        @if($artifact->isp->get('0')->name == request()->withISP) 
                                            <tr>
                                                <td>{{$artifact->id}}</td>
                                                <td>{{$artifact->title}}</td>
                                                <td>{{$artifact->content ? $artifact->content->type : ''}}</td>
                                                <td>{{$artifact->date_published}}</td>
                                                <td>{{$artifact->author}}</td>
                                            </tr>            
                                        @endif
                                    @endif
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- TABLE FOR MOST VIEWED PAGE --}}

        <div class="col-md-6 col-sm-12 d-flex align-items-stretch" style="{{request()->withISP != null ? 'display: none !important;' : ''}}">
            <div class="card">
                <div class="card-header" style="background-color:lightgray">
                    <i class="fas fa-chart-line"></i> 
                    <b>
                    MOST VIEWED {{request()->withISP != null ? strtoupper(request()->withISP) . " RELATED" : "" }}
                    PAGES 
                    </b><br>
                    <small class="font-weight-bold">Content with the most total views</small>
                </div>
                <div class="card-body">
                    <table class="table data-table tech-table table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th style = "width:30%">Title</th>
                                <th style = "width:55%">URL</th>
                                <th style = "width:15%">Views</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($analytics_most_visited_pages->take(7) as $page)
                                <tr>
                                    <td>{{$page['pageTitle']}}</td>
                                    <td>{{$page['url']}}</td>
                                    <td>{{$page['pageViews']}}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- LINE CHART FOR PAGES/SESSION  --}}

        <div class="col-md-6 col-sm-12" style="{{request()->withISP != null ? 'display: none;' : ''}}">
            <div class="card">
                <div class="card-header" style="background-color:lightgray">
                    <i class="fas fa-chart-line"></i> 
                    <b>
                        OTHER STATISTICS
                    </b><br>
                    <small class="font-weight-bold">Bounce Rate, Pages per Session, and Average Duration per Session during the specified period.</small>
                </div>
                <div class="card-body">
                    <canvas id="trend_line" style="height:355px;"></canvas>
                </div>
                <div class="card-footer text-center">
                    **Average Duration per Session is in minutes.**
                </div>
            </div>
        </div>
        
    </div>
    <div class="text-center mt-3">
        <a href="{{ url('/analytics/search/save') }}" class="btn btn-info">Save Page as PDF</a>
    </div>  
</div>
@endsection

<?php
    $barGraph_Labels = array_keys($freq_array);
    $barGraph_DataSets = array_values($freq_array);
    $barGraph_y_max = max(array_values($freq_array)) + max(array_values($new_user_freq_array)) + 3;  

    $bar_graph_title = "No. of site visitors";
    $search_term_title = "Most searched terms";


    if(request()->withISP != null) {
        $barGraph_y_max = max(array_values($freq_array)) + 3;  
        $bar_graph_title = "No. of searches related to ISP " . request()->withISP;
        $search_term_title = "Most searched terms related to ISP " . request()->withISP;
    }

?>
@section('scripts')
<script>
    let daily_visitors = new Chart(document.getElementById('daily_visitors').getContext('2d'), {
        type:'bar',
        data:{
            labels: @php echo json_encode($barGraph_Labels); @endphp,
            datasets:[
                {
                    label: 'Returning Visitors',
                    data: @php echo json_encode($barGraph_DataSets); @endphp,
                    backgroundColor:[
                        'rgb(0,0,128)'
                    ],
                    hoverBorderWidth:3,
                    hoverBorderColor:'#39FF14'
                },
                {
                    label: "New Visitors",
                    data: @php echo json_encode($new_user_freq_array); @endphp,
                    backgroundColor: [
                        'rgb(59,155,207)'
                    ],
                    hoverBorderWidth:3,
                    hoverBorderColor:'#39FF14'
                },
            ]
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
                        stepSize: 1,
                    },
                    max: @php echo $barGraph_y_max @endphp,
                    stacked: true,
                },
                xAxes: {
                    stacked: true,
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
                hoverBorderColor:'#39FF14'
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
                hoverBorderColor:'#39FF14'
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
            labels: @php echo json_encode(array_slice(array_keys($search_query_freq_array), 0, 10)); @endphp,
            datasets:[{
                data: <?php echo json_encode(array_slice(array_values($search_query_freq_array), 0, 10));?>,
                backgroundColor:[
                    'rgba(89, 233, 112, 1)',
                    'rgba(123, 155, 76, 1)',
                    'rgba(154, 125, 98, 1)',
                    'rgba(95, 104, 222, 1)',
                    'rgba(150, 216, 2, 1)',
                ],
                hoverBorderWidth:3,
                hoverBorderColor:'#39FF14'
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
                            value = @php echo json_encode(array_slice(array_keys($search_query_freq_array), 0, 10)); @endphp;
                            const vw = Math.max(document.documentElement.clientWidth || 0, window.innerWidth || 0)
                            if ((vw/30) < value[index].length) {
                                return value[index].substr(0, (vw/30)) , " ...";
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
                    text:  '@php echo $search_term_title; @endphp',
                }
            }
        }
    });
    let daily_searches = new Chart(document.getElementById('daily_searches').getContext('2d'), {
        type:'bar',
        data:{
            labels: @php echo json_encode($barGraph_Labels); @endphp,
            datasets:[{
                    label: "Searches",
                    data: @php echo json_encode($barGraph_DataSets); @endphp,
                    backgroundColor:[
                        'rgb(59,155,207)'
                    ],
                    hoverBorderWidth:3,
                    hoverBorderColor:'#39FF14'
            }]
        },
        options:{
            legend: {
                display: false
            },
            plugins: {
                title: {
                    display: true,
                    text: @php echo json_encode($bar_graph_title); @endphp,
                },
            },
            maintainAspectRatio: false,
            responsive:true,
            scales: {
                yAxes:{
                    display: true,
                    ticks: {
                        beginAtZero: true,
                        stepSize: 1,
                    },
                    max: @php echo $barGraph_y_max @endphp,
                },
            }
        }
    });

    let pie_user_types = new Chart(document.getElementById('pie_user_types').getContext('2d'), {
        type:'doughnut',
        data:{
            labels: [
                'Returning Visitors',
                'New Visitors',
            ],
            datasets:[{
                data: [
                    @php echo $analytics_user_types->totalsForAllResults['ga:newUsers']; @endphp,
                    @php echo $analytics_user_types->totalsForAllResults['ga:users']; @endphp,
                ],
                backgroundColor:[
                    'rgb(0,0,128)', 
                    'rgb(59,155,207)',
                ],
                hoverBorderWidth:3,
                hoverBorderColor:'#39FF14'
            }]
        },
        options:{
            legend: {
                display: false
            },
            tooltips: {
                enabled: false
            },
            maintainAspectRatio: false,
            responsive:true,
        }
    });

    const labels = @php echo json_encode($barGraph_Labels); @endphp;
    const data = {
        labels: labels,
        datasets: [
            {
                label: 'Pages per Session',
                data: @php echo json_encode($pages_per_session);@endphp,
                borderColor: 'rgb(0,0,128)',
            },
            {
                label: 'Average Duration per Session',
                data: @php echo json_encode($avg_duration_session); @endphp,
                borderColor: 'rgb(59,155,207)',
            },
            {
                label: 'Bounce Rate',
                data: @php echo json_encode($bounce_rate); @endphp,
                borderColor: 'rgb(59,0,207)',
            }
        ]
    };
    const config = {
        type: 'line',
        data: data,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
            } 
        },
    };

    const myChart = new Chart(
        document.getElementById('trend_line'),
        config
    );


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

    table {
        border-spacing: 0px;
        table-layout: fixed;
        margin-left: auto;
        margin-right: auto;
    }

    .aanr-content {
        height: 510px;
        overflow-y: scroll;
        margin-top: 0;
    }

    th {
        background: white;
        position: sticky;
        top: -1;
    }
</style>