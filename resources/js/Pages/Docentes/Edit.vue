<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
  docente: Object,
});

const form = useForm({
  cod_doc: props.docente.cod_doc,
  nombre_doc: props.docente.nombre_doc,
  genero_doc: props.docente.genero_doc,
  email: props.docente.email,
  telefono: props.docente.telefono,
  estado: props.docente.estado,
});

const submit = () => {
  form.put(route('docentes.update', props.docente.id));
};
</script>

<template>
  <AppLayout>
    <template #title>
      Editar Docente
    </template>

    <div class="card bg-base-100 shadow-xl max-w-4xl mx-auto">
      <form @submit.prevent="submit" class="card-body space-y-6">
        <h2 class="card-title">Editando a: {{ docente.nombre_doc }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <!-- Código Docente -->
          <div class="form-control">
            <label class="label" for="cod_doc">
              <span class="label-text">Código Docente</span>
            </label>
            <input id="cod_doc" type="text" v-model="form.cod_doc" class="input input-bordered w-full" required />
            <div v-if="form.errors.cod_doc" class="text-error text-xs mt-1">{{ form.errors.cod_doc }}</div>
          </div>

          <!-- Nombre -->
          <div class="form-control">
            <label class="label" for="nombre_doc">
              <span class="label-text">Nombre Completo</span>
            </label>
            <input id="nombre_doc" type="text" v-model="form.nombre_doc" class="input input-bordered w-full" required />
            <div v-if="form.errors.nombre_doc" class="text-error text-xs mt-1">{{ form.errors.nombre_doc }}</div>
          </div>

          <!-- Género -->
          <div class="form-control">
            <label class="label" for="genero_doc"><span class="label-text">Género</span></label>
            <select id="genero_doc" v-model="form.genero_doc" class="select select-bordered w-full">
              <option value="M">Masculino</option>
              <option value="F">Femenino</option>
            </select>
          </div>

          <!-- Teléfono -->
          <div class="form-control">
            <label class="label" for="telefono">
              <span class="label-text">Teléfono</span>
            </label>
            <input id="telefono" type="tel" v-model="form.telefono" class="input input-bordered w-full" />
            <div v-if="form.errors.telefono" class="text-error text-xs mt-1">{{ form.errors.telefono }}</div>
          </div>

          <!-- Email -->
          <div class="form-control md:col-span-2">
            <label class="label" for="email">
              <span class="label-text">Correo Electrónico</span>
            </label>
            <input id="email" type="email" v-model="form.email" class="input input-bordered w-full" />
            <div v-if="form.errors.email" class="text-error text-xs mt-1">{{ form.errors.email }}</div>
          </div>

          <!-- Estado -->
          <div class="form-control">
            <label class="label" for="estado"><span class="label-text">Estado</span></label>
            <select id="estado" v-model="form.estado" class="select select-bordered w-full">
              <option value="activo">Activo</option>
              <option value="inactivo">Inactivo</option>
            </select>
          </div>
        </div>

        <div class="card-actions justify-end gap-4 mt-6">
          <Link :href="route('docentes.index')" class="btn btn-ghost">Cancelar</Link>
          <button type="submit" class="btn btn-primary" :disabled="form.processing">
            <span v-if="form.processing" class="loading loading-spinner"></span>
            Actualizar Docente
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template> 