<?php

namespace App\Http\Controllers;

use App\Service\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function orderSummary(Request $request)
    {
        $request->validate([
            'from' => 'nullable|date',
            'to'   => 'nullable|date|after_or_equal:from',
        ]);

        $from = $request->input('from');
        $to   = $request->input('to');

        return response()->json(
            $this->dashboardService->getOrderSummary($from, $to)
        );
    }
}