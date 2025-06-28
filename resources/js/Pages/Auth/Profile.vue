<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({ user: Object });
const form = useForm({
  name: props.user.name,
  email: props.user.email,
  current_password: '',
  password: '',
  password_confirmation: '',
});

function submit() {
  form.put(route('profile.update'));
}
</script>

<template>
  <AppLayout>
    <Head title="Perfil de Usuario" />
    <template #title>Perfil</template>

    <div class="card bg-base-100 shadow-xl max-w-md mx-auto">
      <form @submit.prevent="submit" class="card-body space-y-4">
        <h2 class="card-title">Editar Perfil</h2>

        <div class="form-control">
          <label class="label" for="name"><span class="label-text">Nombre <span class="text-error">*</span></span></label>
          <input id="name" v-model="form.name" type="text" class="input input-bordered" required />
        </div>

        <div class="form-control">
          <label class="label" for="email"><span class="label-text">Email <span class="text-error">*</span></span></label>
          <input id="email" v-model="form.email" type="email" class="input input-bordered" required />
        </div>

        <div class="form-control">
          <label class="label" for="current_password"><span class="label-text">Contraseña Actual</span></label>
          <input id="current_password" v-model="form.current_password" type="password" class="input input-bordered" />
        </div>

        <div class="form-control">
          <label class="label" for="password"><span class="label-text">Nueva Contraseña</span></label>
          <input id="password" v-model="form.password" type="password" class="input input-bordered" />
        </div>

        <div class="form-control">
          <label class="label" for="password_confirmation"><span class="label-text">Confirmar Nueva Contraseña</span></label>
          <input id="password_confirmation" v-model="form.password_confirmation" type="password" class="input input-bordered" />
        </div>

        <div class="card-actions justify-end gap-2">
          <Link href="/dashboard" class="btn btn-ghost">Cancelar</Link>
          <button type="submit" class="btn btn-primary" :disabled="form.processing">
            <span v-if="form.processing" class="loading loading-spinner loading-sm"></span>
            Guardar Cambios
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template> 