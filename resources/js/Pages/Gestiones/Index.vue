<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import { ref, reactive } from 'vue';
import HIcon from '@/Components/HIcon.vue';

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
  if (confirm(`¿Está seguro de activar la gestión "${gestion.nombre}"? Esto la convertirá en el período académico actual.`)) {
    router.put(route('gestiones.activar', gestion.id), {}, {
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => {
        // Opcional: mostrar un toast o notificación no intrusiva
      }
    });
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
                <HIcon name="MagnifyingGlassIcon" class="h-5 w-5" />
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
              <HIcon name="PlusIcon" class="h-5 w-5" />
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
                  <th>Docentes / Programas</th>
                  <th class="text-center">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="gestion in gestiones.data" :key="gestion.id" class="hover" :class="{'bg-success/5': gestion.es_actual}">
                  <td>
                    <div class="flex items-center gap-3">
                      <div>
                        <div class="font-bold flex items-center gap-2">
                          <span>{{ gestion.nombre }}</span>
                          <span v-if="gestion.es_actual" class="badge badge-success badge-sm" title="Esta es la gestión académica actual">ACTUAL</span>
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
                    <div class="flex items-center gap-2">
                        <div class="flex items-center" title="Docentes">
                            <HIcon name="UserGroupIcon" class="h-4 w-4 mr-1 opacity-60" />
                            <span class="font-mono text-sm">{{ gestion.docentes_count || 0 }}</span>
                        </div>
                        <div class="divider divider-horizontal mx-0"></div>
                        <div class="flex items-center" title="Programas">
                            <HIcon name="AcademicCapIcon" class="h-4 w-4 mr-1 opacity-60" />
                            <span class="font-mono text-sm">{{ gestion.programas_count || 0 }}</span>
                        </div>
                    </div>
                  </td>
                  <td class="whitespace-nowrap">
                      <!-- Botón para Activar (prominente) -->
                      <button 
                          v-if="permisos.puede_activar && !gestion.es_actual" 
                          @click="activarGestion(gestion)" 
                          class="btn btn-sm btn-success btn-outline"
                          title="Convertir en gestión actual">
                          <HIcon name="CheckCircleIcon" class="h-4 w-4" />
                          Activar
                      </button>

                      <!-- Menú de otras acciones -->
                      <div class="dropdown dropdown-end">
                          <div tabindex="0" role="button" class="btn btn-ghost btn-sm">
                              <HIcon name="EllipsisVerticalIcon" class="h-5 w-5" />
                          </div>
                          <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                              <li v-if="permisos.puede_ver_detalles">
                                  <Link :href="route('gestiones.show', gestion.id)">
                                      <HIcon name="EyeIcon" class="h-4 w-4" />
                                      Ver Detalles
                                  </Link>
                              </li>
                              <li v-if="permisos.puede_editar">
                                  <Link :href="route('gestiones.edit', gestion.id)">
                                      <HIcon name="PencilSquareIcon" class="h-4 w-4" />
                                      Editar
                                  </Link>
                              </li>
                              <li v-if="permisos.puede_eliminar">
                                  <button 
                                      @click="eliminarGestion(gestion)" 
                                      class="text-error w-full"
                                      :disabled="gestion.es_actual"
                                      title="No se puede eliminar la gestión actual">
                                      <HIcon name="TrashIcon" class="h-4 w-4" />
                                      Eliminar
                                  </button>
                              </li>
                          </ul>
                      </div>
                  </td>
                </tr>
                <tr v-if="gestiones.data.length === 0">
                    <td colspan="5" class="text-center py-8">
                        <div class="text-lg">No se encontraron gestiones.</div>
                        <div class="text-base-content/60">Intenta ajustar los filtros o crea una nueva.</div>
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