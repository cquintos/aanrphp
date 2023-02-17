<?php

namespace App\Http\Controllers;

use App\ArtifactAANR;
use App\Consortia;
use App\Commodity;
use App\CommoditySubtype;
use App\Content;
use App\ContentSubtype;
use App\ConsortiaMember;
use App\Log;
use App\ISP;
use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;

class ArtifactAANRController extends Controller
{
    public function uploadPDFArtifact(Request $request)
    {
        $this->validate($request, array(
            'file' => 'required|file|max:10240|mimes:pdf'
        ));

        if ($request->hasFile('file')) {
            $pdfFile = $request->file('file');
            $pdfName = uniqid().$pdfFile->getClientOriginalName();
            $pdfFile->move(public_path('/storage/files/'), $pdfName);
            $artifactaanr = new ArtifactAANR();
            $file = $tech->files()->create([
                'filename' => $pdfName,
                'filesize' => 1,
                'category' => $request->category,
                'filetype' => pathinfo(storage_path().'/storage/files/'.$pdfName, PATHINFO_EXTENSION),
                'technology_id' => $artifactaanr_id
            ]);
        }

        return redirect()->back()->with('success', 'File Uploaded!');
    }

    public function addView(Request $request)
    {
        $artifact_key = $request->get('content_id');
        if (!Session::has($artifact_key)) {
            ArtifactAANR::find($artifact_key)->increment('views');
            Session::put($artifact_key, 1);
        }
    }

    public function uploadArtifactAPI(Request $request)
    {
        if ($request->api_link == null) {
            return redirect()->back()->with('error', 'Invalid link.');
        }

        $data = @file_get_contents($request->api_link);
        $publications = [];
        
        if($data != false) {
            $publications = json_decode($data);
        }

        foreach ($publications as $publication) {
            $artifact->content_id = Content::where('type', '=', 'Publications')->first();
            // TODO fix this. this is hard coded.
            $artifact->consortia_id = Consortia::where('short_name', '=', 'STAARRDEC')->first()->id;
            $artifact->contentsubtype_id = ContentSubtype::where('name', '=', $publication->materialtype)->first();
            $artifact = ArtifactAANR::firstOrNew(['title' => $publication->title]);
            $artifact->date_published = date("Y-m-d", strtotime($publication->publicationdate));
            $artifact->description = $publication->summary;
            $artifact->imglink = $publication->thumbnail;
            $artifact->author = $publication->author;
            $artifact->keywords = $publication->subjects;

            if ($artifact->content_id) {
                $artifact->content_id = $artifact->content_id->id;
            }
            
            if ($artifact->contentsubtype_id) {
                $artifact->contentsubtype_id = $artifact->contentsubtype_id->id;
            }
            
            if ($publication->filelocation) {
                $artifact->file = $publication->filelocation;
                $artifact->file_type = 'pdf_link';
            }
            
            $artifact->save();
        }

        return redirect()->back()->with('success', 'ArtifactAANR Added.');
    }

