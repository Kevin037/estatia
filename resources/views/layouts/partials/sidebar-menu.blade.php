<!-- Dashboard -->
<a href="{{ route('dashboard') }}" 
   class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-emerald-700 text-white' : 'text-gray-300 hover:bg-emerald-800 hover:text-white' }} transition-colors duration-150"
   :class="sidebarCollapsed && 'justify-center'">
    <svg class="h-5 w-5 flex-shrink-0" :class="!sidebarCollapsed && 'mr-3'" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
    </svg>
    <span x-show="!sidebarCollapsed">Dashboard</span>
</a>

<!-- Master Data Menu -->
<div x-data="{ open: {{ request()->is('users*') || request()->is('customers*') || request()->is('materials*') || request()->is('suppliers*') || request()->is('sales*') || request()->is('milestones*') || request()->is('formulas*') || request()->is('contractors*') || request()->is('types*') || request()->is('products*') || request()->is('master-data/*') ? 'true' : 'false' }} }" class="space-y-1">
    <button @click="open = !open" 
            class="group flex w-full items-center rounded-lg px-3 py-2 text-sm font-medium text-gray-300 hover:bg-emerald-800 hover:text-white transition-colors duration-150"
            :class="sidebarCollapsed && 'justify-center'"
            aria-expanded="false">
        <svg class="h-5 w-5 flex-shrink-0" :class="!sidebarCollapsed && 'mr-3'" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
        </svg>
        <span x-show="!sidebarCollapsed" class="flex-1 text-left">Master Data</span>
        <svg x-show="!sidebarCollapsed" :class="open && 'rotate-90'" class="ml-auto h-5 w-5 transition-transform duration-150" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
    </button>
    <div x-show="open && !sidebarCollapsed" 
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 -translate-y-2"
         x-transition:enter-end="transform opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 translate-y-0"
         x-transition:leave-end="transform opacity-0 -translate-y-2"
         class="space-y-1 pl-11"
         style="display: none;">
    <a href="{{ route('users.index') }}" class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium {{ request()->is('users*') ? 'bg-emerald-700 text-white' : 'text-gray-400 hover:bg-emerald-800 hover:text-white' }} transition-colors">Users</a>
    <a href="{{ route('customers.index') }}" class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium {{ request()->is('customers*') ? 'bg-emerald-700 text-white' : 'text-gray-400 hover:bg-emerald-800 hover:text-white' }} transition-colors">Customers</a>
        <a href="{{ route('suppliers.index') }}" class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium {{ request()->is('suppliers*') ? 'bg-emerald-700 text-white' : 'text-gray-400 hover:bg-emerald-800 hover:text-white' }} transition-colors">Suppliers</a>
        <a href="{{ route('sales.index') }}" class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium {{ request()->is('sales*') ? 'bg-emerald-700 text-white' : 'text-gray-400 hover:bg-emerald-800 hover:text-white' }} transition-colors">Sales</a>
        <a href="{{ route('materials.index') }}" class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium {{ request()->is('materials*') ? 'bg-emerald-700 text-white' : 'text-gray-400 hover:bg-emerald-800 hover:text-white' }} transition-colors">Materials</a>
        <a href="#" class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium text-gray-400 hover:bg-emerald-800 hover:text-white transition-colors">Accounts</a>
        <a href="{{ route('milestones.index') }}" class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium {{ request()->is('milestones*') ? 'bg-emerald-700 text-white' : 'text-gray-400 hover:bg-emerald-800 hover:text-white' }} transition-colors">Milestones</a>
        <a href="{{ route('formulas.index') }}" class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium {{ request()->is('formulas*') ? 'bg-emerald-700 text-white' : 'text-gray-400 hover:bg-emerald-800 hover:text-white' }} transition-colors">Formulas</a>
        <a href="{{ route('contractors.index') }}" class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium {{ request()->is('contractors*') ? 'bg-emerald-700 text-white' : 'text-gray-400 hover:bg-emerald-800 hover:text-white' }} transition-colors">Contractors</a>
        <a href="{{ route('types.index') }}" class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium {{ request()->is('types*') ? 'bg-emerald-700 text-white' : 'text-gray-400 hover:bg-emerald-800 hover:text-white' }} transition-colors">Types</a>
    </div>
</div>

