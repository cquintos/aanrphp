<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\HeaderLink;
use App\Log;

// This file contains request handling logic for Header Links.
// functions included are:
//     addHeaderLink(Request $request)
//     editHeaderLink(Request $request, $header_id)
//     deleteHeaderLink($header_id)
//
// Certain data are validated.
//
// all changes are logged in a new Log object

class HeaderLinksController extends Controller
{
    public function addHeaderLink(Request $request)
    {
        $this->validate($request, array(
            'name' => 'required|max:50',
            'position' => 'required',

        ));

        $user = auth()->user();
        $temp_changes = '';
        $log = new Log();

        if ($request->position <= HeaderLink::count()) {
            foreach (HeaderLink::where('position', '>=', $request->position)->get() as $link) {
                $link->position = $link->position + 1;
                $link->save();
            }
        }

        $header = new HeaderLink();
        $header->name = $request->name;
        $header->position = $request->position;
        $header->link = "http://" . $request->link;
        $header->save();

        $log->user_id = $user->id;
        $log->user_email = $user->email;
        $log->changes = '<strong>Added:</strong> '.$header->name.'';
        $log->action = 'Added \''. $header->name.'\'';
        $log->IP_address = $request->ip();
        $log->resource = 'Header Links';
        $log->save();

        return redirect()->back()->with('success', 'Header Link Updated.');
    }

    public function editHeaderLink(Request $request, $header_id)
    {
        $this->validate($request, array(
            'name' => 'required|max:50',
            'position' => 'required',
        ));

        $user = auth()->user();
        $header = HeaderLink::find($header_id);
        $log = new Log();
        $temp_changes = '';

        if ($header == null) {
            return redirect()->back()->with('error', 'Header link not found.');
        }

        if ($header->position > $request->position) {
            foreach (HeaderLink::havingBetween('position', [$request->position, $header->position-1])->get() as $item) {
                $item->position = $item->position + 1;
                $item->save();
            }
        }

        if ($header->position < $request->position) {
            foreach (HeaderLink::havingBetween('position', [$header->position+1, $request->position])->get() as $item) {
                $item->position = $item->position - 1;
                $item->save();
            }
        }

        if ($header->name != $request->name) {
            $temp_changes = $temp_changes.'<strong>Name:</strong> '.$header->name.' <strong>-></strong> '.$request->name.'<br>';
        }

        if ($header->position != $request->position) {
            $temp_changes = $temp_changes.'<strong>Position:</strong> '.$header->position.' <strong>-></strong> '.$request->position.'<br>';
        }

        if ($header->link != $request->link) {
            $temp_changes = $temp_changes.'<strong>Link:</strong> '.$header->link.' <strong>-></strong> '.$request->link.'<br>';
        }

        $header->name = $request->name;
        $header->position = $request->position;
        $header->link = $request->link;
        $header->save();

        $log->user_id = $user->id;
        $log->user_email = $user->email;
        $log->changes = $temp_changes;
        $log->action = 'Edited \''. $header->name.'\'';
        $log->IP_address = $request->ip();
        $log->resource = 'Header Links';
        $log->save();

        return redirect()->back()->with('success', 'Header Link Updated.');
    }

    public function deleteHeaderLink(Request $request, $header_id)
    {
        $user = auth()->user();
        $temp_changes = '';
        $log = new Log();

        $header = HeaderLink::find($header_id);

        if ($header == null) {
            return redirect()->back()->with('error', 'Header link not found.');
        }

        foreach (HeaderLink::where('position', '>', $header->position)->get() as $item) {
            $item->position = $item->position - 1;
            $item->save();
        }

        $log->user_id = $user->id;
        $log->user_email = $user->email;
        $log->changes = '<strong>Deleted:</strong> '.$header->name.'';
        $log->action = 'Deleted \''. $header->name.'\'';
        $log->IP_address = $request->ip();
        $log->resource = 'Header Links';
        $log->save();
        $header->delete();

        return redirect()->back()->with('success', 'Header Link Deleted.');
    }
}
