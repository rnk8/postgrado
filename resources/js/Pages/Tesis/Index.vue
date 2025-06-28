<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import { reactive } from 'vue';

const props = defineProps({
  tesis: Object,
  programas: Array,
  tutores: Array,
  filters: Object,
  estadosDisponibles: Object,
  permisos: Object,
});

// Renombrar para evitar conflictos
const tesisList = props.tesis;

const form = reactive({
  search: props.filters?.search || '',
  estado: props.filters?.estado || '',
  programa_id: props.filters?.programa_id || '',
  tutor_id: props.filters?.tutor_id || '',
});

function search() {
  router.get(route('tesis.index'), form, {
    preserveState: true,
    replace: true,
  });
}

function clearFilters() {
  form.search = '';
  form.estado = '';
  form.programa_id = '';
  form.tutor_id = '';
  search();
}

function aprobarTesis(tesis) {
  if (confirm(`¿Está seguro de aprobar la tesis "${tesis.titulo}"?`)) {
    router.post(route('tesis.aprobar', tesis.id));
  }
}

function eliminarTesis(tesis) {
  if (confirm(`¿Está seguro de eliminar la tesis "${tesis.titulo}"? Esta acción no se puede deshacer.`)) {
    router.delete(route('tesis.destroy', tesis.id));
  }
}
</script>

<template>
  <AppLayout>
    <template #title>
      Gestión de Tesis
    </template>

    <div class="space-y-6">
      <!-- Filtros y Búsqueda -->
      <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
          <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 items-end">
            <!-- Búsqueda -->
            <div class="form-control">
              <label class="label">
                <span class="label-text">Buscar tesis</span>
              </label>
              <input
                v-model="form.search"
                type="text"
                placeholder="Título o estudiante..."
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
                <option v-for="(label, value) in estadosDisponibles" :key="value" :value="value">
                  {{ label }}
                </option>
              </select>
            </div>

            <!-- Programa -->
            <div class="form-control">
              <label class="label">
                <span class="label-text">Programa</span>
              </label>
              <select v-model="form.programa_id" class="select select-bordered">
                <option value="">Todos los programas</option>
                <option v-for="programa in programas" :key="programa.id" :value="programa.id">
                  {{ programa.nombre_carrera }}
                </option>
              </select>
            </div>

            <!-- Botones -->
            <div class="flex flex-wrap gap-2 justify-start lg:justify-end">
              <button @click="search" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Buscar
              </button>
              <button @click="clearFilters" class="btn btn-ghost">
                Limpiar
              </button>
              <Link
                v-if="permisos.puede_crear"
                :href="route('tesis.create')"
                class="btn btn-primary"
              >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Nueva Tesis
              </Link>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabla de Tesis -->
      <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
          <h2 class="card-title">Lista de Tesis</h2>
          
          <div class="overflow-x-auto">
            <table class="table">
              <thead>
                <tr>
                  <th>Tesis</th>
                  <th>Estudiante</th>
                  <th>Programa</th>
                  <th>Tutor</th>
                  <th>Estado</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="tesis in tesisList.data" :key="tesis.id" class="hover">
                  <td>
                    <div>
                      <div class="font-bold">{{ tesis.titulo }}</div>
                      <div class="text-sm opacity-50">{{ tesis.codigo }}</div>
                    </div>
                  </td>
                  <td>
                    <div>
                      <div class="font-medium">{{ tesis.nombre_est }}</div>
                      <div class="text-sm opacity-50">{{ tesis.nro_registro_est }}</div>
                    </div>
                  </td>
                  <td>
                    <div class="text-sm">{{ tesis.programa?.nombre_carrera }}</div>
                  </td>
                  <td>
                    <div class="text-sm">{{ tesis.tutor?.nombre_doc }}</div>
                  </td>
                  <td>
                    <div class="badge" :class="{
                      'badge-warning': tesis.estado === 'en_desarrollo',
                      'badge-info': tesis.estado === 'defendida',
                      'badge-success': tesis.estado === 'aprobada',
                      'badge-error': tesis.estado === 'rechazada'
                    }">
                      {{ estadosDisponibles[tesis.estado] }}
                    </div>
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
                          <Link :href="route('tesis.show', tesis.id)">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Ver Detalles
                          </Link>
                        </li>
                        <li v-if="permisos.puede_editar">
                          <Link :href="route('tesis.edit', tesis.id)">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Editar
                          </Link>
                        </li>
                        <li v-if="permisos.puede_aprobar && tesis.estado === 'defendida'">
                          <button @click="aprobarTesis(tesis)" class="text-success">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Aprobar
                          </button>
                        </li>
                        <li v-if="permisos.puede_eliminar">
                          <button @click="eliminarTesis(tesis)" class="text-error">
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
                <tr v-if="tesisList.data.length === 0">
                  <td colspan="6" class="text-center py-4">No se encontraron tesis.</td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Paginación -->
          <Pagination :links="tesisList.links" />
        </div>
      </div>
    </div>
  </AppLayout>
</template> 