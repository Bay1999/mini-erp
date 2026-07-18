<x-layouts.app :breadcrumbs="$breadcrumbs ?? null">
    <x-slot:title>Payment Details</x-slot:title>

    <div class="max-w-2xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">Payment Details</h1>
                <p class="text-sm text-gray-500">View detailed receipt information for this payment transaction.</p>
            </div>
            <a href="{{ route('payment.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-gray-600 hover:text-cyan-600 transition-colors duration-150">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                Back to List
            </a>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="p-6 bg-gray-50/50 border-b border-gray-100 flex items-center gap-6">
                <div class="h-16 w-16 rounded-2xl bg-cyan-600/10 text-cyan-700 flex items-center justify-center font-bold text-2xl">
                    <i class="fa-solid fa-receipt"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Payment Receipt</h2>
                    <p class="text-sm font-mono text-cyan-600 mt-1 font-semibold">{{ $payment->code }}</p>
                </div>
            </div>

            <div class="p-6 divide-y divide-gray-100">
                <div class="py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <span class="text-sm font-medium text-gray-500">Payment Date</span>
                    <span class="text-sm font-semibold text-gray-900">{{ \Carbon\Carbon::parse($payment->date)->format('M d, Y') }}</span>
                </div>

                <div class="py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <span class="text-sm font-medium text-gray-500">Sales Invoice</span>
                    <a href="{{ route('sales.show', $payment->sales_code) }}" class="text-sm font-mono font-bold text-cyan-600 hover:underline">
                        {{ $payment->sales_code }}
                    </a>
                </div>

                <div class="py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <span class="text-sm font-medium text-gray-500">Paid Amount</span>
                    <span class="text-sm font-bold text-cyan-600 text-lg">Rp {{ number_format($payment->amount, 2) }}</span>
                </div>

                <div class="py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <span class="text-sm font-medium text-gray-500">Recorded At</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $payment->created_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</span>
                </div>
            </div>

            <div class="p-6 bg-gray-50/50 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="{{ route('payment.edit', $payment->code) }}" class="inline-flex items-center gap-2 bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 text-sm font-semibold rounded-lg shadow-sm transition-colors duration-150 cursor-pointer">
                    <i class="fa-solid fa-pen text-xs"></i>
                    Edit Payment
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>
