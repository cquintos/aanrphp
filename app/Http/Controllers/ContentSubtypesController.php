<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ContentSubtype;
use App\Log;

// This file contains request handling logic for Content Subtypes.
// functions included are:
//     addContentSubtype(Request $request)
//     editContentSubtype(Request $request, $content_subtype_id)
//     deleteContentSubtype(Request $request, $content_subtype_id)
//
// Certain data are validated.
//
// all changes are logged in a new Log object

class ContentSubtypesController extends Controller
{
    public function addContentSubtype(Request $request)
    {
        $this->validate($request, array(
            'name' => 'required|max:50',
            'content' => 'required'
        ));

        $user = auth()->user();
        $temp_changes = '';
        $log = new Log();
        $content_subtype = new ContentSubtype();
        $content_subtype->name = $request->name;
        $content_subtype->content_id = $request->content;
        $content_subtype->save();

        $log->user_id = $user->id;
        $log->user_email = $user->email;
        $log->changes = '<strong>Added:</strong> '.$content_subtype->name.'';
        $log->action = 'Added \''. $content_subtype->name.'\'';
        $log->IP_address = $request->ip();
        $log->resource = 'Content Subtype';
        $log->save();

        return redirect()->back()->with('success', 'Content Subtype Added.');
    }

    public function editContentSubtype(Request $request, $content_subtype_id)
    {
        $this->validate($request, array(
            'name' => 'required|max:50',
            'content' => 'required'
        ));

        $user = auth()->user();
        $temp_changes = '';
        $log = new Log();
        $content_subtype = ContentSubtype::find($content_subtype_id);

        if ($content_subtype == null) {
            return redirect()->back()->with('error', 'Content Subtype not found.');
        }
        if ($content_subtype->name != $request->name) {
            $temp_changes = $temp_changes.'<strong>Name:</strong> '.$content_subtype->name.' <strong>-></strong> '.$request->name.'<br>';
        }
        if ($content_subtype->content_id != $request->content) {
            $temp_changes = $temp_changes.'<strong>Content ID:</strong> '.$content_subtype->content_id.' <strong>-></strong> '.$request->content.'<br>';
        }

        $content_subtype->name = $request->name;
        $content_subtype->content_id = $request->content;
        $content_subtype->save();

        $log->user_id = $user->id;
        $log->user_email = $user->email;
        $log->changes = $temp_changes;
        $log->action = 'Edited \''. $content_subtype->name.'\'';
        $log->IP_address = $request->ip();
        $log->resource = 'Content Subtype';
        $log->save();

        return redirect()->back()->with('success', 'Content Subtype Updated.');
    }

    public function deleteContentSubtype(Request $request, $content_subtype_id)
    {
        $user = auth()->user();
        $temp_changes = '';
        $log = new Log();
        $content_subtype = ContentSubtype::find($content_subtype_id);
        if ($content_subtype == null) {
            return redirect()->back()->with('error', 'Content Subtype not found.');
        }

        $log->user_id = $user->id;
        $log->user_email = $user->email;
        $log->changes = '<strong>Deleted:</strong> '.$content_subtype->name.'';
        $log->action = 'Deleted \''. $content_subtype->name.'\'';
        $log->IP_address = $request->ip();
        $log->resource = 'Content Subtype';
        $log->save();

        $content_subtype->delete();

        return redirect()->back()->with('success', 'Content Subtype Deleted.');
    }
}
