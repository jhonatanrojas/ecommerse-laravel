@foreach ($items as $item)
    <div class="menu-item-container mb-2" data-id="{{ $item->id }}">
        <div class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-lg shadow-sm group hover:border-blue-300">
            <div class="flex items-center gap-3">
                <span class="cursor-move text-gray-400 hover:text-gray-600 handle">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                    </svg>
                </span>
                
                <div class="flex flex-col">
                    <div class="flex items-center gap-2">
                        <span class="font-medium text-gray-900">{{ $item->label }}</span>
                        @if(!$item->is_active)
                            <span class="px-1.5 py-0.5 text-xs bg-red-100 text-red-800 rounded">Inactivo</span>
                        @endif
                        @if($item->badge_text)
                            <span class="px-1.5 py-0.5 text-xs text-white rounded" style="background-color: {{ $item->badge_color ?? 'red' }}">{{ $item->badge_text }}</span>
                        @endif
                    </div>
                    <span class="text-xs text-gray-500">{{ $item->url ?? $item->route_name }}</span>
                </div>
            </div>

            <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <button type="button" @click="editItem({{ json_encode($item) }})" class="p-1 text-blue-600 hover:bg-blue-50 rounded">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                    </svg>
                </button>
                <form action="{{ route('admin.menu-items.toggle', $item->uuid) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="p-1 {{ $item->is_active ? 'text-green-600 hover:bg-green-50' : 'text-gray-400 hover:bg-gray-50' }} rounded" title="Alternar Estado">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </button>
                </form>
                <form action="{{ route('admin.menu-items.destroy', $item->uuid) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar este item?')">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="$el.closest('form').submit()" class="p-1 text-red-600 hover:bg-red-50 rounded">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        <div class="ml-8 mt-2 nested-sortable min-h-[5px] border-l-2 border-dashed border-gray-200 pl-2">
            @if($item->children->count() > 0)
                @include('admin.menus.partials.menu-item-row', ['items' => $item->children])
            @endif
        </div>
    </div>
@endforeach
