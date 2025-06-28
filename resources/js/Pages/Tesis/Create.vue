<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    programas: Array,
    tutores: Array,
    estadosDisponibles: Object,
    errors: Object
});

const form = useForm({
    titulo: '',
    nombre_est: '',
    nro_registro_est: '',
    fecha_defensa_tfg: '',
    nota_defensa_tfg: null,
    programa_id: null,
    tutor_id: null,
    estado: 'en_desarrollo'
});

const submit = () => {
    form.post(route('tesis.store'));
};
</script>

<template>
    <AppLayout>
        <template #title>
            Registrar Nueva Tesis
        </template>

        <div class="card bg-base-100 shadow-xl max-w-4xl mx-auto">
            <form @submit.prevent="submit" class="card-body space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Columna Izquierda -->
                    <div class="space-y-4">
                        <div class="form-control">
                            <label for="titulo" class="label">Título de la Tesis</label>
                            <input id="titulo" v-model="form.titulo" type="text" class="input input-bordered" required>
                            <span v-if="errors.titulo" class="text-error text-xs mt-1">{{ errors.titulo }}</span>
                        </div>
                        
                        <div class="form-control">
                            <label for="nombre_est" class="label">Nombre del Estudiante</label>
                            <input id="nombre_est" v-model="form.nombre_est" type="text" class="input input-bordered" required>
                            <span v-if="errors.nombre_est" class="text-error text-xs mt-1">{{ errors.nombre_est }}</span>
                        </div>

                        <div class="form-control">
                            <label for="nro_registro_est" class="label">Nro. de Registro del Estudiante</label>
                            <input id="nro_registro_est" v-model="form.nro_registro_est" type="text" class="input input-bordered">
                            <span v-if="errors.nro_registro_est" class="text-error text-xs mt-1">{{ errors.nro_registro_est }}</span>
                        </div>
                    </div>

                    <!-- Columna Derecha -->
                    <div class="space-y-4">
                        <div class="form-control">
                            <label for="fecha_defensa_tfg" class="label">Fecha Defensa</label>
                            <input id="fecha_defensa_tfg" v-model="form.fecha_defensa_tfg" type="date" class="input input-bordered">
                            <span v-if="errors.fecha_defensa_tfg" class="text-error text-xs mt-1">{{ errors.fecha_defensa_tfg }}</span>
                        </div>

                        <div class="form-control">
                            <label for="nota_defensa_tfg" class="label">Nota Defensa</label>
                            <input id="nota_defensa_tfg" v-model="form.nota_defensa_tfg" type="number" step="0.01" min="0" max="100" class="input input-bordered">
                            <span v-if="errors.nota_defensa_tfg" class="text-error text-xs mt-1">{{ errors.nota_defensa_tfg }}</span>
                        </div>

                        <div class="form-control">
                            <label for="programa_id" class="label">Programa Académico</label>
                            <select id="programa_id" v-model="form.programa_id" class="select select-bordered" required>
                                <option :value="null" disabled>Seleccione un programa</option>
                                <option v-for="programa in programas" :key="programa.id" :value="programa.id">{{ programa.nombre_carrera }}</option>
                            </select>
                            <span v-if="errors.programa_id" class="text-error text-xs mt-1">{{ errors.programa_id }}</span>
                        </div>

                        <div class="form-control">
                            <label for="tutor_id" class="label">Tutor Asignado</label>
                            <select id="tutor_id" v-model="form.tutor_id" class="select select-bordered">
                                 <option :value="null" disabled>Seleccione un tutor</option>
                                <option v-for="tutor in tutores" :key="tutor.id" :value="tutor.id">{{ tutor.nombre_doc }}</option>
                            </select>
                            <span v-if="errors.tutor_id" class="text-error text-xs mt-1">{{ errors.tutor_id }}</span>
                        </div>

                        <div class="form-control">
                            <label for="estado" class="label">Estado Inicial</label>
                             <select id="estado" v-model="form.estado" class="select select-bordered">
                                <option v-for="(label, value) in estadosDisponibles" :key="value" :value="value">{{ label }}</option>
                            </select>
                            <span v-if="errors.estado" class="text-error text-xs mt-1">{{ errors.estado }}</span>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="card-actions justify-end gap-4 mt-6">
                    <Link :href="route('tesis.index')" class="btn btn-ghost">Cancelar</Link>
                    <button type="submit" class="btn btn-primary" :disabled="form.processing">
                        Guardar Tesis
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template> 