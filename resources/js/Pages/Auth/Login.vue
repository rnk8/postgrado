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
              <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
              </svg>
              Iniciar Sesión
            </button>
          </div>
        </form>

        <!-- Acceso Rápido -->
        <div class="card-body pt-0">
          <div class="divider">Acceso Rápido de Desarrollo</div>
          <div class="grid grid-cols-1 gap-2">
            <button @click="quickLogin('admin@admin.com', 'password')" class="btn btn-outline btn-sm">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.012-3a7.5 7.5 0 010 15m-5.636-13.636a7.5 7.5 0 010 10.636m-2.828-8.464a7.5 7.5 0 010 6.364" />
              </svg>
              Administrador
            </button>
            <button @click="quickLogin('director@director.com', 'password')" class="btn btn-outline btn-sm">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              Director
            </button>
            <button @click="quickLogin('secretario@secretario.com', 'password')" class="btn btn-outline btn-sm">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
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