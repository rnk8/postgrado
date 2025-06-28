<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import debounce from 'lodash/debounce';
import HIcon from '@/Components/HIcon.vue';
import Pagination from '@/Components/Pagination.vue';
import {
  DocumentArrowUpIcon,
  ClockIcon,
  CheckCircleIcon,
  XCircleIcon,
  ExclamationTriangleIcon,
  CloudArrowUpIcon,
  TableCellsIcon,
  UserGroupIcon,
  ChartBarIcon,
  EyeIcon,
  PlayIcon,
  TrashIcon,
  ArrowDownTrayIcon
} from '@heroicons/vue/24/outline';

const props = defineProps({
  cargas: Object,
  estadisticas: Object,
  filters: Object,
  permisos: Object,
  estadosDisponibles: Object,
  errors: Object
});

// Filtros
const search = ref(props.filters.search);
const estadoFilter = ref(props.filters.estado || '');

const searchFilters = debounce(() => {
    router.get(route('excel.index'), {
        search: search.value,
        estado: estadoFilter.value,
    }, { preserveState: true, replace: true });
}, 300);

watch([search, estadoFilter], searchFilters);

// Formulario de subida
const form = useForm({
  archivo: null,
  descripcion: '',
});

const dragActive = ref(false);
const fileInput = ref(null);

// Computed properties para estadísticas
const tasaExito = computed(() => {
  const total = props.estadisticas.total_cargas;
  const exitosas = props.estadisticas.cargas_exitosas;
  return total > 0 ? Math.round((exitosas / total) * 100) : 0;
});

const promedioRegistros = computed(() => {
  const total = props.estadisticas.total_cargas;
  const registros = props.estadisticas.registros_procesados;
  return total > 0 ? Math.round(registros / total) : 0;
});

const submit = () => {
    form.post(route('excel.upload'), {
        forceFormData: true,
        onSuccess: () => {
            form.reset();
            const fileInput = document.querySelector('input[type="file"]');
            if (fileInput) fileInput.value = '';
        },
        onError: () => {
            // Mantener archivo seleccionado para que usuario vea el error
        },
    });
};

const handleFileChange = (event) => {
    form.archivo = event.target.files[0];
};

// Drag and Drop handlers
const handleDragEnter = (e) => {
    e.preventDefault();
    dragActive.value = true;
};

const handleDragLeave = (e) => {
    e.preventDefault();
    dragActive.value = false;
};

const handleDragOver = (e) => {
    e.preventDefault();
};

const handleDrop = (e) => {
    e.preventDefault();
    dragActive.value = false;
    
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        const file = files[0];
        if (file.type.includes('sheet') || file.name.endsWith('.xlsx') || file.name.endsWith('.xls')) {
            form.archivo = file;
        }
    }
};

// Acciones
const procesarCarga = (id) => {
    if (confirm('¿Iniciar el procesamiento de este archivo? Esta acción puede tardar varios minutos.')) {
        router.post(route('excel.procesar', id), {}, { preserveScroll: true });
    }
}

const deleteCarga = (id) => {
    if (confirm('¿Eliminar esta carga y todos sus datos importados? Esta acción no se puede deshacer.')) {
        router.delete(route('excel.destroy', id), { preserveScroll: true });
    }
}

// Helper para formato de fecha
const formatDate = (dateString) => {
    const options = { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric', 
        hour: '2-digit', 
        minute: '2-digit',
        hour12: false 
    };
    return new Date(dateString).toLocaleDateString('es-ES', options);
}

// Helper para tamaño de archivo
const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Descargar plantilla
const descargarPlantilla = () => {
    window.location.href = route('excel.plantilla');
}
</script>

<template>
  <AppLayout>
    <template #title>
      <div class="flex items-center gap-2">
        <TableCellsIcon class="h-6 w-6 text-primary" />
        Gestión de Carga Excel
      </div>
    </template>

    <div class="space-y-8">
      <!-- Estadísticas Generales -->
      <div class="stats shadow-lg w-full">
  <!-- Total Cargas -->
  <div class="stat">
    <div class="stat-figure text-primary">
      <CloudArrowUpIcon class="h-8 w-8 opacity-80" />
    </div>
    <div class="stat-title">Total Cargas</div>
    <div class="stat-value text-primary">{{ estadisticas.total_cargas }}</div>
    <div class="stat-desc text-base-content/60">Archivos procesados</div>
  </div>

  <!-- Exitosas -->
  <div class="stat">
    <div class="stat-figure text-success">
      <CheckCircleIcon class="h-8 w-8 opacity-80" />
    </div>
    <div class="stat-title">Exitosas</div>
    <div class="stat-value text-success">{{ estadisticas.cargas_exitosas }}</div>
    <div class="stat-desc text-base-content/60">{{ tasaExito }}% de éxito</div>
  </div>

  <!-- Registros -->
  <div class="stat">
    <div class="stat-figure text-warning">
      <UserGroupIcon class="h-8 w-8 opacity-80" />
    </div>
    <div class="stat-title">Registros</div>
    <div class="stat-value text-warning text-2xl">{{ estadisticas.registros_procesados.toLocaleString() }}</div>
    <div class="stat-desc text-base-content/60">{{ promedioRegistros }} promedio/carga</div>
  </div>

  <!-- Con Errores -->
  <div class="stat">
    <div class="stat-figure text-error">
      <XCircleIcon class="h-8 w-8 opacity-80" />
    </div>
    <div class="stat-title">Con Errores</div>
    <div class="stat-value text-error">{{ estadisticas.cargas_error }}</div>
    <div class="stat-desc text-base-content/60">Requieren revisión</div>
  </div>
