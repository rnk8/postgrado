<script setup>
/* global route */
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import { reactive, computed } from 'vue';
import HIcon from '@/Components/HIcon.vue';

const props = defineProps({
  tesis: Object,
  programas: Array,
  tutores: Array,
  filters: Object,
  estadosDisponibles: Object,
  permisos: Object,
  gestionActiva: Boolean,
});

const page = usePage();
const gestionActiva = computed(() => page.props.system.gestion_actual);

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
    router.put(route('tesis.aprobar', tesis.id));
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
                <HIcon name="MagnifyingGlassIcon" class="h-5 w-5" />
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
                <HIcon name="PlusIcon" class="h-5 w-5" />
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
          
          <!-- Alerta de Gestión No Activa -->
          <div v-if="!gestionActiva" role="alert" class="alert alert-warning">
              <HIcon name="ExclamationTriangleIcon" class="h-6 w-6" />
              <div>
                  <h3 class="font-bold">No hay una gestión académica activa.</h3>
                  <div class="text-xs">Para ver, crear o administrar tesis, primero debe
                      <Link :href="route('gestiones.index')" class="link link-primary">activar una gestión</Link>.
                  </div>
              </div>
          </div>

          <div v-else class="overflow-x-auto">
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
                        <HIcon name="EllipsisVerticalIcon" class="h-5 w-5" />
                      </div>
                      <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                        <li>
                          <Link :href="route('tesis.show', tesis.id)">
                            <HIcon name="EyeIcon" class="h-4 w-4" />
                            Ver Detalles
                          </Link>
                        </li>
                        <li v-if="permisos.puede_editar">
                          <Link :href="route('tesis.edit', tesis.id)">
                            <HIcon name="PencilSquareIcon" class="h-4 w-4" />
                            Editar
                          </Link>
                        </li>
                        <li v-if="permisos.puede_aprobar && tesis.estado === 'defendida'">
                          <button @click="aprobarTesis(tesis)" class="text-success">
                            <HIcon name="CheckCircleIcon" class="h-4 w-4" />
                            Aprobar
                          </button>
                        </li>
                        <li v-if="permisos.puede_eliminar">
                          <button @click="eliminarTesis(tesis)" class="text-error">
                            <HIcon name="TrashIcon" class="h-4 w-4" />
                            Eliminar
                          </button>
                        </li>
                      </ul>
                    </div>
                  </td>
                </tr>
                <tr v-if="tesisList.data.length === 0">
                  <td colspan="6" class="text-center py-8">
                    <div class="text-lg">No se encontraron tesis.</div>
                    <div class="text-base-content/60">
                        No hay tesis registradas para la gestión actual ({{ gestionActiva.nombre }}) o que coincidan con su búsqueda.
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Paginación -->
          <Pagination v-if="gestionActiva && tesisList.data.length > 0" :links="tesisList.links" />
        </div>
      </div>
    </div>
  </AppLayout>
</template> 