@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <livewire:production-manager.production-order-management />
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-yellow-600 h-2 rounded-full" style="width: 65%"></div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">PRD-003</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Motor Unit C-300</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">25 units</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Urgent</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Jul 06</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-red-600 h-2 rounded-full" style="width: 25%"></div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">PRD-004</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Control Panel D-400</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">40 units</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Scheduled</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Jul 12</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: 15%"></div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    // Order Status Chart
    var orderStatusOptions = {
        series: [31, 12, 4],
        chart: {
            type: 'donut',
            height: 250
        },
        labels: ['Completed', 'In Progress', 'Urgent'],
        colors: ['#10B981', '#F59E0B', '#EF4444']
    };

    var orderStatusChart = new ApexCharts(document.querySelector("#order-status-chart"), orderStatusOptions);
    orderStatusChart.render();
</script>
@endpush
@endsection
