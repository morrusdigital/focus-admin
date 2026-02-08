<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompanyProfileDownloadResource;
use App\Models\CompanyProfileDownload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Throwable;

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
        $validator = Validator::make($request->all(), $this->storeRules());

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first() ?? 'Invalid request.',
            ], 422);
        }

        $validated = $validator->validated();

        try {
            CompanyProfileDownload::create([
                'name' => $validated['name'],
                'phone' => $validated['phone'],
                'company_phone' => $validated['company_phone'] ?? null,
                'domicile' => $validated['domicile'],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'downloaded_at' => now(),
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'message' => 'Failed to process request. Please try again.',
            ], 400);
        }

        $downloadUrl = Storage::disk('public')->url('company-profile.pdf');

        return response()->json([
            'download_url' => $downloadUrl,
        ], 200);
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
            'company_phone' => ['nullable', 'string', 'max:255'],
            'domicile' => ['required', 'string', 'max:255'],
        ];
    }
}
