<script setup lang="ts">
import type { Estado } from '@/types/solicitud';

defineProps<{ estado: Estado }>();

function getColorClasses(color: string): string {
  const colorMap: Record<string, string> = {
    warning: 'bg-yellow-100 text-yellow-800 border-yellow-300',
    success: 'bg-green-100 text-green-800 border-green-300',
    danger: 'bg-red-100 text-red-800 border-red-300',
    info: 'bg-blue-100 text-blue-800 border-blue-300',
  };
  return colorMap[color] || 'bg-gray-100 text-gray-800 border-gray-300';
}
</script>

<template>
  <span
    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border"
    :class="getColorClasses(estado.color)"
  >
    {{ estado.label }}
  </span>
</template>
