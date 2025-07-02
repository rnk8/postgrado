<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, watch, computed } from 'vue';
import { router, Link, usePage } from '@inertiajs/vue3';
import { throttle } from 'lodash';
import Pagination from '@/Components/Pagination.vue';
import HIcon from '@/Components/HIcon.vue';

const props = defineProps({
  certificaciones: Object,
  programas: Array,
  filters: Object,
  estadosDisponibles: Object,
  permisos: Object,
});

const page = usePage();
const gestionActiva = computed(() => page.props.system.gestion_actual);

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
        <button @click="clearFilters" class="btn btn-ghost">
          Limpiar
        </button>
        <Link
          v-if="permisos.puede_crear"
          :href="route('certificaciones.create')"
          class="btn btn-primary"
        >
          <HIcon name="PlusIcon" class="h-5 w-5" />
          Nueva Certificación
        </Link>
      </div>

      <!-- Alerta de Gestión No Activa -->
      <div v-if="!gestionActiva" role="alert" class="alert alert-warning mb-6">
          <HIcon name="ExclamationTriangleIcon" class="h-6 w-6" />
          <div>
              <h3 class="font-bold">No hay una gestión académica activa.</h3>
              <div class="text-xs">Para ver, crear o administrar certificaciones, primero debe
                  <Link :href="route('gestiones.index')" class="link link-primary">activar una gestión</Link>.
              </div>
          </div>
      </div>

      <!-- Tabla de Certificaciones -->
      <div v-else class="overflow-x-auto">
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
                  <div class="dropdown dropdown-end">
                    <label tabindex="0" class="btn btn-ghost btn-sm join-item">Acciones</label>
                    <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                      <li>
                        <Link :href="route('certificaciones.show', cert.id)">
                          <HIcon name="EyeIcon" class="h-4 w-4" />
                          Ver Detalles
                        </Link>
                      </li>
                      <li v-if="permisos.puede_editar">
                        <Link :href="route('certificaciones.edit', cert.id)">
                          <HIcon name="PencilSquareIcon" class="h-4 w-4" />
                          Editar
                        </Link>
                      </li>
                      <li v-if="permisos.puede_emitir && cert.estado === 'pendiente'">
                        <Link :href="route('certificaciones.emitir', cert.id)" method="put" as="button" class="text-success">
                          <HIcon name="CheckCircleIcon" class="h-4 w-4" />
                          Emitir
                        </Link>
                      </li>
                      <li v-if="permisos.puede_eliminar">
                        <button @click="eliminarCertificacion(cert)" class="text-error">
                          <HIcon name="TrashIcon" class="h-4 w-4" />
                          Eliminar
                        </button>
                      </li>
                    </ul>
                  </div>
                </div>
              </td>
            </tr>
             <tr v-if="certificaciones.data.length === 0">
                <td colspan="7" class="text-center py-8">
                    <div class="text-lg">No se encontraron certificaciones.</div>
                    <div class="text-base-content/60">
                        No hay certificaciones registradas para la gestión actual ({{ gestionActiva.nombre }}) o que coincidan con su búsqueda.
                    </div>
                </td>
            </tr>
          </tbody>
        </table>
      </div>
        
      <!-- Paginación -->
      <Pagination v-if="gestionActiva && certificaciones.data.length > 0" :links="certificaciones.links" />
    </div>
  </AppLayout>
</template> 