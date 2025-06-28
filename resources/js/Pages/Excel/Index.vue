<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';
import { ArrowUpOnSquareIcon, DocumentMagnifyingGlassIcon, EyeIcon } from '@heroicons/vue/24/outline';
import Pagination from '@/Components/Pagination.vue';

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

const fileInput = ref(null);

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

// Acciones
const procesarCarga = (id) => {
    if (confirm('¿Iniciar el procesamiento de este archivo? Esta acción puede tardar.')) {
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
    const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    return new Date(dateString).toLocaleDateString('es-ES', options);
}
</script>

<template>
  <AppLayout>
    <template #title>
      Gestión de Carga Excel
    </template>

    <div class="space-y-6">
      <!-- Card de Carga -->
      <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
          <h2 class="card-title">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
            </svg>
            Cargar Archivo Excel
          </h2>
          
          <form @submit.prevent="submit" class="space-y-4">
            <div class="form-control">
              <label class="label">
                <span class="label-text">Seleccionar archivo Excel <span class="text-error">*</span></span>
              </label>
              <input
                type="file"
                @change="handleFileChange"
                accept=".xlsx,.xls"
                class="file-input file-input-bordered file-input-primary w-full"
                :class="{ 'file-input-error': form.errors.archivo }"
                required
              />
              <div v-if="form.errors.archivo" class="text-error text-sm mt-1">
                {{ form.errors.archivo }}
              </div>
              <div class="label">
                <span class="label-text-alt">Formatos soportados: .xlsx, .xls</span>
              </div>
            </div>

            <div class="form-control">
              <label class="label">
                <span class="label-text">Descripción (opcional)</span>
              </label>
              <textarea
                v-model="form.descripcion"
                placeholder="Descripción de la carga..."
                class="textarea textarea-bordered"
                :class="{ 'textarea-error': form.errors.descripcion }"
              ></textarea>
              <div v-if="form.errors.descripcion" class="text-error text-sm mt-1">
                {{ form.errors.descripcion }}
              </div>
            </div>

            <div class="card-actions justify-end">
              <button type="submit" class="btn btn-primary" :disabled="form.processing || !form.archivo">
                <span v-if="form.processing" class="loading loading-spinner loading-sm"></span>
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Cargar Archivo
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Historial de Cargas -->
      <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
          <h2 class="card-title">Historial de Cargas</h2>
          
          <div class="overflow-x-auto">
            <table class="table">
              <thead>
                <tr>
                  <th>Archivo</th>
                  <th>Usuario</th>
                  <th>Fecha</th>
                  <th>Estado</th>
                  <th>Registros</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="carga in cargas.data" :key="carga.id" class="hover">
                  <td>
                    <div>
                      <div class="font-bold">{{ carga.nombre_archivo }}</div>
                      <div class="text-sm opacity-50">{{ carga.descripcion || 'Sin descripción' }}</div>
                    </div>
                  </td>
                  <td>
                    <div class="text-sm">{{ carga.user?.name }}</div>
                  </td>
                  <td>
                    <div class="text-sm">{{ formatDate(carga.created_at) }}</div>
                  </td>
                  <td>
                    <div class="badge" :class="{
                      'badge-success': carga.estado === 'completado',
                      'badge-warning': carga.estado === 'procesando',
                      'badge-info': carga.estado === 'pendiente',
                      'badge-error': carga.estado === 'error'
                    }">
                      {{ carga.estado }}
                    </div>
                  </td>
                  <td>
                    <div class="stats stats-horizontal">
                      <div class="stat p-2">
                        <div class="stat-value text-sm text-success">{{ carga.registros_exitosos || 0 }}</div>
                        <div class="stat-desc text-xs">Exitosos</div>
                      </div>
                      <div class="stat p-2" v-if="carga.registros_con_error > 0">
                        <div class="stat-value text-sm text-error">{{ carga.registros_con_error || 0 }}</div>
                        <div class="stat-desc text-xs">Fallidos</div>
                      </div>
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
                          <Link :href="route('excel.show', carga.id)">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            Ver Detalles
                          </Link>
                        </li>
                        <li v-if="carga.estado === 'pendiente' && permisos.puede_cargar">
                          <button @click="procesarCarga(carga.id)" class="text-info">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v6h6M20 20v-6h-6M5 19l5-5M19 5l-5 5" />
                            </svg>
                            Procesar
                          </button>
                        </li>
                        <li v-if="permisos.puede_eliminar">
                          <button @click="deleteCarga(carga.id)" class="text-error">
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
          <Pagination :links="cargas.links" />
        </div>
      </div>
    </div>
  </AppLayout>
</template> 