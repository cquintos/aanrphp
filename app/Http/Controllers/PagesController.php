<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Redirect;
use App\Advertisement;
use App\Agenda;
use App\APIEntries;
use App\AANRPage;
use App\Agrisyunaryo;
use App\Announcement;
use App\ArtifactAANR;
use App\Commodity;
use App\CommoditySubtype;
use App\Consortia;
use App\ConsortiaMember;
use App\Content;
use App\ContentSubtype;
use App\Contributor;
use App\Country;
use App\FooterLink;
use App\Headline;
use App\HeaderLink;
use App\Industry;
use App\ISP;
use App\LandingPageElement;
use App\LandingPageSlider;
use App\Log;
use App\PageViews;
use App\PCAARRDPage;
use App\SearchQuery;
use App\Sector;
use App\SocialMediaSticky;
use App\Subscriber;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\Browsershot\Browsershot;
use Elastic\Elasticsearch\ClientBuilder;
use Stevebauman\Location\Facades\Location;

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

    // if(Auth::user()->role != 5){
    //     return Redirect::route('userDashboard')->with('error','Admin users only.');
    // }
    public function searchAnalytics(){
        return view('analytics.search');
    }

    public function searchAnalyticsWithFilter(Request $request){
        if($request->year_from_filter > $request->year_to_filter) {
            return Redirect::route('searchAnalytics')->with('error','Start date cannot be after end date.');
        }

        if($request->isp_filter == 'selected') {
            return redirect('/analytics/search?from='.$request->year_from_filter.'&to='.$request->year_to_filter.'&filter=yes');
        }

        return redirect('/analytics/search?from='.$request->year_from_filter.'&to='.$request->year_to_filter.'&withISP='.$request->isp_filter.'&filter=yes');
    }

    public function saveAnalytics(){
        $file = Browsershot::url('http://km4aanr.pcaarrd.dost.gov.ph/analytics/search')
            ->noSandbox()->landscape()->showBrowserHeaderAndFooter()
            ->windowSize(1920, 1080)->scale(0.75)->pdf();
        
        return response()->stream(function() use ($file) { echo $file; }, 
                200, [
                    'Content-Type' => 'pdf',
                    'Content-Disposition' => 'attachment; filename=aanr_analytics'.Carbon::now()->format('dmy').'.pdf',
                ]); 
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
        $pageView->user_id = 0;

        if(Auth::user()){
            $pageView->user_id = \Auth::user()->id;
        }
        
        $pageView->ip = \Request::getClientIp();
        $pageView->agent = \Request::header('User-Agent');
        $pageView->save();

        return view('pages.index');
    }

    public function artifactEdit($content_id){
        if(Auth::user()->role != 5){
            return Redirect::route('userDashboard')->with('error','Admin users only.');
        }

        return view('pages.artifactEdit', [
            'artifact' => ArtifactAANR::find($content_id),
            'consortia' => Consortia::pluck('short_name', 'id')->all(),
            'content' => Content::pluck('type', 'id')->all(),
            'content_subtype' => ContentSubtype::pluck('name', 'id')->all(),
            'isp' => ISP::pluck('name', 'id')->all(),
            'commodities' => Commodity::orderBy('name')->pluck('name', 'id')->all(),
            'subcommodities' => CommoditySubtype::pluck('name', 'id')->all(),
        ]);
    }

    public function artifactView($content_id){
        return view('pages.artifactView', [
            'content' => Content::pluck('type', 'id')->all(),
            'content_subtype' => ContentSubtype::all(),
            'consortia' => Consortia::pluck('short_name', 'id')->all(),
            'isp' => ISP::pluck('name', 'id')->all(),
            'commodities' => Commodity::pluck('name', 'id')->all(),
            'artifact' => ArtifactAANR::find($content_id),
        ]);
    }

    public function artifactUpload() {
        return view('pages.artifactUpload', [
            'contents' => Content::pluck('type', 'id')->all(),
            'content_subtype' => ContentSubtype::all(),
            'consortia' => Consortia::all(),
            'isp' => ISP::pluck('name', 'id')->all(),
            'commodities' => Commodity::pluck('name', 'id')->all(),
            'consortia_members' => ConsortiaMember::pluck('name', 'id')->all(),
            'commodity_subtypes' => CommoditySubtype::pluck('name', 'id')->all(),
        ]);
    }

    public function search(Request $request){
        $results = ArtifactAANR::query();
        $query = $request->search;

        if($query != null){
            $search_query = new SearchQuery;
            $search_query->query = $request->search;
            $locationData = \Location::get($request->ip());
            $search_query->location = null;
            
            if($locationData != null && $locationData->countryCode == 'PH'){
                $search_query->location = $locationData->regionName;
            }

            $search_query->save();
        }

        if($request->content_type && $request->content_type != 'all'){
            $results = $results->where('content_id', $request->content_type);
        }

        if($request->consortia){
            $results = $results->where('consortia_id', $request->consortia);
        }

        if($request->start && $request->end){
            $startDate = Carbon::createFromFormat('d/m/Y', '01/01/'.$request->start);
            $endDate = Carbon::createFromFormat('d/m/Y', '01/01/'.$request->end);
            $results = $results->whereBetween('date_published', array($startDate, $endDate));
        }

        if($request->is_gad){
            $results->where('is_gad', '=', 1);
        }

        return view('pages.search', [
            'query' => $query,
            'results' => $results->search($query)->paginate(10),
        ]);
    }

    public function advancedSearch(Request $request){
        $results = ArtifactAANR::all()->get()->whereHas('consortia', function($q) {
            $q->where('short_name', 'like', '%' . 'STAARRDEC' . '%');
        })->search($request->search)->paginate(10);
    }

    public function agrisyunaryo(Request $request){
        if($request->letter){
            return view('pages.agrisyunaryo', ['agrisyunaryos' => Agrisyunaryo::where('title','LIKE',$request->letter.'%')->paginate(10)]);
        }

        return view('pages.agrisyunaryo', ['agrisyunaryos' => Agrisyunaryo::paginate(10)]);
    }

    public function agrisyunaryoSearch(Request $request){
        return view('pages.agrisyunaryoSearch', [
            'query' => $request->search,
            'results' => Agrisyunaryo::where('title','LIKE','%'.$request->search.'%')->paginate(10),
        ]);
    }

    public function consortiaAboutPage(){
        return view('pages.consortiaAboutPage', [
            'consortia' => Consortia::pluck('short_name', 'id')->all(),
            'content' => Content::pluck('type', 'id')->all(),
            'content_subtype' => ContentSubtype::all(),
            'industries' => Industry::pluck('name', 'id')->all(),
            'artifactAANR' => ArtifactAANR::where('is_agrisyunaryo', '=', 0)->get(),
        ]);
    }

    public function consortiaLandingPage(){
        return view('pages.consortiaLandingPage', [
            'consortia' => Consortia::pluck('short_name', 'id')->all(),
            'content' => Content::pluck('type', 'id')->all(),
            'content_subtype' => ContentSubtype::all(),
            'industries' => Industry::pluck('name', 'id')->all(),
            'artifactAANR' => ArtifactAANR::where('is_agrisyunaryo', '=', 0)->get(),
        ]);
    }

    public function AANRAboutPage(){
        return view('pages.AANRAboutPage', ['consortia' => Consortia::pluck('short_name', 'id')->all()]);
    }

    public function PCAARRDAboutPage(){
        return view('pages.PCAARRDAboutPage', ['consortia' => Consortia::pluck('short_name', 'id')->all()]);
    }

    public function unitAboutPage(){
        return view('pages.unitAboutPage', ['consortia' => Consortia::pluck('short_name', 'id')->all()]);
    }

    public function dashboardAdmin(){
        if(Auth::user()->role != 5){
            return Redirect::route('userDashboard')->with('error','Admin users only.');
        }

        return view('dashboard.admin', [
            'aanrPage' => AANRPage::first(),
            'agendas' => Agenda::get(),
            'agrisyunaryos' => Agrisyunaryo::get(),
            'api_entries' =>  APIEntries::get(),
            'artifactAANR' => ArtifactAANR::where('is_agrisyunaryo', '=', 0)->with('content', 'content_subtype', 'consortia')->get(),
            'commodities' => Commodity::with('subtypes', 'industry')->get(),
            'consortia' => Consortia::with('consortia_members')->get(),
            'countries' => Country::get(),
            'contents' => Content::with('content_subtypes')->get(),
            'content_subtypes' => ContentSubtype::with('content')->get(),
            'consortia_members' => ConsortiaMember::get(),
            'footer_links' => FooterLink::get(),
            'headlines' => Headline::get(),
            'header_links' => HeaderLink::get(),
            'isps' => ISP::get(),
            'industries' => Industry::with('sectors')->get(),
            'landing_page' => LandingPageElement::find(1),
            'landing_page_sliders' => LandingPageSlider::get()->sortBy('id'),
            'logs' => Log::orderBy('id', 'desc')->get(),
            'pcaarrdPage' => PCAARRDPage::first(),
            'sectors' => Sector::with('industry', 'isps')->get(),
            'social_media' => SocialMediaSticky::get(),
            'users_all' => User::get(),
        ]);
    }

    public function getManagePage(){
        if(Auth::user()->role != 5){
            return Redirect::route('userDashboard')->with('error','Admin users only.');
        }

        return view('pages.manage');
    }

    public function userDashboard(){
        return view('dashboard.userDashboard', [
            'advertisements' => Advertisement::get(),
            'agendas' => Agenda::get(),
            'announcements' => Announcement::get(),
            'artifactAANR' => ArtifactAANR::where('is_agrisyunaryo', '=', 0)->get(),
            'consortia' => Consortia::with('consortia_members')->get(),
            'consortia_members' => ConsortiaMember::get(),
            'content' => Content::get(),
            'content_subtype' => ContentSubtype::get(),
            'contributors' => Contributor::get(),
            'countries' => Country::get(),
            'isps' => ISP::groupBy('name')->get(),
            'sectors' => Sector::get(),
            'industries' => Industry::get(),
            'commodities' => Commodity::groupBy('name')->get(),
            'subscribers' => Subscriber::get(),
        ]);
    }

    public function communityPage() {
        if(Auth::user() && Auth::user()->hasVerifiedEmail()) {
            return Redirect::away('http://community.pcaarrd.dost.gov.ph/moLogin');
        } 

        return Redirect::away('http://community.pcaarrd.dost.gov.ph/');
    }
}
