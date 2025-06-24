@props([
    'headers' => [],
    'rows' => [],
    'title' => null,
    'searchable' => false,
    'sortable' => false,
    'pagination' => false,
    'actions' => true,
    'rowActions' => ['edit', 'delete'], // edit, delete, view
])

<div {{ $attributes->merge(['class' => 'relative flex flex-col min-w-0 mb-6 break-words bg-white dark:bg-zinc-800 border-0 border-transparent border-solid shadow-soft-xl rounded-2xl bg-clip-border']) }}>
    @if($title || $searchable)
        <div class="p-6 pb-0 mb-0 bg-white dark:bg-zinc-800 border-b-0 border-b-solid rounded-t-2xl border-b-transparent">
            <div class="flex justify-between items-center">
                @if($title)
                    <h6 class="text-gray-800 dark:text-white">{{ $title }}</h6>
                @endif

                @if($searchable)
                    <div class="flex items-center">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <i class="fas fa-search text-gray-400"></i>
                            </span>
                            <input
                                type="text"
                                class="pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-[#008800] focus:border-[#008800] dark:bg-gray-700 dark:text-white"
                                placeholder="Search..."
                                x-data="{ search: '' }"
                                x-model="search"
                            >
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <div class="flex-auto px-0 pt-0 pb-2">
        <div class="p-0 overflow-x-auto">
            <table class="items-center w-full mb-0 align-top border-gray-200 dark:border-gray-600 text-slate-500 dark:text-gray-400">
                <thead class="align-bottom">
                    <tr>
                        @foreach($headers as $header)
                            <th class="px-6 py-3 font-bold text-left uppercase align-middle bg-transparent border-b border-gray-200 dark:border-gray-600 shadow-none text-xxs border-b-solid tracking-none whitespace-nowrap text-slate-400 dark:text-gray-500 opacity-70">
                                @if($sortable && is_array($header) && isset($header['sortable']) && $header['sortable'])
                                    <button class="flex items-center space-x-1 hover:text-[#008800] transition-colors">
                                        <span>{{ is_array($header) ? $header['label'] : $header }}</span>
                                        <i class="fas fa-sort text-xs"></i>
                                    </button>
                                @else
                                    {{ is_array($header) ? $header['label'] : $header }}
                                @endif
                            </th>
                        @endforeach

                        @if($actions)
                            <th class="px-6 py-3 font-semibold capitalize align-middle bg-transparent border-b border-gray-200 dark:border-gray-600 border-solid shadow-none tracking-none whitespace-nowrap text-slate-400 dark:text-gray-500 opacity-70">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $index => $row)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            @foreach($row as $cell)
                                <td class="p-2 align-middle bg-transparent border-b dark:border-gray-600 whitespace-nowrap shadow-transparent">
                                    @if(is_array($cell))
                                        @if($cell['type'] === 'user')
                                            <div class="flex px-2 py-1">
                                                <div>
                                                    @if(isset($cell['avatar']))
                                                        <img src="{{ $cell['avatar'] }}" class="inline-flex items-center justify-center mr-4 text-sm text-white transition-all duration-200 ease-soft-in-out h-9 w-9 rounded-xl" alt="{{ $cell['name'] }}" />
                                                    @else
                                                        <div class="inline-flex items-center justify-center mr-4 text-sm text-white bg-gradient-to-tl from-[#044c03] to-[#008800] h-9 w-9 rounded-xl">
                                                            {{ substr($cell['name'], 0, 1) }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="flex flex-col justify-center">
                                                    <h6 class="mb-0 text-sm leading-normal text-gray-800 dark:text-white">{{ $cell['name'] }}</h6>
                                                    @if(isset($cell['email']))
                                                        <p class="mb-0 text-xs leading-tight text-slate-400">{{ $cell['email'] }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        @elseif($cell['type'] === 'status')
                                            <x-ui.status-badge :status="$cell['status']" />
                                        @elseif($cell['type'] === 'progress')
                                            <x-ui.progress-bar :value="$cell['value']" :max="$cell['max'] ?? 100" size="sm" />
                                        @else
                                            {!! $cell['content'] ?? '' !!}
                                        @endif
                                    @else
                                        <span class="text-xs font-semibold leading-tight text-gray-600 dark:text-gray-300">{{ $cell }}</span>
                                    @endif
                                </td>
                            @endforeach

                            @if($actions)
                                <td class="p-2 align-middle bg-transparent border-b dark:border-gray-600 whitespace-nowrap shadow-transparent">
                                    <div class="flex space-x-2">
                                        @foreach($rowActions as $action)
                                            @if($action === 'edit')
                                                <button class="text-xs font-semibold leading-tight text-[#008800] hover:text-[#044c03] transition-colors">
                                                    <i class="fas fa-edit mr-1"></i>Edit
                                                </button>
                                            @elseif($action === 'delete')
                                                <button class="text-xs font-semibold leading-tight text-red-500 hover:text-red-700 transition-colors">
                                                    <i class="fas fa-trash mr-1"></i>Delete
                                                </button>
                                            @elseif($action === 'view')
                                                <button class="text-xs font-semibold leading-tight text-blue-500 hover:text-blue-700 transition-colors">
                                                    <i class="fas fa-eye mr-1"></i>View
                                                </button>
                                            @endif
                                        @endforeach
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($headers) + ($actions ? 1 : 0) }}" class="p-8 text-center text-gray-500 dark:text-gray-400">
                                <i class="fas fa-inbox text-4xl mb-4 opacity-50"></i>
                                <p>No data available</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($pagination)
        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-600">
            <!-- Pagination controls would go here -->
            <div class="flex justify-between items-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Showing 1 to {{ count($rows) }} of {{ count($rows) }} results
                </p>
                <div class="flex space-x-2">
                    <button class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Previous</button>
                    <button class="px-3 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Next</button>
                </div>
            </div>
        </div>
    @endif
</div>
