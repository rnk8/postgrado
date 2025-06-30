<template>
  <div class="min-h-screen hero bg-base-200">
    <div class="hero-content flex-col lg:flex-row-reverse">
      <div class="text-center lg:text-left">
        <h1 class="text-5xl font-bold">Sistema de Postgrado</h1>
        <p class="py-6">
          Accede al sistema de gestión académica para administrar programas, docentes, tesis y certificaciones.
        </p>
      </div>
      
      <div class="card shrink-0 w-full max-w-sm shadow-2xl bg-base-100">
        <div class="flex justify-center pt-6">
          <!-- Logo de la aplicación -->
          <img :src="logoUrl" alt="Logo" class="h-32 w-32" />
        </div>
        <form @submit.prevent="submit" class="card-body">
          <div class="form-control">
            <label class="label">
              <span class="label-text">Email</span>
            </label>
            <input
              id="email"
              v-model="form.email"
              type="email"
              placeholder="email@ejemplo.com"
              class="input input-bordered"
              :class="{ 'input-error': form.errors.email }"
              required
            />
            <div v-if="form.errors.email" class="text-error text-sm mt-1">
              {{ form.errors.email }}
            </div>
          </div>
          
          <div class="form-control">
            <label class="label">
              <span class="label-text">Contraseña</span>
            </label>
            <input
              id="password"
              v-model="form.password"
              type="password"
              placeholder="••••••••"
              class="input input-bordered"
              :class="{ 'input-error': form.errors.password }"
              required
            />
            <div v-if="form.errors.password" class="text-error text-sm mt-1">
              {{ form.errors.password }}
            </div>
          </div>
          
          <div class="form-control">
            <label class="label cursor-pointer justify-start gap-2">
              <input
                v-model="form.remember"
                type="checkbox"
                class="checkbox checkbox-primary"
              />
              <span class="label-text">Recordarme</span>
            </label>
          </div>
          
          <div class="form-control mt-6">
            <button type="submit" class="btn btn-primary" :disabled="form.processing">
              <span v-if="form.processing" class="loading loading-spinner loading-sm"></span>
              <HIcon v-else name="ArrowRightOnRectangleIcon" class="h-5 w-5" />
              Iniciar Sesión
            </button>
          </div>
        </form>

        <!-- Acceso Rápido -->
        <div class="card-body pt-0">
          <div class="divider">Acceso Rápido de Desarrollo</div>
          <div class="grid grid-cols-1 gap-2">
            <button @click="quickLogin('admin@postgrado.uagrm.edu.bo', 'admin123')" class="btn btn-outline btn-sm">
              <HIcon name="ShieldCheckIcon" class="h-4 w-4" />
              Administrador
            </button>
            <button @click="quickLogin('director@postgrado.uagrm.edu.bo', 'director123')" class="btn btn-outline btn-sm">
              <HIcon name="AcademicCapIcon" class="h-4 w-4" />
              Director
            </button>
            <button @click="quickLogin('secretario@postgrado.uagrm.edu.bo', 'secretario123')" class="btn btn-outline btn-sm">
              <HIcon name="ClipboardDocumentIcon" class="h-4 w-4" />
              Secretario
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import HIcon from '@/Components/HIcon.vue';
import logoUrl from '../../../images/favicon.svg';

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  });
};

const quickLogin = (email, password) => {
  form.email = email;
  form.password = password;
  form.remember = false;
  submit();
};
</script>

<style scoped>
</style> 