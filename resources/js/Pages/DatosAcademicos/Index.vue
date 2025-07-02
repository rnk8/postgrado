<template>
  <AppLayout>
    <template #title>Datos Académicos</template>
    
    <div class="max-w-7xl mx-auto space-y-6">
      <!-- Estadísticas -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat bg-base-100 shadow">
          <div class="stat-figure text-primary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
          </div>
          <div class="stat-title">Total Registros</div>
          <div class="stat-value text-primary">{{ estadisticas.total_registros?.toLocaleString() }}</div>
        </div>

        <div class="stat bg-base-100 shadow">
          <div class="stat-figure text-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
            </svg>
          </div>
          <div class="stat-title">Estudiantes</div>
          <div class="stat-value text-secondary">{{ estadisticas.estudiantes_unicos?.toLocaleString() }}</div>
        </div>

        <div class="stat bg-base-100 shadow">
          <div class="stat-figure text-accent">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
            </svg>
          </div>
          <div class="stat-title">Con Defensa</div>
          <div class="stat-value text-accent">{{ estadisticas.con_defensa_tesis?.toLocaleString() }}</div>
        </div>

        <div class="stat bg-base-100 shadow">
          <div class="stat-figure text-info">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
          </div>
          <div class="stat-title">Carreras</div>
          <div class="stat-value text-info">{{ estadisticas.carreras_activas?.toLocaleString() }}</div>
        </div>
      </div>

      <!-- Filtros -->
      <div class="card bg-base-100 shadow">
        <div class="card-body">
          <h3 class="card-title mb-4">Filtros de Búsqueda</h3>
          
          <form @submit.prevent="buscar" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
            <!-- Búsqueda general -->
            <div class="form-control">
              <label class="label">
                <span class="label-text">Buscar</span>
              </label>
              <input 
                v-model="form.search" 
                type="text" 
                placeholder="Estudiante, carrera, docente..." 
                class="input input-bordered" 
              />
            </div>

            <!-- Filtro por carrera -->
            <div class="form-control">
              <label class="label">
                <span class="label-text">Carrera</span>
              </label>
              <select v-model="form.carrera" class="select select-bordered">
                <option value="">Todas las carreras</option>
                <option v-for="carrera in carreras" :key="carrera.cod_carrera" :value="carrera.cod_carrera">
                  {{ carrera.nombre_carrera }}
                </option>
              </select>
            </div>

            <!-- Filtro por defensa -->
            <div class="form-control">
              <label class="label">
                <span class="label-text">Defensa de Tesis</span>
              </label>
              <select v-model="form.con_defensa" class="select select-bordered">
                <option value="">Todos</option>
                <option value="si">Con defensa</option>
                <option value="no">Sin defensa</option>
              </select>
            </div>

            <!-- Filtro por matriculado -->
            <div class="form-control">
              <label class="label">
                <span class="label-text">Matriculado</span>
              </label>
              <select v-model="form.matriculado" class="select select-bordered">
                <option value="">Todos</option>
                <option value="si">Matriculados</option>
                <option value="no">No matriculados</option>
              </select>
            </div>

            <!-- Botones -->
            <div class="form-control">
              <label class="label">
                <span class="label-text">&nbsp;</span>
              </label>
              <div class="join">
                <button type="submit" class="btn btn-primary join-item">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                  </svg>
                </button>
                <button type="button" @click="limpiar" class="btn btn-outline join-item">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                  </svg>
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>

      <!-- Tabla de datos -->
      <div class="card bg-base-100 shadow">
        <div class="card-body">
          <div class="flex justify-between items-center mb-4">
            <h3 class="card-title">Registros Académicos</h3>
            <div class="flex gap-2">
              <button v-if="permisos.puede_exportar" class="btn btn-outline btn-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Exportar
              </button>
            </div>
          </div>

          <div class="overflow-x-auto">
            <table class="table table-zebra">
              <thead>
                <tr>
                  <th>Estudiante</th>
                  <th>Carrera</th>
                  <th>Materia</th>
                  <th>Docente</th>
                  <th>Nota</th>
                  <th>Defensa</th>
                  <th>Estado</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="dato in datosAcademicos.data" :key="dato.id">
                  <td>
                    <div class="flex flex-col">
                      <div class="font-bold">{{ dato.nombre_est }}</div>
                      <div class="text-sm opacity-50">{{ dato.nro_registro_est }}</div>
                    </div>
                  </td>
                  <td>
                    <div class="flex flex-col">
                      <div class="font-medium">{{ dato.nombre_carrera }}</div>
                      <div class="text-sm opacity-50">{{ dato.cod_carrera }}</div>
                    </div>
                  </td>
                  <td>
                    <div class="flex flex-col">
                      <div class="font-medium">{{ dato.nombre_materia }}</div>
                      <div class="text-sm opacity-50">{{ dato.sigla_materia }}</div>
                    </div>
                  </td>
                  <td>
                    <div class="flex flex-col">
                      <div class="font-medium">{{ dato.nombre_doc }}</div>
                      <div class="text-sm opacity-50">{{ dato.cod_doc }}</div>
                    </div>
                  </td>
                  <td>
                    <div class="flex flex-col">
                      <div v-if="dato.nota" class="badge" :class="{
                        'badge-success': dato.nota >= 70,
                        'badge-warning': dato.nota >= 51 && dato.nota < 70,
                        'badge-error': dato.nota < 51
                      }">
                        {{ dato.nota }}
                      </div>
                      <div v-else class="text-sm opacity-50">Sin nota</div>
                    </div>
                  </td>
                  <td>
                    <div v-if="dato.fecha_defensa_tfg" class="flex flex-col">
                      <div class="text-sm">{{ formatDate(dato.fecha_defensa_tfg) }}</div>
                      <div v-if="dato.nota_defensa_tfg" class="badge badge-info badge-sm">
                        {{ dato.nota_defensa_tfg }}
                      </div>
                    </div>
                    <div v-else class="text-sm opacity-50">Sin defensa</div>
                  </td>
                  <td>
                    <div class="flex flex-col gap-1">
                      <div v-if="dato.matriculado === 'S'" class="badge badge-success badge-sm">Matriculado</div>
                      <div v-if="dato.acta_cerrada === 'S'" class="badge badge-info badge-sm">Acta Cerrada</div>
                    </div>
                  </td>
                  <td>
                    <div class="flex gap-2">
                      <Link :href="route('datos-academicos.show', dato.id)" class="btn btn-ghost btn-xs">
                        Ver
                      </Link>
                      <Link :href="route('estudiante.show', dato.nro_registro_est)" class="btn btn-ghost btn-xs">
                        Estudiante
                      </Link>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Paginación -->
          <div class="mt-4">
            <Pagination :links="datosAcademicos.links" />
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
  datosAcademicos: Object,
  estadisticas: Object,
  carreras: Array,
  filters: Object,
  permisos: Object
})

const form = reactive({
  search: props.filters.search || '',
  carrera: props.filters.carrera || '',
  con_defensa: props.filters.con_defensa || '',
  matriculado: props.filters.matriculado || ''
})

function buscar() {
  router.get(route('datos-academicos.index'), form, {
    preserveState: true,
    preserveScroll: true
  })
}

function limpiar() {
  Object.keys(form).forEach(key => form[key] = '')
  buscar()
}

function formatDate(date) {
  if (!date) return ''
  return new Date(date).toLocaleDateString('es-ES')
}
</script> 