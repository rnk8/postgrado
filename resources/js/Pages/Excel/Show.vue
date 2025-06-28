<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import {
  InformationCircleIcon,
  ExclamationTriangleIcon,
  CheckCircleIcon,
  XCircleIcon,
  ClockIcon,
  DocumentTextIcon,
  UserCircleIcon,
  CalendarIcon,
  TableCellsIcon,
  ChartBarSquareIcon,
  ArrowLeftIcon,
  PlayIcon,
  StopIcon,
  ArrowPathIcon,
  CloudArrowUpIcon,
  DocumentArrowDownIcon,
  ExclamationCircleIcon
} from '@heroicons/vue/24/outline';
import { computed } from 'vue';

const props = defineProps({
  carga: Object,
  datosAcademicos: Object,
});

const resumen = props.carga.resumen_procesamiento || {};
const errores = resumen.errores || [];

// Computed properties
const porcentajeExito = computed(() => {
  if (!props.carga.registros_procesados) return 0;
  return Math.round((props.carga.registros_exitosos / props.carga.registros_procesados) * 100);
});

const tiempoTranscurrido = computed(() => {
  const inicio = new Date(props.carga.created_at);
  const fin = props.carga.updated_at ? new Date(props.carga.updated_at) : new Date();
  const diff = fin - inicio;
  const minutos = Math.floor(diff / 60000);
  const segundos = Math.floor((diff % 60000) / 1000);
  return `${minutos}m ${segundos}s`;
});

// Helper functions
const formatDate = (dateString) => {
  const options = { 
    year: 'numeric', 
    month: 'long', 
    day: 'numeric', 
    hour: '2-digit', 
    minute: '2-digit',
    second: '2-digit',
    hour12: false 
  };
  return new Date(dateString).toLocaleDateString('es-ES', options);
};

const formatDateRelative = (dateString) => {
  const now = new Date();
  const date = new Date(dateString);
  const diff = now - date;
  const hours = Math.floor(diff / (1000 * 60 * 60));
  const days = Math.floor(hours / 24);
  
  if (days > 0) return `hace ${days} día${days > 1 ? 's' : ''}`;
  if (hours > 0) return `hace ${hours} hora${hours > 1 ? 's' : ''}`;
  return 'hace unos minutos';
};

const getEstadoInfo = (estado) => {
  const estados = {
    'pendiente': {
      color: 'badge-info',
      icon: ClockIcon,
      titulo: 'Pendiente',
      descripcion: 'En cola para procesamiento'
    },
    'procesando': {
      color: 'badge-warning',
      icon: ArrowPathIcon,
      titulo: 'Procesando',
      descripcion: 'Archivo siendo procesado...'
    },
    'completado': {
      color: 'badge-success',
      icon: CheckCircleIcon,
      titulo: 'Completado',
      descripcion: 'Procesamiento finalizado exitosamente'
    },
    'error': {
      color: 'badge-error',
      icon: XCircleIcon,
      titulo: 'Error',
      descripcion: 'Ocurrió un error durante el procesamiento'
    }
  };
  return estados[estado] || estados['pendiente'];
};

</script>

