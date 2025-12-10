<script setup lang="ts">
import { ref } from 'vue';

const emit = defineEmits<{
  (e: 'submit', nombreDocumento: string): void;
}>();

defineProps<{ isLoading?: boolean }>();

const nombreDocumento = ref('');
const validationError = ref('');

function handleSubmit(): void {
  const trimmed = nombreDocumento.value.trim();

  if (!trimmed) {
    validationError.value = 'El nombre del documento es obligatorio';
    return;
  }

  if (trimmed.length < 3) {
    validationError.value = 'El nombre debe tener al menos 3 caracteres';
    return;
  }

  validationError.value = '';
  emit('submit', trimmed);
  nombreDocumento.value = '';
}
</script>

<template>
  <form @submit.prevent="handleSubmit" class="space-y-4">
    <div>
      <label for="nombre_documento" class="block text-sm font-medium text-gray-700 mb-1">
        Nombre del Documento
      </label>
      <div class="flex gap-3">
        <input
          id="nombre_documento"
          v-model="nombreDocumento"
          type="text"
          placeholder="Ej: Contrato de Servicios"
          :disabled="isLoading"
          class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-4 py-2 border"
          @input="validationError = ''"
        />
        <button
          type="submit"
          :disabled="isLoading"
          class="px-4 py-2 text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 disabled:bg-blue-300"
        >
          {{ isLoading ? 'Creando...' : 'Crear Solicitud' }}
        </button>
      </div>
      <p v-if="validationError" class="mt-1 text-sm text-red-600">
        {{ validationError }}
      </p>
    </div>
  </form>
</template>
