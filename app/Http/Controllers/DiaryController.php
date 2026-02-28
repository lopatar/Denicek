<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DiaryEntry;
use Illuminate\Support\Facades\Crypt;

class DiaryController extends Controller
{
    public function index()
    {
        $diaryEntries = DiaryEntry::with('user')
            ->latest()
            ->where('user_id', auth()->user()->id)
            ->get();

        $ratingTotal = 0;

        foreach ($diaryEntries as $diaryEntry) {
            $ratingTotal += \intval(Crypt::decryptString($diaryEntry->rating));
        }

        return view('home', ['entries' => $diaryEntries, 'average' => $ratingTotal / \count($diaryEntries)]);
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
        return back();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'rating' => 'required|integer|min:1|max:5'
        ]);

        $data['title'] = Crypt::encryptString($data['title']);
        $data['description'] = Crypt::encryptString($data['description']);
        $data['rating'] = Crypt::encryptString($data['rating']);

        auth()->user()->diaryEntries()->create($data);

        return back();
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
