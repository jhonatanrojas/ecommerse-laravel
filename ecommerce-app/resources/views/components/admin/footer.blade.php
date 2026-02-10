<footer class="mt-6 border-t border-gray-200 pt-4 dark:border-gray-700">
    <div class="flex flex-col sm:flex-row items-center justify-between text-sm text-gray-500 dark:text-gray-400">
        <span>
            © {{ now()->year }}
            <a href="{{ url('/') }}" class="hover:underline">
                {{ config('app.name', 'Ecommerce') }}
            </a>. Todos los derechos reservados.
        </span>
        <span class="mt-2 sm:mt-0">
            Panel construido con
            <a href="https://flowbite.com/docs/getting-started/introduction/" target="_blank" rel="noopener" class="font-semibold hover:underline">
                Flowbite Admin & TailwindCSS
            </a>
        </span>
    </div>
</footer>

