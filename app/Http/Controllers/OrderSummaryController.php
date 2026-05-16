<?php

namespace App\Http\Controllers;

use App\Service\OrderService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class OrderSummaryController extends Controller
{
    private OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $from = $request->query('from', now()->startOfMonth()->toDateString());
        $to = $request->query('to', now()->endOfMonth()->toDateString());

        $fromDateTime = Carbon::parse($from)->startOfDay();
        $toDateTime = Carbon::parse($to)->endOfDay();

        return response()->json(
            $this->orderService->getDashboardStats($fromDateTime, $toDateTime)
        );
    }
}
