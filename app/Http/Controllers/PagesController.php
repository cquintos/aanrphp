<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Industry;
use App\Advertisement;
use App\Agenda;
use App\Announcement;
use App\ArtifactAANR;
use App\Content;
use App\ContentSubtype;
use App\Contributor;
use App\ConsortiaMember;
use App\ISP;
use App\Sector;
use App\Commodity;
use App\Consortia;
use App\Subscriber;
use App\Agrisyunaryo;
use App\SearchQuery;
use App\PageViews;
use Auth;
use DB;
use Redirect;
use Carbon\Carbon;
use Stevebauman\Location\Facades\Location;
use Spatie\Browsershot\Browsershot;
use Elastic\Elasticsearch\ClientBuilder;

// This file contains application logic on how to view each page.
// functions included are:
//     industryProfileView()
//     aboutUs()
//     usefulLinks()
//     searchAnalytics()
//     searchAnalyticsWithFilter(Request $request)
//     saveAnalytics()
//     testElastic($age, $name)
//     getLandingPage()
//     contentEdit($content_id)
//     search(Request $request)
//     advancedSearch(Request $request)
//     agrisyunaryo(Request $request)
//     agrisyunaryoSearch(Request $request)
//     consortiaAboutPage()
//     consortiaLandingPage()
//     AANRAboutPage()
//     PCAARRDAboutPage()
//     unitAboutPage()
//     dashboardManage()
//     getManagePage()
//     userDashboard()
//  if(!Auth::check()) checks for registered user
//  if(Auth::user()-role != 5) means the page is restricted to a SUPER ADMIN only

class PagesController extends Controller
{
    public function industryProfileView(){
        return view('pages.industryProfile');
    }

    public function aboutUs(){
        return view('pages.about');
    }

    public function usefulLinks(){
        return view('pages.usefulLinks');
    }

    public function searchAnalytics(){
        if(!Auth::check()){
            return Redirect::route('login')->with('error','Login to access this page.');
        }
        if(Auth::user()->role != 5){
            return Redirect::route('userDashboard')->with('error','Admin users only.');
        }

        return view('analytics.search');
    }

    public function searchAnalyticsWithFilter(Request $request){
        if(!Auth::check()){
            return Redirect::route('login')->with('error','Login to access this page.');
        }
        if(Auth::user()->role != 5){
            return Redirect::route('userDashboard')->with('error','Admin users only.');
        }
        if($request->year_from_filter > $request->year_to_filter) {
            return Redirect::route('searchAnalytics')->with('error','Start date cannot be after end date.');
        }
        if($request->isp_filter == 'selected') {
            return redirect('/analytics/search?from='.$request->year_from_filter.'&to='.$request->year_to_filter.'&filter=yes');
        }
        return redirect('/analytics/search?from='.$request->year_from_filter.'&to='.$request->year_to_filter.'&withISP='.$request->isp_filter.'&filter=yes');
    }

    public function saveAnalytics(){
        if(!Auth::check()){
            return Redirect::route('login')->with('error','Login to access this page.');
        }
        if(Auth::user()->role != 5){
            return Redirect::route('userDashboard')->with('error','Admin users only.');
        }
        $now = Carbon::now();
        $file_name = 'aanr_analytics'. $now->format('dmy').'.pdf';
        $file = Browsershot::url('http://aanr.ph/analytics/search')
            ->noSandbox()
            ->landscape()
            ->showBrowserHeaderAndFooter()
            ->windowSize(1920, 1080)
            ->scale(0.75)
            ->pdf();
        $headers = [
            'Content-Type' => 'pdf',
            'Content-Disposition' => 'attachment; filename='.$file_name,
        ];
        
        return response()->stream(function() use ($file) {
            echo $file;
        }, 200, $headers); 
    }

    public function testElastic($age, $name){
        $client = ClientBuilder::create()->build();	//connect with the client
        $elastic = $this->app->make(App\Elastic\Elastic::class);

        ArtifactAANR::chunk(100, function ($posts) use ($elastic) {
            foreach ($posts as $post) {
                $elastic->index([
                    'index' => 'blog',
                    'type' => 'post',
                    'id' => $post->id,
                    'body' => $post->toArray()
                ]);
            }
        });
    }

    public function getLandingPage(){
        $pageView = new PageViews;
        $pageView->session_id = \Request::getSession()->getId();
        if(Auth::user()){
            $pageView->user_id = \Auth::user()->id;
        } else {
            $pageView->user_id = 0;
        }
        $pageView->ip = \Request::getClientIp();
        $pageView->agent = \Request::header('User-Agent');
        $pageView->save();
        return view('pages.index');
    }

