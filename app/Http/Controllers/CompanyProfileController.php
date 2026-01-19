<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyProfileController extends Controller
{
    public function edit()
    {
        $filePath = 'company-profile.pdf';
        $fileUrl = Storage::disk('public')->exists($filePath)
            ? Storage::disk('public')->url($filePath)
            : null;

        return view('company-profile.edit', [
            'fileUrl' => $fileUrl,
            'title' => 'Company Profile',
            'subtitle' => 'File',
        ]);
    }

    public function update(Request $request)
    {
        $file = $request->file('company_profile');

        if ($file && !$file->isValid()) {
            return back()
                ->withErrors(['company_profile' => $this->uploadErrorMessage($file->getError())])
                ->withInput();
        }

        $validated = $request->validate([
            'company_profile' => ['required', 'file', 'mimes:pdf', 'max:10240'],
        ], $this->validationMessages());

        Storage::disk('public')->putFileAs(
            '',
            $validated['company_profile'],
            'company-profile.pdf'
        );

        return redirect()
            ->route('company-profile.edit')
            ->with('success', 'Company profile updated.');
    }

    private function validationMessages(): array
    {
        return [
            'company_profile.required' => 'File gagal diupload. Pastikan file dipilih dan ukuran tidak melebihi 10MB.',
            'company_profile.file' => 'File tidak valid.',
            'company_profile.mimes' => 'File harus berupa PDF.',
            'company_profile.max' => 'Ukuran file terlalu besar. Maks 10MB.',
            'company_profile.uploaded' => 'Upload gagal. Cek ukuran file dan konfigurasi server (upload_max_filesize/post_max_size).',
        ];
    }

    private function uploadErrorMessage(int $error): string
    {
        return match ($error) {
            UPLOAD_ERR_INI_SIZE => 'Ukuran file melebihi batas server (upload_max_filesize).',
            UPLOAD_ERR_FORM_SIZE => 'Ukuran file melebihi batas form.',
            UPLOAD_ERR_PARTIAL => 'Upload tidak selesai. Coba lagi.',
            UPLOAD_ERR_NO_FILE => 'File belum dipilih.',
            UPLOAD_ERR_NO_TMP_DIR => 'Folder sementara server tidak tersedia.',
            UPLOAD_ERR_CANT_WRITE => 'Gagal menyimpan file ke disk.',
            UPLOAD_ERR_EXTENSION => 'Upload dihentikan oleh ekstensi PHP.',
            default => 'Upload gagal. Coba lagi.',
        };
    }
}
