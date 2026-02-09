<?php

namespace App\Http\Controllers;

use App\Models\CompanyProfileDownload;
use Illuminate\Http\Request;

class CompanyProfileDownloadController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');

        $query = CompanyProfileDownload::query()
            ->when($search, function ($q) use ($search) {
                $q->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', '%'.$search.'%')
                        ->orWhere('company_name', 'like', '%'.$search.'%')
                        ->orWhere('whatsapp', 'like', '%'.$search.'%');
                });
            })
            ->when($dateFrom, fn ($q) => $q->whereDate('downloaded_at', '>=', $dateFrom))
            ->when($dateTo, fn ($q) => $q->whereDate('downloaded_at', '<=', $dateTo))
            ->orderByDesc('downloaded_at');

        $downloads = $query->paginate(15)->withQueryString();

        return view('company-profile-downloads.index', [
            'downloads' => $downloads,
            'title' => 'Company Profile Downloads',
            'subtitle' => 'Logs',
        ]);
    }

    public function show(CompanyProfileDownload $companyProfileDownload)
    {
        return view('company-profile-downloads.show', [
            'download' => $companyProfileDownload,
            'title' => 'Company Profile Downloads',
            'subtitle' => 'Detail',
        ]);
    }

    public function destroy(CompanyProfileDownload $companyProfileDownload)
    {
        $companyProfileDownload->delete();

        return redirect()
            ->route('company-profile-downloads.index')
            ->with('success', 'Download record deleted.');
    }
}
