<?php

namespace App\Http\Controllers;

use App\Models\DiaryEntry;
use App\Models\UploadedFile;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class DiaryController extends Controller
{
    public function page(int $page)
    {
        $diaryEntries = DiaryEntry::with('user')
            ->latest()
            ->where('user_id', Auth::user()->id)
            ->take(7)
            ->paginate(7, page: $page);

        /**
         * @var \Illuminate\Support\Carbon $showTodayAlertAgain
         */
        $showTodayAlertAgain = session()->get('showTodayAlertAgain');

        // If saved tomorrow time indicating showing the alert had passed, reset the session variable
        if ($showTodayAlertAgain?->isNowOrPast()) {
            session([
                'shownTodayAlert' => false,
            ]);
        }

        // If we are on the main page AND there are no entries made today AND we did not show the alert yet. It is a reminder not to forget the todays entry.
        $showTodayAlert = $page === 1
            && !DiaryEntry::whereToday('created_at')->exists()
            && !session()->get('shownTodayAlert');

        // If we are showing the alert, set a session variable indicating its been shown and save tomorrow time for the alert to be reset.
        if ($showTodayAlert) {
            session([
                'shownTodayAlert' => true,
                'showTodayAlertAgain' => Carbon::tomorrow()
            ]);
        }

        $average = \round($diaryEntries->avg('rating'), 1);

        return view('home', ['entries' => $diaryEntries, 'average' => $average, 'showTodayAlert' => $showTodayAlert]);
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
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $data['title'] = Crypt::encryptString($data['title']);
        $data['description'] = Crypt::encryptString($data['description']);

        $entry->update($data);

        return Redirect::route('home');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'uploaded_file' => 'file',
        ]);

        $data['title'] = Crypt::encryptString($data['title']);
        $data['description'] = Crypt::encryptString($data['description']);

        /**
         * @var DiaryEntry $created
         */
        $created = Auth::user()->diaryEntries()->create($data);

        $file = $request->allFiles();

        if ($file !== null) {
            foreach ($file as $f) {
                $fileContent = Crypt::encrypt($f->get());
                $filePath = 'files/'.$f->hashName();
                Storage::put($filePath, $fileContent);
                $filePath = Crypt::encryptString($filePath);
                $created->uploadedFiles()->create([
                    'file_path' => $filePath,
                ]);
            }
        }

        return Redirect::to("/entry/$created->id");
    }

    public function destroy(DiaryEntry $entry)
    {
        $this->ensureOwnership($entry);

        /**
         * Delete the uploaded files from disk
         *
         * @var UploadedFile $file
         */
        foreach ($entry->uploadedFiles()->get() as $file) {
            Storage::delete(Crypt::decryptString($file->file_path));
        }

        $entry->delete();

        return back();
    }

    public function ensureOwnership(DiaryEntry $entry)
    {
        // Return 404 to avoid entry enumeration
        if (Auth::user()->id !== $entry->user_id) {
            abort(404);
        }
    }
}
