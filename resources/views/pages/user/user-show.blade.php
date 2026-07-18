<x-layouts.app :breadcrumbs="$breadcrumbs ?? null">
    <x-slot:title>User Details</x-slot:title>

    <div class="max-w-2xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900">User Details</h1>
                <p class="text-sm text-gray-500">View information about user profile and status.</p>
            </div>
            <a href="{{ route('master.user.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-gray-600 hover:text-cyan-600 transition-colors duration-150">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                Back to List
            </a>
        </div>

        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            
            <div class="p-6 bg-gray-50/50 border-b border-gray-100 flex items-center gap-4">
                <div class="h-16 w-16 rounded-full bg-cyan-600/10 text-cyan-700 flex items-center justify-center font-bold text-2xl">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-900">{{ $user->name }}</h2>
                    <p class="text-sm text-gray-500">User ID: #{{ $user->id }}</p>
                </div>
            </div>

            <div class="p-6 divide-y divide-gray-100">
                <div class="py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <span class="text-sm font-medium text-gray-500">Full Name</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $user->name }}</span>
                </div>

                <div class="py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <span class="text-sm font-medium text-gray-500">Email Address</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $user->email }}</span>
                </div>

                <div class="py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <span class="text-sm font-medium text-gray-500">Joined Date</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $user->created_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</span>
                </div>

                <div class="py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <span class="text-sm font-medium text-gray-500">Last Updated</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $user->updated_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</span>
                </div>
            </div>

            <div class="p-6 bg-gray-50/50 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="{{ route('master.user.edit', $user->id) }}" class="inline-flex items-center gap-2 bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 text-sm font-semibold rounded-lg shadow-sm transition-colors duration-150 cursor-pointer">
                    <i class="fa-solid fa-pen text-xs"></i>
                    Edit Profile
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>
