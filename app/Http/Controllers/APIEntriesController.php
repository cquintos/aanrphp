<?php

namespace App\Http\Controllers;

use App\APIEntries;
use Illuminate\Http\Request;
use App\Log;

// This file contains request handling logic for API entries.
// functions included are:
//     addAPIEntry(Request $request)
//     editAPIEntry(Request $request, $entry_id)
//     deleteAPIEntry($entry_id, Request $request)
//
// Certain data are validated.
//
// all changes are logged in a new Log object

class APIEntriesController extends Controller
{
    public function addAPIEntry(Request $request)
    {
        $user = auth()->user();
        $temp_changes = '';
        $log = new Log();
        $entry = new APIEntries();
        $entry->link = $request->link;
        $entry->description = $request->description;
        $entry->frequency = $request->frequency;
        $entry->time = $request->time;
        $entry->save();

        $log->user_id = $user->id;
        $log->user_email = $user->email;
        $log->changes = 'Added \''. $entry->description.'\' to API';
        $log->action = 'Added \''. $entry->description.'\' to API';
        $log->IP_address = $request->ip();
        $log->resource = 'API Entry';
        $log->save();

        return redirect()->back()->with('success', 'API Added.');
    }

    public function editAPIEntry(Request $request, $entry_id)
    {
        $user = auth()->user();
        $temp_changes = '';
        $log = new Log();
        $entry = APIEntries::find($entry_id);
        
        if($entry == null) {
            return redirect()->back()->with('error', 'API Entry not found.');
        }
        if ($entry->link != $request->link) {
            $temp_changes = $temp_changes.'<strong>Link:</strong> '.$entry->link.' <strong>-></strong> '.$request->link.'<br>';
        }
        if ($entry->description != $request->description) {
            $temp_changes = $temp_changes.'<strong>Description:</strong> '.$entry->description.' <strong>-></strong> '.$request->description.'<br>';
        }
        if ($entry->frequency != $request->frequency) {
            $temp_changes = $temp_changes.'<strong>Frequency:</strong> '.$entry->frequency.' <strong>-></strong> '.$request->frequency.'<br>';
        }
        if ($entry->time != $request->time) {
            $temp_changes = $temp_changes.'<strong>Time:</strong> '.$entry->time.' <strong>-></strong> '.$request->time.'<br>';
        }

        $entry->link = $request->link;
        $entry->description = $request->description;
        $entry->frequency = $request->frequency;
        $entry->time = $request->time;
        $entry->save();

        $log->user_id = $user->id;
        $log->user_email = $user->email;
        $log->changes = $temp_changes;
        $log->action = 'Added \''. $entry->description.'\' to API';
        $log->IP_address = $request->ip();
        $log->resource = 'API Entry';
        $log->save();

        return redirect()->back()->with('success', 'API Updated.');
    }

    public function deleteAPIEntry($entry_id, Request $request)
    {
        $user = auth()->user();
        $temp_changes = '';
        $log = new Log();
        $entry = APIEntries::find($entry_id);

        if($entry == null) {
            return redirect()->back()->with('error', 'API Entry not found.');
        }
        
        $entry->delete();
        $log->user_id = $user->id;
        $log->user_email = $user->email;
        $log->changes = 'Deleted \''. $entry->description.'\'';
        $log->action = 'Deleted \''. $entry->description.'\'';
        $log->IP_address = $request->ip();
        $log->resource = 'API Entry';
        $log->save();

        return redirect()->back()->with('success', 'API Deleted.');
    }
}