<!-- Production Menu -->
<div x-data="{ open: {{ request()->is('production/*') ? 'true' : 'false' }} || request()->is('projects*') || request()->is('units*') || request()->is('clusters*') }" class="space-y-1">
    <button @click="open = !open" 
            class="group flex w-full items-center rounded-lg px-3 py-2 text-sm font-medium text-gray-300 hover:bg-emerald-800 hover:text-white transition-colors duration-150"
            :class="sidebarCollapsed && 'justify-center'">
        <svg class="h-5 w-5 flex-shrink-0" :class="!sidebarCollapsed && 'mr-3'" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205l3 1m1.5.5l-1.5-.5M6.75 7.364V3h-3v18m3-13.636l10.5-3.819" />
        </svg>
        <span x-show="!sidebarCollapsed" class="flex-1 text-left">Production</span>
        <svg x-show="!sidebarCollapsed" :class="open && 'rotate-90'" class="ml-auto h-5 w-5 transition-transform duration-150" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
    </button>
    <div x-show="open && !sidebarCollapsed" 
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 -translate-y-2"
         x-transition:enter-end="transform opacity-100 translate-y-0"
         class="space-y-1 pl-11"
         style="display: none;">
        <a href="{{ route('products.index') }}" class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium {{ request()->is('products*') ? 'bg-emerald-700 text-white' : 'text-gray-400 hover:bg-emerald-800 hover:text-white' }} transition-colors">Products</a>
        <a href="{{ route('lands.index') }}" class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium {{ request()->is('lands*') ? 'bg-emerald-700 text-white' : 'text-gray-400 hover:bg-emerald-800 hover:text-white' }} transition-colors">Lands</a>
        <a href="{{ route('projects.index') }}" 
           class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium {{ request()->is('projects*') ? 'bg-emerald-700 text-white' : 'text-gray-400 hover:bg-emerald-800 hover:text-white' }} transition-colors">
            <svg class="h-4 w-4 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
            </svg>
            Projects
        </a>
        <a href="{{ route('clusters.index') }}" 
           class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium {{ request()->is('clusters*') ? 'bg-emerald-700 text-white' : 'text-gray-400 hover:bg-emerald-800 hover:text-white' }} transition-colors">
            <svg class="h-4 w-4 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
            </svg>
            Clusters
        </a>
        <a href="{{ route('units.index') }}" 
           class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium {{ request()->is('units*') ? 'bg-emerald-700 text-white' : 'text-gray-400 hover:bg-emerald-800 hover:text-white' }} transition-colors">
            <svg class="h-4 w-4 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V3.545M12.75 21h7.5V10.75M2.25 21h1.5m18 0h-18M2.25 9l4.5-1.636M18.75 3l-1.5.545m0 6.205l3 1m1.5.5l-1.5-.5M6.75 7.364V3h-3v18m3-13.636l10.5-3.819" />
            </svg>
            Units
        </a>
    </div>
</div>

<!-- Purchasing Menu -->
<div x-data="{ open: {{ request()->is('purchasing/*') ? 'true' : 'false' }} }" class="space-y-1">
    <button @click="open = !open" 
            class="group flex w-full items-center rounded-lg px-3 py-2 text-sm font-medium text-gray-300 hover:bg-emerald-800 hover:text-white transition-colors duration-150"
            :class="sidebarCollapsed && 'justify-center'">
        <svg class="h-5 w-5 flex-shrink-0" :class="!sidebarCollapsed && 'mr-3'" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
        </svg>
        <span x-show="!sidebarCollapsed" class="flex-1 text-left">Purchasing</span>
        <svg x-show="!sidebarCollapsed" :class="open && 'rotate-90'" class="ml-auto h-5 w-5 transition-transform duration-150" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
    </button>
    <div x-show="open && !sidebarCollapsed" 
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 -translate-y-2"
         x-transition:enter-end="transform opacity-100 translate-y-0"
         class="space-y-1 pl-11"
         style="display: none;">
        <a href="{{ route('purchase-orders.index') }}" 
           class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium {{ request()->is('purchase-orders*') ? 'bg-emerald-700 text-white' : 'text-gray-400 hover:bg-emerald-800 hover:text-white' }} transition-colors">
            <svg class="h-4 w-4 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
            </svg>
            Purchase Orders
        </a>
        <a href="#" class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium text-gray-400 hover:bg-emerald-800 hover:text-white transition-colors">Material Stock</a>
    </div>
</div>

