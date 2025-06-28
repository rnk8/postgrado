<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';
import HIcon from '@/Components/HIcon.vue';
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
            <HIcon name="ArrowUpOnSquareIcon" class="h-6 w-6" />
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
                <HIcon v-else name="ArrowUpOnSquareIcon" class="h-5 w-5" />
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
                        <HIcon name="EllipsisVerticalIcon" class="h-5 w-5" />
                      </div>
                      <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                        <li>
                          <Link :href="route('excel.show', carga.id)">
                            <HIcon name="EyeIcon" class="h-4 w-4" />
                            Ver Detalles
                          </Link>
                        </li>
                        <li v-if="carga.estado === 'pendiente' && permisos.puede_cargar">
                          <button @click="procesarCarga(carga.id)" class="text-info">
                            <HIcon name="ArrowPathIcon" class="h-4 w-4" />
                            Procesar
                          </button>
                        </li>
                        <li v-if="permisos.puede_eliminar">
                          <button @click="deleteCarga(carga.id)" class="text-error">
                            <HIcon name="TrashIcon" class="h-4 w-4" />
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