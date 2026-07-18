<x-layouts.app :breadcrumbs="$breadcrumbs ?? null">
    <x-slot:title>
        {{ isset($payment) ? 'Edit Payment' : 'Create Payment' }}
    </x-slot:title>

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <style>
            .select2-container--default .select2-selection--single {
                border-color: #e2e8f0 !important;
                border-radius: 0.5rem !important;
                height: 38px !important;
                padding-top: 3px !important;
            }
            .select2-container--default .select2-selection--single .select2-selection__arrow {
                height: 36px !important;
            }
            .select2-container .select2-selection--single .select2-selection__rendered {
                padding-left: 12px !important;
                font-size: 0.875rem !important;
                color: #374151 !important;
            }
            .select2-dropdown {
                border-color: #e2e8f0 !important;
                border-radius: 0.5rem !important;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
                z-index: 9999 !important;
            }
            .select2-container--default .select2-results__option--highlighted[aria-selected] {
                background-color: #0891b2 !important;
            }
        </style>
    @endpush

    <div class="max-w-3xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                    {{ isset($payment) ? 'Edit Payment' : 'Create Payment' }}
                </h1>
                <p class="text-sm text-gray-500">
                    {{ isset($payment) ? 'Modify details of existing payment receipt.' : 'Record a new payment transaction.' }}
                </p>
            </div>
            <a href="{{ route('payment.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-gray-600 hover:text-cyan-600 transition-colors duration-150">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                Back to List
            </a>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <form id="payment-form" class="p-6 space-y-6">
                @csrf
                @if(isset($payment))
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700">Payment Code</label>
                        <input type="text" id="code" value="{{ $payment->code ?? $code }}" disabled class="mt-1 block w-full px-3 py-2 border border-gray-200 bg-gray-50 rounded-lg text-sm text-gray-500 cursor-not-allowed">
                    </div>

                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                        <input type="text" name="date" id="date" required class="mt-1 block w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-150">
                        <span class="text-xs text-rose-500 mt-1 hidden" id="error-date"></span>
                    </div>

                    <div>
                        <label for="sales_code" class="block text-sm font-medium text-gray-700">Sales Invoice</label>
                        @if(isset($payment))
                            <input type="text" value="{{ $payment->sales_code }}" disabled class="mt-1 block w-full px-3 py-2 border border-gray-200 bg-gray-50 rounded-lg text-sm text-gray-500 cursor-not-allowed">
                            <input type="hidden" name="sales_code" id="sales_code" value="{{ $payment->sales_code }}">
                        @else
                            <select name="sales_code" id="sales_code" required class="block w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-150">
                                <option value="" disabled selected>-- Select Sales Invoice --</option>
                                @foreach($sales as $sale)
                                    @php
                                        $paid = $sale->payments->sum('amount');
                                        $remaining = $sale->grand_total - $paid;
                                    @endphp
                                    <option value="{{ $sale->sales_code }}" 
                                            data-total="{{ $sale->grand_total }}" 
                                            data-paid="{{ $paid }}" 
                                            data-remaining="{{ $remaining }}">
                                        {{ $sale->sales_code }} (Grand Total: Rp {{ number_format($sale->grand_total, 2) }}, Remaining: Rp {{ number_format($remaining, 2) }})
                                    </option>
                                @endforeach
                            </select>
                        @endif
                        <span class="text-xs text-rose-500 mt-1 hidden" id="error-sales_code"></span>
                    </div>

                    <div>
                        <label for="amount-display" class="block text-sm font-medium text-gray-700">Amount</label>
                        <div class="mt-1 relative rounded-md">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">Rp</span>
                            </div>
                            <input type="text" id="amount-display" required class="block w-full pl-9 pr-3 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-150">
                            <input type="hidden" name="amount" id="amount" value="{{ old('amount', $payment->amount ?? '') }}">
                        </div>
                        <span class="text-xs text-rose-500 mt-1 hidden" id="error-amount"></span>
                    </div>
                </div>

                <!-- Info Box for Sales Invoice details -->
                <div id="sales-info-box" class="hidden p-4 rounded-xl border bg-cyan-50/50 border-cyan-100 flex flex-col gap-2">
                    <h4 class="font-bold text-cyan-800 text-sm">Invoice Information</h4>
                    <div class="grid grid-cols-3 gap-4 text-xs font-semibold text-cyan-900 mt-1">
                        <div>
                            <span class="text-cyan-700 font-normal block">Grand Total</span>
                            <span id="info-grand-total">Rp 0.00</span>
                        </div>
                        <div>
                            <span class="text-cyan-700 font-normal block">Total Paid Already</span>
                            <span id="info-total-paid">Rp 0.00</span>
                        </div>
                        <div>
                            <span class="text-cyan-700 font-normal block">Remaining Balance</span>
                            <span id="info-remaining" class="text-cyan-800 font-bold">Rp 0.00</span>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                    <a href="{{ route('payment.index') }}" class="px-4 py-2 border border-gray-200 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50 transition-colors duration-150">
                        Cancel
                    </a>
                    <button type="submit" id="submit-btn" class="bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 text-sm font-semibold rounded-lg shadow-sm transition-colors duration-150 cursor-pointer">
                        {{ isset($payment) ? 'Update Payment' : 'Create Payment' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
            $(document).ready(function() {
                flatpickr("#date", {
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "d-m-Y",
                    defaultDate: "{{ old('date', isset($payment) ? $payment->date : date('Y-m-d')) }}"
                });
                // Initialize Select2 on sales_code select
                @if(!isset($payment))
                    $('#sales_code').select2({
                        width: '100%'
                    });
                @endif

                // Bind custom currency formatter
                window.initCurrencyInput('#amount-display', '#amount');

                // If editing, set initial amount values
                @if(isset($payment))
                    $('#amount-display').val(window.formatOnBlur('{{ $payment->amount }}'));
                    
                    // Show edit info details
                    @php
                        $paid = $payment->salesHeader->payments->where('code', '!=', $payment->code)->sum('amount');
                        $remaining = $payment->salesHeader->grand_total - $paid;
                    @endphp
                    $('#info-grand-total').text('Rp ' + parseFloat('{{ $payment->salesHeader->grand_total }}').toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    $('#info-total-paid').text('Rp ' + parseFloat('{{ $paid }}').toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    $('#info-remaining').text('Rp ' + parseFloat('{{ $remaining }}').toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    $('#sales-info-box').removeClass('hidden');
                @endif

                // Handle select change to update info box
                $('#sales_code').change(function() {
                    const selected = $(this).find('option:selected');
                    if (selected.val()) {
                        const total = parseFloat(selected.data('total')) || 0;
                        const paid = parseFloat(selected.data('paid')) || 0;
                        const remaining = parseFloat(selected.data('remaining')) || 0;

                        $('#info-grand-total').text('Rp ' + total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                        $('#info-total-paid').text('Rp ' + paid.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                        $('#info-remaining').text('Rp ' + remaining.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                        
                        // Set the input amount value automatically to the remaining balance
                        $('#amount').val(remaining);
                        $('#amount-display').val(window.formatOnBlur(String(remaining)));

                        $('#sales-info-box').removeClass('hidden');
                    } else {
                        $('#sales-info-box').addClass('hidden');
                    }
                });

                // Ajax form submission
                $('#payment-form').submit(function(e) {
                    e.preventDefault();
                    
                    $('.text-rose-500').addClass('hidden').text('');
                    $('#submit-btn').prop('disabled', true).addClass('opacity-50 cursor-not-allowed').text('Saving...');

                    let ajaxUrl = "{{ isset($payment) ? route('payment.update', $payment->code) : route('payment.store') }}";
                    
                    $.ajax({
                        url: ajaxUrl,
                        type: "{{ isset($payment) ? 'PUT' : 'POST' }}",
                        data: $(this).serialize(),
                        success: function(response) {
                            if (response.success) {
                                Toastify({
                                    text: response.message,
                                    duration: 2000,
                                    gravity: "top",
                                    position: "right",
                                    backgroundColor: "#06b6d4",
                                }).showToast();

                                setTimeout(function() {
                                    window.location.href = "{{ route('payment.index') }}";
                                }, 1000);
                            }
                        },
                        error: function(xhr) {
                            $('#submit-btn').prop('disabled', false).removeClass('opacity-50 cursor-not-allowed').text("{{ isset($payment) ? 'Update Payment' : 'Create Payment' }}");
                            
                            if (xhr.status === 422) {
                                let errors = xhr.responseJSON.errors;
                                if (errors && typeof errors === 'object') {
                                    $.each(errors, function(key, val) {
                                        $(`#error-${key}`).removeClass('hidden').text(val[0]);
                                    });
                                }
                                Toastify({
                                    text: xhr.responseJSON.message || 'Please check your inputs.',
                                    duration: 3000,
                                    gravity: "top",
                                    position: "right",
                                    backgroundColor: "#ef4444",
                                }).showToast();
                            } else {
                                Toastify({
                                    text: 'A system error occurred.',
                                    duration: 3000,
                                    gravity: "top",
                                    position: "right",
                                    backgroundColor: "#ef4444",
                                }).showToast();
                            }
                        }
                    });
                });
            });
        </script>
    @endpush
</x-layouts.app>
