<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    programa: Object,
    docentes: Array,
});

const form = useForm({
    cod_carrera: props.programa.cod_carrera,
    nombre_carrera: props.programa.nombre_carrera,
    tipo: props.programa.tipo,
    modalidad: props.programa.modalidad,
    estado: props.programa.estado,
    coordinador_id: props.programa.coordinador_id,
});

const submit = () => {
    form.put(route('programas.update', props.programa.id));
};
</script>

<template>
    <AppLayout>
        <template #title>
            Editar Programa Académico
        </template>

        <div class="card bg-base-100 shadow-xl max-w-4xl mx-auto">
            <form @submit.prevent="submit" class="card-body space-y-6">
                <h2 class="card-title">Editando: {{ programa.nombre_carrera }}</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    
                    <div class="form-control">
                        <label class="label" for="nombre_carrera"><span class="label-text">Nombre del Programa</span></label>
                        <input id="nombre_carrera" type="text" v-model="form.nombre_carrera" class="input input-bordered w-full" required />
                        <div v-if="form.errors.nombre_carrera" class="text-error text-xs mt-1">{{ form.errors.nombre_carrera }}</div>
                    </div>

                    <div class="form-control">
                        <label class="label" for="cod_carrera"><span class="label-text">Código</span></label>
                        <input id="cod_carrera" type="text" v-model="form.cod_carrera" class="input input-bordered w-full" required />
                        <div v-if="form.errors.cod_carrera" class="text-error text-xs mt-1">{{ form.errors.cod_carrera }}</div>
                    </div>

                    <div class="form-control">
                        <label class="label" for="tipo"><span class="label-text">Tipo</span></label>
                        <select id="tipo" v-model="form.tipo" class="select select-bordered w-full">
                            <option value="maestria">Maestría</option>
                            <option value="doctorado">Doctorado</option>
                            <option value="especialidad">Especialidad</option>
                        </select>
                    </div>
                    
                    <div class="form-control">
                        <label class="label" for="modalidad"><span class="label-text">Modalidad</span></label>
                        <select id="modalidad" v-model="form.modalidad" class="select select-bordered w-full">
                            <option value="presencial">Presencial</option>
                            <option value="virtual">Virtual</option>
                            <option value="semipresencial">Semi-presencial</option>
                        </select>
                    </div>

                    <div class="form-control md:col-span-2">
                        <label class="label" for="coordinador_id"><span class="label-text">Coordinador Académico</span></label>
                        <select id="coordinador_id" v-model="form.coordinador_id" class="select select-bordered w-full">
                            <option :value="null">Seleccione un coordinador</option>
                            <option v-for="docente in docentes" :key="docente.id" :value="docente.id">{{ docente.nombre_doc }}</option>
                        </select>
                        <div v-if="form.errors.coordinador_id" class="text-error text-xs mt-1">{{ form.errors.coordinador_id }}</div>
                    </div>
                     <div class="form-control">
                        <label class="label" for="estado"><span class="label-text">Estado</span></label>
                        <select id="estado" v-model="form.estado" class="select select-bordered w-full">
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                    </div>
                </div>

                <div class="card-actions justify-end gap-4 mt-6">
                    <Link :href="route('programas.index')" class="btn btn-ghost">Cancelar</Link>
                    <button type="submit" class="btn btn-primary" :disabled="form.processing">
                        <span v-if="form.processing" class="loading loading-spinner"></span>
                        Actualizar Programa
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template> 