    public function uploadArtifactCSV(Request $request)
    {
        if ($request->file('csv_file') == null) {
            return redirect()->back()->with('error', 'Invalid file.');
        }

        if(strpos($request->file('csv_file')->getClientOriginalName(), ".csv") === false) {
            return redirect()->back()->with('error', 'Invalid file format. Please make sure the file is in .csv extension.');
        } 

        $upload = $request->file('csv_file');
        $filePath = $upload->getRealPath();
        $file = fopen($filePath, 'r');
        $header = fgetcsv($file);
        $count = 0; 
        $err_required = 0; 
        $err_consortia = 0; 
        $err_cType = 0; 
        $err_subCType = 0; 
        $err_CMI = 0; 
        $err_duplicate = 0; 
        $output = '';

        while ($columns = fgetcsv($file)) {
            $count = $count + 1;

            if(count($header) != count($columns)) {
                return redirect()->back()->with('error', 'Something went wrong. Try again.');
            }

            $data = array_combine($header, $columns);
            $artifact = new ArtifactAANR();

            foreach ($data as $key => $value) {
                $key = strtolower($key);
                $value = ($key == "gad") ? (int)$value : (string)$value;
            }
            
            if ($data['title']==null || $data['consortia'] ==null || $data['content_type'] ==null) { // DATA VALIDATION
                $err_required = $err_required + 1;   //checks if the required fields: title, consortia, and content type are included in the entry
                continue;
            }
            
            $artifact->content_id = Content::where('type', '=', $data['content_type'])->first(); //checks if the content id is in the database to make sure it is a valid content type
            if ($artifact->content_id==null) {
                $err_cType = $err_cType + 1;
                continue;
            }
            
            $artifact->consortia_id = Consortia::where('short_name', '=', $data['consortia'])->first(); //checks if the content id is in the database to make sure it is a valid consortia
            if ($artifact->consortia_id == null) {
                $err_consortia = $err_consortia + 1;
                continue;
            }
            
            //content subtype is nullable but user input still needs to be checked
            $contentsubtype_id = $data['subcontent_type'] == null ? null : $data['subcontent_type']; 
            if ($contentsubtype_id != null) {
                $contentsubtype_id = ContentSubtype::where('name', '=', $data['subcontent_type'])->first(); //null result means user input is invalid
                if ($contentsubtype_id == null) {
                    $err_subCType = $err_subCType+1;
                    continue;
                }
                $contentsubtype_id = $contentsubtype_id->id;
            }
            
            $consortia_member_id = $data['CMI']; //consortia id is nullable but user input still needs to be checked
            if ($consortia_member_id != null) {
                $consortia_member_id = ConsortiaMember::where('name', '=', $data['CMI'])->first(); //null result means user input is invalid
                if ($consortia_member_id == null) {
                    $err_CMI = $err_CMI + 1;
                    continue;
                }
                $artifact->author_institution = $consortia_member_id->name;
                $artifact->consortia_member_id = $consortia_member_id->id;
            } else {
                $artifact->consortia_member_id = null; //null means no input and is acceptable
                $artifact->author_institution = null;
            }

            $artifact->title = $data['title'];
            $artifact->date_published = date("Y-m-d", strtotime($data['date_published']));
            $artifact->content_id = $artifact->content_id->id;
            $artifact->contentsubtype_id = $contentsubtype_id;
            $artifact->consortia_id = $artifact->consortia_id->id;
            $artifact->description = $data['abstract'];
            $artifact->link = $data['link'];
            $artifact->embed_link = $data['embed_link'];
            $artifact->author = $data['author'];
            $artifact->author_affiliation = $data['author_affiliation'];
            $artifact->keywords = $data['keywords'];
            $artifact->is_gad = $data['is_gad']==null ? 0 : $data['is_gad'];

            if (DB::table('artifactaanr') //  CHECK IF THIS ENTRY EXISTS
                ->where('title', $artifact->title)
                ->where('date_published', $artifact->date_published)
                ->where('author', $artifact->author)
                ->where('description', $artifact->description)
                ->where('consortia_id', $artifact->consortia_id)
                ->where('content_id', $artifact->content_id)
                ->where('is_gad', $artifact->is_gad)
                ->exists()) 
            {
                $err_duplicate = $err_duplicate + 1;
                continue;
            }

            $artifact->save();
        }

        $err_total = $err_required+$err_consortia+$err_cType+$err_subCType+$err_CMI+$err_duplicate;
        $count = $count - $err_total;

        if($err_required) {
            $output .= $err_required.' entries with missing required fields. ';
        }
        if($err_consortia) {
            $output .= $err_consortia.' entries with invalid consortia. ';
        }
        if($err_cType) {
            $output .= $err_cType.' entries with missing content type field. ';
        }
        if($err_subCType) {
            $output .= $err_subCType.' entries with invalid subcontent type. ';
        }
        if($err_CMI) {
            $output .= $err_CMI.' entries with invalid consortia member. ';
        }
        if($err_duplicate) {
            $output .= $err_duplicate.' duplicate entries. ';
        }
        if($err_total) {
            return Redirect::to(route('dashboardAdmin').'?asset=Artifacts')->with('error',"Total of ".$count." artifacts uploaded. ".$output);        
        }

        return Redirect::to(route('dashboardAdmin').'?asset=Artifacts')->with('success', $count." artifacts uploaded successfully." );        
    }
    
