<x-layouts.app :breadcrumbs="$breadcrumbs ?? null">
    <x-slot:title>
        {{ isset($sales) ? 'Edit Sales' : 'Create Sales' }}
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

    <div class="max-w-5xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">
                    {{ isset($sales) ? 'Edit Sales' : 'Create Sales' }}
                </h1>
                <p class="text-sm text-gray-500">
                    {{ isset($sales) ? 'Modify details of existing sales record.' : 'Create a new sales transaction invoice.' }}
                </p>
            </div>
            <a href="{{ route('sales.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-gray-600 hover:text-cyan-600 transition-colors duration-150">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                Back to List
            </a>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <form id="sales-form" class="p-6 space-y-6">
                @csrf
                @if(isset($sales))
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="sales_code" class="block text-sm font-medium text-gray-700">Sales Code</label>
                        <input type="text" id="sales_code" value="{{ $sales->sales_code ?? $code }}" disabled class="mt-1 block w-full px-3 py-2 border border-gray-200 bg-gray-50 rounded-lg text-sm text-gray-500 cursor-not-allowed">
                    </div>

                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700">Date</label>
                        <input type="text" name="date" id="date" required class="mt-1 block w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-150">
                        <span class="text-xs text-rose-500 mt-1 hidden" id="error-date"></span>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-800">Sales Items</h3>
                        <button type="button" id="add-row-btn" class="inline-flex items-center gap-1.5 bg-cyan-50 text-cyan-700 hover:bg-cyan-100 px-3.5 py-2 text-xs font-semibold rounded-lg shadow-sm transition-all duration-150 cursor-pointer">
                            <i class="fa-solid fa-plus"></i> Add Item
                        </button>
                    </div>

                    <div class="overflow-x-auto border border-gray-100 rounded-xl">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600">Item</th>
                                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600 w-24">Qty</th>
                                    <th scope="col" class="px-4 py-3 text-left font-semibold text-gray-600 w-44">Price</th>
                                    <th scope="col" class="px-4 py-3 text-right font-semibold text-gray-600 w-44">Subtotal</th>
                                    <th scope="col" class="px-4 py-3 text-center font-semibold text-gray-600 w-16">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="items-tbody" class="divide-y divide-gray-200 bg-white">
                                <!-- Dynamic rows go here -->
                            </tbody>
                            <tfoot class="bg-gray-50/50">
                                <tr>
                                    <td colspan="3" class="px-4 py-4 text-right font-bold text-gray-700">Grand Total</td>
                                    <td class="px-4 py-4 text-right font-bold text-cyan-600 text-base" id="grand-total-display">Rp 0.00</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <span class="text-xs text-rose-500 mt-2 block hidden" id="error-items"></span>
                </div>

                <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                    <a href="{{ route('sales.index') }}" class="px-4 py-2 border border-gray-200 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50 transition-colors duration-150">
                        Cancel
                    </a>
                    <button type="submit" id="submit-btn" class="bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 text-sm font-semibold rounded-lg shadow-sm transition-colors duration-150 cursor-pointer">
                        {{ isset($sales) ? 'Update Sales' : 'Create Sales' }}
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
                    defaultDate: "{{ old('date', isset($sales) ? $sales->date : date('Y-m-d')) }}"
                });

                const itemsData = @json($items);
                let rowCounter = 0;

                // Build a row template
                function createRow(itemCode = '', qty = 1, price = 0) {
                    const rowId = `row-${rowCounter++}`;
                    
                    let options = '<option value="" disabled selected>-- Select Item --</option>';
                    itemsData.forEach(item => {
                        options += `<option value="${item.code}" data-price="${item.price}" ${item.code === itemCode ? 'selected' : ''}>${item.name} (${item.code})</option>`;
                    });

                    const tr = `
                        <tr id="${rowId}" class="item-row">
                            <td class="px-4 py-3">
                                <select name="items[${rowCounter}][item_code]" class="item-select block w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-150" required>
                                    ${options}
                                </select>
                            </td>
                            <td class="px-4 py-3">
                                <input type="number" name="items[${rowCounter}][qty]" value="${qty}" min="1" class="qty-input block w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-150" required>
                            </td>
                            <td class="px-4 py-3">
                                <div class="relative rounded-md">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 text-xs">Rp</span>
                                    </div>
                                    <input type="text" id="${rowId}-price-display" class="price-display-input block w-full pl-8 pr-3 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-150" required>
                                    <input type="hidden" name="items[${rowCounter}][price]" class="price-raw-input" value="${price}">
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-700 subtotal-column">
                                Rp 0.00
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button type="button" class="remove-row-btn text-rose-500 hover:text-rose-700 hover:bg-rose-50 p-1.5 rounded-lg transition-all duration-150" title="Remove Item">
                                    <i class="fa-solid fa-trash text-sm"></i>
                                </button>
                            </td>
                        </tr>
                    `;

                    $('#items-tbody').append(tr);
                    
                    const $row = $(`#${rowId}`);
                    
                    // Initialize Select2 on this row's select field
                    $row.find('.item-select').select2({
                        width: '100%'
                    });

                    // Bind custom currency formatter
                    window.initCurrencyInput(`#${rowId}-price-display`, `#${rowId} .price-raw-input`);

                    // Set initial display price
                    if (price > 0) {
                        $row.find('.price-display-input').val(window.formatOnBlur(String(price)));
                    }

                    // On change of item dropdown
                    $row.find('.item-select').change(function() {
                        const selectedOption = $(this).find('option:selected');
                        const defaultPrice = selectedOption.data('price') || 0;
                        
                        $row.find('.price-raw-input').val(defaultPrice);
                        $row.find('.price-display-input').val(window.formatOnBlur(String(defaultPrice)));
                        
                        updateDropdowns();
                        calculateRowTotal($row);
                    });

                    // Recalculate on input
                    $row.find('.qty-input, .price-display-input').on('input blur change', function() {
                        calculateRowTotal($row);
                    });

                    calculateRowTotal($row);
                    updateDropdowns();
                }

                // Calculate row total and grand total
                function calculateRowTotal($row) {
                    const qty = parseInt($row.find('.qty-input').val()) || 0;
                    const price = parseFloat($row.find('.price-raw-input').val()) || 0;
                    const subtotal = qty * price;
                    
                    $row.find('.subtotal-column').text('Rp ' + subtotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                    calculateGrandTotal();
                }

                function calculateGrandTotal() {
                    let grandTotal = 0;
                    $('#items-tbody tr').each(function() {
                        const qty = parseInt($(this).find('.qty-input').val()) || 0;
                        const price = parseFloat($(this).find('.price-raw-input').val()) || 0;
                        grandTotal += qty * price;
                    });
                    $('#grand-total-display').text('Rp ' + grandTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
                }

                // Update dropdown options to disable duplicates
                function updateDropdowns() {
                    const selectedCodes = [];
                    
                    // Collect all selected values
                    $('.item-select').each(function() {
                        const val = $(this).val();
                        if (val) {
                            selectedCodes.push(val);
                        }
                    });

                    // Update all dropdowns
                    $('.item-select').each(function() {
                        const currentVal = $(this).val();
                        $(this).find('option').each(function() {
                            const optionVal = $(this).val();
                            if (optionVal && optionVal !== currentVal) {
                                if (selectedCodes.includes(optionVal)) {
                                    $(this).attr('disabled', 'disabled');
                                } else {
                                    $(this).removeAttr('disabled');
                                }
                            }
                        });
                        
                        // Notify Select2 that option state has updated
                        $(this).trigger('change.select2');
                    });
                }

                // Add row button
                $('#add-row-btn').click(function() {
                    createRow();
                });

                // Remove row
                $(document).on('click', '.remove-row-btn', function() {
                    $(this).closest('tr').remove();
                    updateDropdowns();
                    calculateGrandTotal();
                });

                // If edit mode, populate initial rows
                @if(isset($sales) && count($sales->details))
                    @foreach($sales->details as $detail)
                        createRow('{{ $detail->item_code }}', {{ $detail->qty }}, {{ $detail->price }});
                    @endforeach
                @else
                    // Add an initial row for convenience on create mode
                    createRow();
                @endif

                // Ajax form submission
                $('#sales-form').submit(function(e) {
                    e.preventDefault();
                    
                    $('.text-rose-500').addClass('hidden').text('');
                    $('#submit-btn').prop('disabled', true).addClass('opacity-50 cursor-not-allowed').text('Saving...');

                    let ajaxUrl = "{{ isset($sales) ? route('sales.update', $sales->sales_code) : route('sales.store') }}";
                    
                    $.ajax({
                        url: ajaxUrl,
                        type: "{{ isset($sales) ? 'PUT' : 'POST' }}",
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
                                    window.location.href = "{{ route('sales.index') }}";
                                }, 1000);
                            }
                        },
                        error: function(xhr) {
                            $('#submit-btn').prop('disabled', false).removeClass('opacity-50 cursor-not-allowed').text("{{ isset($sales) ? 'Update Sales' : 'Create Sales' }}");
                            
                            if (xhr.status === 422) {
                                let errors = xhr.responseJSON.errors;
                                $.each(errors, function(key, val) {
                                    let errorId = key.replace(/\./g, '-');
                                    let errorElement = $(`#error-${errorId}`);
                                    if (errorElement.length) {
                                        errorElement.removeClass('hidden').text(val[0]);
                                    } else {
                                        $(`#error-items`).removeClass('hidden').text(val[0]);
                                    }
                                });
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
