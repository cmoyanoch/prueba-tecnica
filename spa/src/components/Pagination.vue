<script setup lang="ts">
import type { PaginationMeta } from '@/types/solicitud';
import { computed } from 'vue';

const props = defineProps<{ pagination: PaginationMeta | null }>();

const emit = defineEmits<{ (e: 'page-change', page: number): void }>();

function goToPage(page: number): void {
  if (page >= 1 && page <= (props.pagination?.last_page ?? 1)) {
    emit('page-change', page);
  }
}

const pages = computed(() => {
  if (!props.pagination) return [];
  const current = props.pagination.current_page;
  const last = props.pagination.last_page;
  const pages: (number | string)[] = [];

  if (last <= 7) {
    // Mostrar todas las páginas si son 7 o menos
    for (let i = 1; i <= last; i++) {
      pages.push(i);
    }
  } else {
    // Lógica para mostrar páginas con elipsis
    if (current <= 3) {
      for (let i = 1; i <= 4; i++) pages.push(i);
      pages.push('...');
      pages.push(last);
    } else if (current >= last - 2) {
      pages.push(1);
      pages.push('...');
      for (let i = last - 3; i <= last; i++) pages.push(i);
    } else {
      pages.push(1);
      pages.push('...');
      for (let i = current - 1; i <= current + 1; i++) pages.push(i);
      pages.push('...');
      pages.push(last);
    }
  }

  return pages;
});
</script>

<template>
  <nav
    v-if="pagination && pagination.last_page > 1"
    class="flex items-center justify-between border-t border-gray-200 bg-white px-4 py-3 sm:px-6"
    aria-label="Pagination"
  >
    <div class="hidden sm:block">
      <p class="text-sm text-gray-700">
        Mostrando
        <span class="font-medium">{{ pagination.from }}</span>
        a
        <span class="font-medium">{{ pagination.to }}</span>
        de
        <span class="font-medium">{{ pagination.total }}</span>
        resultados
      </p>
    </div>
    <div class="flex flex-1 justify-between sm:justify-end">
      <button
        :disabled="pagination.current_page === 1"
        class="relative mr-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:bg-gray-100 disabled:text-gray-400"
        @click="goToPage(pagination.current_page - 1)"
      >
        Anterior
      </button>
      <div class="hidden sm:flex sm:gap-1">
        <button
          v-for="(page, index) in pages"
          :key="index"
          :disabled="page === '...' || page === pagination.current_page"
          class="relative inline-flex items-center rounded-md border px-4 py-2 text-sm font-medium disabled:cursor-default"
          :class="
            page === pagination.current_page
              ? 'z-10 bg-blue-600 border-blue-600 text-white'
              : page === '...'
                ? 'border-gray-300 bg-white text-gray-700'
                : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50'
          "
          @click="typeof page === 'number' && goToPage(page)"
        >
          {{ page }}
        </button>
      </div>
      <button
        :disabled="pagination.current_page === pagination.last_page"
        class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:bg-gray-100 disabled:text-gray-400"
        @click="goToPage(pagination.current_page + 1)"
      >
        Siguiente
      </button>
    </div>
  </nav>
</template>
