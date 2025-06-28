<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    gestion: Object,
    estadosDisponibles: Array,
});

const form = useForm({
    nombre: props.gestion.nombre,
    descripcion: props.gestion.descripcion,
    fecha_inicio: props.gestion.fecha_inicio,
    fecha_fin: props.gestion.fecha_fin,
    estado: props.gestion.estado,
    es_actual: props.gestion.es_actual,
});

const submit = () => {
    form.put(route('gestiones.update', props.gestion.id));
};
</script>

<template>
    <AppLayout>
        <template #title>
            Editar Gestión: {{ gestion.nombre }}
        </template>

        <div class="card bg-base-100 shadow-xl max-w-2xl mx-auto">
            <form @submit.prevent="submit" class="card-body space-y-6">
                <h2 class="card-title">Editar Gestión Académica</h2>
                
                <!-- Campo Nombre -->
                <div class="form-control">
                    <label for="nombre" class="label">
                        <span class="label-text">Nombre de la Gestión <span class="text-error">*</span></span>
                    </label>
                    <input
                        id="nombre"
                        v-model="form.nombre"
                        type="text"
                        placeholder="Ej: 2024-I"
                        class="input input-bordered"
                        :class="{ 'input-error': form.errors.nombre }"
                        required
                    />
                    <div v-if="form.errors.nombre" class="text-error text-sm mt-1">
                        {{ form.errors.nombre }}
                    </div>
                </div>

                <!-- Campo Descripción -->
                <div class="form-control">
                    <label for="descripcion" class="label">
                        <span class="label-text">Descripción</span>
                    </label>
                    <textarea
                        id="descripcion"
                        v-model="form.descripcion"
                        placeholder="Descripción opcional de la gestión..."
                        class="textarea textarea-bordered h-24"
                        :class="{ 'textarea-error': form.errors.descripcion }"
                    ></textarea>
                    <div v-if="form.errors.descripcion" class="text-error text-sm mt-1">
                        {{ form.errors.descripcion }}
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Campo Fecha de Inicio -->
                    <div class="form-control">
                        <label for="fecha_inicio" class="label">
                            <span class="label-text">Fecha de Inicio <span class="text-error">*</span></span>
                        </label>
                        <input
                            id="fecha_inicio"
                            v-model="form.fecha_inicio"
                            type="date"
                            class="input input-bordered"
                            :class="{ 'input-error': form.errors.fecha_inicio }"
                            required
                        />
                        <div v-if="form.errors.fecha_inicio" class="text-error text-sm mt-1">
                            {{ form.errors.fecha_inicio }}
                        </div>
                    </div>

                    <!-- Campo Fecha de Fin -->
                    <div class="form-control">
                        <label for="fecha_fin" class="label">
                            <span class="label-text">Fecha de Fin <span class="text-error">*</span></span>
                        </label>
                        <input
                            id="fecha_fin"
                            v-model="form.fecha_fin"
                            type="date"
                            class="input input-bordered"
                            :class="{ 'input-error': form.errors.fecha_fin }"
                            required
                        />
                        <div v-if="form.errors.fecha_fin" class="text-error text-sm mt-1">
                            {{ form.errors.fecha_fin }}
                        </div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                    <!-- Campo Estado -->
                    <div class="form-control">
                        <label for="estado" class="label">
                            <span class="label-text">Estado <span class="text-error">*</span></span>
                        </label>
                        <select 
                            id="estado"
                            v-model="form.estado"
                            class="select select-bordered"
                            :class="{ 'select-error': form.errors.estado }"
                            required
                        >
                            <option disabled value="">Seleccione un estado</option>
                            <option v-for="estado in estadosDisponibles" :key="estado.value" :value="estado.value">
                                {{ estado.label }}
                            </option>
                        </select>
                        <div v-if="form.errors.estado" class="text-error text-sm mt-1">
                            {{ form.errors.estado }}
                        </div>
                    </div>

                    <!-- Toggle Es Actual -->
                    <div class="form-control">
                        <label for="es_actual" class="label cursor-pointer justify-start gap-4">
                            <span class="label-text">¿Es la gestión actual?</span>
                            <input
                                id="es_actual"
                                v-model="form.es_actual"
                                type="checkbox"
                                class="toggle toggle-primary"
                                :class="{ 'toggle-error': form.errors.es_actual }"
                            />
                        </label>
                        <div v-if="form.errors.es_actual" class="text-error text-sm mt-1">
                            {{ form.errors.es_actual }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            Si activa esta opción, las demás gestiones se desactivarán automáticamente
                        </div>
                    </div>
                </div>

                <!-- Botones -->
                <div class="card-actions justify-end mt-8 gap-4">
                    <Link :href="route('gestiones.index')" class="btn btn-ghost">Cancelar</Link>
                    <button type="submit" class="btn btn-primary" :disabled="form.processing">
                        <span v-if="form.processing" class="loading loading-spinner loading-sm"></span>
                        Actualizar Gestión
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template> 