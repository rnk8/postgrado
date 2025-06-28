<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
  estadosDisponibles: Array,
});

const currentYear = new Date().getFullYear();

const form = useForm({
    anio: currentYear,
    periodo: 'I', // I o II
    descripcion: '',
    fecha_inicio: '',
    fecha_fin: '',
    estado: 'activo',
    es_actual: false,
    nombre: '', // se rellena automáticamente
});

// Computar nombre cada vez que anio o periodo cambien
watch(() => [form.anio, form.periodo], ([a, p]) => {
    const periodoRoman = p === '1' ? 'I' : p === '2' ? 'II' : p;
    form.nombre = `${a}-${periodoRoman}`;
});

const submit = () => {
    form.post(route('gestiones.store'));
};
</script>

<template>
    <AppLayout>
        <template #title>
            Crear Nueva Gestión Académica
        </template>

        <form @submit.prevent="submit" class="card bg-base-100 shadow-xl md:card-bordered max-w-2xl mx-auto">
            <div class="card-body space-y-6">
                <!-- Bloque Básicos -->
                <h2 class="card-title">Datos básicos</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Año -->
                    <label class="form-control w-full">
                        <div class="label"><span class="label-text">Año</span></div>
                        <input id="anio" v-model="form.anio" type="number" min="2000" max="2100" class="input validator w-full" required />
                        <p v-if="form.errors.anio" class="validator-hint text-error">{{ form.errors.anio }}</p>
                    </label>

                    <!-- Periodo -->
                    <label class="form-control w-full">
                        <div class="label"><span class="label-text">Periodo</span></div>
                        <select id="periodo" v-model="form.periodo" class="select validator w-full">
                            <option value="I">I</option>
                            <option value="II">II</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                        </select>
                    </label>

                    <!-- Nombre generado -->
                    <label class="form-control w-full">
                        <div class="label"><span class="label-text">Nombre</span></div>
                        <input id="nombre" v-model="form.nombre" type="text" class="input input-bordered w-full" readonly />
                    </label>
                </div>

                <!-- Descripción -->
                <label class="form-control">
                    <div class="label"><span class="label-text">Descripción</span></div>
                    <textarea v-model="form.descripcion" class="textarea textarea-bordered h-24"></textarea>
                </label>

                <div class="divider"></div>

                <!-- Bloque Fechas y Estado -->
                <h2 class="card-title">Fechas y estado</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <label class="form-control w-full">
                        <div class="label"><span class="label-text">Fecha de Inicio</span></div>
                        <input type="date" v-model="form.fecha_inicio" class="input validator w-full" required />
                        <p v-if="form.errors.fecha_inicio" class="validator-hint text-error">{{ form.errors.fecha_inicio }}</p>
                    </label>

                    <label class="form-control w-full">
                        <div class="label"><span class="label-text">Fecha de Fin</span></div>
                        <input type="date" v-model="form.fecha_fin" class="input validator w-full" required />
                        <p v-if="form.errors.fecha_fin" class="validator-hint text-error">{{ form.errors.fecha_fin }}</p>
                    </label>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                    <label class="form-control w-full">
                        <div class="label"><span class="label-text">Estado</span></div>
                        <select v-model="form.estado" class="select validator w-full" required>
                            <option disabled value="">Seleccione un estado</option>
                            <option v-for="e in props.estadosDisponibles" :key="e.value" :value="e.value">{{ e.label }}</option>
                        </select>
                        <p v-if="form.errors.estado" class="validator-hint text-error">{{ form.errors.estado }}</p>
                    </label>

                    <div class="form-control mt-6">
                        <label class="label cursor-pointer gap-4">
                            <span class="label-text">¿Es la gestión actual?</span>
                            <input type="checkbox" v-model="form.es_actual" class="toggle toggle-success" />
                        </label>
                    </div>
                </div>

                <!-- Botones -->
                <div class="card-actions justify-end mt-6 gap-4">
                    <Link :href="route('gestiones.index')" class="btn btn-ghost">Cancelar</Link>
                    <button type="submit" class="btn btn-primary" :disabled="form.processing">
                        <span v-if="form.processing" class="loading loading-spinner"></span>
                        Guardar Gestión
                    </button>
                </div>
            </div>
        </form>
    </AppLayout>
</template> 