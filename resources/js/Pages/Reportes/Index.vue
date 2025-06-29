<script setup>
import { ref, watch, onMounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
    gestiones: Array,
    facultades: Array
});

// Formularios para cada reporte
const formInformeAnual = useForm({ gestion_id: null, programa_id: null, format: 'pdf' });
const formResumenProgramas = useForm({ gestion_id: null, cod_facultad: null, format: 'pdf' });
const formEstadoAlumnos = useForm({ gestion_id: null, programa_id: null, format: 'pdf' });
const formReporteDocentes = useForm({ gestion_id: null, docente_id: null, format: 'pdf' });
const formResumenDefensas = useForm({ gestion_id: null, cod_facultad: null, format: 'pdf' });

// Datos dinámicos para los select
const programas = ref([]);
const docentes = ref([]);
const facultadesPorGestion = ref([]);

const activeGestion = ref(null);

onMounted(() => {
    const gestionActual = props.gestiones.find(g => g.es_actual);
    if (gestionActual) {
        activeGestion.value = gestionActual.id;
        // Cargar datos iniciales basados en la gestión actual
        formInformeAnual.gestion_id = gestionActual.id;
        formResumenProgramas.gestion_id = gestionActual.id;
        formEstadoAlumnos.gestion_id = gestionActual.id;
        formReporteDocentes.gestion_id = gestionActual.id;
        formResumenDefensas.gestion_id = gestionActual.id;
        fetchDataForGestion(gestionActual.id);
    }
});


const fetchDataForGestion = async (gestionId) => {
    if (!gestionId) {
        programas.value = [];
        docentes.value = [];
        facultadesPorGestion.value = [];
        return;
    }
    
    try {
        const [programasRes, docentesRes, facultadesRes] = await Promise.all([
            axios.get(route('reportes.data', { tipo: 'programas', gestion_id: gestionId })),
            axios.get(route('reportes.data', { tipo: 'docentes', gestion_id: gestionId })),
            axios.get(route('reportes.data', { tipo: 'facultades', gestion_id: gestionId })),
        ]);
        programas.value = programasRes.data;
        docentes.value = docentesRes.data;
        facultadesPorGestion.value = facultadesRes.data;
    } catch (error) {
        console.error("Error fetching data:", error);
    }
};

watch(() => formInformeAnual.gestion_id, (newVal) => fetchDataForGestion(newVal));
watch(() => formResumenProgramas.gestion_id, (newVal) => fetchDataForGestion(newVal));
watch(() => formEstadoAlumnos.gestion_id, (newVal) => fetchDataForGestion(newVal));
watch(() => formReporteDocentes.gestion_id, (newVal) => fetchDataForGestion(newVal));
watch(() => formResumenDefensas.gestion_id, (newVal) => fetchDataForGestion(newVal));

const submit = (form, routeName, requiredFields = []) => {
    // Validar campos requeridos
    for (const field of requiredFields) {
        if (!form[field]) {
            alert(`El campo '${field.replace('_id', '').replace('cod_', '')}' es obligatorio para este reporte.`);
            return;
        }
    }

    // Construir la URL con los parámetros del formulario
    const params = Object.fromEntries(
        Object.entries(form.data()).filter(([, v]) => v != null)
    );
    
    const url = route(routeName, params);
    
    // Abrir la URL en una nueva pestaña para que el navegador maneje la descarga del PDF
    window.open(url, '_blank');
};

</script>

