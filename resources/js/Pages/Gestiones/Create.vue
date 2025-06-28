<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
  estadosDisponibles: Array,
  gestionActual: Object,
});

const currentYear = new Date().getFullYear();

// Estados locales para la UI
const anio = ref(currentYear);
const periodo = ref('I');

const form = useForm({
    nombre: `${currentYear}-I`, // se rellena automáticamente
    descripcion: '',
    fecha_inicio: '',
    fecha_fin: '',
    estado: 'activo',
    es_actual: false,
});

// Computar nombre cada vez que anio o periodo cambien
watch(() => [anio.value, periodo.value], ([a, p]) => {
    const periodoRoman = p === '1' ? 'I' : p === '2' ? 'II' : p;
    form.nombre = `${a}-${periodoRoman}`;
});

const submit = () => {
    console.log('Enviando datos:', form.data());
    form.post(route('gestiones.store'), {
        onError: (errors) => {
            console.error('Errores de validación:', errors);
        },
        onSuccess: () => {
            console.log('Gestión creada exitosamente');
        }
    });
};
</script>

<template>
    <AppLayout>
        <template #title>
            Crear Nueva Gestión Académica
        </template>

        <form @submit.prevent="submit" class="card bg-base-100 shadow-xl md:card-bordered max-w-2xl mx-auto">
            <div class="card-body space-y-6">
                <!-- Errores generales -->
                <div v-if="Object.keys(form.errors).length > 0" class="alert alert-error">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <h3 class="font-bold">¡Error en el formulario!</h3>
                        <div class="text-xs">
                            <ul class="list-disc list-inside">
                                <li v-for="(error, field) in form.errors" :key="field">
                                    <strong>{{ field }}:</strong> {{ Array.isArray(error) ? error[0] : error }}
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Bloque Básicos -->
                <h2 class="card-title">Datos básicos</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Año -->
                    <label class="form-control w-full">
                        <div class="label"><span class="label-text">Año</span></div>
                        <input id="anio" v-model="anio" type="number" min="2000" max="2100" class="input input-bordered w-full" required />
                    </label>

                    <!-- Periodo -->
                    <label class="form-control w-full">
                        <div class="label"><span class="label-text">Periodo</span></div>
                        <select id="periodo" v-model="periodo" class="select select-bordered w-full">
                            <option value="I">I</option>
                            <option value="II">II</option>
                        </select>
                    </label>

                    <!-- Nombre generado -->
                    <label class="form-control w-full">
                        <div class="label"><span class="label-text">Nombre</span></div>
                        <input id="nombre" v-model="form.nombre" type="text" class="input input-bordered w-full" readonly />
                        <div v-if="form.errors.nombre" class="label">
                            <span class="label-text-alt text-error">{{ form.errors.nombre }}</span>
                        </div>
                    </label>
                </div>

                <!-- Descripción -->
                <label class="form-control">
                    <div class="label"><span class="label-text">Descripción</span></div>
                    <textarea v-model="form.descripcion" class="textarea textarea-bordered h-24" placeholder="Descripción opcional de la gestión académica..."></textarea>
                    <div v-if="form.errors.descripcion" class="label">
                        <span class="label-text-alt text-error">{{ form.errors.descripcion }}</span>
                    </div>
                </label>

                <div class="divider"></div>

                <!-- Bloque Fechas y Estado -->
                <h2 class="card-title">Fechas y estado</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <label class="form-control w-full">
                        <div class="label"><span class="label-text">Fecha de Inicio</span></div>
                        <input type="date" v-model="form.fecha_inicio" class="input input-bordered w-full" required />
                        <div v-if="form.errors.fecha_inicio" class="label">
                            <span class="label-text-alt text-error">{{ form.errors.fecha_inicio }}</span>
                        </div>
                    </label>

                    <label class="form-control w-full">
                        <div class="label"><span class="label-text">Fecha de Fin</span></div>
                        <input type="date" v-model="form.fecha_fin" class="input input-bordered w-full" required />
                        <div v-if="form.errors.fecha_fin" class="label">
                            <span class="label-text-alt text-error">{{ form.errors.fecha_fin }}</span>
                        </div>
                    </label>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                    <label class="form-control w-full">
                        <div class="label"><span class="label-text">Estado</span></div>
                        <select v-model="form.estado" class="select select-bordered w-full" required>
                            <option disabled value="">Seleccione un estado</option>
                            <option v-for="e in props.estadosDisponibles" :key="e.value" :value="e.value">{{ e.label }}</option>
                        </select>
                        <div v-if="form.errors.estado" class="label">
                            <span class="label-text-alt text-error">{{ form.errors.estado }}</span>
                        </div>
                    </label>

                    <div class="form-control mt-6">
                        <label class="label cursor-pointer gap-4">
                            <span class="label-text">¿Es la gestión actual?</span>
                            <input type="checkbox" v-model="form.es_actual" class="toggle toggle-success" />
                        </label>
                        <div v-if="form.es_actual && props.gestionActual" class="text-xs text-warning mt-2 p-2 bg-warning/10 rounded-md">
                            <p>
                                <strong>Atención:</strong> Al guardar, la gestión actual 
                                <span class="font-bold">"{{ props.gestionActual.nombre }}"</span> 
                                será desactivada automáticamente.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Resumen de datos (para debugging) -->
                <div class="collapse collapse-arrow bg-base-200">
                    <input type="checkbox" />
                    <div class="collapse-title text-sm font-medium">
                        🔍 Ver datos que se enviarán (debugging)
                    </div>
                    <div class="collapse-content">
                        <pre class="text-xs">{{ JSON.stringify(form.data(), null, 2) }}</pre>
                    </div>
                </div>

                <!-- Botones -->
                <div class="card-actions justify-end mt-6 gap-4">
                    <Link :href="route('gestiones.index')" class="btn btn-ghost">Cancelar</Link>
                    <button type="submit" class="btn btn-primary" :disabled="form.processing">
                        <span v-if="form.processing" class="loading loading-spinner loading-sm"></span>
                        {{ form.processing ? 'Guardando...' : 'Guardar Gestión' }}
                    </button>
                </div>
            </div>
        </form>
    </AppLayout>
</template> 