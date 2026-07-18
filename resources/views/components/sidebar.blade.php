<aside class="bg-white border-r border-gray-200 flex flex-col h-full shrink-0 transition-all duration-300 ease-in-out"
       :class="sidebarCollapsed ? 'w-20 overflow-visible' : 'w-64 overflow-hidden'">
    
    <div class="h-16 flex items-center justify-between transition-all duration-300 ease-in-out px-6"
         :class="sidebarCollapsed ? 'justify-center px-0' : 'px-6'">
        <div x-show="!sidebarCollapsed" class="flex items-center gap-3">
            <span class="text-lg font-bold tracking-tight text-gray-900 truncate">Mini ERP</span>
        </div>
        <div x-show="sidebarCollapsed" class="flex items-center gap-3">
            <span class="text-lg font-bold tracking-tight text-gray-900 truncate">ERP</span>
        </div>
    </div>

    <nav class="flex-1 py-2 space-y-6 transition-all duration-300"
         :class="sidebarCollapsed ? 'px-2 overflow-visible' : 'px-4 overflow-y-auto'">
        
        <div>
            <div x-show="!sidebarCollapsed" x-transition.opacity.duration.200ms class="text-[11px] font-semibold text-gray-400 uppercase tracking-[0.08em] px-3 mb-2.5">
                Dashboards
            </div>
            <div x-show="sidebarCollapsed" class="h-px bg-gray-100 my-4 mx-2"></div>
            
            <div class="space-y-1">
                <a href="{{ route('dashboard') }}" 
                   class="group flex items-center gap-3 rounded transition-all duration-150 {{ request()->routeIs('dashboard') ? 'bg-cyan-100 text-cyan-950 font-semibold' : 'text-gray-500 hover:bg-cyan-100/30 hover:text-cyan-950' }}"
                   :class="sidebarCollapsed ? 'justify-center px-0 py-2.5' : 'px-3 py-2 text-sm font-medium'"
                   title="Dashboard">
                    <i class="fa-solid fa-table-cells-large text-[15px] w-5 text-center shrink-0 transition-colors duration-150 {{ request()->routeIs('dashboard') ? 'text-cyan-950' : 'text-gray-400 group-hover:text-cyan-600' }}"></i>
                    <span x-show="!sidebarCollapsed" x-transition.opacity.duration.200ms class="truncate transition-colors duration-150 {{ request()->routeIs('dashboard') ? 'text-cyan-950 font-medium' : 'text-gray-500 group-hover:text-cyan-950' }}">Dashboard</span>
                </a>
                <div x-data="{ open: {{ request()->routeIs('master.user.*', 'master.item.*') ? 'true' : 'false' }} }" 
                     class="relative space-y-1"
                     @click.outside="if (sidebarCollapsed) open = false">
                    
                    <button @click="open = !open" 
                            class="group w-full flex items-center justify-between gap-3 rounded transition-all duration-150 {{ request()->routeIs('master.user.*', 'master.item.*') ? 'bg-cyan-100 text-cyan-950 font-semibold' : 'text-gray-500 hover:bg-cyan-100/30 hover:text-cyan-950' }} cursor-pointer"
                            :class="sidebarCollapsed ? 'justify-center px-0 py-2.5' : 'px-3 py-2 text-sm font-medium'"
                            title="Master">
                        <div class="flex items-center gap-3">
                            <i class="fa-regular fa-folder text-[15px] w-5 text-center shrink-0 transition-colors duration-150 {{ request()->routeIs('master.user.*', 'master.item.*') ? 'text-cyan-950' : 'text-gray-400' }}"></i>
                            <span x-show="!sidebarCollapsed" x-transition.opacity.duration.200ms class="truncate transition-colors duration-150 {{ request()->routeIs('master.user.*', 'master.item.*') ? 'text-cyan-950 font-medium' : 'text-gray-500 group-hover:text-cyan-950' }}">Master</span>
                        </div>
                        <i x-show="!sidebarCollapsed" 
                           class="fa-solid fa-chevron-down text-[10px] text-gray-400 transition-transform duration-200 shrink-0" 
                           :class="open ? 'rotate-180' : ''"></i>
                    </button>
                    
                    <div x-show="open" 
                         :class="sidebarCollapsed 
                             ? 'absolute left-full top-0 ml-2 w-48 bg-white border border-gray-200 rounded shadow-lg py-2 px-1.5 z-50 space-y-1' 
                             : 'pl-[38px] space-y-1 mt-1'"
                         x-transition:enter="transition ease-out duration-100" 
                         x-transition:enter-start="transform opacity-0 scale-95" 
                         x-transition:enter-end="transform opacity-100 scale-100" 
                         x-transition:leave="transition ease-in duration-75" 
                         x-transition:leave-start="transform opacity-100 scale-100" 
                         x-transition:leave-end="transform opacity-0 scale-95">
                        
                        <a href="{{ route('master.user.index') }}" 
                           class="group flex items-center gap-2 rounded-lg py-1.5 px-3 text-sm font-medium transition-all duration-150 {{ request()->routeIs('master.user.*') ? 'bg-cyan-100 text-cyan-950 font-semibold' : 'text-gray-500 hover:bg-cyan-100/30 hover:text-cyan-950' }}">
                            <span class="transition-colors duration-150 {{ request()->routeIs('master.user.*') ? 'text-cyan-950 font-medium' : 'text-gray-500 group-hover:text-cyan-950' }}">User</span>
                        </a>
                        
                        <a href="{{ route('master.item.index') }}" 
                           class="group flex items-center gap-2 rounded-lg py-1.5 px-3 text-sm font-medium transition-all duration-150 {{ request()->routeIs('master.item.*') ? 'bg-cyan-100 text-cyan-950 font-semibold' : 'text-gray-500 hover:bg-cyan-100/30 hover:text-cyan-950' }}">
                            <span class="transition-colors duration-150 {{ request()->routeIs('master.item.*') ? 'text-cyan-950 font-medium' : 'text-gray-500 group-hover:text-cyan-950' }}">Item</span>
                        </a>
                    </div>
                </div>
                
                <a href="{{ route('payment.index') }}" 
                   class="group flex items-center gap-3 rounded transition-all duration-150 {{ request()->routeIs('payment.*') ? 'bg-cyan-100 text-cyan-950 font-semibold' : 'text-gray-500 hover:bg-cyan-100/30 hover:text-cyan-950' }}"
                   :class="sidebarCollapsed ? 'justify-center px-0 py-2.5' : 'px-3 py-2 text-sm font-medium'"
                   title="Payment">
                    <i class="fa-regular fa-credit-card text-[15px] w-5 text-center shrink-0 transition-colors duration-150 {{ request()->routeIs('payment.*') ? 'text-cyan-950' : 'text-gray-400 group-hover:text-cyan-600' }}"></i>
                    <span x-show="!sidebarCollapsed" x-transition.opacity.duration.200ms class="truncate transition-colors duration-150 {{ request()->routeIs('payment.*') ? 'text-cyan-950 font-medium' : 'text-gray-500 group-hover:text-cyan-950' }}">Payment</span>
                </a>
                
                <a href="{{ route('sales.index') }}" 
                   class="group flex items-center gap-3 rounded transition-all duration-150 {{ request()->routeIs('sales.*') ? 'bg-cyan-100 text-cyan-950 font-semibold' : 'text-gray-500 hover:bg-cyan-100/30 hover:text-cyan-950' }}"
                   :class="sidebarCollapsed ? 'justify-center px-0 py-2.5' : 'px-3 py-2 text-sm font-medium'"
                   title="Sales">
                    <i class="fa-regular fa-chart-bar text-[15px] w-5 text-center shrink-0 transition-colors duration-150 {{ request()->routeIs('sales.*') ? 'text-cyan-950' : 'text-gray-400 group-hover:text-cyan-600' }}"></i>
                    <span x-show="!sidebarCollapsed" x-transition.opacity.duration.200ms class="truncate transition-colors duration-150 {{ request()->routeIs('sales.*') ? 'text-cyan-950 font-medium' : 'text-gray-500 group-hover:text-cyan-950' }}">Sales</span>
                </a>
            </div>
        </div>
    </nav>

    <div class="p-4 border-t border-gray-100 flex items-center justify-between gap-3 bg-gray-50/50 transition-all duration-300"
         :class="sidebarCollapsed ? 'flex-col p-3 gap-3 justify-center' : 'p-4 justify-between gap-3'">
        <div class="flex items-center gap-3 min-w-0"
             :class="sidebarCollapsed ? 'flex-col text-center' : ''">
            <div class="h-10 w-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-sm shrink-0">
                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
            </div>
            <div class="min-w-0" x-show="!sidebarCollapsed" x-transition.opacity.duration.200ms>
                <p class="text-sm font-semibold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-500 truncate">Admin</p>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}" id="logout-form-sidebar"
              :class="sidebarCollapsed ? 'w-full flex justify-center' : ''">
            @csrf
            <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-150 cursor-pointer" title="Log Out">
                <i class="fa-solid fa-right-from-bracket text-lg w-5 text-center shrink-0"></i>
            </button>
        </form>
    </div>
</aside>
