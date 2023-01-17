<?php

namespace App\Listeners;

use App\Mail\SendInitialNewsletterClass;
use App\Mail\SendInitialMailClass;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Http;
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
        $compiled_featured_artifacts = collect();

        Http::post('https://community.pcaarrd.dost.gov.ph/user/register?_format=json', [
            "name" => ["value" => $user->first_name],
            "mail" => ["value" => $user->email],
            "pass" => ["value" => $user->password]
        ]);

        if(!$user->subscribed) {
            $details = [
                'title' => 'Email Confirmed!',  
                'body' => 'Thank you for confirming your email! '
            ];
    
            Mail::to($user->email)->send(new SendInitialMailClass($details));
            return;
        }

        if($user->is_organization_other != 1){
            $user_consortia_id = \App\Consortia::where('short_name', '=', $user->organization)->first()->id;
            $compiled_artifacts = \App\ArtifactAANR::where('consortia_id', '=', $user_consortia_id)->get();
        }

        //get all relevant consortia from the user's interest
        $consortia_ids = \App\Consortia::select('id')->whereIn('short_name', json_decode($user->interest))->get();
        $compiled_artifacts = $compiled_artifacts->merge(\App\ArtifactAANR::whereIn('consortia_id',$consortia_ids->pluck('id'))->get());
        
        //get all relevant commodities from the user's interest
        $commodities_ids = \App\Commodity::select('id')->whereIn('name', json_decode($user->interest))->get();
        $artifact_ids = DB::table('artifactaanr_commodity')->select('artifactaanr_id')->whereIn('commodity_id', $commodities_ids->pluck('id'))->get();
        
        //get all relevant ISP from the user's interest
        $isp_ids = \App\ISP::select('id')->whereIn('name', json_decode($user->interest))->get();
        $artifact_ids = $artifact_ids->merge(DB::table('artifactaanr_isp')->select('artifactaanr_id')->whereIn('isp_id', $isp_ids->pluck('id'))->get());
        
        $compiled_artifacts = $compiled_artifacts->merge(\App\ArtifactAANR::whereIn('id',$artifact_ids->pluck('artifactaanr_id'))->get());

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
    }
}
