<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Content;
use App\Log;

// This file contains request handling logic for content.
// functions included are:
//     addContent(Request $request)
//     editContent(Request $request, $content_id)
//     deleteContent(Request $request, $content_id)
//
// if ($user->role != 5 && $user->role != 2)
//     Means only a SUPER ADMIN (role = 5) and a CONSORTIA ADMIN (role = 2) may use the function.      
//
// Certain data are validated.
//
// all changes are logged in a new Log object

class ContentController extends Controller
{
    public function addContent(Request $request)
    {
        $this->validate($request, array(
            'type' => 'required|max:50'
        ));
        $user = auth()->user();
        $temp_changes = '';
        $log = new Log();
        if ($user->role != 5 && $user->role != 2) {
            return redirect()->back()->with('error', 'Your account is not authorized to use this function.');
        } else {
            $content = new Content();
            $content->type = $request->type;
            $content->save();

            $log->user_id = $user->id;
            $log->user_email = $user->email;
            $log->changes = '<strong>Added:</strong> '.$content->type.'';
            $log->action = 'Added \''. $content->type.'\'';
            $log->IP_address = $request->ip();
            $log->resource = 'Content Type';
            $log->save();

            return redirect()->back()->with('success', 'Content Added.');
        }
    }

    public function editContent(Request $request, $content_id)
    {
        $this->validate($request, array(
            'type' => 'required|max:50'
        ));
        $user = auth()->user();
        $temp_changes = '';
        $log = new Log();
        if ($user->role != 5 && $user->role != 2) {
            return redirect()->back()->with('error', 'Your account is not authorized to use this function.');
        } else {
            $content = Content::find($content_id);

            if ($content->type != $request->type) {
                $temp_changes = $temp_changes.'<strong>Type:</strong> '.$content->type.' <strong>-></strong> '.$request->type.'<br>';
            }

            $content->type = $request->type;
            $content->save();

            $log->user_id = $user->id;
            $log->user_email = $user->email;
            $log->changes = $temp_changes;
            $log->action = 'Edited \''. $content->type.'\'';
            $log->IP_address = $request->ip();
            $log->resource = 'Content Type';
            $log->save();

            return redirect()->back()->with('success', 'Content Updated.');
        }
    }

    public function deleteContent(Request $request, $content_id)
    {
        $user = auth()->user();
        $temp_changes = '';
        $log = new Log();
        if ($user->role != 5 && $user->role != 2) {
            return redirect()->back()->with('error', 'Your account is not authorized to use this function.');
        } else {
            $content = Content::find($content_id);

            $log->user_id = $user->id;
            $log->user_email = $user->email;
            $log->changes = '<strong>Deleted:</strong> '.$content->type.'';
            $log->action = 'Deleted \''. $content->type.'\'';
            $log->IP_address = $request->ip();
            $log->resource = 'Content Type';
            $log->save();

            $content->delete();

            return redirect()->back()->with('success', 'Content Type Deleted.');
        }
    }
}
