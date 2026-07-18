@props(['breadcrumbs' => null])
<header class="flex flex-col border-b border-gray-200 bg-white shrink-0">
    <div class="h-16 px-6 flex items-center justify-between">
        <div class="flex items-center">
            <button @click="sidebarCollapsed = !sidebarCollapsed" 
                    class="p-1.5 mr-4 text-gray-500 hover:text-gray-900 hover:bg-gray-50 border border-gray-200 rounded-lg transition-colors duration-150 shrink-0 cursor-pointer" 
                    title="Toggle Sidebar">
                <i class="fa-solid fa-bars text-sm"></i>
            </button>

            @if (isset($breadcrumbs))
                <nav class="flex items-center text-sm font-medium text-gray-500" aria-label="Breadcrumb">
                    <ol class="flex items-center space-x-2">
                        @foreach ($breadcrumbs as $index => $crumb)
                            @if ($index > 0)
                                <li class="text-gray-300">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                                    </svg>
                                </li>
                            @endif
                            <li>
                                @if ($crumb['url'])
                                    <a href="{{ $crumb['url'] }}" class="hover:text-gray-700 transition-colors duration-150">{{ $crumb['label'] }}</a>
                                @else
                                    <span class="text-gray-900 font-semibold">{{ $crumb['label'] }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </nav>
            @endif
        </div>
    </div>
</header>
