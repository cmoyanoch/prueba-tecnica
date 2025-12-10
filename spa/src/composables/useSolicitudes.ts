import { ApiError, solicitudService } from '@/services/solicitudService';
import type {
  CreateSolicitudPayload,
  EstadoValue,
  PaginatedResponse,
  PaginationMeta,
  Solicitud,
} from '@/types/solicitud';
import { computed, readonly, ref } from 'vue';

export function useSolicitudes() {
  const solicitudes = ref<Solicitud[]>([]);
  const isLoading = ref(false);
  const error = ref<string | null>(null);
  const pagination = ref<PaginationMeta | null>(null);
  const currentPage = ref(1);
  const perPage = ref(5);

  const totalSolicitudes = computed(() => pagination.value?.total ?? solicitudes.value.length);
  const solicitudesPendientes = computed(() =>
    solicitudes.value.filter(s => s.estado.value === 'pendiente').length
  );
  const hasPagination = computed(() => pagination.value !== null);
  const totalPages = computed(() => pagination.value?.last_page ?? 1);
mkdir -p src/composables

  function handleError(e: unknown): void {
    if (e instanceof ApiError) {
      error.value = e.errors
        ? Object.values(e.errors).flat().join(', ')
        : e.message;
    } else if (e instanceof Error) {
      error.value = e.message;
    } else {
      error.value = 'Ha ocurrido un error inesperado';
    }
  }

  async function fetchSolicitudes(): Promise<void> {
    isLoading.value = true;
    error.value = null;
    try {
      solicitudes.value = await solicitudService.getAll();
      pagination.value = null;
    } catch (e) {
      handleError(e);
    } finally {
      isLoading.value = false;
    }
  }

  async function fetchSolicitudesPaginated(page: number = 1): Promise<void> {
    isLoading.value = true;
    error.value = null;
    currentPage.value = page;
    try {
      const response: PaginatedResponse<Solicitud> = await solicitudService.getAllPaginated(
        page,
        perPage.value
      );
      solicitudes.value = response.data;
      pagination.value = response.meta;
    } catch (e) {
      handleError(e);
    } finally {
      isLoading.value = false;
    }
  }

  async function createSolicitud(payload: CreateSolicitudPayload): Promise<boolean> {
    isLoading.value = true;
    error.value = null;
    try {
      const nueva = await solicitudService.create(payload);
      if (hasPagination.value) {
        await fetchSolicitudesPaginated(currentPage.value);
      } else {
      solicitudes.value = [nueva, ...solicitudes.value];
      }
      return true;
    } catch (e) {
      handleError(e);
      return false;
    } finally {
      isLoading.value = false;
    }
  }

  async function updateEstado(id: number, estado: EstadoValue): Promise<boolean> {
    error.value = null;
    try {
      const actualizada = await solicitudService.updateEstado(id, { estado });
      const index = solicitudes.value.findIndex(s => s.id === id);
      if (index !== -1) {
        solicitudes.value[index] = actualizada;
      }
      return true;
    } catch (e) {
      handleError(e);
      return false;
    }
  }

  async function deleteSolicitud(id: number): Promise<boolean> {
    error.value = null;
    try {
      await solicitudService.delete(id);
      // Remover de la lista local
      const index = solicitudes.value.findIndex(s => s.id === id);
      if (index !== -1) {
        solicitudes.value.splice(index, 1);
      }
      return true;
    } catch (e) {
      handleError(e);
      return false;
    }

  }

  function clearError(): void {
    error.value = null;
  }

  function setPerPage(value: number): void {
    perPage.value = value;
    if (hasPagination.value) {
      fetchSolicitudesPaginated(1);
    }
  }

  return {
    solicitudes: readonly(solicitudes),
    isLoading: readonly(isLoading),
    error: readonly(error),
    pagination: readonly(pagination),
    currentPage: readonly(currentPage),
    perPage: readonly(perPage),
    totalSolicitudes,
    solicitudesPendientes,
    hasPagination,
    totalPages,
    fetchSolicitudes,
    fetchSolicitudesPaginated,
    createSolicitud,
    updateEstado,
    deleteSolicitud,
    clearError,
    setPerPage,
  };
}
