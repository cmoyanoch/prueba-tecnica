export type EstadoValue = 'pendiente' | 'aprobado' | 'rechazado' | 'modificar';

export interface Estado {
  value: EstadoValue;
  label: string;
  color: 'warning' | 'success' | 'danger' | 'info';
}

export interface Solicitud {
  id: number;
  nombre_documento: string;
  estado: Estado;
  fecha_creacion: string;
  fecha_actualizacion: string;
}

export interface CreateSolicitudPayload {
  nombre_documento: string;
}

export interface UpdateEstadoPayload {
  estado: EstadoValue;
}

export interface ApiResponse<T> {
  data: T;
}

export interface PaginationMeta {
  current_page: number;
  per_page: number;
  total: number;
  last_page: number;
  from: number;
  to: number;
}

export interface PaginationLinks {
  first: string | null;
  last: string | null;
  prev: string | null;
  next: string | null;
}

export interface PaginatedResponse<T> {
  data: T[];
  links: PaginationLinks;
  meta: PaginationMeta;
}
