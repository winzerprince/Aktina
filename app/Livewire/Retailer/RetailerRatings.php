<?php

namespace App\Livewire\Retailer;

use App\Services\RetailerSalesService;
use Livewire\Component;

class RetailerRatings extends Component
{
    public $timeframe = '30';
    public $sortBy = 'rating';
    public $sortDirection = 'desc';

    public function mount()
    {
        // Initialize component
    }

    public function updateTimeframe($timeframe)
    {
        $this->timeframe = $timeframe;
    }

    public function sortRatings($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'desc';
        }
    }

    public function render()
    {
        $retailerSalesService = app(RetailerSalesService::class);
        $user = auth()->user();

        // Mock data for ratings until we have actual rating system
        $ratingsData = [
            'average_rating' => 4.2,
            'total_reviews' => 156,
            'rating_distribution' => [
                5 => 65,
                4 => 45,
                3 => 30,
                2 => 12,
                1 => 4
            ],
            'recent_reviews' => [
                [
                    'customer_name' => 'John Smith',
                    'rating' => 5,
                    'comment' => 'Excellent service and fast delivery!',
                    'date' => now()->subDays(2),
                    'product' => 'Premium Coffee Beans'
                ],
                [
                    'customer_name' => 'Jane Doe',
                    'rating' => 4,
                    'comment' => 'Good quality products, will order again.',
                    'date' => now()->subDays(5),
                    'product' => 'Organic Tea Leaves'
                ],
                [
                    'customer_name' => 'Mike Johnson',
                    'rating' => 5,
                    'comment' => 'Outstanding customer support and product quality.',
                    'date' => now()->subWeek(),
                    'product' => 'Specialty Spices'
                ],
            ]
        ];

        $performanceMetrics = [
            'response_time' => '2.5 hours',
            'resolution_rate' => '98%',
            'customer_satisfaction' => '95%',
            'repeat_customer_rate' => '78%'
        ];

        return view('livewire.retailer.retailer-ratings', [
            'ratingsData' => $ratingsData,
            'performanceMetrics' => $performanceMetrics,
        ]);
    }
}