    public function uploadArtifactForm(Request $request) 
    {
        $this->validate($request, [
            'author' => 'max:200',
            'author_affiliation' => 'max:200',
            'consortia' => 'required',
            'content' => 'required',
            'date_published' => 'before:tomorrow',
            'description' => 'max:2000',
            'file' => 'file|max:10240|mimes:pdf,jpeg,png',
            'title' => 'required|max:200',
        ]);
        
        $artifactaanr = new ArtifactAANR();
        $artifactaanr->title = $request->title;
        $artifactaanr->date_published = $request->date_published;
        $artifactaanr->description = $request->description;
        $artifactaanr->content_id = $request->content;
        $artifactaanr->consortia_id = $request->consortia;
        $artifactaanr->consortia_member_id = $request->consortia_member;
        $artifactaanr->contentsubtype_id = $request->content_subtype;
        $artifactaanr->link = $request->link;
        $artifactaanr->embed_link = $request->embed_link;
        $artifactaanr->author = $request->author;
        $artifactaanr->author_affiliation = $request->author_affiliation;
        $artifactaanr->keywords = $request->keywords;
        $artifactaanr->is_gad = $request->is_gad;
        $artifactaanr->imglink = $request->imglink;
        $artifactaanr->author_institution  = ConsortiaMember::find($request->consortia_member);

        if ($artifactaanr->author_institution != null) {
            $artifactaanr->author_institution  = $artifactaanr->author_institution->name;
        }

        if ($request->hasFile('file')) {
            $pdfFile = $request->file('file');
            $pdfName = uniqid().$pdfFile->getClientOriginalName();
            $pdfFile->move(public_path('/storage/files/'), $pdfName);
            $artifactaanr->file = $pdfName;
            $artifactaanr->file_type = pathinfo(storage_path().'/storage/files/'.$pdfName, PATHINFO_EXTENSION);
        }

        $artifactaanr->save();
        $artifactaanrobject = ArtifactAANR::find($artifactaanr->id);
        $artifactaanrobject->isp()->sync($request->isp);
        $artifactaanrobject->commodities()->sync($request->commodities);
        $artifactaanr->commodity_subtypes()->sync($request->commodity_subtypes);
        $artifactaanrobject->save();
        
        foreach($artifactaanr->isp as $record) {
            $record->pivot->update(['industry_id' => ISP::find($record->pivot->isp_id)->sector->industry->id]);
        }

        foreach($artifactaanr->commodities as $record) {
            $record->pivot->update(['industry_id' => Commodity::find($record->pivot->commodity_id)->industry_id]);
        }
        
        return redirect()->route('dashboardAdmin', ['asset' => 'Artifacts'])->with('success', 'Successfuly uploaded artifact.');        
    }