<!-- Transaction Menu -->
<div x-data="{ open: {{ request()->is('purchase-orders*') || request()->is('orders*') || request()->is('invoices*') || request()->is('payments*') || request()->is('tickets*') || request()->is('feedbacks*') || request()->is('formulas*') || request()->is('products*') || request()->is('contractors*') || request()->is('types*') || request()->is('transaction/*') ? 'true' : 'false' }} }" class="space-y-1">
    <button @click="open = !open" 
            class="group flex w-full items-center rounded-lg px-3 py-2 text-sm font-medium text-gray-300 hover:bg-emerald-800 hover:text-white transition-colors duration-150"
            :class="sidebarCollapsed && 'justify-center'">
        <svg class="h-5 w-5 flex-shrink-0" :class="!sidebarCollapsed && 'mr-3'" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
        </svg>
        <span x-show="!sidebarCollapsed" class="flex-1 text-left">Transaction</span>
        <svg x-show="!sidebarCollapsed" :class="open && 'rotate-90'" class="ml-auto h-5 w-5 transition-transform duration-150" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
    </button>
    <div x-show="open && !sidebarCollapsed" 
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 -translate-y-2"
         x-transition:enter-end="transform opacity-100 translate-y-0"
         class="space-y-1 pl-11"
         style="display: none;">
        <a href="{{ route('orders.index') }}" 
           class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium {{ request()->is('orders*') ? 'bg-emerald-700 text-white' : 'text-gray-400 hover:bg-emerald-800 hover:text-white' }} transition-colors">
            <svg class="h-4 w-4 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
            </svg>
            Orders
        </a>
        <a href="{{ route('invoices.index') }}" 
           class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium {{ request()->is('invoices*') ? 'bg-emerald-700 text-white' : 'text-gray-400 hover:bg-emerald-800 hover:text-white' }} transition-colors">
            <svg class="h-4 w-4 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
            </svg>
            Invoices
        </a>
        <a href="{{ route('payments.index') }}" 
           class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium {{ request()->is('payments*') ? 'bg-emerald-700 text-white' : 'text-gray-400 hover:bg-emerald-800 hover:text-white' }} transition-colors">
            <svg class="h-4 w-4 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
            </svg>
            Payments
        </a>
        <a href="{{ route('tickets.index') }}" 
           class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium {{ request()->is('tickets*') ? 'bg-emerald-700 text-white' : 'text-gray-400 hover:bg-emerald-800 hover:text-white' }} transition-colors">
            <svg class="h-4 w-4 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z" />
            </svg>
            Tickets
        </a>
        <a href="{{ route('feedbacks.index') }}" 
           class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium {{ request()->is('feedbacks*') ? 'bg-emerald-700 text-white' : 'text-gray-400 hover:bg-emerald-800 hover:text-white' }} transition-colors">
            <svg class="h-4 w-4 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
            </svg>
            Feedbacks
        </a>
    </div>
</div>

<!-- Sales Menu -->
<div x-data="{ open: {{ request()->is('sales/*') ? 'true' : 'false' }} }" class="space-y-1">
    <button @click="open = !open" 
            class="group flex w-full items-center rounded-lg px-3 py-2 text-sm font-medium text-gray-300 hover:bg-emerald-800 hover:text-white transition-colors duration-150"
            :class="sidebarCollapsed && 'justify-center'">
        <svg class="h-5 w-5 flex-shrink-0" :class="!sidebarCollapsed && 'mr-3'" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
        </svg>
        <span x-show="!sidebarCollapsed" class="flex-1 text-left">Sales</span>
        <svg x-show="!sidebarCollapsed" :class="open && 'rotate-90'" class="ml-auto h-5 w-5 transition-transform duration-150" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
    </button>
    <div x-show="open && !sidebarCollapsed" 
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 -translate-y-2"
         x-transition:enter-end="transform opacity-100 translate-y-0"
         class="space-y-1 pl-11"
         style="display: none;">
        <a href="#" class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium text-gray-400 hover:bg-emerald-800 hover:text-white transition-colors">Orders</a>
        <a href="#" class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium text-gray-400 hover:bg-emerald-800 hover:text-white transition-colors">Invoices</a>
        <a href="#" class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium text-gray-400 hover:bg-emerald-800 hover:text-white transition-colors">Payments</a>
    </div>
</div>

<!-- Customer Service Menu -->
<div x-data="{ open: {{ request()->is('customer-service/*') ? 'true' : 'false' }} }" class="space-y-1">
    <button @click="open = !open" 
            class="group flex w-full items-center rounded-lg px-3 py-2 text-sm font-medium text-gray-300 hover:bg-emerald-800 hover:text-white transition-colors duration-150"
            :class="sidebarCollapsed && 'justify-center'">
        <svg class="h-5 w-5 flex-shrink-0" :class="!sidebarCollapsed && 'mr-3'" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" />
        </svg>
        <span x-show="!sidebarCollapsed" class="flex-1 text-left">Customer Service</span>
        <svg x-show="!sidebarCollapsed" :class="open && 'rotate-90'" class="ml-auto h-5 w-5 transition-transform duration-150" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
    </button>
    <div x-show="open && !sidebarCollapsed" 
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 -translate-y-2"
         x-transition:enter-end="transform opacity-100 translate-y-0"
         class="space-y-1 pl-11"
         style="display: none;">
        <a href="#" class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium text-gray-400 hover:bg-emerald-800 hover:text-white transition-colors">Tickets</a>
        <a href="#" class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium text-gray-400 hover:bg-emerald-800 hover:text-white transition-colors">Feedbacks</a>
    </div>
</div>

