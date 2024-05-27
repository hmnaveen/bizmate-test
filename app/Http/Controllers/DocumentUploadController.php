<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;
use App\Models\Document;
use File;
use URL;

use App\Models\SumbUsers;
use App\Models\SumbClients;


class DocumentUploadController extends Controller
{
    public function __construct() {
        //none at this time
    }

    public function index(Request $request) {
        $userinfo =$request->get('userinfo');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'pagetitle' => 'Docs'
        );

        //pagination
        $itemsperpage = 10;
        if (!empty($request->input('ipp'))) { $itemsperpage = $request->input('ipp'); }
        $pagedata['ipp'] = $itemsperpage;        
        $doclist = Document::orderBy('created_at','desc')->paginate($itemsperpage)->toArray();
        $pagedata['doclist'] = $doclist;

        $allrequest = $request->all();
        $pfirst = $allrequest; $pfirst['page'] = 1;
        $pprev = $allrequest; $pprev['page'] = $doclist['current_page']-1;
        $pnext = $allrequest; $pnext['page'] = $doclist['current_page']+1;
        $plast = $allrequest; $plast['page'] = $doclist['last_page'];
        $pagedata['paging'] = [
            'current' => url()->current().'?'.http_build_query($allrequest),
            'starpage' => url()->current().'?'.http_build_query($pfirst),
            'first' => ($doclist['current_page'] == 1) ? '' : url()->current().'?'.http_build_query($pfirst),
            'prev' => ($doclist['current_page'] == 1) ? '' : url()->current().'?'.http_build_query($pprev),
            'now' => 'Page '.$doclist['current_page']." of ".$doclist['last_page'],
            'next' => ($doclist['current_page'] >= $doclist['last_page']) ? '' : url()->current().'?'.http_build_query($pnext),
            'last' => ($doclist['current_page'] >= $doclist['last_page']) ? '' : url()->current().'?'.http_build_query($plast),
        ];         
        return view('docupload.doc-upload', $pagedata)->with(compact('doclist'));
    }
 
    public function store(Request $request)
    {         
            $request->validate([
                'file' => 'required|mimes:csv,txt,xlx,xls,xlsx,pdf,docx,pptx,jpg,png|max:2048'
            ]);

            $file = $request->file;
            $filetypeallowed = ['text/plain', '	application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/csv', 'image/png', 'application/pdf', 'image/png', 'imge/jpeg', 'image/jpg', 'image/gif'];            
            if(!empty($file))
            {
                if (in_array($file->getMimeType(), $filetypeallowed)) {
                    $docdestinationPath = 'docfiles';
                    $olddocfile = $file->getClientOriginalName();                   
                    $extensionname = $request->file->extension();
                    $size = $request->file->getSize();
                    $newdocfile = md5($olddocfile) . "." . $extensionname;
                    $file->move($docdestinationPath,$olddocfile);
                    rename(public_path($docdestinationPath.'/'.$olddocfile), public_path($docdestinationPath.'/'.$newdocfile));
                    $docfileurl = $docdestinationPath.'/'.$newdocfile;
                    $docname = pathinfo($olddocfile, PATHINFO_FILENAME);
                    $docModel = new Document;
                    
                    $docModel->name = $docname;
                    $docModel->originalname = $olddocfile;
                    $docModel->extensionname = $extensionname;
                    $docModel->encryptname = $newdocfile;
                    $docModel->filesize = $size;
                    $docModel->save();                    
                }        
                return redirect()->back()
                        ->with('success','Document has been uploaded.')
                        ->with('file', $docfileurl); 
            }
    }


    public function downloadfile(Request $request)
    {
        $document = Document::where('id', $request->id)->first();
        $name = $document->name;
        
        if($varname = $name){
            $filepath = public_path(). '/docfiles/'.$document->encryptname;
            $newname = $varname. "." .$document->extensionname;  
            return Response::download($filepath, $newname);
        }        
    }

    public function show($docid)
    {

    }

    public function docedit(Request $request)
    {
        $userinfo =$request->get('userinfo');
        $docid =$request->get('id');
        $pagedata = array(
            'userinfo'=>$userinfo,
            'docid'=>$docid,
            'pagetitle' => 'Docs'
        );
        $doclist = Document::findOrFail($docid);
        $pagedata['data']=$doclist;
        return view('docupload.doc-edit', $pagedata);
        
    }

    public function doceditprocess(Request $request, $docid)
    {
        $updateData = $request->validate([
            'name' => 'required|max:255',
        ]);
        $newdoc = Document::whereId($docid)->update($updateData);               
        return redirect()->route('doc-upload')->with('success', 'Document has been updated');        
    }

    public function destroy(Request $request)
    {          
        $document = Document::where('id', $request->id)->first();
        $name = $document->name;
        $filepath = public_path(). '/docfiles/'.$document->encryptname; 

        if(!empty($name)){
            File::delete($filepath);
        }
        $document->delete(); 
        return redirect()->route('doc-upload')->with('success', 'Document has been deleted');   
    }

    public function docview(Request $request)
    {        
        $document = Document::where('id', $request->id)->first();
        $name = $document->name;
        $filepath = public_path(). '/docfiles/'.$document->encryptname;
        
        if(!empty($name)){
            $mime = File::mimeType($filepath);     
            $file = File::get($filepath);
            $response = Response::make($file, 200);            
            $response->header('Content-Type', $mime);            
            return $response;
        }
    }

}