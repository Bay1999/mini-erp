<x-layouts.app :breadcrumbs="$breadcrumbs ?? null">
    <x-slot:title>Sales Details</x-slot:title>

    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">Sales Details</h1>
                <p class="text-sm text-gray-500">View detailed invoice information for this sales transaction.</p>
            </div>
            <a href="{{ route('sales.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-gray-600 hover:text-cyan-600 transition-colors duration-150">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                Back to List
            </a>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="p-6 bg-gray-50/50 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Sales Invoice</h2>
                    <p class="text-sm font-mono text-cyan-600 mt-1 font-semibold">{{ $sales->sales_code }}</p>
                </div>
                <div>
                    @php
                        $statusColors = [
                            'paid' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'unpaid' => 'bg-rose-50 text-rose-700 border-rose-200',
                            'partially_paid' => 'bg-amber-50 text-amber-700 border-amber-200',
                        ];
                        $colorClass = $statusColors[$sales->status] ?? 'bg-gray-50 text-gray-700 border-gray-200';
                    @endphp
                    <span class="px-3 py-1.5 rounded-full border text-xs font-bold {{ $colorClass }}">
                        {{ ucfirst(str_replace('_', ' ', $sales->status)) }}
                    </span>
                </div>
            </div>

            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-4 border-b border-gray-100 bg-gray-50/10">
                <div>
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Transaction Date</span>
                    <span class="text-sm font-bold text-gray-800 mt-1 block">{{ \Carbon\Carbon::parse($sales->date)->format('M d, Y') }}</span>
                </div>
                <div>
                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Created At</span>
                    <span class="text-sm font-bold text-gray-800 mt-1 block">{{ $sales->created_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</span>
                </div>
            </div>

            <div class="p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Transaction Items</h3>
                <div class="overflow-x-auto border border-gray-100 rounded-xl">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Item</th>
                                <th scope="col" class="px-4 py-3 text-right font-semibold text-gray-600 w-24">Qty</th>
                                <th scope="col" class="px-4 py-3 text-right font-semibold text-gray-600 w-44">Price</th>
                                <th scope="col" class="px-4 py-3 text-right font-semibold text-gray-600 w-44">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach($sales->details as $detail)
                                <tr>
                                    <td class="px-4 py-3 font-semibold text-gray-800">
                                        {{ $detail->item->name ?? 'Unknown Item' }}
                                        <span class="text-xs text-gray-400 font-mono block">{{ $detail->item_code }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-right text-gray-600">{{ $detail->qty }}</td>
                                    <td class="px-4 py-3 text-right text-gray-600">Rp {{ number_format($detail->price, 2) }}</td>
                                    <td class="px-4 py-3 text-right font-bold text-gray-700">Rp {{ number_format($detail->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50/50">
                            <tr>
                                <td colspan="3" class="px-4 py-4 text-right font-bold text-gray-700">Grand Total</td>
                                <td class="px-4 py-4 text-right font-black text-cyan-600 text-base">Rp {{ number_format($sales->grand_total, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="p-6 bg-gray-50/50 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="{{ route('sales.edit', $sales->sales_code) }}" class="inline-flex items-center gap-2 bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 text-sm font-semibold rounded-lg shadow-sm transition-colors duration-150 cursor-pointer">
                    <i class="fa-solid fa-pen text-xs"></i>
                    Edit Sales
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>
