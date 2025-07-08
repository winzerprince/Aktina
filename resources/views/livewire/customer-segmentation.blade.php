<div>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Customer Segmentation Analysis</h2>
            <button wire:click="loadSegmentData" class="p-2 text-blue-500 hover:text-blue-700 rounded-full focus:outline-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
            </button>
        </div>

        <div class="space-y-6">
            @if($isLoading)
                <div class="flex justify-center items-center h-80">
                    <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
                </div>
            @elseif($serviceError)
                <div class="bg-red-50 dark:bg-red-900 p-4 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800 dark:text-red-200">ML Service Unavailable</h3>
                            <p class="mt-2 text-sm text-red-700 dark:text-red-300">
                                Unable to connect to the ML microservice. Please try again later or contact support.
                            </p>
                        </div>
                    </div>
                </div>
            @elseif(empty($segmentData['labels']))
                <div class="bg-yellow-50 dark:bg-yellow-900 p-4 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">No Segment Data Available</h3>
                            <p class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                There isn't enough customer data to generate segments. Add more retailers with demographic information.
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2">
                        <div id="customer-segment-chart" class="h-80"></div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-900 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Segment Analysis</h3>
                        <ul class="space-y-2">
                            @foreach($segmentData['labels'] as $index => $label)
                                <li class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <span class="h-3 w-3 rounded-full bg-blue-{{ ($index * 100) + 300 }} mr-2"></span>
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $label }}</span>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $segmentData['series'][$index] ?? 0 }} retailers
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Segments are based on retailer demographics and sales volume. Retailers with similar characteristics are grouped together.
                            </p>
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('livewire:load', function() {
                        renderCustomerSegmentChart(@json($segmentData));
                    });

                    document.addEventListener('livewire:update', function() {
                        renderCustomerSegmentChart(@json($segmentData));
                    });

                    function renderCustomerSegmentChart(data) {
                        if (!data || !data.labels || !data.series || data.labels.length === 0) {
                            return;
                        }

                        const options = {
                            series: data.series,
                            chart: {
                                type: 'pie',
                                height: 320,
                                fontFamily: 'Inter, sans-serif',
                            },
                            labels: data.labels,
                            legend: {
                                position: 'bottom'
                            },
                            responsive: [{
                                breakpoint: 480,
                                options: {
                                    chart: {
                                        width: 300
                                    },
                                    legend: {
                                        position: 'bottom'
                                    }
                                }
                            }],
                            colors: ['#3B82F6', '#60A5FA', '#93C5FD', '#BFDBFE', '#DBEAFE']
                        };

                        const chart = new ApexCharts(document.querySelector("#customer-segment-chart"), options);
                        chart.render();
                    }
                </script>
            @endif
        </div>
    </div>
</div>
