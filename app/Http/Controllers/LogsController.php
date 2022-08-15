<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Log;
use Response;
use Carbon\Carbon;
use Auth;
use Redirect;

// This file contains request handling logic for Logs.
// functions included are:
//     exportLogs()
//     exportConsortiaLogs()
//
// Auth::check() checks for a registered user
// user()-> role = 5: admin
// 

class LogsController extends Controller
{
    public function exportLogs()
    {
        if (!Auth::check()) {
            return Redirect::route('login')->with('error', 'You have to be logged in to access this page.');
        }
        if (Auth::user()->role != 5) {
            return Redirect::route('userDashboard')->with('error', 'You have to be logged in as admin to access that page.');
        }

        $now = Carbon::now();
        $headers = [
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0'
            ,   'Content-type'        => 'text/csv'
            ,   'Content-Disposition' => 'attachment; filename=aanrlog_'.$now->format('dmy').'.csv'
            ,   'Expires'             => '0'
            ,   'Pragma'              => 'public'
        ];

        $list = Log::all()->toArray();
        rsort($list);
        # add headers for each column in the CSV download
        array_unshift($list, array_keys($list[0]));

        $callback = function () use ($list) {
            $FH = fopen('php://output', 'w');
            foreach ($list as $row) {
                fputcsv($FH, $row);
            }
            fclose($FH);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportConsortiaLogs()
    {
        if (!Auth::check()) {
            return Redirect::route('login')->with('error', 'You have to be logged in to access this page.');
        }
        if (Auth::user()->role == 1) {
            return Redirect::route('userDashboard')->with('error', 'You have to be logged in as admin to access that page.');
        }

        $now = Carbon::now();
        $headers = [
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0'
            ,   'Content-type'        => 'text/csv'
            ,   'Content-Disposition' => 'attachment; filename=aanrlog_'.$now->format('dmy').'.csv'
            ,   'Expires'             => '0'
            ,   'Pragma'              => 'public'
        ];

        $list = Log::where('user_id', '=', auth()->user()->id)->orderBy('id', 'desc')->get()->toArray();
        if($list == null) {
            return redirect()->back()->with('error', 'No logs found.');
        }
        # add headers for each column in the CSV download
        array_unshift($list, array_keys($list[0]));

        $callback = function () use ($list) {
            $FH = fopen('php://output', 'w');
            foreach ($list as $row) {
                fputcsv($FH, $row);
            }
            fclose($FH);
        };

        return response()->stream($callback, 200, $headers);
    }
}
