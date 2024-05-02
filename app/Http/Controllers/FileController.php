<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function download($file_path)
    {
        $path = storage_path('app/' . $file_path);
        
        return response()->download($path);
    }

    public function store(Request $request)
    {
        $fields = $request->validate([
            'todo_group_id' => 'required|string',
            'file.' => 'array'
        ]);
        foreach ($request->file('files') as $file) {
            $filePath = $file->store();
            $fileModel = new File();
            $fileModel->file_path = $filePath;
            $fileModel->file_name = $file->getClientOriginalName();
            $fileModel->todo_group_id = $fields['todo_group_id'];
            $fileModel->save();
        }
        return response($filePath, 201);
    }

    public function destroy(Request $request, File $file)
    {
        $fields = $request->validate([
            'ids' => 'required|array'
        ]);
        foreach($fields['ids'] as $id){
            $todo = File::findOrFail($id);
            $todo->delete();
        }
        return response('Deleted');
    }
}
