<?php

namespace App\Http\Controllers;

use App\Models\DiaryEntry;
use App\Models\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function file(UploadedFile $file)
    {
        $this->ensureOwnership($file->diary_entry()->get()[0]);
        $filePath = Crypt::decryptString($file->file_path);
        // get the file name after folder name
        $fileName = \explode('/', $filePath)[1];

        return response()->streamDownload(function () use ($filePath) {
            echo Crypt::decrypt(Storage::get($filePath));
        }, $fileName);
    }

    public function deleteFile(UploadedFile $file)
    {
        $this->ensureOwnership($file->diary_entry()->get()[0]);
        Storage::delete(Crypt::decryptString($file->file_path));
        $file->delete();

        return back();
    }

    private function ensureOwnership(DiaryEntry $entry)
    {
        // Return 404 to avoid entry enumeration
        if (auth()->user()->id !== $entry->user_id) {
            abort(404);
        }
    }
}
