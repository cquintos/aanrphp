<?php

namespace App\Jobs;

use App\Mail\QuarterlyMailClass;
use App\Mail\WelcomeMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use DB;

class QuarterlyMailJob implements ShouldQueue
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

    public function getFromDB($interest, $compiled) {
        $temp = collect();
        $from = now()->subMonths(1)->subYear()->firstOfQuarter();
        $to = now()->subMonths(1)->subYear()->endOfQuarter();
        
        //get all relevant consortia from the user's interest
        $consortia_ids = \App\Consortia::select('id')->where('short_name', $interest)->get();
        $temp = $temp->merge(\App\ArtifactAANR::whereIn('consortia_id',$consortia_ids->pluck('id'))
                            ->whereNotIn('id', $compiled)
                            ->whereBetween('date_published', [$from, $to])
                            ->orderByDesc('date_published')->take(3)->get());
        
        //get all relevant commodities from the user's interest
        $commodities_ids = \App\Commodity::select('id')->where('name', $interest)->get();
        $artifact_ids = DB::table('artifactaanr_commodity')
                            ->join('artifactaanr', 'artifactaanr_id', '=', 'artifactaanr.id')
                            ->select('artifactaanr_id')
                            ->whereIn('commodity_id', $commodities_ids->pluck('id'))
                            ->whereNotIn('artifactaanr_id', $compiled)
                            ->whereBetween('date_published', [$from, $to])
                            ->orderByDesc('artifactaanr.date_published')
                            ->take(3)->get();
        
        //get all relevant ISP from the user's interest
        $isp_ids = \App\ISP::select('id')->where('name', $interest)->get();
        $artifact_ids = $artifact_ids->merge(DB::table('artifactaanr_isp')
                                    ->join('artifactaanr', 'artifactaanr_id', '=', 'artifactaanr.id')
                                    ->select('artifactaanr_id')
                                    ->whereIn('isp_id', $isp_ids->pluck('id'))
                                    ->whereNotIn('artifactaanr_id', $compiled)
                                    ->whereBetween('date_published', [$from, $to])
                                    ->orderByDesc('artifactaanr.date_published')
                                    ->take(3)->get());
        
        return $temp->merge(\App\ArtifactAANR::whereIn('id',$artifact_ids->pluck('artifactaanr_id'))->take(3)->get());
    }

    public function getInterest($user) {

        
        $compiled = collect();

        foreach(json_decode($user->interest) as $interest) {
            //Checker for repetitions
            if(isset($artifacts[$interest])) {
                continue;
            }
            
            $artifacts[$interest] = $this->getFromDB($interest, $compiled);  
            
            // Checker for interests with null content
            if(count($artifacts[$interest])==0) {
                unset($artifacts[$interest]);
                continue;
            }

            $compiled = $compiled->merge($artifacts[$interest]->pluck('id'));
        }

        $details = [
            'interests' => array_keys($artifacts),
            'contents' => $artifacts
        ];

        return $details;
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
            Mail::to($user->email)->send(new QuarterlyMailClass($this->getInterest($user)));
        }
    }
}
