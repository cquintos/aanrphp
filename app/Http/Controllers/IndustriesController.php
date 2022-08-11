<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Industry;
use App\Log;

// This file contains request handling logic for Industries.
// functions included are:
//     addIndustry(Request $request)
//     editIndustry(Request $request, $industry_id)
//     deleteIndustry($industry_id, Request $request)
//
// if ($user->role != 5 && $user->role != 2)
//     Means only a SUPER ADMIN (role = 5) and a CONSORTIA ADMIN (role = 2) may use the function.      
//
// Certain data are validated.
//
// all changes are logged in a new Log object

class IndustriesController extends Controller
{
    public function addIndustry(Request $request)
    {
        $this->validate($request, array(
            'name' => 'required|max:255'
        ));
        $user = auth()->user();
        $temp_changes = '';
        $log = new Log();
        if ($user->role != 5 && $user->role != 2) {
            return redirect()->back()->with('error', 'Your account is not authorized to use this function.');
        } else {
            $industry = new Industry();
            $industry->name = $request->name;
            $industry->save();

            $log->user_id = $user->id;
            $log->user_email = $user->email;
            $log->changes = '<strong>Added:</strong> '.$industry->name.'';
            $log->action = 'Added \''. $industry->name.'\'';
            $log->IP_address = $request->ip();
            $log->resource = 'Industries';
            $log->save();

            return redirect()->back()->with('success', 'Industry Added.');
        }
    }

    public function editIndustry(Request $request, $industry_id)
    {
        $this->validate($request, array(
            'name' => 'required|max:255'
        ));
        $user = auth()->user();
        $temp_changes = '';
        $log = new Log();
        if ($user->role != 5 && $user->role != 2) {
            return redirect()->back()->with('error', 'Your account is not authorized to use this function.');
        } else {
            $industry = Industry::find($industry_id);

            if ($industry->name != $request->name) {
                $temp_changes = $temp_changes.'<strong>Name:</strong> '.$industry->name.' <strong>-></strong> '.$request->name.'<br>';
            }

            $industry->name = $request->name;
            $industry->save();

            $log->user_id = $user->id;
            $log->user_email = $user->email;
            $log->changes = $temp_changes;
            $log->action = 'Edited \''. $industry->name.'\'';
            $log->IP_address = $request->ip();
            $log->resource = 'Industries';
            $log->save();

            return redirect()->back()->with('success', 'Industry Updated.');
        }
    }

    public function deleteIndustry($industry_id, Request $request)
    {
        $user = auth()->user();
        $temp_changes = '';
        $log = new Log();
        if ($user->role != 5 && $user->role != 2) {
            return redirect()->back()->with('error', 'Your account is not authorized to use this function.');
        } else {
            $industry = Industry::find($industry_id);
            $deletedName = $industry->name;
            $industry->articles()->delete();
            $industry->delete();

            $log->user_id = $user->id;
            $log->user_email = $user->email;
            $log->changes = '<strong>Deleted:</strong> '.$deletedName.'';
            $log->action = 'Deleted \''. $deletedName.'\'';
            $log->IP_address = $request->ip();
            $log->resource = 'Industries';
            $log->save();

            return redirect()->back()->with('success', 'Industry Deleted.');
        }
    }
}
