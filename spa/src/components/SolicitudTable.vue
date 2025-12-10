<script setup lang="ts">
import type { EstadoValue, Solicitud } from '@/types/solicitud';
import SolicitudRow from './SolicitudRow.vue';

defineProps<{
  solicitudes: Solicitud[];
  isLoading?: boolean;
}>();

defineEmits<{
  (e: 'update-estado', id: number, estado: EstadoValue): void;
  (e: 'delete-solicitud', id: Solicitud['id']): void;
}>();
</script>

<template>
  <div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 rounded-lg">
    <table class="min-w-full divide-y divide-gray-300">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
          <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Documento</th>
          <th class="sticky left-[140px] z-10 bg-gray-50 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase shadow-[2px_0_4px_-2px_rgba(0,0,0,0.1)]">Estado</th>
          <th class="sticky left-[280px] z-10 bg-gray-50 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase shadow-[2px_0_4px_-2px_rgba(0,0,0,0.1)]">Fecha</th>
          <th class="sticky right-0 z-10 bg-gray-50 px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase shadow-[-2px_0_4px_-2px_rgba(0,0,0,0.1)]">Acciones</th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        <tr v-if="isLoading">
          <td colspan="5" class="px-6 py-12 text-center text-gray-500">
            Cargando solicitudes...
          </td>
        </tr>
        <tr v-else-if="solicitudes.length === 0">
          <td colspan="5" class="px-6 py-12 text-center text-gray-500">
            No hay solicitudes registradas
          </td>
        </tr>
        <SolicitudRow
          v-for="solicitud in solicitudes"
          v-else
          :key="solicitud.id"
          :solicitud="solicitud"
          @update-estado="(id, estado) => $emit('update-estado', id, estado)"
          @delete-solicitud="(id) => $emit('delete-solicitud', id )"
        />
      </tbody>
    </table>
  </div>
</template>
