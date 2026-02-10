<aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-16 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700" aria-label="Sidebar">
    <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
        <ul class="space-y-2 font-medium">
            <li>
                <a href="{{ route('dashboard') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('dashboard') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                    <svg class="w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 21">
                        <path d="M16.975 11H10v7h6.975a1.025 1.025 0 0 0 1.025-1.024v-4.952A1.025 1.025 0 0 0 16.975 11Z"/>
                        <path d="M9.5 11H3.025A1.025 1.025 0 0 0 2 12.024v4.952A1.024 1.024 0 0 0 3.025 18H9.5v-7Z"/>
                        <path d="M16.975 2H10v7h6.975A1.025 1.025 0 0 0 18 7.976V3.024A1.025 1.025 0 0 0 16.975 2Z"/>
                        <path d="M9.5 2H3.025A1.025 1.025 0 0 0 2 3.024v4.952A1.024 1.024 0 0 0 3.025 9H9.5V2Z"/>
                    </svg>
                    <span class="ms-3">Dashboard</span>
                </a>
            </li>
            {{-- Ejemplos de secciones típicas del panel de e-commerce --}}
            <li>
                <a href="" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('admin.products.*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                    <svg class="flex-shrink-0 w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 3.5A1.5 1.5 0 0 1 3.5 2h13A1.5 1.5 0 0 1 18 3.5v2A1.5 1.5 0 0 1 16.5 7h-13A1.5 1.5 0 0 1 2 5.5v-2Z"/>
                        <path d="M2 10.5A1.5 1.5 0 0 1 3.5 9h13A1.5 1.5 0 0 1 18 10.5v2A1.5 1.5 0 0 1 16.5 14h-13A1.5 1.5 0 0 1 2 12.5v-2Z"/>
                        <path d="M2 17.5A1.5 1.5 0 0 1 3.5 16h13A1.5 1.5 0 0 1 18 17.5v.5H2v-.5Z"/>
                    </svg>
                    <span class="ms-3">Productos</span>
                </a>
            </li>
            <li>
                <a href="" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('admin.categories.*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                    <svg class="flex-shrink-0 w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 2a2 2 0 0 0-2 2v3h7V2H4Z"/><path d="M11 2v5h7V4a2 2 0 0 0-2-2h-5Z"/><path d="M2 11v3a2 2 0 0 0 2 2h5v-5H2Z"/><path d="M11 11v5h5a2 2 0 0 0 2-2v-3h-7Z"/>
                    </svg>
                    <span class="ms-3">Categorías</span>
                </a>
            </li>
            <li>
                <a href="" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('admin.orders.*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                    <svg class="flex-shrink-0 w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 3a2 2 0 0 0-2 2v1h16V5a2 2 0 0 0-2-2H4Z"/><path d="M18 8H2v7a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/>
                    </svg>
                    <span class="ms-3">Órdenes</span>
                </a>
            </li>
            <li>
                <a href="" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('admin.users.*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                    <svg class="flex-shrink-0 w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 9a3 3 0 1 0-3-3 3 3 0 0 0 3 3Z"/><path d="M2 16a6 6 0 1 1 12 0H2Z"/>
                    </svg>
                    <span class="ms-3">Usuarios</span>
                </a>
            </li>
            <li>
                <a href="" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('admin.reports.*') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                    <svg class="flex-shrink-0 w-6 h-6 text-gray-500 transition duration-75 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 3a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.414a2 2 0 0 0-.586-1.414l-3.414-3.414A2 2 0 0 0 12.586 2H4Z"/><path d="M7 9h2v5H7V9Z"/><path d="M11 11h2v3h-2v-3Z"/>
                    </svg>
                    <span class="ms-3">Reportes</span>
                </a>
            </li>
        </ul>
    </div>
</aside>

