<?php

namespace App\Http\Controllers;

use App\Exports\FamilyExport;
use App\Services\FamilyService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class FamilyController extends Controller
{
    public function __construct(
        protected FamilyService $familyService,
        protected UserService $userService,
    ) {}

    public function index(Request $request)
    {
        $search = $request->search;
        $families = [];
        $totalFamilies = 0;

        if (! $search) {
            // Get total families count
            $totalFamilies = $this->userService->getTotalFamilies();

            return view('families.index', compact('families', 'search', 'totalFamilies'));
        }

        // Search for families
        $result = $this->familyService->searchFamilies($search);
        $families = $result['families'];
        $totalFamilies = $result['count'];

        return view('families.index', compact('families', 'search', 'totalFamilies'));
    }

    public function show($familyCode)
    {
        $data = $this->familyService->getFamilyDetails($familyCode);

        return view('families.show', $data);
    }

    public function export($familyCode)
    {
        $data = $this->familyService->getFamilyDetails($familyCode);

        if (empty($data['membersData'])) {
            return redirect()->back()->with('error', 'No family members found');
        }

        return Excel::download(
            new FamilyExport($data['membersData'], $familyCode),
            'family_'.$familyCode.'_'.date('Y-m-d_H-i-s').'.xlsx'
        );
    }
}