<template>
    <AppLayout>
        <template #title>
            Módulo de Reportes
        </template>

        <div class="space-y-8">
            <!-- Reporte 1: Informe Anual -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title">1. Formulario de Informe Anual</h2>
                    <p>Genera el informe anual de permanencia de estudiantes para un programa específico.</p>
                    <form @submit.prevent="submit(formInformeAnual, 'reportes.informeAnual', ['gestion_id', 'programa_id'])" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        <div class="form-control">
                            <label class="label"><span class="label-text">Gestión Académica</span></label>
                            <select v-model="formInformeAnual.gestion_id" class="select select-bordered" required>
                                <option v-for="gestion in gestiones" :key="gestion.id" :value="gestion.id">{{ gestion.nombre }}</option>
                            </select>
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text">Programa</span></label>
                            <select v-model="formInformeAnual.programa_id" class="select select-bordered" required :disabled="!formInformeAnual.gestion_id">
                                <option :value="null" disabled>Seleccione un programa</option>
                                <option v-for="programa in programas" :key="programa.id" :value="programa.id">{{ programa.nombre_carrera }}</option>
                            </select>
                        </div>
                        <div class="card-actions">
                            <button type="submit" class="btn btn-primary">Generar PDF</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Reporte 2: Resumen de Programas -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title">2. Resumen de Programas por UPG</h2>
                    <p>Muestra un resumen de los programas y el número de inscritos por facultad.</p>
                    <form @submit.prevent="submit(formResumenProgramas, 'reportes.resumenProgramas', ['gestion_id'])" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        <div class="form-control">
                            <label class="label"><span class="label-text">Gestión Académica</span></label>
                            <select v-model="formResumenProgramas.gestion_id" class="select select-bordered" required>
                                <option v-for="gestion in gestiones" :key="gestion.id" :value="gestion.id">{{ gestion.nombre }}</option>
                            </select>
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text">Facultad (UPG)</span></label>
                            <select v-model="formResumenProgramas.cod_facultad" class="select select-bordered" :disabled="!formResumenProgramas.gestion_id">
                                <option :value="null">Todas las facultades</option>
                                <option v-for="facultad in facultadesPorGestion" :key="facultad.cod_facultad" :value="facultad.cod_facultad">{{ facultad.nombre_facultad }}</option>
                            </select>
                        </div>
                        <div class="card-actions">
                            <button type="submit" class="btn btn-primary">Generar PDF</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Reporte 3: Estado de Alumnos -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title">3. Estado de Alumnos</h2>
                    <p>Detalla el estado académico de los alumnos, incluyendo materias cursadas y pendientes.</p>
                     <form @submit.prevent="submit(formEstadoAlumnos, 'reportes.estadoAlumnos', ['gestion_id'])" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        <div class="form-control">
                            <label class="label"><span class="label-text">Gestión Académica</span></label>
                            <select v-model="formEstadoAlumnos.gestion_id" class="select select-bordered" required>
                                <option v-for="gestion in gestiones" :key="gestion.id" :value="gestion.id">{{ gestion.nombre }}</option>
                            </select>
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text">Programa (Opcional)</span></label>
                            <select v-model="formEstadoAlumnos.programa_id" class="select select-bordered" :disabled="!formEstadoAlumnos.gestion_id">
                                <option :value="null">Todos los programas</option>
                                <option v-for="programa in programas" :key="programa.id" :value="programa.id">{{ programa.nombre_carrera }}</option>
                            </select>
                        </div>
                        <div class="card-actions">
                            <button type="submit" class="btn btn-primary">Generar PDF</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Reporte 4: Reporte de Docentes -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title">4. Reporte de Docentes</h2>
                    <p>Muestra las materias y programas asignados a los docentes.</p>
                    <form @submit.prevent="submit(formReporteDocentes, 'reportes.reporteDocentes', ['gestion_id'])" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        <div class="form-control">
                            <label class="label"><span class="label-text">Gestión Académica</span></label>
                            <select v-model="formReporteDocentes.gestion_id" class="select select-bordered" required>
                                <option v-for="gestion in gestiones" :key="gestion.id" :value="gestion.id">{{ gestion.nombre }}</option>
                            </select>
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text">Docente (Opcional)</span></label>
                            <select v-model="formReporteDocentes.docente_id" class="select select-bordered" :disabled="!formReporteDocentes.gestion_id">
                                 <option :value="null">Todos los docentes</option>
                                <option v-for="docente in docentes" :key="docente.id" :value="docente.id">{{ docente.nombre_doc }}</option>
                            </select>
                        </div>
                        <div class="card-actions">
                            <button type="submit" class="btn btn-primary">Generar PDF</button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Reporte 5: Resumen de Defensas -->
             <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title">5. Resumen de Defensas de TFG/Tesis</h2>
                    <p>Presenta un resumen de las defensas de tesis agrupadas por programa y facultad.</p>
                    <form @submit.prevent="submit(formResumenDefensas, 'reportes.resumenDefensas', ['gestion_id'])" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        <div class="form-control">
                            <label class="label"><span class="label-text">Gestión Académica</span></label>
                            <select v-model="formResumenDefensas.gestion_id" class="select select-bordered" required>
                                <option v-for="gestion in gestiones" :key="gestion.id" :value="gestion.id">{{ gestion.nombre }}</option>
                            </select>
                        </div>
                        <div class="form-control">
                            <label class="label"><span class="label-text">Facultad (UPG)</span></label>
                            <select v-model="formResumenDefensas.cod_facultad" class="select select-bordered" :disabled="!formResumenDefensas.gestion_id">
                                <option :value="null">Todas las facultades</option>
                                <option v-for="facultad in facultadesPorGestion" :key="facultad.cod_facultad" :value="facultad.cod_facultad">{{ facultad.nombre_facultad }}</option>
                            </select>
                        </div>
                        <div class="card-actions">
                            <button type="submit" class="btn btn-primary">Generar PDF</button>
                        </div>
                    </form>
                </div>
            </div>
            
        </div>
    </AppLayout>
</template> 