<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DiaryEntry;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class DiaryController extends Controller
{
    public function index()
    {
        $diaryEntries = DiaryEntry::with('user')
            ->latest()
            ->where('user_id', auth()->user()->id)
            ->get();

        $ratingTotal = 0;
        $entryCount = \count($diaryEntries);

        foreach ($diaryEntries as $diaryEntry) {
            $ratingTotal += \intval(Crypt::decryptString($diaryEntry->rating));
        }

        $average = ($entryCount > 0) ? \round($ratingTotal / $entryCount, 1) : 0;

        return view('home', ['entries' => $diaryEntries, 'average' => $average]);
    }

    public function detail(DiaryEntry $entry)
    {
        $this->ensureOwnership($entry);

        return view('detail', ['entry' => $entry]);
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
        $data['rating'] = Crypt::encryptString($data['rating']);

        $entry->update($data);
        return redirect()->route('home');
    }

    public function file(DiaryEntry $entry)
    {
        $this->ensureOwnership($entry);

        $filePath = Crypt::decryptString($entry->uploaded_file);
        return Storage::download($filePath);
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
        $data['rating'] = Crypt::encryptString($data['rating']);
        
        $file = $request->file('uploaded_file');

        if ($file !== null) {
            $path = $file->store('files');
            $data['uploaded_file'] = Crypt::encryptString($path);
        }

        $created = auth()->user()->diaryEntries()->create($data);

        return redirect("/entry/$created->id");
    }

    public function destroy(DiaryEntry $entry)
    {
        $this->ensureOwnership($entry);

        $entry->delete();
        return back();
    }

    private function ensureOwnership(DiaryEntry $entry)
    {
        if (auth()->user()->id !== $entry->user_id) {
            abort(404);
        }
    }
}
