<x-layouts.app :breadcrumbs="$breadcrumbs ?? null">
    <x-slot:title>{{ isset($user) ? 'Edit User' : 'Create User' }}</x-slot:title>

    <div class="max-w-2xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">{{ isset($user) ? 'Edit User' : 'Create User' }}</h1>
                <p class="text-sm text-gray-500">{{ isset($user) ? 'Modify the settings and information of an existing user.' : 'Create a new user in the system.' }}</p>
            </div>
            <a href="{{ route('master.user.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-gray-600 hover:text-cyan-600 transition-colors duration-150">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                Back to List
            </a>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden p-6">
            <form id="user-form" class="space-y-4">
                @csrf
                @if(isset($user))
                    @method('PUT')
                @endif
                
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name ?? '') }}" required class="mt-1 block w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-150">
                    <span class="text-xs text-rose-500 mt-1 hidden" id="error-name"></span>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email ?? '') }}" required class="mt-1 block w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-150">
                    <span class="text-xs text-rose-500 mt-1 hidden" id="error-email"></span>
                </div>

                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                    <select name="role" id="role" required class="mt-1 block w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-150">
                        <option value="" disabled {{ !isset($user) ? 'selected' : '' }}>-- Select Role --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ (old('role', isset($user) ? $user->roles->first()?->name : '')) === $role->name ? 'selected' : '' }}>
                                {{ ucfirst($role->name) }}
                            </option>
                        @endforeach
                    </select>
                    <span class="text-xs text-rose-500 mt-1 hidden" id="error-role"></span>
                </div>

                @if(isset($user))
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                        <input type="password" name="current_password" id="current_password" class="mt-1 block w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-150" placeholder="Required only if changing password">
                        <span class="text-xs text-rose-500 mt-1 hidden" id="error-current_password"></span>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                        <input type="password" name="password" id="password" class="mt-1 block w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-150" placeholder="Enter new password">
                        <span class="text-xs text-rose-500 mt-1 hidden" id="error-password"></span>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="mt-1 block w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-150" placeholder="Confirm new password">
                        <span class="text-xs text-rose-500 mt-1 hidden" id="error-password_confirmation"></span>
                    </div>
                @else
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" name="password" id="password" required class="mt-1 block w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-150">
                        <span class="text-xs text-rose-500 mt-1 hidden" id="error-password"></span>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required class="mt-1 block w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 transition-all duration-150">
                        <span class="text-xs text-rose-500 mt-1 hidden" id="error-password_confirmation"></span>
                    </div>
                @endif

                <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                    <a href="{{ route('master.user.index') }}" class="px-4 py-2 border border-gray-200 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-50 transition-colors duration-150">
                        Cancel
                    </a>
                    <button type="submit" id="submit-btn" class="bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 text-sm font-semibold rounded-lg shadow-sm transition-colors duration-150 cursor-pointer">
                        {{ isset($user) ? 'Update User' : 'Create User' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script>
            $(document).ready(function() {
                $('#user-form').submit(function(e) {
                    e.preventDefault();
                    
                    $('.text-rose-500').addClass('hidden').text('');
                    $('#submit-btn').prop('disabled', true).addClass('opacity-50 cursor-not-allowed').text('Saving...');

                    let ajaxUrl = "{{ isset($user) ? route('master.user.update', $user->id) : route('master.user.store') }}";

                    $.ajax({
                        url: ajaxUrl,
                        type: "POST",
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
                                    window.location.href = "{{ route('master.user.index') }}";
                                }, 1000);
                            }
                        },
                        error: function(xhr) {
                            $('#submit-btn').prop('disabled', false).removeClass('opacity-50 cursor-not-allowed').text("{{ isset($user) ? 'Update User' : 'Create User' }}");
                            
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
