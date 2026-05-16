<?php

namespace App\Http\Controllers;

use App\Service\OrderSummaryService;
use Illuminate\Http\Request;

class OrderSummaryController extends Controller
{
    private OrderSummaryService $service;

    public function __construct(OrderSummaryService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');

        $summary = $this->service->getSummary($from, $to);
        return response()->json($summary, 200);
    }
}