    public function contentEdit($content_id){
        if(!Auth::check()){
            return Redirect::route('login')->with('error','Login to access this page.');
        }
        if(Auth::user()->role != 5){
            return Redirect::route('userDashboard')->with('error','Admin users only.');
        }

        $advertisements = Advertisement::all();
        $content = Content::pluck('type', 'id')->all();
        $content_subtype = ContentSubtype::all();
        $consortia = Consortia::pluck('short_name', 'id')->all();
        $isp = ISP::pluck('name', 'id')->all();
        $commodities = Commodity::pluck('name', 'id')->all();
        $artifact = ArtifactAANR::find($content_id);
        return view('pages.artifactEdit')
            ->withArtifact($artifact)
            ->withConsortia($consortia)
            ->withContent($content)
            ->withContentSubtypes($content_subtype)
            ->withISP($isp)
            ->withCommodities($commodities);
    }

    public function contentView($content_id){
        $content = Content::pluck('type', 'id')->all();
        $content_subtype = ContentSubtype::all();
        $consortia = Consortia::pluck('short_name', 'id')->all();
        $isp = ISP::pluck('name', 'id')->all();
        $commodities = Commodity::pluck('name', 'id')->all();
        $artifact = ArtifactAANR::find($content_id);
        return view('pages.artifactView')
            ->withArtifact($artifact)
            ->withConsortia($consortia)
            ->withContent($content)
            ->withContentSubtypes($content_subtype)
            ->withISP($isp)
            ->withCommodities($commodities);
    }

    public function search(Request $request){
        $query = $request->search;
        if($query != null){
            $search_query = new SearchQuery;
            $search_query->query = $request->search;
            $userIp = $request->ip();
            $locationData = \Location::get($userIp);
            if($locationData){
                if($locationData->countryCode == 'PH'){
                    $search_query->location = $locationData->regionName;
                } else {
                    $search_query->location = null;
                }
            }
            $search_query->save();
        }
        $content_type = $request->content_type;
        $consortia = $request->consortia;
        $start = $request->start;
        $end = $request->end;
        $is_gad = $request->is_gad;
        $results = ArtifactAANR::query();
        if($content_type && $content_type != 'all'){
            $results = $results->where('content_id', $content_type);
        }

        if($consortia){
            $results = $results->where('consortia_id', $consortia);
        }

        if($start && $end){
            $startDate = Carbon::createFromFormat('d/m/Y', '01/01/'.$request->start);
            $endDate = Carbon::createFromFormat('d/m/Y', '01/01/'.$request->end);
            $results = $results->whereBetween('date_published', array($startDate, $endDate));
        }

        if($is_gad){
            $results->where('is_gad', '=', 1);
        }
        $results = $results->search($query)->paginate(10);
        return view('pages.search')
            ->withQuery($query)
            ->withResults($results);
    }

    public function advancedSearch(Request $request){
        $query = $request->search;
        $results = ArtifactAANR::all()->get();

        $results = $results->whereHas('consortia', function($q) {
            $q->where('short_name', 'like', '%' . 'STAARRDEC' . '%');
        })->search($query)->paginate(10);
    }

    public function agrisyunaryo(Request $request){
        if($request->letter){
            $agrisyunaryos = Agrisyunaryo::where('title','LIKE',$request->letter.'%')->paginate(10);
        } else {
            $agrisyunaryos = Agrisyunaryo::paginate(10);
        }
        return view('pages.agrisyunaryo')
            ->withAgrisyunaryos($agrisyunaryos);
    }

    public function agrisyunaryoSearch(Request $request){
        $query = $request->search;
        $results = Agrisyunaryo::where('title','LIKE','%'.$query.'%')->paginate(10);
        return view('pages.agrisyunaryoSearch')
            ->withQuery($query)
            ->withResults($results);
    }

    public function consortiaAboutPage(){
        $consortia = Consortia::pluck('short_name', 'id')->all();
        $industries = Industry::pluck('name', 'id')->all();
        $artifactAANR = ArtifactAANR::where('is_agrisyunaryo', '=', 0)->get();
        $consortia = Consortia::pluck('short_name', 'id')->all();
        $content = Content::pluck('type', 'id')->all();
        $content_subtype = ContentSubtype::all();
        return view('pages.consortiaAboutPage')
            ->withConsortia($consortia)
            ->withContent($content)
            ->withContentSubtypes($content_subtype)
            ->withIndustries($industries)
            ->withArtifactAANR($artifactAANR);
    }

