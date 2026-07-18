<x-layouts.app :breadcrumbs="$breadcrumbs ?? null">
    <x-slot:title>{{ isset($item) ? 'Edit Item' : 'Create Item' }}</x-slot:title>

    <div class="max-w-2xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">{{ isset($item) ? 'Edit Item' : 'Create Item' }}</h1>
                <p class="text-sm text-gray-500">{{ isset($item) ? 'Modify catalog item details.' : 'Add a new item to the system.' }}</p>
            </div>
            <a href="{{ route('master.item.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-gray-600 hover:text-cyan-600 transition-colors duration-150">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                Back to List
            </a>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden p-6">
            <form id="item-form" class="space-y-4" enctype="multipart/form-data">
                @csrf
                @if(isset($item))
                    @method('PUT')
                @endif
                
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700">Item Code</label>
                    <input type="text" id="code" value="{{ $item->code ?? $code }}" disabled class="mt-1 block w-full px-3 py-2 border border-gray-250 bg-gray-50 rounded-lg text-sm text-gray-500 cursor-not-allowed">
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Item Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $item->name ?? '') }}" required class="mt-1 block w-full px-3 py-2 border border-gray-255 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-150">
                    <span class="text-xs text-rose-500 mt-1 hidden" id="error-name"></span>
                </div>

                <div>
                    <label for="price-display" class="block text-sm font-medium text-gray-700">Price</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 text-sm">Rp</span>
                        </div>
                        <input type="text" id="price-display" required class="block w-full pl-9 pr-3 py-2 border border-gray-255 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-150">
                        <input type="hidden" name="price" id="price" value="{{ old('price', $item->price ?? '') }}">
                    </div>
                    <span class="text-xs text-rose-500 mt-1 hidden" id="error-price"></span>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Item Image</label>
                    <div class="mt-2 flex items-center gap-4">
                        <div id="image-preview-container" class="h-16 w-16 rounded-xl border border-gray-200 overflow-hidden bg-gray-50 flex items-center justify-center shrink-0 cursor-pointer hover:opacity-80 transition-opacity duration-150">
                            @if(isset($item) && $item->image)
                                <img id="image-preview" src="{{ asset('storage/' . $item->image) }}" class="h-full w-full object-cover">
                                <span id="image-placeholder" class="hidden text-xl font-semibold text-gray-400">?</span>
                            @else
                                <img id="image-preview" src="" class="hidden h-full w-full object-cover">
                                <span id="image-placeholder" class="text-2xl font-bold text-gray-400">?</span>
                            @endif
                        </div>
                        <div class="flex flex-col gap-1">
                            <input type="file" name="image" id="image-input" accept="image/*" {{ !isset($item) ? 'required' : '' }} class="block w-full text-xs text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-cyan-50 file:text-cyan-700 file:cursor-pointer hover:file:bg-cyan-100 transition-colors duration-150">
                            <p class="text-xs text-gray-400">JPEG, PNG, JPG, WebP up to 2MB.</p>
                        </div>
                    </div>
                    <span class="text-xs text-rose-500 mt-1 block hidden" id="error-image"></span>
                </div>

                <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                    <a href="{{ route('master.item.index') }}" class="px-4 py-2 border border-gray-200 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50 transition-colors duration-150">
                        Cancel
                    </a>
                    <button type="submit" id="submit-btn" class="bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 text-sm font-semibold rounded-lg shadow-sm transition-colors duration-150 cursor-pointer">
                        {{ isset($item) ? 'Update Item' : 'Create Item' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script>
            $(document).ready(function() {
                // Image preview handler
                $('#image-input').change(function() {
                    const file = this.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            $('#image-preview').attr('src', e.target.result).removeClass('hidden');
                            $('#image-placeholder').addClass('hidden');
                        }
                        reader.readAsDataURL(file);
                    } else {
                        $('#image-preview').attr('src', '').addClass('hidden');
                        $('#image-placeholder').removeClass('hidden');
                    }
                });

                // Image preview popup click handler
                $('#image-preview-container').click(function() {
                    const src = $('#image-preview').attr('src');
                    if (src) {
                        window.showImagePopup(src);
                    }
                });

                // Initialize price input formatting
                window.initCurrencyInput('#price-display', '#price');

                // AJAX submission
                $('#item-form').submit(function(e) {
                    e.preventDefault();
                    
                    $('.text-rose-500').addClass('hidden').text('');
                    $('#submit-btn').prop('disabled', true).addClass('opacity-50 cursor-not-allowed').text('Saving...');

                    let ajaxUrl = "{{ isset($item) ? route('master.item.update', $item->code) : route('master.item.store') }}";
                    
                    // Use FormData to support image uploads
                    let formData = new FormData(this);

                    $.ajax({
                        url: ajaxUrl,
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
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
                                    window.location.href = "{{ route('master.item.index') }}";
                                }, 1000);
                            }
                        },
                        error: function(xhr) {
                            $('#submit-btn').prop('disabled', false).removeClass('opacity-50 cursor-not-allowed').text("{{ isset($item) ? 'Update Item' : 'Create Item' }}");
                            
                            if (xhr.status === 422) {
                                let errors = xhr.responseJSON.errors;
                                $.each(errors, function(key, val) {
                                    $(`#error-${key}`).removeClass('hidden').text(val[0]);
                                });
                                Toastify({
                                    text: xhr.responseJSON.message || 'Please check your form inputs.',
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
