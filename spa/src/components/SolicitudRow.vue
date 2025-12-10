<script setup lang="ts">
import type { EstadoValue, Solicitud } from '@/types/solicitud';
import { computed, onUnmounted, ref, watch } from 'vue';
import ConfirmDialog from './ConfirmDialog.vue';
import EstadoBadge from './EstadoBadge.vue';

const props = defineProps<{ solicitud: Solicitud }>();
const emit = defineEmits<{
  (e: 'update-estado', id: number, estado: EstadoValue): void;
  (e: 'delete-solicitud', id: number): void;
}>();

const isUpdating = ref(false);
const isDeleting = ref(false);
const showDeleteDialog = ref(false);
const lastEstado = ref<EstadoValue>(props.solicitud.estado.value);
let updateTimeout: ReturnType<typeof setTimeout> | null = null;

// Observar cambios en el estado de la solicitud para resetear isUpdating
watch(
  () => props.solicitud.estado.value,
  (newEstado) => {
    // Si el estado cambió y estábamos actualizando, resetear el flag
    if (isUpdating.value && newEstado !== lastEstado.value) {
      isUpdating.value = false;
      if (updateTimeout) {
        clearTimeout(updateTimeout);
        updateTimeout = null;
      }
    }
    lastEstado.value = newEstado;
  },
  { immediate: true } // Ejecutar inmediatamente para inicializar lastEstado
);

const fechaFormateada = computed(() => {
  const date = new Date(props.solicitud.fecha_creacion);
  return new Intl.DateTimeFormat('es-CL', {
    dateStyle: 'medium',
    timeStyle: 'short',
  }).format(date);
});

const puedeActuar = computed(() => props.solicitud.estado.value === 'pendiente' || props.solicitud.estado.value === 'modificar');

function handleUpdateEstado(estado: EstadoValue): void {
  if (isUpdating.value) return;

  isUpdating.value = true;
  lastEstado.value = props.solicitud.estado.value; // Guardar estado actual antes de cambiar

  // Timeout como fallback si la operación falla y el estado no cambia
  if (updateTimeout) {
    clearTimeout(updateTimeout);
  }
  updateTimeout = setTimeout(() => {
    if (isUpdating.value && props.solicitud.estado.value === lastEstado.value) {
      // Si después de 3 segundos el estado no cambió, probablemente hubo un error
      isUpdating.value = false;
    }
    updateTimeout = null;
  }, 3000);

  emit('update-estado', props.solicitud.id, estado);
  // El watch detectará el cambio de estado y reseteará isUpdating automáticamente
}

function handleDeleteClick(): void {
  if (isDeleting.value) return;
  showDeleteDialog.value = true;
}

function handleDeleteConfirm(): void {
  isDeleting.value = true;
  emit('delete-solicitud', props.solicitud.id);
  showDeleteDialog.value = false;
  // El componente padre debe manejar el reseteo del estado cuando la operación termine
}

function handleDeleteCancel(): void {
  showDeleteDialog.value = false;
}

// Limpiar timeout al desmontar el componente
onUnmounted(() => {
  if (updateTimeout) {
    clearTimeout(updateTimeout);
  }
});

// Métodos expuestos para que el componente padre pueda resetear los estados (si es necesario)
defineExpose({
  resetUpdating: () => {
    isUpdating.value = false;
    if (updateTimeout) {
      clearTimeout(updateTimeout);
      updateTimeout = null;
    }
  },
  resetDeleting: () => { isDeleting.value = false; },
});
</script>

<template>
  <tr class="group hover:bg-gray-50">
    <td class="px-6 py-4 text-sm text-gray-500">#{{ solicitud.id }}</td>
    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ solicitud.nombre_documento }}</td>
    <td class="sticky left-[140px] z-10 bg-white px-6 py-4 shadow-[2px_0_4px_-2px_rgba(0,0,0,0.1)] group-hover:bg-gray-50 transition-colors"><EstadoBadge :estado="solicitud.estado" /></td>
    <td class="sticky left-[280px] z-10 bg-white px-6 py-4 text-sm text-gray-500 shadow-[2px_0_4px_-2px_rgba(0,0,0,0.1)] group-hover:bg-gray-50 transition-colors">{{ fechaFormateada }}</td>
    <td class="sticky right-0 z-10 bg-white px-6 py-4 text-right shadow-[-2px_0_4px_-2px_rgba(0,0,0,0.1)] group-hover:bg-gray-50 transition-colors">
      <div v-if="puedeActuar" class="flex justify-end gap-2">
        <button
          :disabled="isUpdating"
          class="px-4 py-1.5 text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 disabled:bg-green-300"
          @click="handleUpdateEstado('aprobado')"
        >
          ✓ Aprobar
        </button>
        <button
          :disabled="isUpdating"
          class="px-3 py-1.5 text-xs font-medium rounded-md text-white bg-red-600 hover:bg-red-700 disabled:bg-red-300"
          @click="handleUpdateEstado('rechazado')"
        >
          ✗ Rechazar
        </button>

      </div>
      <div v-else class="flex justify-end gap-2">
        <button
          :disabled="isUpdating"
          class="px-3 py-1.5 text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 disabled:bg-blue-300"
          @click="handleUpdateEstado('modificar')"
        >
          ✎ Modificar
        </button>
      <button
        :disabled="isDeleting"
        class="px-3 py-1.5 text-xs font-medium rounded-md text-white bg-gray-600 hover:bg-gray-700 disabled:bg-gray-300"
        @click="handleDeleteClick"
      >
        🗑️ Eliminar
      </button>
      </div>

    </td>
  </tr>

  <ConfirmDialog
    :is-open="showDeleteDialog"
    title="Eliminar Solicitud"
    :message="`¿Estás seguro de que deseas eliminar la solicitud '${solicitud.nombre_documento}'? Esta acción no se puede deshacer.`"
    confirm-text="Eliminar"
    cancel-text="Cancelar"
    variant="danger"
    @confirm="handleDeleteConfirm"
    @cancel="handleDeleteCancel"
    @update:is-open="showDeleteDialog = $event"
  />
</template>
