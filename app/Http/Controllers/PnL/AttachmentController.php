<?php

namespace App\Http\Controllers\PnL;

use App\Http\Controllers\Controller;
use App\Models\PnL\PnlAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AttachmentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'attachable_type' => 'required|string',
            'attachable_id' => 'required|uuid',
            'file' => 'required|file|max:10240', // 10MB max
            'type' => ['required', Rule::in(array_keys(PnlAttachment::getTypes()))],
            'description' => 'nullable|string|max:500',
        ]);

        $file = $request->file('file');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('pnl-attachments/' . date('Y/m'), $filename, 'public');

        $attachment = PnlAttachment::create([
            'attachable_type' => $validated['attachable_type'],
            'attachable_id' => $validated['attachable_id'],
            'user_id' => auth()->id(),
            'filename' => $filename,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'path' => $path,
            'type' => $validated['type'],
            'description' => $validated['description'],
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'attachment' => $attachment,
            ]);
        }

        return back()->with('success', 'File uploaded successfully!');
    }

    public function download(PnlAttachment $attachment)
    {
        $this->authorize('view', $attachment);

        return Storage::disk('public')->download(
            $attachment->path,
            $attachment->original_filename
        );
    }

    public function destroy(PnlAttachment $attachment)
    {
        $this->authorize('delete', $attachment);

        $attachment->deleteFile();
        $attachment->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'File deleted successfully!');
    }
}