<template>
  <AppLayout>
    <template #title>
      <div class="flex items-center gap-3">
        <div class="avatar placeholder">
          <div class="bg-primary text-primary-content rounded-lg w-8 h-8">
            <TableCellsIcon class="h-5 w-5" />
          </div>
        </div>
        <div>
          <div class="text-lg font-bold">{{ carga.nombre_archivo }}</div>
          <div class="text-sm text-base-content/70">Carga #{{ carga.id }}</div>
        </div>
      </div>
    </template>

    <div class="space-y-8">
      <!-- Breadcrumb y Acciones -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="breadcrumbs text-sm">
          <ul>
            <li><Link :href="route('excel.index')">Gestión Excel</Link></li>
            <li class="text-base-content/70">Detalles de Carga</li>
          </ul>
        </div>
        <div class="flex gap-2">
          <Link :href="route('excel.index')" class="btn btn-outline gap-2">
            <ArrowLeftIcon class="h-4 w-4" />
            Volver al Historial
          </Link>
        </div>
      </div>

      <!-- Estado y Timeline -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Estado Actual -->
        <div class="lg:col-span-2">
          <div class="card bg-gradient-to-br from-base-100 to-base-200 shadow-2xl border border-base-300">
            <div class="card-body">
              <h2 class="card-title text-xl mb-4">
                <ChartBarSquareIcon class="h-6 w-6 text-primary" />
                Estado del Procesamiento
              </h2>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Info Principal -->
                <div class="space-y-4">
                  <div class="flex items-center gap-3">
                    <component :is="getEstadoInfo(carga.estado).icon" class="h-8 w-8 text-base-content/70" />
                    <div>
                      <div class="text-lg font-semibold">{{ getEstadoInfo(carga.estado).titulo }}</div>
                      <div class="text-sm text-base-content/70">{{ getEstadoInfo(carga.estado).descripcion }}</div>
                    </div>
                  </div>
                  
                  <div class="divider"></div>
                  
                  <div class="space-y-3">
                    <div class="flex items-center gap-2">
                      <UserCircleIcon class="h-5 w-5 text-base-content/70" />
                      <span class="text-sm">{{ carga.user?.name }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                      <CalendarIcon class="h-5 w-5 text-base-content/70" />
                      <span class="text-sm">{{ formatDate(carga.created_at) }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                      <ClockIcon class="h-5 w-5 text-base-content/70" />
                      <span class="text-sm">{{ formatDateRelative(carga.created_at) }}</span>
                    </div>
                  </div>
                </div>

                <!-- Progress Visual -->
                <div class="space-y-4">
                  <div v-if="carga.registros_procesados > 0" class="text-center">
                    <div class="radial-progress text-primary mb-3" 
                         :style="`--value:${porcentajeExito}; --size:6rem; --thickness: 8px;`">
                      <div class="text-lg font-bold">{{ porcentajeExito }}%</div>
                    </div>
                    <div class="text-sm text-base-content/70">Tasa de Éxito</div>
                  </div>
                  
                  <div class="stats stats-vertical shadow">
                    <div class="stat py-2">
                      <div class="stat-title text-xs">Procesados</div>
                      <div class="stat-value text-lg">{{ carga.registros_procesados || 0 }}</div>
                    </div>
                    <div class="stat py-2">
                      <div class="stat-title text-xs">Exitosos</div>
                      <div class="stat-value text-lg text-success">{{ carga.registros_exitosos || 0 }}</div>
                    </div>
                    <div class="stat py-2">
                      <div class="stat-title text-xs">Con Error</div>
                      <div class="stat-value text-lg text-error">{{ carga.registros_con_error || 0 }}</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Timeline del Proceso -->
      <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h3 class="card-title text-lg mb-4">
              <DocumentTextIcon class="h-5 w-5 text-secondary" />
              Timeline
            </h3>
            
            <ul class="timeline timeline-vertical">
              <li>
                <div class="timeline-start timeline-box bg-primary text-primary-content">
                  <CloudArrowUpIcon class="h-4 w-4" />
                </div>
                <div class="timeline-middle">
                  <div class="w-2 h-2 bg-primary rounded-full"></div>
                </div>
                <div class="timeline-end mb-4">
                  <div class="text-sm font-semibold">Archivo Cargado</div>
                  <div class="text-xs text-base-content/70">{{ formatDate(carga.created_at) }}</div>
                </div>
                <hr class="bg-primary" />
              </li>
              
              <li v-if="carga.estado !== 'pendiente'">
                <hr class="bg-warning" />
                <div class="timeline-start timeline-box bg-warning text-warning-content">
                  <PlayIcon class="h-4 w-4" />
                </div>
                <div class="timeline-middle">
                  <div class="w-2 h-2 bg-warning rounded-full"></div>
                </div>
                <div class="timeline-end mb-4">
                  <div class="text-sm font-semibold">Procesamiento Iniciado</div>
                  <div class="text-xs text-base-content/70">{{ formatDate(carga.updated_at) }}</div>
                </div>
                <hr v-if="carga.estado !== 'procesando'" 
                    :class="carga.estado === 'completado' ? 'bg-success' : 'bg-error'" />
              </li>
              
              <li v-if="carga.estado === 'completado'">
                <hr class="bg-success" />
                <div class="timeline-start timeline-box bg-success text-success-content">
                  <CheckCircleIcon class="h-4 w-4" />
                </div>
                <div class="timeline-middle">
                  <div class="w-2 h-2 bg-success rounded-full"></div>
                </div>
                <div class="timeline-end">
                  <div class="text-sm font-semibold">Completado</div>
                  <div class="text-xs text-base-content/70">{{ formatDate(carga.updated_at) }}</div>
                  <div class="text-xs text-success font-medium">Tiempo: {{ tiempoTranscurrido }}</div>
                </div>
              </li>
              
              <li v-else-if="carga.estado === 'error'">
                <hr class="bg-error" />
                <div class="timeline-start timeline-box bg-error text-error-content">
                  <XCircleIcon class="h-4 w-4" />
                    </div>
                <div class="timeline-middle">
                  <div class="w-2 h-2 bg-error rounded-full"></div>
                </div>
                <div class="timeline-end">
                  <div class="text-sm font-semibold">Error</div>
                  <div class="text-xs text-base-content/70">{{ formatDate(carga.updated_at) }}</div>
                  <div class="text-xs text-error font-medium">Requiere revisión</div>
                </div>
              </li>
            </ul>
            </div>
        </div>
      </div>

      <!-- Resumen Detallado -->
      <div class="stats shadow-lg w-full">
        <div class="stat">
          <div class="stat-figure text-primary">
            <DocumentArrowDownIcon class="h-8 w-8" />
          </div>
          <div class="stat-title">Archivo Cargado</div>
          <div class="stat-value text-lg">{{ carga.nombre_archivo }}</div>
          <div class="stat-desc">{{ carga.descripcion || 'Sin descripción' }}</div>
        </div>

        <div class="stat">
          <div class="stat-figure text-info">
            <CalendarIcon class="h-8 w-8" />
          </div>
          <div class="stat-title">Fecha de Carga</div>
          <div class="stat-value text-lg">{{ new Date(carga.created_at).toLocaleDateString('es-ES') }}</div>
          <div class="stat-desc">{{ formatDateRelative(carga.created_at) }}</div>
        </div>

        <div class="stat">
          <div class="stat-figure text-secondary">
            <UserCircleIcon class="h-8 w-8" />
          </div>
          <div class="stat-title">Usuario</div>
          <div class="stat-value text-lg">{{ carga.user?.name }}</div>
          <div class="stat-desc">{{ carga.user?.email }}</div>
        </div>

        <div class="stat">
          <div class="stat-figure" :class="{
            'text-success': carga.estado === 'completado',
            'text-warning': carga.estado === 'procesando',
            'text-info': carga.estado === 'pendiente',
            'text-error': carga.estado === 'error'
          }">
            <component :is="getEstadoInfo(carga.estado).icon" class="h-8 w-8" />
          </div>
          <div class="stat-title">Estado Actual</div>
          <div class="stat-value text-lg">{{ getEstadoInfo(carga.estado).titulo }}</div>
          <div class="stat-desc">{{ getEstadoInfo(carga.estado).descripcion }}</div>
        </div>
      </div>

      <!-- Análisis de Errores -->
      <div v-if="errores.length > 0" class="card bg-base-100 shadow-xl border-l-4 border-error">
        <div class="card-body">
          <h2 class="card-title text-error mb-4">
            <ExclamationTriangleIcon class="h-6 w-6" />
            Errores de Validación Detectados
            <div class="badge badge-error">{{ errores.length }}</div>
          </h2>
          
          <div class="alert alert-error mb-6">
            <ExclamationCircleIcon class="h-6 w-6" />
            <div>
              <h3 class="font-bold">Se encontraron {{ errores.length }} error(es) en el archivo</h3>
              <p class="text-sm">Por favor, corrije los siguientes problemas y vuelve a intentar la carga.</p>
            </div>
          </div>

          <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
              <thead>
                <tr class="bg-base-200">
                  <th class="font-semibold">#</th>
                  <th class="font-semibold">Fila</th>
                  <th class="font-semibold">Campo</th>
                  <th class="font-semibold">Error</th>
                  <th class="font-semibold">Valor Problemático</th>
                  <th class="font-semibold">Sugerencia</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(error, index) in errores" :key="index" class="hover">
                  <td>
                    <div class="badge badge-ghost">{{ index + 1 }}</div>
                  </td>
                  <td>
                    <div class="badge badge-outline badge-error">
                      Fila {{ error.fila }}
                    </div>
                  </td>
                  <td>
                    <div class="font-mono text-sm bg-base-200 px-2 py-1 rounded">
                      {{ error.atributo }}
                    </div>
                  </td>
                  <td>
                    <div class="text-error font-medium">
                      {{ error.errores.join(', ') }}
                    </div>
                  </td>
                  <td>
                    <code class="bg-error/10 text-error px-2 py-1 rounded text-xs">
                      {{ error.valor || 'vacío' }}
                    </code>
                  </td>
                  <td>
                    <div class="text-sm text-base-content/70">
                      <div v-if="error.atributo.includes('email')" class="text-info">
                        💡 Debe ser un email válido
                      </div>
                      <div v-else-if="error.atributo.includes('fecha')" class="text-info">
                        💡 Formato: DD/MM/AAAA
                      </div>
                      <div v-else-if="error.atributo.includes('numero')" class="text-info">
                        💡 Solo números permitidos
                      </div>
                      <div v-else class="text-info">
                        💡 Revisa el formato
                      </div>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <div class="card-actions justify-end mt-6">
            <div class="btn btn-outline btn-info gap-2">
              <DocumentArrowDownIcon class="h-4 w-4" />
              Descargar Reporte de Errores
            </div>
          </div>
        </div>
      </div>

       <!-- Mensaje de Éxito -->
      <div v-if="errores.length === 0 && carga.estado === 'completado'" class="card bg-gradient-to-r from-success/10 to-success/5 border border-success/20 shadow-xl">
        <div class="card-body text-center">
          <div class="flex justify-center mb-4">
            <div class="p-4 bg-success text-success-content rounded-full">
              <CheckCircleIcon class="h-12 w-12" />
            </div>
          </div>
          <h3 class="text-2xl font-bold text-success mb-2">¡Procesamiento Exitoso!</h3>
          <p class="text-base-content/70 mb-4">
            Todos los {{ carga.registros_exitosos }} registros fueron procesados correctamente sin errores.
          </p>
          <div class="flex justify-center gap-4">
            <div class="stat bg-success/10 rounded-lg">
              <div class="stat-title text-success">Tiempo Total</div>
              <div class="stat-value text-success">{{ tiempoTranscurrido }}</div>
            </div>
            <div class="stat bg-success/10 rounded-lg">
              <div class="stat-title text-success">Eficiencia</div>
              <div class="stat-value text-success">{{ porcentajeExito }}%</div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Estado de Procesando -->
      <div v-if="carga.estado === 'procesando'" class="card bg-gradient-to-r from-warning/10 to-warning/5 border border-warning/20 shadow-xl">
        <div class="card-body text-center">
          <div class="flex justify-center mb-4">
            <div class="p-4 bg-warning text-warning-content rounded-full">
              <ArrowPathIcon class="h-12 w-12 animate-spin" />
            </div>
          </div>
          <h3 class="text-2xl font-bold text-warning mb-2">Procesando Archivo...</h3>
          <p class="text-base-content/70 mb-4">
            El archivo está siendo procesado. Este proceso puede tardar varios minutos dependiendo del tamaño del archivo.
          </p>
          <div class="loading loading-dots loading-lg text-warning"></div>
        </div>
      </div>

      <!-- Estado Pendiente -->
      <div v-if="carga.estado === 'pendiente'" class="card bg-gradient-to-r from-info/10 to-info/5 border border-info/20 shadow-xl">
        <div class="card-body text-center">
          <div class="flex justify-center mb-4">
            <div class="p-4 bg-info text-info-content rounded-full">
              <ClockIcon class="h-12 w-12" />
            </div>
          </div>
          <h3 class="text-2xl font-bold text-info mb-2">En Cola de Procesamiento</h3>
          <p class="text-base-content/70 mb-4">
            El archivo está esperando ser procesado. El procesamiento iniciará automáticamente.
          </p>
        </div>
      </div>
    </div>
  </AppLayout>
</template> 