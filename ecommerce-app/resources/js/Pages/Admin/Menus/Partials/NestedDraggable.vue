<script setup>
import { computed } from 'vue';
import draggable from 'vuedraggable';

const props = defineProps({
    modelValue: Array,
});

const emit = defineEmits(['update:modelValue', 'edit', 'delete']);

const list = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value),
});
</script>

<template>
    <draggable 
        v-model="list" 
        group="menu-items" 
        item-key="id" 
        ghost-class="ghost"
        class="space-y-2 min-h-[10px]"
    >
        <template #item="{ element }">
            <div class="border rounded bg-white overflow-hidden mb-2">
                <div class="p-3 flex justify-between items-center bg-gray-50 hover:bg-gray-100 cursor-move border-b">
                    <div class="flex items-center">
                         <!-- Icon handling could be improved with dynamic component or icon library -->
                        <span class="mr-2 text-gray-500 font-mono text-xs">
                           ⠿
                        </span>
                        <span class="font-medium text-sm">{{ element.label }}</span>
                        <span v-if="element.url" class="ml-2 text-xs text-gray-400">({{ element.url }})</span>
                        <span v-if="!element.is_active" class="ml-2 text-xs text-red-500 bg-red-50 px-1 rounded">Inactivo</span>
                    </div>
                    <div>
                        <button type="button" @click="$emit('edit', element)" class="text-blue-600 text-xs mr-2 hover:underline">Editar</button>
                        <button type="button" @click="$emit('delete', element)" class="text-red-600 text-xs hover:underline">Eliminar</button>
                    </div>
                </div>
                <!-- Children Area -->
                <div class="pl-8 pr-2 py-2 bg-white" v-if="element.children">
                     <NestedDraggable 
                        v-model="element.children" 
                        @edit="$emit('edit', $event)"
                        @delete="$emit('delete', $event)"
                     />
                </div>
            </div>
        </template>
    </draggable>
</template>

<style scoped>
.ghost {
    opacity: 0.5;
    background: #c8ebfb;
    border: 1px dashed #4a90e2;
}
</style>