<!-- Accounting Menu -->
<div x-data="{ open: {{ request()->is('accounting/*') ? 'true' : 'false' }} }" class="space-y-1">
    <button @click="open = !open" 
            class="group flex w-full items-center rounded-lg px-3 py-2 text-sm font-medium text-gray-300 hover:bg-emerald-800 hover:text-white transition-colors duration-150"
            :class="sidebarCollapsed && 'justify-center'">
        <svg class="h-5 w-5 flex-shrink-0" :class="!sidebarCollapsed && 'mr-3'" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span x-show="!sidebarCollapsed" class="flex-1 text-left">Accounting</span>
        <svg x-show="!sidebarCollapsed" :class="open && 'rotate-90'" class="ml-auto h-5 w-5 transition-transform duration-150" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
    </button>
    <div x-show="open && !sidebarCollapsed" 
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 -translate-y-2"
         x-transition:enter-end="transform opacity-100 translate-y-0"
         class="space-y-1 pl-11"
         style="display: none;">
        <a href="#" class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium text-gray-400 hover:bg-emerald-800 hover:text-white transition-colors">Chart of Accounts</a>
        <a href="#" class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium text-gray-400 hover:bg-emerald-800 hover:text-white transition-colors">Journal Entries</a>
        <a href="#" class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium text-gray-400 hover:bg-emerald-800 hover:text-white transition-colors">General Ledger</a>
    </div>
</div>

<!-- Accounting Menu -->
<div class="space-y-1">
    <a href="{{ route('chart-of-accounts.index') }}" 
       class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium {{ request()->is('chart-of-accounts*') ? 'bg-emerald-700 text-white' : 'text-gray-300 hover:bg-emerald-800 hover:text-white' }} transition-colors duration-150"
       :class="sidebarCollapsed && 'justify-center'">
        <svg class="h-5 w-5 flex-shrink-0" :class="!sidebarCollapsed && 'mr-3'" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z" />
        </svg>
        <span x-show="!sidebarCollapsed">Chart of Account</span>
    </a>
    
    <a href="{{ route('general-ledger.index') }}" 
       class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium {{ request()->is('general-ledger*') ? 'bg-emerald-700 text-white' : 'text-gray-300 hover:bg-emerald-800 hover:text-white' }} transition-colors duration-150"
       :class="sidebarCollapsed && 'justify-center'">
        <svg class="h-5 w-5 flex-shrink-0" :class="!sidebarCollapsed && 'mr-3'" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" />
        </svg>
        <span x-show="!sidebarCollapsed">Buku Besar</span>
    </a>
</div>

<!-- Reports Menu -->
<div x-data="{ open: {{ request()->is('reports/*') ? 'true' : 'false' }} }" class="space-y-1">
    <button @click="open = !open" 
            class="group flex w-full items-center rounded-lg px-3 py-2 text-sm font-medium text-gray-300 hover:bg-emerald-800 hover:text-white transition-colors duration-150"
            :class="sidebarCollapsed && 'justify-center'">
        <svg class="h-5 w-5 flex-shrink-0" :class="!sidebarCollapsed && 'mr-3'" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
        </svg>
        <span x-show="!sidebarCollapsed" class="flex-1 text-left">Reports</span>
        <svg x-show="!sidebarCollapsed" :class="open && 'rotate-90'" class="ml-auto h-5 w-5 transition-transform duration-150" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
        </svg>
    </button>
    <div x-show="open && !sidebarCollapsed" 
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 -translate-y-2"
         x-transition:enter-end="transform opacity-100 translate-y-0"
         class="space-y-1 pl-11"
         style="display: none;">
        <a href="#" class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium text-gray-400 hover:bg-emerald-800 hover:text-white transition-colors">Sales Report</a>
        <a href="#" class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium text-gray-400 hover:bg-emerald-800 hover:text-white transition-colors">Purchase Report</a>
        <a href="#" class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium text-gray-400 hover:bg-emerald-800 hover:text-white transition-colors">Project Report</a>
        <a href="#" class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium text-gray-400 hover:bg-emerald-800 hover:text-white transition-colors">Financial Report</a>
        <a href="#" class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium text-gray-400 hover:bg-emerald-800 hover:text-white transition-colors">Inventory Report</a>
    </div>
</div>

<!-- Divider -->
<div class="border-t border-gray-700 my-4"></div>

<!-- Settings -->
<a href="#" 
   class="group flex items-center rounded-lg px-3 py-2 text-sm font-medium text-gray-300 hover:bg-emerald-800 hover:text-white transition-colors duration-150"
   :class="sidebarCollapsed && 'justify-center'">
    <svg class="h-5 w-5 flex-shrink-0" :class="!sidebarCollapsed && 'mr-3'" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
    </svg>
    <span x-show="!sidebarCollapsed">Settings</span>
</a>
