@props([
    'title' => '',
    'chartId' => 'chart',
    'chartType' => 'bar', // bar, line, pie, doughnut
    'height' => '170',
    'description' => null,
    'stats' => null,
    'chartData' => null,
])

<div {{ $attributes->merge(['class' => 'border-black/12.5 shadow-soft-xl relative z-20 flex min-w-0 flex-col break-words rounded-2xl border-0 border-solid bg-white dark:bg-zinc-800 bg-clip-border']) }}>
    <div class="flex-auto p-4">
        <div class="py-4 pr-1 mb-4 bg-gradient-to-tl from-[#044c03] to-[#008800] rounded-xl">
            <div>
                <canvas id="{{ $chartId }}" height="{{ $height }}"></canvas>
            </div>
        </div>

        @if($title)
            <h6 class="mt-6 mb-0 ml-2 text-gray-800 dark:text-white">{{ $title }}</h6>
        @endif

        @if($description)
            <p class="ml-2 leading-normal text-sm text-gray-600 dark:text-gray-400">{{ $description }}</p>
        @endif

        @if($stats)
            <div class="w-full px-6 mx-auto max-w-screen-2xl rounded-xl">
                <div class="flex flex-wrap mt-0 -mx-3">
                    @foreach($stats as $stat)
                        <div class="flex-none w-1/4 max-w-full py-4 pl-0 pr-3 mt-0">
                            <div class="flex mb-2">
                                <div class="flex items-center justify-center w-5 h-5 mr-2 text-center bg-center rounded fill-current shadow-soft-2xl bg-gradient-to-tl from-[#044c03] to-[#008800] text-neutral-900">
                                    @if(isset($stat['icon']))
                                        <i class="ni ni-{{ $stat['icon'] }} text-xs text-white"></i>
                                    @else
                                        <svg width="10px" height="10px" viewBox="0 0 40 44" version="1.1" xmlns="http://www.w3.org/2000/svg">
                                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                <g transform="translate(-1870.000000, -591.000000)" fill="#FFFFFF" fill-rule="nonzero">
                                                    <g transform="translate(1716.000000, 291.000000)">
                                                        <g transform="translate(154.000000, 300.000000)">
                                                            <path class="color-background" d="M40,40 L36.3636364,40 L36.3636364,3.63636364 L5.45454545,3.63636364 L5.45454545,0 L38.1818182,0 C39.1854545,0 40,0.814545455 40,1.81818182 L40,40 Z" opacity="0.603585379"></path>
                                                            <path class="color-background" d="M30.9090909,7.27272727 L1.81818182,7.27272727 C0.814545455,7.27272727 0,8.08727273 0,9.09090909 L0,41.8181818 C0,42.8218182 0.814545455,43.6363636 1.81818182,43.6363636 L30.9090909,43.6363636 C31.9127273,43.6363636 32.7272727,42.8218182 32.7272727,41.8181818 L32.7272727,9.09090909 C32.7272727,8.08727273 31.9127273,7.27272727 30.9090909,7.27272727 Z M18.1818182,34.5454545 L7.27272727,34.5454545 L7.27272727,30.9090909 L18.1818182,30.9090909 L18.1818182,34.5454545 Z M25.4545455,27.2727273 L7.27272727,27.2727273 L7.27272727,23.6363636 L25.4545455,23.6363636 L25.4545455,27.2727273 Z M25.4545455,20 L7.27272727,20 L7.27272727,16.3636364 L25.4545455,16.3636364 L25.4545455,20 Z"></path>
                                                        </g>
                                                    </g>
                                                </g>
                                            </g>
                                        </svg>
                                    @endif
                                </div>
                                <p class="mt-1 mb-0 font-semibold leading-tight text-xs text-gray-700 dark:text-gray-300">{{ $stat['label'] ?? 'Stat' }}</p>
                            </div>
                            <h4 class="font-bold text-gray-800 dark:text-white">{{ $stat['value'] ?? '0' }}</h4>
                            @if(isset($stat['progress']))
                                <x-ui.progress-bar :value="$stat['progress']['value']" :max="$stat['progress']['max'] ?? 100" size="sm" color="primary" />
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('{{ $chartId }}').getContext('2d');

        @if($chartData)
            const chartData = @json($chartData);
        @else
            const chartData = {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Data',
                    data: [12, 19, 3, 5, 2, 3],
                    backgroundColor: 'rgba(0, 136, 0, 0.5)',
                    borderColor: 'rgba(0, 136, 0, 1)',
                    borderWidth: 1
                }]
            };
        @endif

        new Chart(ctx, {
            type: '{{ $chartType }}',
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#fff'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        ticks: {
                            color: '#fff'
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
