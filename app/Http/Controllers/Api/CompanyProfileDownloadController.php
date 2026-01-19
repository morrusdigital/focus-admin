<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyProfileDownloadResource;
use App\Models\CompanyProfileDownload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CompanyProfileDownloadController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        $perPage = (int) $request->query('per_page', 10);
        $perPage = $perPage > 0 ? $perPage : 10;

        $query = CompanyProfileDownload::query()
            ->when($search, function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', '%'.$search.'%')
                        ->orWhere('phone', 'like', '%'.$search.'%');
                });
            })
            ->when($dateFrom, fn ($q) => $q->whereDate('downloaded_at', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->whereDate('downloaded_at', '<=', $dateTo))
            ->orderByDesc('downloaded_at');

        $downloads = $query->paginate($perPage)->withQueryString();

        return CompanyProfileDownloadResource::collection($downloads)
            ->additional([
                'meta' => [
                    'pagination' => [
                        'current_page' => $downloads->currentPage(),
                        'last_page' => $downloads->lastPage(),
                        'per_page' => $downloads->perPage(),
                        'total' => $downloads->total(),
                    ],
                ],
            ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->storeRules());

        $download = CompanyProfileDownload::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'domicile' => $validated['domicile'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'downloaded_at' => now(),
        ]);

        $downloadUrl = Storage::disk('public')->url('company-profile.pdf');

        return response()->json([
            'message' => 'OK',
            'download_url' => $downloadUrl,
        ], 201);
    }

    public function show(CompanyProfileDownload $companyProfileDownload)
    {
        return new CompanyProfileDownloadResource($companyProfileDownload);
    }

    public function destroy(CompanyProfileDownload $companyProfileDownload)
    {
        $companyProfileDownload->delete();

        return response()->noContent();
    }

    private function storeRules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'phone' => ['required', 'regex:/^[0-9]{10,15}$/'],
            'domicile' => ['required', 'string', 'max:255'],
        ];
    }
}