    public function editArtifact(Request $request, $artifact_id)
    {
        $this->validate($request, [
            'author' => 'max:200',
            'author_affiliation' => 'max:200',
            'consortia' => 'required',
            'content' => 'required',
            'date_published' => 'before:tomorrow',
            'description' => 'max:2000',
            'file' => 'file|max:10240|mimes:pdf,jpeg,png',
            'title' => 'required|max:200',
        ]);

        $user = auth()->user();
        $temp_changes = '';
        $log = new Log();
        $artifactaanr = ArtifactAANR::find($artifact_id);

        if ($artifactaanr->title != $request->title) {
            $temp_changes = $temp_changes.'<strong>Title:</strong> '.$artifactaanr->title.' <strong>-></strong> '.$request->title.'<br>';
        }
        if ($artifactaanr->date_published != $request->date_published) {
            $temp_changes = $temp_changes.'<strong>Date Published:</strong> '.$artifactaanr->date_published.' <strong>-></strong> '.$request->date_published.'<br>';
        }
        if ($artifactaanr->description != $request->description) {
            $temp_changes = $temp_changes.'<strong>Description:</strong> '.$artifactaanr->description.' <strong>-></strong> '.$request->description.'<br>';
        }
        if ($artifactaanr->content_id != $request->content) {
            $temp_changes = $temp_changes.'<strong>Content ID:</strong> '.$artifactaanr->content_id.' <strong>-></strong> '.$request->content.'<br>';
        }
        if ($artifactaanr->contentsubtype_id != $request->subcontent_type) {
            $temp_changes = $temp_changes.'<strong>Subcontent Type ID:</strong> '.$artifactaanr->contentsubtype_id.' <strong>-></strong> '.$request->subcontent_type.'<br>';
        }
        if ($artifactaanr->consortia_id != $request->consortia) {
            $temp_changes = $temp_changes.'<strong>Consortia ID:</strong> '.$artifactaanr->consortia_id.' <strong>-></strong> '.$request->consortia.'<br>';
        }
        if ($artifactaanr->consortia_member_id != $request->consortia_member) {
            $temp_changes = $temp_changes.'<strong>Consortia Member ID:</strong> '.$artifactaanr->consortia_member_id.' <strong>-></strong> '.$request->consortia_member.'<br>';
        }
        if ($artifactaanr->link != $request->link) {
            $temp_changes = $temp_changes.'<strong>Link:</strong> '.$artifactaanr->link.' <strong>-></strong> '.$request->link.'<br>';
        }
        if ($artifactaanr->author != $request->author) {
            $temp_changes = $temp_changes.'<strong>Author:</strong> '.$artifactaanr->author.' <strong>-></strong> '.$request->author.'<br>';
        }
        if ($artifactaanr->embed_link != $request->embed_link) {
            $temp_changes = $temp_changes.'<strong>Embed Link:</strong> '.$artifactaanr->embed_link.' <strong>-></strong> '.$request->embed_link.'<br>';
        }
        if ($artifactaanr->author_institution != $request->author_institution) {
            $temp_changes = $temp_changes.'<strong>Author Insitution:</strong> '.$artifactaanr->author_institution.' <strong>-></strong> '.$request->author_institution.'<br>';
        }
        if ($artifactaanr->author_affiliation != $request->author_affiliation) {
            $temp_changes = $temp_changes.'<strong>Author Affiliation:</strong> '.$artifactaanr->author_affiliation.' <strong>-></strong> '.$request->author_affiliation.'<br>';
        }
        if ($artifactaanr->keywords != $request->keywords) {
            $temp_changes = $temp_changes.'<strong>Keywords:</strong> '.$artifactaanr->keywords.' <strong>-></strong> '.$request->keywords.'<br>';
        }
        if ($artifactaanr->gad != $request->gad) {
            $temp_changes = $temp_changes.'<strong>GAD:</strong> '.$artifactaanr->gad.' <strong>-></strong> '.$request->gad.'<br>';
        }
        if ($artifactaanr->imglink != $request->imglink) {
            $temp_changes = $temp_changes.'<strong>Image Link:</strong> '.$artifactaanr->imglink.' <strong>-></strong> '.$request->imglink.'<br>';
        }
        if ($artifactaanr->is_gad != $request->is_gad) {
            $temp_changes = $temp_changes.'<strong>Is GAD?:</strong> '.$artifactaanr->is_gad.' <strong>-></strong> '.$request->is_gad.'<br>';
        }

        $artifactaanr->title = $request->title;
        $artifactaanr->date_published = $request->date_published;
        $artifactaanr->description = $request->description;
        $artifactaanr->content_id = $request->content;
        $artifactaanr->contentsubtype_id = $request->content_subtype;
        $artifactaanr->consortia_id = $request->consortia;
        $artifactaanr->consortia_member_id = $request->consortia_member;
        $artifactaanr->link = $request->link;
        $artifactaanr->author = $request->author;
        $artifactaanr->embed_link = $request->embed_link;
        $artifactaanr->author_affiliation = $request->author_affiliation;
        $artifactaanr->keywords = $request->keywords;
        $artifactaanr->gad = $request->gad;
        $artifactaanr->imglink = $request->imglink;
        $artifactaanr->is_gad = $request->is_gad;
        $artifactaanr->isp()->sync($request->isp);
        $artifactaanr->commodities()->sync($request->commodities);
        $artifactaanr->commodity_subtypes()->sync($request->commodity_subtypes);
        $artifactaanr->author_institution  = ConsortiaMember::find($request->consortia_member);

        if ($artifactaanr->author_institution != null) {
            $artifactaanr->author_institution  = $artifactaanr->author_institution->name;
        }

        if ($request->hasFile('file')) {
            $pdfFile = $request->file('file');
            $pdfName = uniqid().$pdfFile->getClientOriginalName();
            $pdfFile->move(public_path('/storage/files/'), $pdfName);
            $artifactaanr->file = $pdfName;
            $artifactaanr->file_type = pathinfo(storage_path().'/storage/files/'.$pdfName, PATHINFO_EXTENSION);
        }

        $artifactaanr->save();
        $log->user_id = $user->id;
        $log->user_email = $user->email;
        $log->changes = $temp_changes;
        $log->action = 'Edited \''. $artifactaanr->title.'\' details';
        $log->IP_address = $request->ip();
        $log->resource = 'Artifacts';
        $log->save();

        foreach($artifactaanr->isp as $record) {
            $record->pivot->update(['industry_id' => ISP::find($record->pivot->isp_id)->sector->industry->id]);
        }

        foreach($artifactaanr->commodities as $record) {
            $record->pivot->update(['industry_id' => Commodity::find($record->pivot->commodity_id)->industry_id]);
        }

        return redirect()->route('artifactView', [$artifactaanr->id])->with('success', 'AANR Content Updated.');
    }

