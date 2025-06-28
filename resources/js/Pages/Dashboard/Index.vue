<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import HIcon from '@/Components/HIcon.vue';
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
            <HIcon name="UserGroupIcon" class="inline-block w-8 h-8" />
          </div>
          <div class="stat-title">Total Docentes</div>
          <div class="stat-value">{{ estadisticas.total_docentes }}</div>
          <div class="stat-desc">Registrados en el sistema</div>
        </div>

        <div class="stat">
          <div class="stat-figure text-secondary">
            <HIcon name="AcademicCapIcon" class="inline-block w-8 h-8" />
          </div>
          <div class="stat-title">Total Programas</div>
          <div class="stat-value">{{ estadisticas.total_programas }}</div>
          <div class="stat-desc">Programas académicos</div>
        </div>

        <div class="stat">
          <div class="stat-figure text-secondary">
            <HIcon name="DocumentTextIcon" class="inline-block w-8 h-8" />
          </div>
          <div class="stat-title">Total Tesis</div>
          <div class="stat-value">{{ estadisticas.total_tesis }}</div>
          <div class="stat-desc">Trabajos de investigación</div>
        </div>

        <div class="stat">
          <div class="stat-figure text-secondary">
            <HIcon name="CheckBadgeIcon" class="inline-block w-8 h-8" />
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