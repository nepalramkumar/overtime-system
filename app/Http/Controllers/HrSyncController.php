<?php

namespace App\Http\Controllers;

use App\Services\HrSyncService;
use Illuminate\Http\Request;

class HrSyncController extends Controller
{
    public function index()
    {
        return view('hr-sync.index');
    }

    public function run(HrSyncService $syncService)
    {
        $summary = $syncService->runFullSync();

        return redirect()->route('hr-sync.index')->with('summary', $summary);
    }
}