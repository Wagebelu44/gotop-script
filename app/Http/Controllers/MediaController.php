<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Image;

class MediaController
{
    public $basePath = '';
    public $originalPath = '';
    public $file = '';
    public $name = '';
    public $thumbPath = '';
    public $thumb = false;
    public $storageFolder = 'storage/';

    //Common File Upload Function...
    private function upload()
    {
        $file = $this->file;
        if ($this->name) {
            $fileName = Str::slug($this->name, '-').'.'.$file->getClientOriginalExtension();
        } else {
            $newName = str_replace('.'.$file->getClientOriginalExtension(), '', $file->getClientOriginalName());
            $fileName = time().'-'.Str::slug($newName, '-').'.'.$file->getClientOriginalExtension();
        }
        $data['name'] = $fileName;
        $data['originalName'] = $file->getClientOriginalName();
        $data['size'] = $file->getSize();
        $data['mime_type'] = $file->getMimeType();
        $data['ext'] = $file->getClientOriginalExtension();
        $data['url'] = url($this->storageFolder.$this->originalPath.$data['name']);
        
        Storage::putFileAs($this->originalPath, $file, $data['name']);

        if ($this->thumb) {
            Image::make($this->storageFolder.$this->originalPath.$data['name'])
            ->resize(300, 300)
            ->save($this->storageFolder.$this->thumbPath.'/'.$data['name']);
        }
        return $data;
    }

    //Upload Image ("$definePath" and "$definePath/thumb") folder....
    public function imageUpload($requestFile, $path, $thumb = false, $name = null)
    {
        //Path Create...
        $realPath = $this->basePath.$path.'/';
        if (!Storage::exists($realPath)) { 
            Storage::makeDirectory($realPath); 
        }

        if (!Storage::exists($realPath.'thumb') && $thumb) { 
            Storage::makeDirectory($realPath.'thumb'); 
        }

        $this->file = $requestFile;
        $this->originalPath = $realPath;
        $this->thumbPath = $realPath.'thumb';
        $this->thumb = $thumb;
        $this->name = $name;
        return $this->upload();
    }

    //Upload Video in "$definePath" folder....
    public function videoUpload($requestFile, $path, $name = null)
    {
        //Path Create...
        $realPath = $this->basePath.$path.'/';
        if (!Storage::exists($realPath)) { 
            Storage::makeDirectory($realPath); 
        }

        $this->file = $requestFile;
        $this->originalPath = $realPath;
        $this->name = $name;
        return $this->upload();
    }

    //Upload AnyFile in "$definePath" folder....
    public function anyUpload($requestFile, $path, $name = null)
    {
        //Path Create...
        $realPath = $this->basePath.$path.'/';
        if (!Storage::exists($realPath)) { 
            Storage::makeDirectory($realPath); 
        }

        $this->file = $requestFile;
        $this->originalPath = $realPath;
        $this->name = $name;
        return $this->upload();
    }

    //Only thumb image create in "$definePath/thumb" folder....
    public function thumb($path, $file, $thumbPath = false)
    {
        $realPath = $this->basePath.$path;
        if (!$thumbPath) {
            $thumbPath = $this->basePath.$path.'/thumb';
        }

        if (!Storage::exists($thumbPath)) { 
            Storage::makeDirectory($thumbPath); 
        }

        $img = Image::make($this->storageFolder.$realPath.'/'.$file)
        ->resize(300, 300)
        ->save($this->storageFolder.$thumbPath.'/'.$file);

        if (isset($img->filename)) {
            return true;
        } else {
            return false;
        }
    }

    //Delete file "$definePath" folder....
    public function delete($path, $file, $thumb = false)
    {
        $path = $this->basePath.$path.'/';
        if (file_exists($path.'/'.$file)) {
            Storage::delete($path.'/'.$file);

            if ($thumb) {
                Storage::delete($path.'/thumb/'.$file);
            }
            return true;
        }
        return false;
    }
}
