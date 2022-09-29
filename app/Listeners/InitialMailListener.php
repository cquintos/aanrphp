<?php

namespace App\Listeners;

use App\Mail\SendInitialNewsletterClass;
use App\Mail\SendInitialMailClass;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\Verified;
use DB;

class InitialMailListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $user = $event->user;
        echo $user;

        if($user->subscribed && $user != null) {
            $compiled_featured_artifacts = collect();

            //get all consortia related artifacts
            if($user->organization != null && $user->is_organization_other != 1){
                $user_consortia_id = \App\Consortia::where('short_name', '=', $user->organization)->first()->id;
                $organization_artifacts = \App\ArtifactAANR::where('consortia_id', '=', $user_consortia_id)->get();
                foreach($organization_artifacts as $organization_artifact){
                    $compiled_featured_artifacts->push($organization_artifact);
                }
            }
            if(!empty($user->interest) && $user->interest != "null" && $user->interest != "NULL"){
                //get all relevant consortia from the user's interest
                $all_consortia_interest = \App\Consortia::whereIn('short_name', json_decode($user->interest))->get();
                $consortia_interest_array = array();
                foreach($all_consortia_interest as $consortium_interest){
                    array_push($consortia_interest_array, $consortium_interest->id);
                }
                $consortia_interest_artifacts = \App\ArtifactAANR::whereIn('consortia_id',$consortia_interest_array)->get();
                foreach($consortia_interest_artifacts as $consortia_interest_artifact){
                    $compiled_featured_artifacts->push($consortia_interest_artifact);
                }

                //get all relevant commodities from the user's interest
                $all_commodities_interest = \App\Commodity::whereIn('name', json_decode($user->interest))->get();
                $commodity_interest_array = array();
                foreach($all_commodities_interest as $commodity_interest){
                    array_push($commodity_interest_array, $commodity_interest->id);
                }
                $all_artifactaanr_commodity_query = DB::table('artifactaanr_commodity')->whereIn('commodity_id', $commodity_interest_array)->get();
                $all_artifactaanr_commodity_idarray = array();
                foreach($all_artifactaanr_commodity_query as $artifactaanr_commodity_query){
                    array_push($all_artifactaanr_commodity_idarray, $artifactaanr_commodity_query->artifactaanr_id);
                }
                $commodity_interest_artifacts = \App\ArtifactAANR::whereIn('id',$all_artifactaanr_commodity_idarray)->get();
                foreach($commodity_interest_artifacts as $commodity_interest_artifact){
                    $compiled_featured_artifacts->push($commodity_interest_artifact);
                }

                //get all relevant ISP from the user's interest
                $all_isps_interest = \App\ISP::whereIn('name', json_decode($user->interest))->get();
                $isp_interest_array = array();
                foreach($all_isps_interest as $isp_interest){
                    array_push($isp_interest_array, $isp_interest->id);
                }
                $all_artifactaanr_isp_query = DB::table('artifactaanr_isp')->whereIn('isp_id', $isp_interest_array)->get();
                $all_artifactaanr_isp_idarray = array();
                foreach($all_artifactaanr_isp_query as $artifactaanr_isp_query){
                    array_push($all_artifactaanr_isp_idarray, $artifactaanr_isp_query->artifactaanr_id);
                }
                $isp_interest_artifacts = \App\ArtifactAANR::whereIn('id',$all_artifactaanr_isp_idarray)->get();
                foreach($isp_interest_artifacts as $isp_interest_artifact){
                    $compiled_featured_artifacts->push($isp_interest_artifact);
                }
            }

            $id_array = collect();
            $result = collect();
            
            foreach($compiled_featured_artifacts as $artifacts) {
                $id_array->push($artifacts->id);
            }
    
            //content with most hits as priority
            $content_most_views = DB::table('artifactaanr_views')
                            ->select('id_artifact as id', 'title', DB::raw('count(*) as total'))
                            ->whereIn('id_artifact', $id_array)
                            ->groupBy('title')
                            ->orderByDesc('total')
                            ->take(5)
                            ->get();
    
            //newest related content as priority
            $content_latest = DB::table('artifactaanr')
                            ->select('id', 'title')
                            ->whereIn('id', $id_array)
                            ->orderByDesc('date_published')
                            ->take(5)
                            ->get();
    
            //newest unrelated content as fall back if both above are null
            $content_latest_unrelated = DB::table('artifactaanr')
                            ->select('id', 'title')
                            ->orderByDesc('date_published')
                            ->take(5)
                            ->get();
    
            $results = $content_most_views->merge($content_latest)->unique('id');
            $results = $result->merge($content_latest_unrelated)->unique('id')->take(3);
            $result_ids = collect();
            $result_titles = collect();

            foreach ($results as $content) {
                $result_ids->push($content->id);
                $result_titles->push($content->title);
            }
            
            $details = $result_ids->combine($result_titles);

            Mail::to($event->user->email)->send(new SendInitialNewsletterClass($details));
        } else {
            $details = [
                'title' => 'Email Confirmed!',  
                'body' => 'Thank you for confirming your email! '
            ];

            Mail::to($event->user->email)->send(new SendInitialMailClass($details));
        }
    }
}
