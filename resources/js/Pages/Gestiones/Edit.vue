<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    gestion: Object,
    estadosDisponibles: Array,
});

const form = useForm({
    _method: 'PUT',
    nombre: props.gestion.nombre,
    descripcion: props.gestion.descripcion,
    fecha_inicio: props.gestion.fecha_inicio,
    fecha_fin: props.gestion.fecha_fin,
    estado: props.gestion.estado,
    es_actual: props.gestion.es_actual,
});

const submit = () => {
    form.post(route('gestiones.update', props.gestion.id), {
        onError: (errors) => {
            console.error('Errores de validación:', errors);
        }
    });
};
</script>

<template>
    <AppLayout>
        <template #title>
            Editar Gestión: {{ form.nombre }}
        </template>

        <form @submit.prevent="submit" class="card bg-base-100 shadow-xl md:card-bordered max-w-2xl mx-auto">
            <div class="card-body space-y-6">
                <!-- Bloque de errores de validación -->
                <div v-if="Object.keys(form.errors).length" class="alert alert-error">
                    <h3 class="font-bold">¡Error de validación!</h3>
                    <ul class="list-disc list-inside text-sm">
                        <li v-for="error in form.errors">{{ error }}</li>
                    </ul>
                </div>

                <!-- Campo Nombre -->
                <label class="form-control w-full">
                    <div class="label"><span class="label-text">Nombre de la Gestión</span></div>
                    <input v-model="form.nombre" type="text" placeholder="Ej: 2024-I" class="input input-bordered w-full" :class="{ 'input-error': form.errors.nombre }" required />
                </label>

                <!-- Campo Descripción -->
                <label class="form-control w-full">
                    <div class="label"><span class="label-text">Descripción</span></div>
                    <textarea v-model="form.descripcion" placeholder="Descripción opcional..." class="textarea textarea-bordered h-24" :class="{ 'textarea-error': form.errors.descripcion }"></textarea>
                </label>

                <div class="divider"></div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Campo Fecha de Inicio -->
                    <label class="form-control w-full">
                        <div class="label"><span class="label-text">Fecha de Inicio</span></div>
                        <input v-model="form.fecha_inicio" type="date" class="input input-bordered w-full" :class="{ 'input-error': form.errors.fecha_inicio }" required />
                    </label>

                    <!-- Campo Fecha de Fin -->
                    <label class="form-control w-full">
                        <div class="label"><span class="label-text">Fecha de Fin</span></div>
                        <input v-model="form.fecha_fin" type="date" class="input input-bordered w-full" :class="{ 'input-error': form.errors.fecha_fin }" required />
                    </label>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                    <!-- Campo Estado -->
                    <label class="form-control w-full">
                        <div class="label"><span class="label-text">Estado</span></div>
                        <select v-model="form.estado" class="select select-bordered w-full" :class="{ 'select-error': form.errors.estado }" required>
                            <option v-for="estado in estadosDisponibles" :key="estado.value" :value="estado.value">
                                {{ estado.label }}
                            </option>
                        </select>
                    </label>

                    <!-- Toggle Es Actual -->
                    <div class="form-control mt-6">
                        <label class="label cursor-pointer justify-start gap-4">
                            <span class="label-text">¿Es la gestión actual?</span>
                            <input v-model="form.es_actual" type="checkbox" class="toggle toggle-primary" :disabled="gestion.es_actual" />
                        </label>
                         <p v-if="gestion.es_actual" class="text-xs text-info mt-1">Esta ya es la gestión actual. Para cambiarla, active otra gestión desde el listado.</p>
                         <p v-else class="text-xs text-gray-500 mt-1">Al activar, esta se convertirá en la gestión actual.</p>
                    </div>
                </div>

                <!-- Botones -->
                <div class="card-actions justify-end mt-8 gap-4">
                    <Link :href="route('gestiones.index')" class="btn btn-ghost">Cancelar</Link>
                    <button type="submit" class="btn btn-primary" :disabled="form.processing">
                        <span v-if="form.processing" class="loading loading-spinner loading-sm"></span>
                        {{ form.processing ? 'Actualizando...' : 'Actualizar Gestión' }}
                    </button>
                </div>
            </div>
        </form>
    </AppLayout>
</template> 