@props([
    'id', 
    'url', 
    'headers' => [], 
    'columns' => [], 
    'searchPlaceholder' => 'Search...'
])

@once
    @push('styles')
        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
        <style>
            .dataTables_wrapper {
                padding: 0 !important;
            }
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter {
                margin: 0 !important;
                color: #4b5563 !important;
                font-size: 0.875rem !important;
            }
            .dataTables_wrapper .dataTables_filter {
                width: 100%;
            }
            @media (min-width: 640px) {
                .dataTables_wrapper .dataTables_filter {
                    width: auto;
                }
            }
            .dataTables_wrapper .dataTables_filter label {
                display: flex !important;
                align-items: center !important;
                gap: 0.5rem !important;
                position: relative;
            }
            .dataTables_wrapper .dataTables_filter input {
                margin-left: 0 !important;
                padding: 0.5rem 0.75rem 0.5rem 2.25rem !important;
                border: 1px solid #e5e7eb !important;
                border-radius: 0.5rem !important;
                font-size: 0.875rem !important;
                line-height: 1.25rem !important;
                width: 100% !important;
                background-color: #fff !important;
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%239ca3af' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z'/%3E%3C/svg%3E") !important;
                background-repeat: no-repeat !important;
                background-position: 0.75rem center !important;
                background-size: 1.1rem !important;
                outline: none !important;
                transition: all 150ms ease !important;
            }
            @media (min-width: 640px) {
                .dataTables_wrapper .dataTables_filter input {
                    width: 250px !important;
                }
            }
            .dataTables_wrapper .dataTables_filter input:focus {
                border-color: #06b6d4 !important;
                box-shadow: 0 0 0 2px rgba(6, 182, 212, 0.2) !important;
            }
            .dataTables_wrapper .dataTables_filter label {
                font-size: 0 !important;
            }
            .dataTables_wrapper .dataTables_length select {
                padding: 0.375rem 1.75rem 0.375rem 0.75rem !important;
                border: 1px solid #e5e7eb !important;
                border-radius: 0.375rem !important;
                background-color: #fff !important;
                outline: none !important;
            }
            .dataTables_wrapper .dataTables_length select:focus {
                border-color: #06b6d4 !important;
            }
            table.dataTable {
                border-collapse: collapse !important;
                margin: 0 !important;
                width: 100% !important;
            }
            table.dataTable border-b {
                border-bottom: 1px solid #e5e7eb !important;
            }
            table.dataTable thead th {
                border-bottom: 1px solid #e5e7eb !important;
                background-color: #f9fafb !important;
                color: #6b7280 !important;
                font-size: 11px !important;
                font-weight: 700 !important;
                text-transform: uppercase !important;
                letter-spacing: 0.05em !important;
                padding: 0.875rem 1.5rem !important;
            }
            table.dataTable tbody td {
                padding: 1rem 1.5rem !important;
                border-bottom: 1px solid #f3f4f6 !important;
            }
            table.dataTable tbody tr:hover {
                background-color: rgba(6, 182, 212, 0.03) !important;
            }
            .dataTables_wrapper .dataTables_info {
                color: #6b7280 !important;
                font-size: 0.875rem !important;
                padding-top: 0 !important;
            }
            .dataTables_wrapper .dataTables_paginate {
                padding-top: 0 !important;
                display: flex !important;
                gap: 0.25rem !important;
            }
            .dataTables_wrapper .dataTables_paginate .paginate_button {
                padding: 0.375rem 0.75rem !important;
                margin: 0 !important;
                border-radius: 0.375rem !important;
                border: 1px solid #e5e7eb !important;
                background: #fff !important;
                color: #4b5563 !important;
                font-size: 0.875rem !important;
                transition: all 150ms ease !important;
            }
            .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
                background: rgba(6, 182, 212, 0.05) !important;
                color: #083344 !important;
                border-color: #06b6d4 !important;
            }
            .dataTables_wrapper .dataTables_paginate .paginate_button.current,
            .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
                background: rgba(6, 182, 212, 0.1) !important;
                color: #083344 !important;
                border-color: #06b6d4 !important;
                font-weight: 600 !important;
            }
            .dataTables_wrapper .dataTables_paginate .paginate_button.disabled,
            .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover,
            .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:active {
                background: #f3f4f6 !important;
                color: #9ca3af !important;
                border-color: #e5e7eb !important;
                cursor: not-allowed !important;
            }
        </style>
    @endpush
@endonce

<table id="{{ $id }}" class="w-full text-left border-collapse">
    <thead>
        <tr>
            @foreach($headers as $header)
                <th>{{ $header }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody class="divide-y divide-gray-100">
    </tbody>
</table>

@once
    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    @endpush
@endonce

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#{{ $id }}').DataTable({
                processing: true,
                serverSide: true,
                ajax: typeof window['dt_ajax_{{ $id }}'] === 'function' ? window['dt_ajax_{{ $id }}']("{{ $url }}") : "{{ $url }}",
                columns: @json($columns),
                dom: '<"p-5 border-b border-gray-200 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4"lf>rt<"px-6 py-4 border-t border-gray-200 bg-gray-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4"ip>',
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "{{ $searchPlaceholder }}",
                    lengthMenu: "Show _MENU_ entries",
                    paginate: {
                        previous: '<i class="fa-solid fa-chevron-left"></i>',
                        next: '<i class="fa-solid fa-chevron-right"></i>'
                    }
                }
            });
        });
    </script>
@endpush
