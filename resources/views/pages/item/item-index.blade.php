<x-layouts.app :breadcrumbs="$breadcrumbs ?? null">
    <x-slot:title>Item List</x-slot:title>

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">Item List</h1>
                <p class="text-sm text-gray-500">Manage catalog items, pricing, and images.</p>
            </div>
            <div>
                <a href="{{ route('master.item.create') }}" class="inline-flex items-center gap-2 bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 text-sm font-semibold rounded-lg shadow-sm transition-colors duration-150 cursor-pointer">
                    <i class="fa-solid fa-plus text-xs"></i>
                    Add Item
                </a>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden flex flex-col">
            <div class="overflow-x-auto">
                <x-datatable 
                    id="items-table" 
                    :url="route('master.item.data')" 
                    :headers="['Code', 'Image', 'Name', 'Price', 'Actions']"
                    :columns="[
                        ['data' => 'code', 'name' => 'code'],
                        ['data' => 'image', 'name' => 'image', 'orderable' => false, 'searchable' => false],
                        ['data' => 'name', 'name' => 'name'],
                        ['data' => 'price', 'name' => 'price'],
                        ['data' => 'action', 'name' => 'action', 'orderable' => false, 'searchable' => false]
                    ]"
                    search-placeholder="Search items..."
                />
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function deleteItem(code, name) {
                Swal.fire({
                    title: 'Delete Item',
                    text: `Are you sure you want to delete item "${name}" (${code})?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#0891b2',
                    cancelButtonColor: '#ef4444',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/master/item/${code}`,
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
                                        $('#items-table').DataTable().ajax.reload();
                                    });
                                }
                            },
                            error: function(xhr) {
                                let message = 'An error occurred while deleting the item.';
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
