<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { InformationCircleIcon, ExclamationTriangleIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  carga: Object,
});

const resumen = props.carga.resumen_procesamiento || {};
const errores = resumen.errores || [];

</script>

<template>
  <AppLayout>
    <template #title>
      Detalles de la Carga #{{ carga.id }}
    </template>

    <div class="space-y-6">
      <!-- Resumen de la Carga -->
      <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title">Resumen del Proceso</h2>
            <div class="stats stats-vertical lg:stats-horizontal shadow">
                <div class="stat">
                    <div class="stat-title">Nombre del Archivo</div>
                    <div class="stat-value text-lg">{{ carga.nombre_archivo }}</div>
                </div>
                <div class="stat">
                    <div class="stat-title">Fecha de Carga</div>
                    <div class="stat-value text-lg">{{ new Date(carga.created_at).toLocaleString() }}</div>
                </div>
                <div class="stat">
                    <div class="stat-title">Estado</div>
                    <div class="stat-value">
                         <span class="badge text-lg" :class="{
                            'badge-success': carga.estado === 'completado',
                            'badge-error': carga.estado === 'error',
                            'badge-warning': carga.estado === 'procesando',
                        }">{{ carga.estado }}</span>
                    </div>
                </div>
                 <div class="stat">
                    <div class="stat-title">Registros Procesados</div>
                    <div class="stat-value">{{ carga.registros_procesados }}</div>
                    <div class="stat-desc text-success">{{ carga.registros_exitosos }} exitosos | {{ carga.registros_con_error }} con error</div>
                </div>
            </div>
        </div>
      </div>

      <!-- Errores de Validación -->
      <div v-if="errores.length > 0" class="card bg-base-100 shadow-xl">
        <div class="card-body">
          <h2 class="card-title text-error">
              <ExclamationTriangleIcon class="h-6 w-6"/>
              Errores de Validación Encontrados
          </h2>
          <p>Se encontraron los siguientes problemas en el archivo. Por favor, corrígelos y vuelve a intentarlo.</p>
          <div class="overflow-x-auto mt-4">
            <table class="table table-zebra w-full">
              <thead>
                <tr>
                  <th>Fila</th>
                  <th>Atributo</th>
                  <th>Error</th>
                  <th>Valor Problemático</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(error, index) in errores" :key="index">
                  <td><span class="badge badge-ghost">{{ error.fila }}</span></td>
                  <td><span class="font-mono">{{ error.atributo }}</span></td>
                  <td>{{ error.errores.join(', ') }}</td>
                  <td><code>{{ error.valor }}</code></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

       <!-- Mensaje de Éxito -->
      <div v-if="errores.length === 0 && carga.estado === 'completado'">
        <div role="alert" class="alert alert-success">
            <InformationCircleIcon class="h-6 w-6"/>
            <span>Todos los registros fueron procesados exitosamente y sin errores.</span>
        </div>
      </div>
      
      <div class="text-center mt-6">
        <Link :href="route('excel.index')" class="btn btn-primary">
            Volver al Historial de Cargas
        </Link>
      </div>

    </div>
  </AppLayout>
</template> 