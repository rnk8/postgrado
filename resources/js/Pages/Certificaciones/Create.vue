<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
  programas: Array,
  estadosDisponibles: Object,
});

const form = useForm({
  programa_id: null,
  nombre_est: '',
  nro_registro_est: '',
  genero_est: 'M',
  nota: null,
  nota_defensa_tfg: null,
  fecha_defensa_tfg: null,
  fecha_emision: null,
  estado: 'pendiente'
});

const submit = () => {
  form.post(route('certificaciones.store'));
};
</script>

<template>
  <AppLayout>
    <template #title>
      Crear Nueva Certificación
    </template>

    <div class="card bg-base-100 shadow-xl max-w-4xl mx-auto">
      <form @submit.prevent="submit" class="card-body space-y-6">

        <!-- Datos del Estudiante -->
        <div class="card bg-base-200 p-4 space-y-4">
          <h3 class="text-lg font-semibold">Datos del Estudiante</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-control">
              <label class="label" for="nombre_est"><span class="label-text">Nombre Completo</span></label>
              <input id="nombre_est" type="text" v-model="form.nombre_est" class="input input-bordered w-full" required />
              <div v-if="form.errors.nombre_est" class="text-error text-xs mt-1">{{ form.errors.nombre_est }}</div>
            </div>
            <div class="form-control">
              <label class="label" for="nro_registro_est"><span class="label-text">Número de Registro</span></label>
              <input id="nro_registro_est" type="text" v-model="form.nro_registro_est" class="input input-bordered w-full" required />
              <div v-if="form.errors.nro_registro_est" class="text-error text-xs mt-1">{{ form.errors.nro_registro_est }}</div>
            </div>
            <div class="form-control">
              <label class="label" for="genero_est"><span class="label-text">Género</span></label>
              <select id="genero_est" v-model="form.genero_est" class="select select-bordered w-full">
                <option value="M">Masculino</option>
                <option value="F">Femenino</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Datos Académicos -->
        <div class="card bg-base-200 p-4 space-y-4">
          <h3 class="text-lg font-semibold">Datos Académicos</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-control">
              <label class="label" for="programa_id"><span class="label-text">Programa Académico</span></label>
              <select id="programa_id" v-model="form.programa_id" class="select select-bordered w-full" required>
                <option :value="null" disabled>Seleccione un programa</option>
                <option v-for="programa in programas" :key="programa.id" :value="programa.id">{{ programa.nombre_carrera }}</option>
              </select>
              <div v-if="form.errors.programa_id" class="text-error text-xs mt-1">{{ form.errors.programa_id }}</div>
            </div>
            <div class="form-control">
              <label class="label" for="nota"><span class="label-text">Nota Trabajo Final</span></label>
              <input id="nota" type="number" step="0.01" min="0" max="100" v-model="form.nota" class="input input-bordered w-full" />
              <div v-if="form.errors.nota" class="text-error text-xs mt-1">{{ form.errors.nota }}</div>
            </div>
            <div class="form-control">
              <label class="label" for="nota_defensa_tfg"><span class="label-text">Nota Defensa</span></label>
              <input id="nota_defensa_tfg" type="number" step="0.01" min="0" max="100" v-model="form.nota_defensa_tfg" class="input input-bordered w-full" />
              <div v-if="form.errors.nota_defensa_tfg" class="text-error text-xs mt-1">{{ form.errors.nota_defensa_tfg }}</div>
            </div>
            <div class="form-control">
              <label class="label" for="fecha_defensa_tfg"><span class="label-text">Fecha Defensa</span></label>
              <input id="fecha_defensa_tfg" type="date" v-model="form.fecha_defensa_tfg" class="input input-bordered w-full" />
              <div v-if="form.errors.fecha_defensa_tfg" class="text-error text-xs mt-1">{{ form.errors.fecha_defensa_tfg }}</div>
            </div>
          </div>
        </div>

        <!-- Datos de Registro -->
        <div class="card bg-base-200 p-4 space-y-4">
          <h3 class="text-lg font-semibold">Datos de Registro</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-control">
              <label class="label" for="fecha_emision"><span class="label-text">Fecha Emisión</span></label>
              <input id="fecha_emision" type="date" v-model="form.fecha_emision" class="input input-bordered w-full" />
              <div v-if="form.errors.fecha_emision" class="text-error text-xs mt-1">{{ form.errors.fecha_emision }}</div>
            </div>
            <div class="form-control">
              <label class="label" for="estado"><span class="label-text">Estado</span></label>
              <select id="estado" v-model="form.estado" class="select select-bordered w-full" disabled>
                <option v-for="(label, value) in estadosDisponibles" :key="value" :value="value">{{ label }}</option>
              </select>
            </div>
          </div>
        </div>

        <div class="card-actions justify-end gap-4 mt-6">
          <Link :href="route('certificaciones.index')" class="btn btn-ghost">Cancelar</Link>
          <button type="submit" class="btn btn-primary" :disabled="form.processing">
            <span v-if="form.processing" class="loading loading-spinner"></span>
            Guardar Certificación
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template> 