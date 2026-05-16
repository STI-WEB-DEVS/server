<?php

namespace App\Service;

use App\Repository\DashboardRepository;

class DashboardService
{
    private DashboardRepository $dashboardRepository;

    public function __construct(DashboardRepository $dashboardRepository)
    {
        $this->dashboardRepository = $dashboardRepository;
    }

    public function getOrderSummary(?string $from, ?string $to): array
    {
        return [
            'from'              => $from,
            'to'                => $to,
            'total_revenue'     => $this->dashboardRepository->getTotalRevenue($from, $to),
            'total_customers'   => $this->dashboardRepository->getUniqueCustomerCount($from, $to),
            'top_products'      => $this->dashboardRepository->getTopProducts($from, $to),
        ];
    }
}