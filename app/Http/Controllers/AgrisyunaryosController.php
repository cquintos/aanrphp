<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Agrisyunaryo;
use App\ArtifactAANR;
use App\Log;

// This file contains request handling logic for Agrisyunaryo.
// functions included are:
//     addAgrisyunaryo(Request $request)
//     editAgrisyunaryo(Request $request, $agrisyunaryo_id)
//     deleteAgrisyunaryo(Request $request)editAANRPageDetails(Request $request, $consortia_id) 
//
// Certain data are validated.
//
// all changes are logged in a new Log object

class AgrisyunaryosController extends Controller
{
    public function addAgrisyunaryo(Request $request)
    {
        $this->validate($request, array(
            'title' => 'required|max:255',
            'image' => ['required', 'mimes:jpeg,jpg,png,gif', 'max:10240']
        ));

        $user = auth()->user();
        $temp_changes = '';
        $log = new Log();
        $agrisyunaryo = new Agrisyunaryo();
        $agrisyunaryo->title = $request->title;
        $agrisyunaryo->description = $request->description;
        $agrisyunaryo->link = $request->link;
        $agrisyunaryo->keywords = $request->keywords;

        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $imageName = uniqid().$imageFile->getClientOriginalName();
            $imageFile->move(public_path('/storage/page_images/'), $imageName);
            $agrisyunaryo->image = $imageName;
        }

        $agrisyunaryo->save();
        $artifact = ArtifactAANR::firstOrNew(['title' => $request->title]);
        $artifact->description = $request->description;
        $artifact->link = $request->link;
        $artifact->imglink = $agrisyunaryo->image;
        $artifact->keywords = $request->keywords;
        $artifact->is_agrisyunaryo = 1;
        $artifact->save();

        $log->user_id = $user->id;
        $log->user_email = $user->email;
        $log->changes = 'Added \''. $agrisyunaryo->title.'\' to Agrisyunaryo';
        $log->action = 'Added \''. $agrisyunaryo->title.'\' to Agrisyunaryo';
        $log->IP_address = $request->ip();
        $log->resource = 'Agrisyunaryo';
        $log->save();

        return redirect()->back()->with('success', 'Agrisyunaryo Added.');
    }

    public function editAgrisyunaryo(Request $request, $agrisyunaryo_id)
    {
        $this->validate($request, array(
            'title' => 'required|max:255',
            'image' => ['required', 'mimes:jpeg,bmp,png,gif', 'max:10240']
        ));

        $user = auth()->user();
        $temp_changes = '';
        $log = new Log();
        $agrisyunaryo = Agrisyunaryo::find($agrisyunaryo_id);

        if($agrisyunaryo == null) {
            return redirect()->back()->with('error', 'Agrisyunaryo not found.');
        }
        if ($agrisyunaryo->title != $request->title) {
            $temp_changes = $temp_changes.'<strong>Title:</strong> '.$agrisyunaryo->title.' <strong>-></strong> '.$request->title.'<br>';
        }
        if ($agrisyunaryo->description != $request->description) {
            $temp_changes = $temp_changes.'<strong>Description:</strong> '.$agrisyunaryo->description.' <strong>-></strong> '.$request->description.'<br>';
        }
        if ($agrisyunaryo->link != $request->link) {
            $temp_changes = $temp_changes.'<strong>Link:</strong> '.$agrisyunaryo->link.' <strong>-></strong> '.$request->link.'<br>';
        }
        if ($agrisyunaryo->keywords != $request->keywords) {
            $temp_changes = $temp_changes.'<strong>Keywords:</strong> '.$agrisyunaryo->keywords.' <strong>-></strong> '.$request->keywords.'<br>';
        }

        $artifact = ArtifactAANR::firstOrNew(['title' => $agrisyunaryo->title]);
        $artifact->title = $request->title;
        $artifact->description = $request->description;
        $artifact->link = $request->link;
        $artifact->imglink = $agrisyunaryo->image;
        $artifact->keywords = $request->keywords;
        $artifact->is_agrisyunaryo = 1;
        $artifact->save();
        $agrisyunaryo->title = $request->title;
        $agrisyunaryo->description = $request->description;
        $agrisyunaryo->link = $request->link;
        $agrisyunaryo->keywords = $request->keywords;

        if ($request->hasFile('image')) {
            if ($agrisyunaryo->image != null) {
                $image_path = public_path().'/storage/page_images/'.$agrisyunaryo->image;
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
            $imageFile = $request->file('image');
            $imageName = uniqid().$imageFile->getClientOriginalName();
            $imageFile->move(public_path('/storage/page_images/'), $imageName);
            $temp_changes = $temp_changes.'<strong>Image:</strong> '.$agrisyunaryo->image.' <strong>-></strong> '.$imageName.'<br>';
            $agrisyunaryo->image = $imageName;
        }

        $agrisyunaryo->save();
        $log->user_id = $user->id;
        $log->user_email = $user->email;
        $log->changes = $temp_changes;
        $log->action = 'Edited \''. $agrisyunaryo->title.'\' details';
        $log->IP_address = $request->ip();
        $log->resource = 'Agrisyunaryo';
        $log->save();

        return redirect()->back()->with('success', 'Agrisyunaryo Updated.');
    }

    public function deleteAgrisyunaryo(Request $request)
    {
        $this->validate($request, array(
            'agrisyunaryo_check' => 'required',
        ));

        $user = auth()->user();
        $temp_changes = '';
        $log = new Log();
        $agrisyunaryo = Agrisyunaryo::whereIn('id', $request->input('agrisyunaryo_check'))->get();

        foreach ($agrisyunaryo as $agrisyunaryo_single) {
            if($agrisyunaryo_single == null) {
                continue;    
            }
            if ($agrisyunaryo_single->image) {
                $image_path = public_path().'/storage/page_images/'.$agrisyunaryo_single->image;
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }
            $temp_changes = $temp_changes.'<strong>Image:</strong> '.$agrisyunaryo_single->title.' <strong>-></strong> '.$agrisyunaryo_single->image.'<br>';
            $agrisyunaryo_single->delete();
        }

        $log->user_id = $user->id;
        $log->user_email = $user->email;
        $log->changes = $temp_changes;
        $log->action = 'Deleted content';
        $log->IP_address = $request->ip();
        $log->resource = 'Agrisyunaryo';
        $log->save();

        return redirect()->back()->with('success', 'Agrisyunaryo Deleted.');
    }
}