    public function consortiaLandingPage(){
        $consortia = Consortia::pluck('short_name', 'id')->all();
        $industries = Industry::pluck('name', 'id')->all();
        $artifactAANR = ArtifactAANR::all();
        $consortia = Consortia::pluck('short_name', 'id')->all();
        $content = Content::pluck('type', 'id')->all();
        $content_subtype = ContentSubtype::all();
        return view('pages.consortiaLandingPage')
            ->withContent($content)
            ->withContentSubtypes($content_subtype)
            ->withConsortia($consortia)
            ->withIndustries($industries)
            ->withArtifactAANR($artifactAANR);
    }

    public function AANRAboutPage(){
        $consortia = Consortia::pluck('short_name', 'id')->all();
        return view('pages.AANRAboutPage')
            ->withConsortia($consortia);
    }

    public function PCAARRDAboutPage(){
        $consortia = Consortia::pluck('short_name', 'id')->all();
        return view('pages.PCAARRDAboutPage')
            ->withConsortia($consortia);
    }

    public function unitAboutPage(){
        $consortia = Consortia::pluck('short_name', 'id')->all();
        return view('pages.unitAboutPage')
            ->withConsortia($consortia);
    }

    public function dashboardManage(){
        if(!Auth::check()){
            return Redirect::route('login')->with('error','Login to access this page.');
        }
        if(Auth::user()->role != 5){
            return Redirect::route('userDashboard')->with('error','Admin users only.');
        }
        $advertisements = Advertisement::all();
        $agendas = Agenda::all();
        $announcements = Announcement::all();
        $artifactAANR = ArtifactAANR::where('is_agrisyunaryo', '=', 0)->get();
        $content = Content::pluck('type', 'id')->all();
        $content_subtype = ContentSubtype::all();
        $contributors = Contributor::all();
        $consortia = Consortia::pluck('short_name', 'id')->all();
        $isp = ISP::pluck('name', 'id')->all();
        $sectors = Sector::pluck('name', 'id')->all();
        $industries = Industry::pluck('name', 'id')->all();
        $commodities = Commodity::pluck('name', 'id')->all();
        $subscribers = Subscriber::all();
        return view('dashboard.manage')
            ->withAdvertisements($advertisements)
            ->withAgendas($agendas)
            ->withAnnouncements($announcements)
            ->withArtifactAANR($artifactAANR)
            ->withConsortia($consortia)
            ->withContent($content)
            ->withContentSubtypes($content_subtype)
            ->withContributors($contributors)
            ->withISP($isp)
            ->withSectors($sectors)
            ->withIndustries($industries)
            ->withCommodities($commodities)
            ->withSubscribers($subscribers);
    }

    public function getManagePage(){
        if(!Auth::check()){
            return Redirect::route('login')->with('error','Login to access this page.');
        }
        if(Auth::user()->role != 5){
            return Redirect::route('userDashboard')->with('error','Admin users only.');
        }
        
        return view('pages.manage');
    }

    public function userDashboard(){
        if(Auth::check()){
            $advertisements = Advertisement::all();
            $agendas = Agenda::all();
            $announcements = Announcement::all();
            $artifactAANR = ArtifactAANR::where('is_agrisyunaryo', '=', 0)->get();
            $content = Content::pluck('type', 'id')->all();
            $content_subtype = ContentSubtype::all();
            $contributors = Contributor::all();
            $consortia = Consortia::pluck('short_name', 'id')->all();
            $isp = ISP::pluck('name', 'id')->all();
            $sectors = Sector::pluck('name', 'id')->all();
            $industries = Industry::pluck('name', 'id')->all();
            $commodities = Commodity::all();
            $subscribers = Subscriber::all();
            return view('dashboard.userDashboard')
                ->withAdvertisements($advertisements)
                ->withAgendas($agendas)
                ->withAnnouncements($announcements)
                ->withArtifactAANR($artifactAANR)
                ->withConsortia($consortia)
                ->withContent($content)
                ->withContentSubtypes($content_subtype)
                ->withContributors($contributors)
                ->withISP($isp)
                ->withSectors($sectors)
                ->withIndustries($industries)
                ->withCommodities($commodities)
                ->withSubscribers($subscribers);
        } else {
            return Redirect::route('login')->with('error','Login to access this page.');
        }
    }
}
