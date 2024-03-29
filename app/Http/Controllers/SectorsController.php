<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sector;
use App\Log;

// This file contains request handling logic for Sectors.
// functions included are:
//     addSector(Request $request)
//     editSector(Request $request, $sector_id)
//     deleteSector($sector_id, Request $request)
//
// Certain data are validated.
//
// all changes are logged in a new Log object

class SectorsController extends Controller
{
    public function addSector(Request $request)
    {
        $this->validate($request, array(
            'name' => 'required|max:100',
            'industry' => 'required'
        ));

        $user = auth()->user();
        $temp_changes = '';
        $log = new Log();
        $sector = new Sector();
        $sector->name = $request->name;
        $sector->industry_id = $request->industry;
        $sector->description = $request->description;
        $sector->save();

        $log->user_id = $user->id;
        $log->user_email = $user->email;
        $log->changes = '<strong>Added:</strong> '.$sector->name.'';
        $log->action = 'Added \''. $sector->name.'\'';
        $log->IP_address = $request->ip();
        $log->resource = 'Sectors';
        $log->save();

        return redirect()->back()->with('success', 'Sector Added.');
    }

    public function editSector(Request $request, $sector_id)
    {
        $this->validate($request, array(
            'name' => 'required|max:100',
            'industry' => 'required'
        ));

        $user = auth()->user();
        $temp_changes = '';
        $log = new Log();
        $sector = Sector::find($sector_id);

        if ($sector == null) {
            return redirect()->back()->with('error', 'Sector not found.');
        }
        if ($sector->name != $request->name) {
            $temp_changes = $temp_changes.'<strong>Name:</strong> '.$sector->name.' <strong>-></strong> '.$request->name.'<br>';
        }
        if ($sector->industry_id != $request->industry) {
            $temp_changes = $temp_changes.'<strong>Industry ID:</strong> '.$sector->industry_id.' <strong>-></strong> '.$request->industry.'<br>';
        }
        if ($sector->description != $request->description) {
            $temp_changes = $temp_changes.'<strong>Description:</strong> '.$sector->description.' <strong>-></strong> '.$request->description.'<br>';
        }

        $sector->name = $request->name;
        $sector->industry_id = $request->industry;
        $sector->description = $request->description;
        $sector->save();

        $log->user_id = $user->id;
        $log->user_email = $user->email;
        $log->changes = $temp_changes;
        $log->action = 'Edited \''. $sector->name.'\'';
        $log->IP_address = $request->ip();
        $log->resource = 'Sectors';
        $log->save();

        return redirect()->back()->with('success', 'Sector Updated.');
    }

    public function deleteSector($sector_id, Request $request)
    {
        $user = auth()->user();
        $temp_changes = '';
        $log = new Log();
        $sector = Sector::find($sector_id);

        if ($sector == null) {
            return redirect()->back()->with('error', 'Sector not found.');
        }

        $deletedName = $sector->name;
        $sector->delete();

        $log->user_id = $user->id;
        $log->user_email = $user->email;
        $log->changes = '<strong>Deleted:</strong> '.$deletedName.'';
        $log->action = 'Deleted \''. $deletedName.'\'';
        $log->IP_address = $request->ip();
        $log->resource = 'Sectors';
        $log->save();

        return redirect()->back()->with('success', 'Sector Deleted.');
    }
}
