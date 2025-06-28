<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import {
  UserGroupIcon,
  AcademicCapIcon,
  DocumentTextIcon,
  CheckBadgeIcon,
  ClockIcon,
  CheckCircleIcon,
  ExclamationCircleIcon,
  XCircleIcon,
  EyeIcon,
} from '@heroicons/vue/24/outline';

const props = defineProps({
  estadisticas: Object,
  tesis_por_estado: Object,
  paginas_mas_visitadas: Array,
  ultimas_tesis: Array,
  permisos: Object,
});
</script>

<template>
  <AppLayout>
    <template #title>
      Dashboard - Sistema de Postgrado
    </template>

    <div class="space-y-6">
      <!-- Stats Cards -->
      <div class="stats stats-vertical lg:stats-horizontal shadow w-full">
        <div class="stat">
          <div class="stat-figure text-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div class="stat-title">Total Docentes</div>
          <div class="stat-value">{{ estadisticas.total_docentes }}</div>
          <div class="stat-desc">Registrados en el sistema</div>
        </div>

        <div class="stat">
          <div class="stat-figure text-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
            </svg>
          </div>
          <div class="stat-title">Total Programas</div>
          <div class="stat-value">{{ estadisticas.total_programas }}</div>
          <div class="stat-desc">Programas académicos</div>
        </div>

        <div class="stat">
          <div class="stat-figure text-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
          </div>
          <div class="stat-title">Total Tesis</div>
          <div class="stat-value">{{ estadisticas.total_tesis }}</div>
          <div class="stat-desc">Trabajos de investigación</div>
        </div>

        <div class="stat">
          <div class="stat-figure text-secondary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-8 h-8 stroke-current">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
            </svg>
          </div>
          <div class="stat-title">Total Certificaciones</div>
          <div class="stat-value">{{ estadisticas.total_certificaciones }}</div>
          <div class="stat-desc">Certificados emitidos</div>
        </div>
      </div>

      <!-- Grid Layout for Charts and Tables -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Tesis por Estado -->
        <div class="card bg-base-100 shadow-xl">
          <div class="card-body">
            <h2 class="card-title">Tesis por Estado</h2>
            <div class="space-y-2">
              <div v-for="(cantidad, estado) in tesis_por_estado" :key="estado" class="flex justify-between items-center">
                <span class="capitalize">{{ estado.replace('_', ' ') }}</span>
                <div class="badge badge-primary">{{ cantidad }}</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Páginas Más Visitadas -->
        <div class="card bg-base-100 shadow-xl">
          <div class="card-body">
            <h2 class="card-title">Páginas Más Visitadas</h2>
            <div class="overflow-x-auto">
              <table class="table table-sm">
                <thead>
                  <tr>
                    <th>Página</th>
                    <th>Visitas</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="pagina in paginas_mas_visitadas" :key="pagina.route">
                    <td>{{ pagina.route }}</td>
                    <td>
                      <div class="badge badge-outline">{{ pagina.visitas }}</div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Últimas Tesis -->
      <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
          <h2 class="card-title">Últimas Tesis Registradas</h2>
          <div class="overflow-x-auto">
            <table class="table">
              <thead>
                <tr>
                  <th>Título</th>
                  <th>Programa</th>
                  <th>Tutor</th>
                  <th>Estado</th>
                  <th>Fecha</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="tesis in ultimas_tesis" :key="tesis.id" class="hover">
                  <td>
                    <div class="font-bold">{{ tesis.titulo }}</div>
                    <div class="text-sm opacity-50">{{ tesis.nombre_est }}</div>
                  </td>
                  <td>{{ tesis.programa?.nombre_carrera }}</td>
                  <td>{{ tesis.tutor?.nombre_doc }}</td>
                  <td>
                    <div class="badge" :class="{
                      'badge-success': tesis.estado === 'aprobada',
                      'badge-warning': tesis.estado === 'en_desarrollo',
                      'badge-info': tesis.estado === 'defendida',
                      'badge-error': tesis.estado === 'rechazada'
                    }">
                      {{ tesis.estado.replace('_', ' ') }}
                    </div>
                  </td>
                  <td>{{ new Date(tesis.created_at).toLocaleDateString() }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
          <h2 class="card-title">Acciones Rápidas</h2>
          <div class="card-actions justify-end">
            <Link v-if="permisos?.puede_crear_gestiones" :href="route('gestiones.create')" class="btn btn-primary">
              Nueva Gestión
            </Link>
            <Link v-if="permisos?.puede_cargar_excel" :href="route('excel.index')" class="btn btn-secondary">
              Cargar Excel
            </Link>
            <Link :href="route('search.index')" class="btn btn-outline">
              Búsqueda Avanzada
            </Link>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template> 