    public function addISPIndustryID(Request $request)
    {
        foreach (DB::table('artifactaanr_isp')->all() as $entry) {
            $temp_sector_industry_id = DB::table('isp')->where('id', '=', $entry->isp_id)->first()->industry_id;
            $entry->industry_id = DB::table('industry')->where('id', '=', $temp_sector_industry_id)->id;
        }

        return redirect()->back()->with('success', 'Artifact ISP Industry ID added.');
    }

    public function deleteArtifact(Request $request)
    {
        $user = auth()->user();
        
        if (!$request->input('artifactaanr_check')) {
            return redirect()->back()->with('error', 'No content selected.');
        }
        
        $temp_changes = '';
        $log = new Log();
        $temp_changes = 'Deleted: ';
        $artifactaanr = ArtifactAANR::whereIn('id', $request->input('artifactaanr_check'))->get();
        foreach ($artifactaanr as $artifact) {
            if ($artifact->file) {
                $filePath = public_path().'/storage/files/'.$artifact->file;
                unlink($filePath);
            }
            $temp_changes = $temp_changes.$artifact->title.'<br>';
            $artifact->isp()->detach();
            $artifact->commodities()->detach();
            $artifact->delete();
        }

        $log->user_id = $user->id;
        $log->user_email = $user->email;
        $log->changes = $temp_changes;
        $log->action = 'Deleted content';
        $log->IP_address = $request->ip();
        $log->resource = 'Artifacts';
        $log->save();

        return redirect()->back()->with('success', 'AANR Content Deleted.');
    }

    public function deleteSingleArtifact($artifact_id)
    {
        $artifactaanr = ArtifactAANR::find($artifact_id);

        if ($artifactaanr->file) {
            $filePath = public_path().'/storage/files/'.$artifactaanr->file;
            unlink($filePath);
        }

        $artifactaanr->isp()->detach();
        $artifactaanr->commodities()->detach();
        $artifactaanr->delete();

        return redirect()->back()->with('success', 'AANR Content Deleted.');
    }

    public function fetchConsortiaMemberDependent(Request $request)
    {
        $consortia_members = Consortia::find($request->get('value'))->consortia_members;
        $output = '<option value="">Select Consortia Member</option>';

        if(count($consortia_members) === 0) {
            return '<option value="">---------------------</option>';
        }

        foreach($consortia_members as $entry) {
            $output .= '<option value="'.$entry->id.'">'.$entry->name.'</option>';
        }
        
        echo $output;
    }

    public function fetchContentSubtypeDependent(Request $request)
    {
        $content_subtypes = Content::find($request->get('value'))->content_subtypes;
        $output = '<option value="">Select Content Subtype</option>';

        if(count($content_subtypes) === 0) {
            return '<option value="">---------------------</option>';
        }

        foreach($content_subtypes as $entry) {
            $output .= '<option value="'.$entry->id.'">'.$entry->name.'</option>';
        }
        
        echo $output;
    }

    public function fetchCommodityDependent(Request $request)
    {
        $commodity = $request->get('commodity');
        $value = $request->get('value');
        $dependent = $request->get('dependent');
        $data = Commodity::all()->where($commodity, $value);
        $output = '<option value="">Select '.ucfirst($dependent).'</option>';
        foreach ($data as $row) {
            $output .= '<option value="'.$row->id.'">'.$row->name.'</option>';
        }
        echo $output;
    }

    public function fetchCommoditySubtypeDependent(Request $request)
    {
        $ids = $request->get('ids');
        $output = '<option value="">Select Commodity Subtype</option>';
        
        if(!$ids) {
            return '<option value="">---------------------</option>';
        }

        foreach($ids as $id) {
            $arr = Commodity::find($id)->subtypes->pluck('name', 'id');
            foreach($arr as $key => $value) {
                $output .= '<option value="'.$key.'">'.$value.'</option>';
            }
        }
        
        return $output == '<option value"">Select Commodity Subtype</option>' 
            ? '<option value"">---------------------</option>'
            : $output;
    }

    public function artifactModalView(Request $request)
    {
        return view('dashboard.modals.artifact_view_modal', ['artifact' => ArtifactAANR::find($request->get('id'))]);
    }
}
