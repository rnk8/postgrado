<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import { ref, reactive } from 'vue';

const props = defineProps({
  gestiones: Object,
  resumen: Object,
  filters: Object,
  estadosDisponibles: Array,
  añosDisponibles: Array,
  permisos: Object,
});

const form = reactive({
  search: props.filters?.search || '',
  estado: props.filters?.estado || '',
  año: props.filters?.año || '',
});

function search() {
  router.get(route('gestiones.index'), form, {
    preserveState: true,
    replace: true,
  });
}

function clearFilters() {
  form.search = '';
  form.estado = '';
  form.año = '';
  search();
}

function formatDate(date) {
  return new Date(date).toLocaleDateString('es-ES');
}

function activarGestion(gestion) {
  if (confirm(`¿Está seguro de activar la gestión "${gestion.nombre}"?`)) {
    router.put(route('gestiones.activar', gestion.id));
  }
}

function eliminarGestion(gestion) {
  if (confirm(`¿Está seguro de eliminar la gestión "${gestion.nombre}"? Esta acción no se puede deshacer.`)) {
    router.delete(route('gestiones.destroy', gestion.id));
  }
}
</script>

<template>
  <AppLayout>
    <template #title>
      Gestión de Períodos Académicos
    </template>

    <div class="space-y-6">
      <!-- Filtros y Búsqueda -->
      <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
          <div class="flex flex-col lg:flex-row gap-4 items-end">
            <!-- Búsqueda -->
            <div class="form-control flex-1">
              <label class="label">
                <span class="label-text">Buscar gestión</span>
              </label>
              <input
                v-model="form.search"
                type="text"
                placeholder="Nombre o descripción..."
                class="input input-bordered"
                @keyup.enter="search"
              />
            </div>

            <!-- Estado -->
            <div class="form-control">
              <label class="label">
                <span class="label-text">Estado</span>
              </label>
              <select v-model="form.estado" class="select select-bordered">
                <option value="">Todos los estados</option>
                <option v-for="estado in estadosDisponibles" :key="estado.value" :value="estado.value">
                  {{ estado.label }}
                </option>
              </select>
            </div>

            <!-- Año -->
            <div class="form-control">
              <label class="label">
                <span class="label-text">Año</span>
              </label>
              <select v-model="form.año" class="select select-bordered">
                <option value="">Todos los años</option>
                <option v-for="año in añosDisponibles" :key="año.value" :value="año.value">
                  {{ año.label }}
                </option>
              </select>
            </div>

            <!-- Botones -->
            <div class="flex gap-2">
              <button @click="search" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Buscar
              </button>
              <button @click="clearFilters" class="btn btn-ghost">
                Limpiar
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Resumen -->
      <div class="stats stats-vertical lg:stats-horizontal shadow w-full">
        <div class="stat">
          <div class="stat-title">Total Gestiones</div>
          <div class="stat-value">{{ resumen.total_gestiones }}</div>
        </div>
        <div class="stat">
          <div class="stat-title">Gestión Actual</div>
          <div class="stat-value text-primary">{{ resumen.gestion_actual?.nombre || 'Ninguna' }}</div>
        </div>
        <div class="stat">
          <div class="stat-title">Activas</div>
          <div class="stat-value text-success">{{ resumen.gestiones_activas }}</div>
        </div>
        <div class="stat">
          <div class="stat-title">Inactivas</div>
          <div class="stat-value text-warning">{{ resumen.gestiones_inactivas }}</div>
        </div>
      </div>

      <!-- Tabla de Gestiones -->
      <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
          <div class="flex justify-between items-center mb-4">
            <h2 class="card-title">Lista de Gestiones</h2>
            <Link
              v-if="permisos.puede_crear"
              :href="route('gestiones.create')"
              class="btn btn-primary"
            >
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              Nueva Gestión
            </Link>
          </div>

          <div class="overflow-x-auto">
            <table class="table">
              <thead>
                <tr>
                  <th>Nombre</th>
                  <th>Período</th>
                  <th>Estado</th>
                  <th>Docentes</th>
                  <th>Programas</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="gestion in gestiones.data" :key="gestion.id" class="hover">
                  <td>
                    <div class="flex items-center gap-3">
                      <div>
                        <div class="font-bold flex items-center gap-2">
                          {{ gestion.nombre }}
                          <div v-if="gestion.es_actual" class="badge badge-primary badge-sm">ACTUAL</div>
                        </div>
                        <div class="text-sm opacity-50">{{ gestion.descripcion }}</div>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="text-sm">
                      <div>{{ formatDate(gestion.fecha_inicio) }}</div>
                      <div class="opacity-50">{{ formatDate(gestion.fecha_fin) }}</div>
                    </div>
                  </td>
                  <td>
                    <div class="badge" :class="{
                      'badge-success': gestion.estado === 'activo',
                      'badge-warning': gestion.estado === 'inactivo'
                    }">
                      {{ gestion.estado }}
                    </div>
                  </td>
                  <td>
                    <div class="badge badge-outline">{{ gestion.docentes_count || 0 }}</div>
                  </td>
                  <td>
                    <div class="badge badge-outline">{{ gestion.programas_count || 0 }}</div>
                  </td>
                  <td>
                    <div class="dropdown dropdown-end">
                      <div tabindex="0" role="button" class="btn btn-ghost btn-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                        </svg>
                      </div>
                      <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                        <li>
                          <Link :href="route('gestiones.show', gestion.id)">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Ver Detalles
                          </Link>
                        </li>
                        <li v-if="permisos.puede_editar">
                          <Link :href="route('gestiones.edit', gestion.id)">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Editar
                          </Link>
                        </li>
                        <li v-if="permisos.puede_activar && !gestion.es_actual">
                          <button @click="activarGestion(gestion)" class="text-success">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Activar
                          </button>
                        </li>
                        <li v-if="permisos.puede_eliminar && !gestion.es_actual">
                          <button @click="eliminarGestion(gestion)" class="text-error">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Eliminar
                          </button>
                        </li>
                      </ul>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Paginación -->
          <Pagination :links="gestiones.links" />
        </div>
      </div>
    </div>
  </AppLayout>
</template> 