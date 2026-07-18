<x-layouts.app :breadcrumbs="$breadcrumbs ?? null">
    <x-slot:title>Payment List</x-slot:title>

    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    @endpush

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">Payment List</h1>
                <p class="text-sm text-gray-500">Manage payment receipts, dates, and amounts linked to sales.</p>
            </div>
            <div>
                <a href="{{ route('payment.create') }}" class="inline-flex items-center gap-2 bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 text-sm font-semibold rounded-lg shadow-sm transition-colors duration-150 cursor-pointer">
                    <i class="fa-solid fa-plus text-xs"></i>
                    Add Payment
                </a>
            </div>
        </div>

        <!-- Date Filter Card -->
        <div class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm flex flex-wrap items-end gap-4">
            <div class="w-full sm:w-auto">
                <label for="start_date" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Start Date</label>
                <input type="text" id="start_date" class="block w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-150">
            </div>
            <div class="w-full sm:w-auto">
                <label for="end_date" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">End Date</label>
                <input type="text" id="end_date" class="block w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-150">
            </div>
            <div class="flex gap-2">
                <button type="button" id="btn-filter" class="bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 text-sm font-semibold rounded-lg shadow-sm transition-colors duration-150 cursor-pointer">
                    Filter
                </button>
                <button type="button" id="btn-reset" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 text-sm font-semibold rounded-lg transition-colors duration-150 cursor-pointer">
                    Reset
                </button>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden flex flex-col">
            <div class="overflow-x-auto">
                <x-datatable 
                    id="payments-table" 
                    :url="route('payment.data')" 
                    :headers="['Payment Code', 'Date', 'Sales Code', 'Amount', 'Actions']"
                    :columns="[
                        ['data' => 'code', 'name' => 'code'],
                        ['data' => 'date', 'name' => 'date'],
                        ['data' => 'sales_code', 'name' => 'sales_code'],
                        ['data' => 'amount', 'name' => 'amount'],
                        ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false]
                    ]"
                    search-placeholder="Search payments..."
                />
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script>
            // Hook into DataTable AJAX configuration before it initializes
            window['dt_ajax_payments-table'] = function(url) {
                return {
                    url: url,
                    data: function(d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                    }
                };
            };
        </script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            $(document).ready(function() {
                const fpStart = flatpickr("#start_date", {
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "d-m-Y",
                    defaultDate: "{{ date('Y-m-01') }}"
                });

                const fpEnd = flatpickr("#end_date", {
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "d-m-Y",
                    defaultDate: "{{ date('Y-m-t') }}"
                });

                $('#btn-filter').click(function() {
                    $('#payments-table').DataTable().ajax.reload();
                });

                $('#btn-reset').click(function() {
                    fpStart.setDate('{{ date("Y-m-01") }}');
                    fpEnd.setDate('{{ date("Y-m-t") }}');
                    $('#payments-table').DataTable().ajax.reload();
                });
            });

            function deletePayment(code) {
                Swal.fire({
                    title: 'Delete Payment',
                    text: `Are you sure you want to delete payment record "${code}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#0891b2',
                    cancelButtonColor: '#ef4444',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/payment/${code}`,
                            type: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                _method: 'DELETE'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Deleted!',
                                        text: response.message,
                                        icon: 'success',
                                        confirmButtonColor: '#0891b2'
                                    }).then(() => {
                                        $('#payments-table').DataTable().ajax.reload();
                                    });
                                }
                            },
                            error: function(xhr) {
                                let message = 'An error occurred while deleting the payment record.';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    message = xhr.responseJSON.message;
                                }
                                Swal.fire({
                                    title: 'Error!',
                                    text: message,
                                    icon: 'error',
                                    confirmButtonColor: '#0891b2'
                                });
                            }
                        });
                    }
                });
            }
        </script>
    @endpush
</x-layouts.app>
