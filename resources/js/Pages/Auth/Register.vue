<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({ roles: Array });
const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  role: '',
});

function submit() {
  form.post(route('register.post'));
}
</script>

<template>
  <AppLayout>
    <Head title="Registro de Usuario" />
    <template #title>Registro</template>

    <div class="card bg-base-100 shadow-xl max-w-md mx-auto">
      <form @submit.prevent="submit" class="card-body space-y-4">
        <h2 class="card-title">Registro de Usuario</h2>

        <div class="form-control">
          <label class="label" for="name"><span class="label-text">Nombre <span class="text-error">*</span></span></label>
          <input id="name" v-model="form.name" type="text" class="input input-bordered" required />
        </div>

        <div class="form-control">
          <label class="label" for="email"><span class="label-text">Email <span class="text-error">*</span></span></label>
          <input id="email" v-model="form.email" type="email" class="input input-bordered" required />
        </div>

        <div class="form-control">
          <label class="label" for="role"><span class="label-text">Rol <span class="text-error">*</span></span></label>
          <select id="role" v-model="form.role" class="select select-bordered" required>
            <option value="" disabled>Seleccionar rol</option>
            <option v-for="r in props.roles" :key="r.value" :value="r.value">{{ r.label }}</option>
          </select>
        </div>

        <div class="form-control">
          <label class="label" for="password"><span class="label-text">Contraseña <span class="text-error">*</span></span></label>
          <input id="password" v-model="form.password" type="password" class="input input-bordered" required />
        </div>

        <div class="form-control">
          <label class="label" for="password_confirmation"><span class="label-text">Confirmar Contraseña <span class="text-error">*</span></span></label>
          <input id="password_confirmation" v-model="form.password_confirmation" type="password" class="input input-bordered" required />
        </div>

        <div class="card-actions justify-end gap-2">
          <Link href="/login" class="btn btn-ghost">Cancelar</Link>
          <button type="submit" class="btn btn-primary" :disabled="form.processing">
            <span v-if="form.processing" class="loading loading-spinner loading-sm"></span>
            Registrar
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template> 