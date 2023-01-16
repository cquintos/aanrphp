<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FooterLink;
use App\Log;

// This file contains request handling logic for Footer Links.
// functions included are:
//     addFooterLink(Request $request)
//     editFooterLink(Request $request, $footer_id)
//     deleteFooterLink(Request $request, $footer_id)
//
// Certain data are validated.
//
// all changes are logged in a new Log object

class FooterLinksController extends Controller
{
    public function addFooterLink(Request $request)
    {
        $this->validate($request, array(
            'name' => 'required|max:50'
        ));

        $user = auth()->user();
        $temp_changes = '';
        $log = new Log();
        $footer = new FooterLink();
        $footer->name = $request->name;
        $footer->position = $request->weight;
        $footer->link = $request->link;
        $footer->save();

        $log->user_id = $user->id;
        $log->user_email = $user->email;
        $log->changes = '<strong>Added:</strong> '.$footer->name.'';
        $log->action = 'Added \''. $footer->name.'\'';
        $log->IP_address = $request->ip();
        $log->resource = 'Footer Links';
        $log->save();

        return redirect()->back()->with('success', 'Footer Link Updated.');
    }

    public function editFooterLink(Request $request, $footer_id)
    {
        $this->validate($request, array(
            'name' => 'required|max:50'
        ));

        $user = auth()->user();
        $temp_changes = '';
        $log = new Log();
        $footer = FooterLink::find($footer_id);

        if ($footer == null) {
            return redirect()->back()->with('error', 'Footer link not found.');
        }
        if ($footer->name != $request->name) {
            $temp_changes = $temp_changes.'<strong>Name:</strong> '.$footer->name.' <strong>-></strong> '.$request->name.'<br>';
        }
        if ($footer->position != $request->position) {
            $temp_changes = $temp_changes.'<strong>Position:</strong> '.$footer->position.' <strong>-></strong> '.$request->position.'<br>';
        }
        if ($footer->link != $request->link) {
            $temp_changes = $temp_changes.'<strong>Link:</strong> '.$footer->link.' <strong>-></strong> '.$request->link.'<br>';
        }

        $footer->name = $request->name;
        $footer->position = $request->weight;
        $footer->link = $request->link;
        $footer->save();

        $log->user_id = $user->id;
        $log->user_email = $user->email;
        $log->changes = $temp_changes;
        $log->action = 'Edited \''. $footer->name.'\'';
        $log->IP_address = $request->ip();
        $log->resource = 'Footer Links';
        $log->save();

        return redirect()->back()->with('success', 'Footer Link Updated.');
    }

    public function deleteFooterLink(Request $request, $footer_id)
    {
        $user = auth()->user();
        $temp_changes = '';
        $log = new Log();
        $footer = FooterLink::find($footer_id);

        if ($footer == null) {
            return redirect()->back()->with('error', 'Footer link not found.');
        }

        $footer->delete();

        $log->user_id = $user->id;
        $log->user_email = $user->email;
        $log->changes = '<strong>Deleted:</strong> '.$footer->name.'';
        $log->action = 'Deleted \''. $footer->name.'\'';
        $log->IP_address = $request->ip();
        $log->resource = 'Footer Links';
        $log->save();

        return redirect()->back()->with('success', 'Footer Link Deleted.');
    }
}
