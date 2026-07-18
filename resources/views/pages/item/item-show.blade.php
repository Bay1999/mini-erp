<x-layouts.app :breadcrumbs="$breadcrumbs ?? null">
    <x-slot:title>Item Details</x-slot:title>

    <div class="max-w-2xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">Item Details</h1>
                <p class="text-sm text-gray-500">View detailed catalog information for this item.</p>
            </div>
            <a href="{{ route('master.item.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-gray-600 hover:text-cyan-600 transition-colors duration-150">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                Back to List
            </a>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="p-6 bg-gray-50/50 border-b border-gray-100 flex items-center gap-6">
                <div class="h-24 w-24 rounded-2xl border border-gray-200 overflow-hidden bg-gray-50 flex items-center justify-center shrink-0 shadow-sm cursor-pointer hover:opacity-80 transition-opacity duration-150" onclick="window.showImagePopup('{{ $item->image ? asset('storage/' . $item->image) : 'https://images.unsplash.com/photo-1546502208-81d149d52bd7?w=800&auto=format&fit=crop' }}')">
                    <img src="{{ $item->image ? asset('storage/' . $item->image) : 'https://images.unsplash.com/photo-1546502208-81d149d52bd7?w=200&auto=format&fit=crop&q=60' }}" class="h-full w-full object-cover">
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $item->name }}</h2>
                    <p class="text-sm font-mono text-cyan-600 mt-1 font-semibold">{{ $item->code }}</p>
                </div>
            </div>

            <div class="p-6 divide-y divide-gray-100">
                <div class="py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <span class="text-sm font-medium text-gray-500">Item Code</span>
                    <span class="text-sm font-mono font-semibold text-gray-900">{{ $item->code }}</span>
                </div>

                <div class="py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <span class="text-sm font-medium text-gray-500">Item Name</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $item->name }}</span>
                </div>

                <div class="py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <span class="text-sm font-medium text-gray-500">Unit Price</span>
                    <span class="text-sm font-bold text-cyan-600 text-lg">Rp {{ number_format($item->price, 2) }}</span>
                </div>

                <div class="py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <span class="text-sm font-medium text-gray-500">Created At</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $item->created_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</span>
                </div>

                <div class="py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <span class="text-sm font-medium text-gray-500">Last Updated</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $item->updated_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</span>
                </div>
            </div>

            <div class="p-6 bg-gray-50/50 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="{{ route('master.item.edit', $item->code) }}" class="inline-flex items-center gap-2 bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 text-sm font-semibold rounded-lg shadow-sm transition-colors duration-150 cursor-pointer">
                    <i class="fa-solid fa-pen text-xs"></i>
                    Edit Item
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>