</div>

      <!-- Card de Carga de Archivo -->
      <div class="card bg-gradient-to-br from-base-100 to-base-200 shadow-2xl border border-base-300">
        <div class="card-body">
          <div class="flex items-center justify-between mb-6">
            <h2 class="card-title text-2xl">
              <DocumentArrowUpIcon class="h-8 w-8 text-primary" />
              Cargar Nuevo Archivo Excel
            </h2>
            <button @click="descargarPlantilla" class="btn btn-outline btn-primary btn-sm gap-2">
              <ArrowDownTrayIcon class="h-4 w-4" />
              Descargar Plantilla
            </button>
          </div>
          
          <form @submit.prevent="submit" class="space-y-6">
            <!-- Drag and Drop Zone -->
            <div class="form-control">
              <label class="label">
                <span class="label-text font-semibold">Seleccionar archivo Excel</span>
                <span class="label-text-alt text-error">* Requerido</span>
              </label>
              
              <div 
                @dragenter="handleDragEnter"
                @dragleave="handleDragLeave"
                @dragover="handleDragOver"
                @drop="handleDrop"
                class="border-2 border-dashed rounded-xl p-8 text-center transition-all duration-300"
                :class="{
                  'border-primary bg-primary/5': dragActive,
                  'border-base-300 hover:border-primary/50': !dragActive,
                  'border-error bg-error/5': form.errors.archivo
                }"
              >
                <div class="flex flex-col items-center gap-4">
                  <div class="p-4 rounded-full bg-primary/10">
                    <CloudArrowUpIcon class="h-12 w-12 text-primary" />
                  </div>
                  
                  <div v-if="form.archivo" class="text-center">
                    <div class="font-semibold text-success">{{ form.archivo.name }}</div>
                    <div class="text-sm text-base-content/70">
                      {{ formatFileSize(form.archivo.size) }}
                    </div>
                  </div>
                  
                  <div v-else class="text-center">
                    <p class="text-lg font-medium mb-2">
                      Suelta tu archivo aquí o 
                      <span class="text-primary cursor-pointer hover:underline" @click="$refs.fileInput.click()">
                        haz clic para seleccionar
                      </span>
                    </p>
                    <p class="text-sm text-base-content/70">
                      Formatos soportados: .xlsx, .xls • Máximo 10MB
                    </p>
                  </div>
                </div>
                
                <input
                  ref="fileInput"
                  type="file"
                  @change="handleFileChange"
                  accept=".xlsx,.xls"
                  class="hidden"
                  required
                />
              </div>
              
              <div v-if="form.errors.archivo" class="label">
                <span class="label-text-alt text-error flex items-center gap-1">
                  <ExclamationTriangleIcon class="h-4 w-4" />
                  {{ form.errors.archivo }}
                </span>
              </div>
            </div>

            <!-- Descripción -->
            <div class="form-control">
              <label class="label">
                <span class="label-text font-semibold">Descripción</span>
                <span class="label-text-alt">Opcional</span>
              </label>
              <textarea
                v-model="form.descripcion"
                placeholder="Ej: Datos académicos periodo 2024-1, Importación de certificaciones, etc."
                class="textarea textarea-bordered h-24 resize-none"
                :class="{ 'textarea-error': form.errors.descripcion }"
              ></textarea>
              <div v-if="form.errors.descripcion" class="label">
                <span class="label-text-alt text-error">{{ form.errors.descripcion }}</span>
              </div>
            </div>

            <!-- Botón de Envío -->
            <div class="card-actions justify-end pt-4">
              <button 
                type="submit" 
                class="btn btn-primary btn-lg gap-2 px-8" 
                :disabled="form.processing || !form.archivo"
              >
                <span v-if="form.processing" class="loading loading-spinner loading-sm"></span>
                <DocumentArrowUpIcon v-else class="h-5 w-5" />
                {{ form.processing ? 'Cargando...' : 'Cargar Archivo' }}
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Filtros y Búsqueda -->
      <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
          <div class="flex flex-col lg:flex-row gap-4">
            <div class="form-control flex-1">
              <input
                v-model="search"
                type="text"
                placeholder="Buscar por nombre de archivo o descripción..."
                class="input input-bordered w-full"
              />
            </div>
            <div class="form-control lg:w-48">
              <select v-model="estadoFilter" class="select select-bordered">
                <option value="">Todos los estados</option>
                <option v-for="(label, value) in estadosDisponibles" :key="value" :value="value">
                  {{ label }}
                </option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- Historial de Cargas -->
      <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
          <div class="flex items-center justify-between mb-6">
            <h2 class="card-title text-xl">
              <ChartBarIcon class="h-6 w-6 text-secondary" />
              Historial de Cargas
            </h2>
            <div class="badge badge-neutral">
              {{ cargas.total }} cargas totales
            </div>
          </div>
          
          <div class="overflow-x-auto">
            <table class="table table-zebra w-full">
              <thead>
                <tr class="text-base-content/80">
                  <th class="font-semibold">Archivo</th>
                  <th class="font-semibold">Usuario</th>
                  <th class="font-semibold">Fecha</th>
                  <th class="font-semibold">Estado</th>
                  <th class="font-semibold">Progreso</th>
                  <th class="font-semibold">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="carga in cargas.data" :key="carga.id" class="hover">
                  <td>
                    <div class="flex items-center gap-3">
                      <div class="avatar placeholder">
                        <div class="bg-primary text-primary-content rounded-lg w-12 h-12">
                          <TableCellsIcon class="h-6 w-6" />
                        </div>
                      </div>
                      <div>
                        <div class="font-bold">{{ carga.nombre_archivo }}</div>
                        <div class="text-sm opacity-70">
                          {{ carga.descripcion || 'Sin descripción' }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="flex items-center gap-2">
                      <div class="avatar placeholder">
                        <div class="bg-neutral text-neutral-content rounded-full w-8 h-8 text-xs">
                          {{ carga.user?.name.charAt(0) }}
                        </div>
                      </div>
                      <span class="text-sm">{{ carga.user?.name }}</span>
                    </div>
                  </td>
                  <td>
                    <div class="text-sm">
                      <div>{{ formatDate(carga.created_at) }}</div>
                      <div class="text-xs opacity-60">
                        {{ new Date(carga.created_at).toLocaleDateString('es-ES', { weekday: 'long' }) }}
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="flex items-center gap-2">
                      <div class="badge badge-lg font-medium" :class="{
                        'badge-success': carga.estado === 'completado',
                        'badge-warning': carga.estado === 'procesando',
                        'badge-info': carga.estado === 'pendiente',
                        'badge-error': carga.estado === 'error'
                      }">
                        <CheckCircleIcon v-if="carga.estado === 'completado'" class="h-4 w-4 mr-1" />
                        <ClockIcon v-else-if="carga.estado === 'procesando'" class="h-4 w-4 mr-1" />
                        <ExclamationTriangleIcon v-else-if="carga.estado === 'pendiente'" class="h-4 w-4 mr-1" />
                        <XCircleIcon v-else class="h-4 w-4 mr-1" />
                        {{ carga.estado }}
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="space-y-2">
                      <div class="flex gap-2">
                        <div class="badge badge-success badge-sm">
                          {{ carga.registros_exitosos || 0 }} ✓
                        </div>
                        <div v-if="carga.registros_con_error > 0" class="badge badge-error badge-sm">
                          {{ carga.registros_con_error || 0 }} ✗
                        </div>
                      </div>
                      <div v-if="carga.registros_procesados > 0" class="w-24">
                        <div class="text-xs mb-1">
                          {{ carga.registros_exitosos }}/{{ carga.registros_procesados }}
                        </div>
                        <progress 
                          class="progress progress-success h-2" 
                          :value="carga.registros_exitosos" 
                          :max="carga.registros_procesados"
                        ></progress>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="dropdown dropdown-end">
                      <div tabindex="0" role="button" class="btn btn-ghost btn-sm">
                        <div class="w-1 h-1 bg-current rounded-full"></div>
                        <div class="w-1 h-1 bg-current rounded-full"></div>
                        <div class="w-1 h-1 bg-current rounded-full"></div>
                      </div>
                      <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow-lg bg-base-100 rounded-box w-52 border border-base-300">
                        <li>
                          <Link :href="route('excel.show', carga.id)" class="gap-2">
                            <EyeIcon class="h-4 w-4" />
                            Ver Detalles
                          </Link>
                        </li>
                        <li v-if="carga.estado === 'pendiente' && permisos.puede_cargar">
                          <button @click="procesarCarga(carga.id)" class="gap-2 text-info">
                            <PlayIcon class="h-4 w-4" />
                            Procesar Ahora
                          </button>
                        </li>
                        <li v-if="permisos.puede_eliminar">
                          <button @click="deleteCarga(carga.id)" class="gap-2 text-error">
                            <TrashIcon class="h-4 w-4" />
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
          <div v-if="cargas.last_page > 1" class="flex justify-center mt-6">
            <Pagination :links="cargas.links" />
          </div>

          <!-- Estado vacío -->
          <div v-if="cargas.data.length === 0" class="text-center py-12">
            <div class="flex flex-col items-center gap-4">
              <div class="p-4 rounded-full bg-base-200">
                <TableCellsIcon class="h-12 w-12 text-base-content/50" />
              </div>
              <div>
                <h3 class="text-lg font-semibold mb-2">No hay cargas registradas</h3>
                <p class="text-base-content/70">Comienza cargando tu primer archivo Excel</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template> 