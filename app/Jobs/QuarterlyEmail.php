<?php

namespace App\Jobs;

use App\Mail\DigestMail;
use App\Mail\WelcomeMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use DB;

class QuarterlyEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function getInterest($user) {
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
        $from = now()->addDays(4)->subMonths(1)->subYear()->firstOfQuarter();
        $to = now()->addDays(4)->subMonths(1)->subYear()->endOfQuarter();

        foreach($compiled_featured_artifacts as $artifacts) {
            $id_array->push($artifacts->id);
        }

        //newest related content as priority
        $content_latest = DB::table('artifactaanr')
                        ->select('id')
                        ->whereIn('id', $id_array)
                        ->whereBetween('date_published', [$from, $to])
                        ->orderByDesc('date_published')
                        ->get();

        //newest unrelated content as fall back if both above are null
        // $content_latest_unrelated = DB::table('artifactaanr')
        //                 ->select('id')
        //                 ->orderByDesc('date_published')
        //                 ->take(5)
        //                 ->get();

        // $result = $content_latest->merge($content_latest_unrelated)->unique('id');
        $result_ids = collect();

        foreach ($content_latest as $content) {
            $result_ids->push($content->id);
        }
        
        return $result_ids;
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $subscribers = DB::table('users')
                        ->select('*')
                        ->where('subscribed', '=', '1')
                        ->get();
        
        foreach($subscribers as $user) {
            echo $user->email;

            $details = [
                'title' => 'Quarterly set of digest from '.now()->subMonths(3)->format('F')." to ".now()->subMonths(1)->format('F'),
                'body' => 'Here is what we got for this quarter for you: ',
                'ids' => $this->getInterest($user)
            ];

            Mail::to($user->email)->send(new DigestMail($details));
        }
    }
}
