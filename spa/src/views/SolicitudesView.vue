<script setup lang="ts">
import Pagination from '@/components/Pagination.vue';
import SolicitudForm from '@/components/SolicitudForm.vue';
import SolicitudTable from '@/components/SolicitudTable.vue';
import { useSolicitudes } from '@/composables/useSolicitudes';
import type { EstadoValue } from '@/types/solicitud';
import { onMounted } from 'vue';

const {
  solicitudes,
  isLoading,
  error,
  pagination,
  currentPage,
  perPage,
  totalSolicitudes,
  solicitudesPendientes,
  fetchSolicitudesPaginated,
  createSolicitud,
  updateEstado,
  deleteSolicitud,
  clearError,
  setPerPage,
} = useSolicitudes();

onMounted(() => {
  fetchSolicitudesPaginated(1);
});

async function handleCreate(nombreDocumento: string): Promise<void> {
  await createSolicitud({ nombre_documento: nombreDocumento });
}

async function handleUpdateEstado(id: number, estado: EstadoValue): Promise<void> {
  const success = await updateEstado(id, estado);
  // Si la operación fue exitosa, los estados se resetean automáticamente
  // Si falló, el error se muestra y el estado se mantiene para permitir reintento
  if (!success) {
    // El error ya está manejado por el composable
    // Los estados de SolicitudRow se mantienen para permitir reintento
  }
}

async function handleDeleteSolicitud(id: number): Promise<void> {
  const success = await deleteSolicitud(id);

  if (success) {
    // Si la eliminación fue exitosa, recargar la página si hay paginación
    if (pagination.value) {
      await fetchSolicitudesPaginated(currentPage.value);
    }
    // El componente se elimina de la lista, así que no necesitamos resetear estados
  }
  // Si falló, el error se muestra y el estado se mantiene para permitir reintento
  // En este caso, el componente sigue en la lista y el estado isDeleting se mantiene
}

function handlePageChange(page: number): void {
  fetchSolicitudesPaginated(page);
}

function handlePerPageChange(event: Event): void {
  const target = event.target as HTMLSelectElement;
  setPerPage(Number(target.value));
}
</script>

<template>
  <div class="min-h-screen bg-gray-100">
    <header class="bg-white shadow">
      <div class="max-w-7xl mx-auto px-4 py-6 flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">📋 Gestor de Solicitudes</h1>
          <p class="mt-1 text-sm text-gray-500">Sistema de gestión de documentos</p>
        </div>
        <div class="flex gap-6">
          <div class="text-center">
            <div class="text-2xl font-bold text-gray-900">{{ totalSolicitudes }}</div>
            <div class="text-xs text-gray-500 uppercase">Total</div>
          </div>
          <div class="text-center">
            <div class="text-2xl font-bold text-yellow-600">{{ solicitudesPendientes }}</div>
            <div class="text-xs text-gray-500 uppercase">Pendientes</div>
          </div>
        </div>
      </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-8">
      <section class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-medium text-gray-900 mb-4">Nueva Solicitud</h2>
        <SolicitudForm :is-loading="isLoading" @submit="handleCreate" />
      </section>

      <div v-if="error" class="bg-red-50 border border-red-400 text-red-800 rounded-lg p-4 mb-6">
        {{ error }}
        <button @click="clearError" class="ml-2 text-red-600 hover:text-red-800">✕</button>
      </div>

      <section>
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-medium text-gray-900">Listado de Solicitudes</h2>
          <div class="flex items-center gap-2">
            <label for="per-page" class="text-sm text-gray-700">Por página:</label>
            <select
              id="per-page"
              :value="perPage"
              class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
              @change="handlePerPageChange"
            >
              <option value="5">5</option>
              <option value="10">10</option>
              <option value="15">15</option>
              <option value="25">25</option>
              <option value="50">50</option>
            </select>
          </div>
        </div>
        <SolicitudTable
          :solicitudes="solicitudes"
          :is-loading="isLoading && solicitudes.length === 0"
          @update-estado="handleUpdateEstado"
          @delete-solicitud="handleDeleteSolicitud"
        />
        <Pagination v-if="pagination" :pagination="pagination" @page-change="handlePageChange" />
      </section>
    </main>

    <footer class="max-w-7xl mx-auto px-4 py-6">
      <p class="text-center text-sm text-gray-500">
        Prueba Técnica Full Stack — Laravel + Vue 3 (Arquitectura Modular)
      </p>
    </footer>
  </div>
</template>
