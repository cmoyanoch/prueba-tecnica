import type {
  ApiResponse,
  CreateSolicitudPayload,
  PaginatedResponse,
  Solicitud,
  UpdateEstadoPayload,
} from '@/types/solicitud';

const API_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api';

export class ApiError extends Error {
  constructor(
    message: string,
    public status: number,
    public errors?: Record<string, string[]>
  ) {
    super(message);
    this.name = 'ApiError';
  }
}

async function request<T>(endpoint: string, options: RequestInit = {}): Promise<T> {
  const url = `${API_BASE_URL}${endpoint}`;
  const config: RequestInit = {
    headers: {
      'Content-Type': 'application/json',
      Accept: 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      ...options.headers,
    },
    ...options,
  };

  const response = await fetch(url, config);

  if (!response.ok) {
    const errorData = await response.json().catch(() => ({}));
    throw new ApiError(
      errorData.message || 'Error en la petición',
      response.status,
      errorData.errors
    );
  }

  return response.json();
}

export const solicitudService = {
  async getAll(): Promise<Solicitud[]> {
    const response = await request<ApiResponse<Solicitud[]>>('/solicitudes');
    return response.data;
  },

  async getAllPaginated(page: number = 1, perPage: number = 5): Promise<PaginatedResponse<Solicitud>> {
    const response = await request<PaginatedResponse<Solicitud>>(
      `/solicitudes?per_page=${perPage}&page=${page}`
    );
    return response;
  },

  async create(payload: CreateSolicitudPayload): Promise<Solicitud> {
    const response = await request<ApiResponse<Solicitud>>('/solicitudes', {
      method: 'POST',
      body: JSON.stringify(payload),
    });
    return response.data;
  },

  async updateEstado(id: number, payload: UpdateEstadoPayload): Promise<Solicitud> {
    const response = await request<ApiResponse<Solicitud>>(`/solicitudes/${id}`, {
      method: 'PATCH',
      body: JSON.stringify(payload),
    });
    return response.data;
  },

  async delete(id: number): Promise<void> {
    await request<{message: string}>(`/solicitudes/${id}`, {
      method: 'DELETE',
    });
  },
};
