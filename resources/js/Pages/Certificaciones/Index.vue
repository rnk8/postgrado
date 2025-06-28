<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, watch } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import { throttle } from 'lodash';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
  certificaciones: Object,
  programas: Array,
  filters: Object,
  estadosDisponibles: Object,
});

const filters = ref({
  search: props.filters.search || '',
  estado: props.filters.estado || '',
  programa_id: props.filters.programa_id || '',
});

watch(filters, throttle(() => {
  router.get(route('certificaciones.index'), filters.value, {
    preserveState: true,
    replace: true,
  });
}, 300), { deep: true });
</script>

<template>
  <AppLayout>
    <template #title>
      Gestión de Certificaciones
    </template>

    <div class="bg-base-100 p-6 rounded-box shadow-lg">
      <!-- Filtros y Acciones -->
      <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
        <div class="flex flex-wrap gap-4">
            <input type="text" v-model="filters.search" placeholder="Buscar por nombre, registro..." class="input input-bordered w-full max-w-xs" />
            <select v-model="filters.estado" class="select select-bordered">
                <option value="">Todos los estados</option>
                <option v-for="(label, value) in estadosDisponibles" :key="value" :value="value">{{ label }}</option>
            </select>
            <select v-model="filters.programa_id" class="select select-bordered">
                <option value="">Todos los programas</option>
                <option v-for="programa in programas" :key="programa.id" :value="programa.id">{{ programa.nombre }}</option>
            </select>
        </div>
        <Link :href="route('certificaciones.create')" class="btn btn-primary">
          Nueva Certificación
        </Link>
      </div>

      <!-- Tabla de Certificaciones -->
      <div class="overflow-x-auto">
        <table class="table table-zebra w-full">
          <thead>
            <tr>
              <th>Número</th>
              <th>Estudiante</th>
              <th>Programa</th>
              <th>Promedio</th>
              <th>Fecha Emisión</th>
              <th>Estado</th>
              <th class="text-right">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="cert in certificaciones.data" :key="cert.id">
              <td class="font-mono">{{ cert.numero }}</td>
              <td>{{ cert.nombre_est }}</td>
              <td>{{ cert.programa?.nombre_carrera || 'N/A' }}</td>
              <td>{{ cert.promedio }}</td>
              <td>{{ cert.fecha_emision ? new Date(cert.fecha_emision).toLocaleDateString() : 'N/A' }}</td>
              <td>
                <span class="badge" :class="{
                  'badge-info': cert.estado === 'pendiente',
                  'badge-success': cert.estado === 'emitido',
                  'badge-neutral': cert.estado === 'entregado',
                }">{{ cert.estado }}</span>
              </td>
              <td class="text-right">
                <div class="join">
                    <Link :href="route('certificaciones.show', cert.id)" class="btn btn-ghost btn-sm join-item">Ver</Link>
                    <Link :href="route('certificaciones.edit', cert.id)" class="btn btn-ghost btn-sm join-item">Editar</Link>
                    <Link v-if="cert.estado === 'pendiente'" :href="route('certificaciones.emitir', cert.id)" method="patch" as="button" class="btn btn-ghost btn-sm join-item text-success">Emitir</Link>
                    <Link :href="route('certificaciones.destroy', cert.id)" method="delete" as="button" class="btn btn-ghost btn-sm join-item text-red-500" :onBefore="() => confirm('¿Seguro que quieres eliminar esta certificación?')">Eliminar</Link>
                </div>
              </td>
            </tr>
             <tr v-if="certificaciones.data.length === 0">
                <td colspan="7" class="text-center py-4">No se encontraron certificaciones.</td>
            </tr>
          </tbody>
        </table>
      </div>
        
      <!-- Paginación -->
      <Pagination :links="certificaciones.links" />
    </div>
  </AppLayout>
</template> 