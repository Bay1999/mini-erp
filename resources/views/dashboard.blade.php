<x-layouts.app>
    <x-slot:title>Dashboard</x-slot:title>

    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    @endpush

    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">Dashboard</h1>
                <p class="text-sm text-gray-500">Welcome back, {{ Auth::user()->name }}. Here is your sales activity overview.</p>
            </div>
            
            <!-- Date Filter Panel -->
            <div class="bg-white border border-gray-200 rounded-xl p-3 shadow-sm flex flex-wrap items-end gap-3 self-start md:self-auto">
                <div class="w-full sm:w-auto">
                    <label for="start_date" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Start Date</label>
                    <input type="text" id="start_date" class="block w-full px-3 py-1.5 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-150">
                </div>
                <div class="w-full sm:w-auto">
                    <label for="end_date" class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">End Date</label>
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
        </div>

        <!-- Widgets section -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <!-- Total Transactions -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm flex items-center gap-5 hover:shadow-md transition-all duration-200">
                <div class="h-14 w-14 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center text-2xl font-bold">
                    <i class="fa-solid fa-file-invoice-dollar"></i>
                </div>
                <div>
                    <span class="text-sm font-semibold text-gray-400 block">Total Transactions</span>
                    <span id="widget-transactions" class="text-2xl font-extrabold text-gray-800 mt-1 block">0</span>
                </div>
            </div>

            <!-- Total Sales in Rupiah -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm flex items-center gap-5 hover:shadow-md transition-all duration-200">
                <div class="h-14 w-14 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl font-bold">
                    <i class="fa-solid fa-money-bill-trend-up"></i>
                </div>
                <div>
                    <span class="text-sm font-semibold text-gray-400 block">Total Sales</span>
                    <span id="widget-sales" class="text-2xl font-extrabold text-gray-800 mt-1 block">Rp 0.00</span>
                </div>
            </div>

            <!-- Total Qty sold -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm flex items-center gap-5 hover:shadow-md transition-all duration-200">
                <div class="h-14 w-14 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl font-bold">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
                <div>
                    <span class="text-sm font-semibold text-gray-400 block">Items Sold (Qty)</span>
                    <span id="widget-qty" class="text-2xl font-extrabold text-gray-800 mt-1 block">0</span>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Monthly Sales Chart -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h3 class="text-base font-bold text-gray-800 mb-4">Monthly Sales Revenue</h3>
                <div class="relative h-[300px]">
                    <canvas id="monthlySalesChart"></canvas>
                </div>
            </div>

            <!-- Item Qty Chart -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h3 class="text-base font-bold text-gray-800 mb-4">Items Sold Quantities</h3>
                <div class="relative h-[300px]">
                    <canvas id="itemSalesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            $(document).ready(function() {
                // Initialize Flatpickr inputs
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

                // Chart References
                let monthlyChart = null;
                let itemChart = null;

                // Load Data function
                function loadDashboardData() {
                    const startVal = fpStart ? fpStart.input.value : $('#start_date').val();
                    const endVal = fpEnd ? fpEnd.input.value : $('#end_date').val();

                    $.ajax({
                        url: "{{ route('dashboard.data') }}",
                        type: "GET",
                        data: {
                            start_date: startVal,
                            end_date: endVal
                        },
                        success: function(response) {
                            if (response.success) {
                                // 1. Update Widgets
                                $('#widget-transactions').text(response.widgets.total_transactions);
                                $('#widget-sales').text(response.widgets.total_sales);
                                $('#widget-qty').text(response.widgets.total_qty);

                                // 2. Update Monthly Chart
                                if (monthlyChart) monthlyChart.destroy();
                                monthlyChart = new Chart(document.getElementById('monthlySalesChart'), {
                                    type: 'bar',
                                    data: {
                                        labels: response.charts.monthly.labels,
                                        datasets: [{
                                            label: 'Sales (Rp)',
                                            data: response.charts.monthly.data,
                                            backgroundColor: '#06b6d4',
                                            borderColor: '#0891b2',
                                            borderWidth: 1,
                                            borderRadius: 6
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        scales: {
                                            y: {
                                                beginAtZero: true,
                                                ticks: {
                                                    callback: function(value) {
                                                        return 'Rp ' + value.toLocaleString();
                                                    }
                                                }
                                            }
                                        },
                                        plugins: {
                                            tooltip: {
                                                callbacks: {
                                                    label: function(context) {
                                                        return 'Revenue: Rp ' + context.raw.toLocaleString();
                                                    }
                                                }
                                            }
                                        }
                                    }
                                });

                                // 3. Update Items Chart
                                if (itemChart) itemChart.destroy();
                                itemChart = new Chart(document.getElementById('itemSalesChart'), {
                                    type: 'bar',
                                    data: {
                                        labels: response.charts.items.labels,
                                        datasets: [{
                                            label: 'Quantity Sold',
                                            data: response.charts.items.data,
                                            backgroundColor: '#f59e0b',
                                            borderColor: '#d97706',
                                            borderWidth: 1,
                                            borderRadius: 6
                                        }]
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: false,
                                        indexAxis: 'y', // Horizontal bar chart
                                        scales: {
                                            x: {
                                                beginAtZero: true,
                                                ticks: {
                                                    precision: 0
                                                }
                                            }
                                        }
                                    }
                                });
                            }
                        }
                    });
                }

                // Filter & Reset actions
                $('#btn-filter').click(loadDashboardData);
                $('#btn-reset').click(function() {
                    fpStart.setDate('{{ date("Y-m-01") }}');
                    fpEnd.setDate('{{ date("Y-m-t") }}');
                    loadDashboardData();
                });

                // Initial Load
                loadDashboardData();
            });
        </script>
    @endpush
</x-layouts.app>
