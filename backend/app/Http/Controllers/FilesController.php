<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;

class FilesController extends Controller
{
    public function filesList()
    {
        return File::orderByDesc('created_at')->limit(10)->get();
    }

    public function upload(Request $request)
    {

        $request->validate([
            'files' => 'required|max:2048'
        ]);

        if ($request->file()) {
            $files = $request->file('files');
            foreach ($files as $file) {
                $fileUpload = new File();
                $file_name = $file->getClientOriginalName();
                $file_path = $file->storeAs('uploads', $file_name, 'public');

                $fileUpload->name = $file_name;
                $fileUpload->size = $file->getSize();
                $fileUpload->url = storage_path('app/public/'.$file_path);
                $fileUpload->save();
            }

            return response()->json(['success' => 'File uploaded successfully.']);

        }
    }

    public function download($id)
    {
        $file = File::where('id', $id)->firstOrFail();

        return response()->download($file->url);
    }
}
