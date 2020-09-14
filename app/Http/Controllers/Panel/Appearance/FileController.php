<?php

namespace App\Http\Controllers\Panel\Appearance;

use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Image;

class FileController extends Controller
{
    public function index()
    {
        if (Auth::user()->can('files')) {
            $data = File::where('panel_id', Auth::user()->panel_id)->orderBy('id', 'DESC')->get();
            return view('panel.appearance.file', compact('data'));
        }else{
            return view('panel.permission');
        }
    }

    public function store(Request $request)
    {
        if (Auth::user()->can('files')) {
            if ($request->has('files')){
                $data = [];
                foreach ($request->file('files') as $file){
                    $size = $file->getSize();
                    $mimeType = $file->getMimeType();
                    $ext  = $file->getClientOriginalExtension();
                    $mainName  = $file->getClientOriginalName();
                    $fileName = uniqid('file-').'.'.$ext;
                    $url = asset('storage/files/'.$fileName);
                    $file = Image::make($file);
                    $data[] = [
                        'panel_id'   => Auth::user()->panel_id,
                        'name'       => $mainName,
                        'size'       => $size,
                        'mime_type'  => $mimeType,
                        'extension'  => $ext,
                        'url'        => $url,
                        'created_by' => Auth::user()->id,
                    ];
                    Storage::disk('public')->put("files/".$fileName, (string) $file->encode());
                }
            }

            if(!empty($data)){
                File::insert($data);
                return redirect()->back()->with('success', 'File save successfully !!');
            }else{
                return redirect()->back()->with('error', "File con't save ! Please try again !!");
            }
        }else{
            return view('panel.permission');
        }

    }

    public function destroy($id)
    {
        if (Auth::user()->can('files')) {
            $data = File::where('panel_id', Auth::user()->panel_id)->where('id', $id)->first();
            if (empty($data)){
                return redirect()->back()->with('error', "This file can't deleted! Please try again !!");
            }
            $data->delete();
            return redirect()->back()->with('success', 'This file delete successfully !!');
        }else{
            return view('panel.permission');
        }
    }
}
