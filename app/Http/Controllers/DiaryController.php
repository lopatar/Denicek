<?php

namespace App\Http\Controllers;

use App\Models\UploadedFile;
use Illuminate\Http\Request;
use App\Models\DiaryEntry;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class DiaryController extends Controller
{
    public function index()
    {
         return redirect('/page/1');
    }

    public function page(int $page)
    {
        $diaryEntries = $this->getDiaryEntries($page);
        $average = \round($diaryEntries->avg('rating'), 1);
        return view('home', ['entries' => $diaryEntries, 'average' => $average]);
    }

    public function detail(DiaryEntry $entry)
    {
        $this->ensureOwnership($entry);

        /**
         * @var UploadedFile[] $uploadedFiles
         */
        $uploadedFiles = $entry->uploadedFiles()->get();

        return view('detail', ['entry' => $entry, 'uploadedFiles' => $uploadedFiles]);
    }

    public function update(Request $request, DiaryEntry $entry)
    {
        $this->ensureOwnership($entry);

        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'rating' => 'required|integer|min:1|max:5'
        ]);

        $data['title'] = Crypt::encryptString($data['title']);
        $data['description'] = Crypt::encryptString($data['description']);

        $entry->update($data);
        return redirect()->route('home');
    }

    public function file(UploadedFile $file)
    {
        $this->ensureOwnership($file->diary_entry()->get()[0]);
        $filePath = Crypt::decryptString($file->file_path);
        $fileName = \explode('/', $filePath)[1];

        return response()->streamDownload(function() use($filePath) {
            echo Crypt::decrypt(Storage::get($filePath));
        }, $fileName);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'uploaded_file' => 'file'
        ]);

        $data['title'] = Crypt::encryptString($data['title']);
        $data['description'] = Crypt::encryptString($data['description']);
        
        /**
         * @var DiaryEntry $created
         */
        $created = auth()->user()->diaryEntries()->create($data);

        $file = $request->allFiles();
        
        if ($file !== null) {
            foreach ($file as $f)
            {
                $fileContent = Crypt::encrypt($f->get());
                $filePath = 'files/' . $f->hashName();
                Storage::put($filePath, $fileContent);
                $filePath = Crypt::encryptString($filePath);
                $created->uploadedFiles()->create([
                    'file_path' => $filePath
                ]);
            }
        }

        return redirect("/entry/$created->id");
    }

    public function destroy(DiaryEntry $entry)
    {
        $this->ensureOwnership($entry);
        
        /**
         * @var UploadedFile $file
         */
        foreach ($entry->uploadedFiles()->get() as $file)
        {
            Storage::delete(Crypt::decryptString($file->file_path));
        }

        $entry->delete();

        return back();
    }

    private function ensureOwnership(DiaryEntry $entry)
    {
        if (auth()->user()->id !== $entry->user_id) {
            abort(404);
        }
    }

    private function getDiaryEntries(?int $page = null): LengthAwarePaginator
    {
        return DiaryEntry::with('user')
            ->latest()
            ->where('user_id', auth()->user()->id)
            ->take(7)
            ->paginate(7, page: $page);
    }
}
