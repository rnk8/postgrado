<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import { ref, computed } from 'vue';
import HIcon from '@/Components/HIcon.vue';

const props = defineProps({
    programas: Object,
    filters: Object,
    permisos: Object,
});

const page = usePage();
const gestionActiva = computed(() => page.props.system.gestion_actual);

const search = ref(props.filters?.search || '');

function performSearch() {
    router.get(route('programas.index'), { search: search.value }, {
        preserveState: true,
        replace: true,
    });
}

function eliminarPrograma(programa) {
    if (confirm(`¿Está seguro de eliminar el programa "${programa.nombre_carrera}"? Esta acción no se puede deshacer.`)) {
        router.delete(route('programas.destroy', programa.id));
    }
}
</script>

<template>
    <AppLayout>
        <template #title>
            Gestión de Programas Académicos
        </template>

        <div class="space-y-6">
            <!-- Filtros y Búsqueda -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <div class="flex flex-col lg:flex-row gap-4 items-end">
                        <!-- Búsqueda -->
                        <div class="form-control flex-1">
                            <label class="label">
                                <span class="label-text">Buscar programa</span>
                            </label>
                            <div class="join">
                                <input
                                    v-model="search"
                                    type="text"
                                    placeholder="Nombre o código..."
                                    class="input input-bordered join-item flex-1"
                                    @keyup.enter="performSearch"
                                />
                                <button @click="performSearch" class="btn btn-primary join-item">
                                    <HIcon name="MagnifyingGlassIcon" class="h-5 w-5" />
                                </button>
                            </div>
                        </div>

                        <!-- Botón Crear -->
                        <Link
                            v-if="permisos.puede_crear"
                            :href="route('programas.create')"
                            class="btn btn-primary"
                        >
                            <HIcon name="PlusIcon" class="h-5 w-5" />
                            Nuevo Programa
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Tabla de Programas -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title">Lista de Programas</h2>
                    
                    <!-- Alerta de Gestión No Activa -->
                    <div v-if="!gestionActiva" role="alert" class="alert alert-warning">
                        <HIcon name="ExclamationTriangleIcon" class="h-6 w-6" />
                        <div>
                            <h3 class="font-bold">No hay una gestión académica activa.</h3>
                            <div class="text-xs">Para ver, crear o administrar programas, primero debe
                                <Link :href="route('gestiones.index')" class="link link-primary">activar una gestión</Link>.
                            </div>
                        </div>
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Programa</th>
                                    <th>Tipo</th>
                                    <th>Modalidad</th>
                                    <th>Coordinador</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="programa in programas.data" :key="programa.id" class="hover">
                                    <td>
                                        <div>
                                            <div class="font-bold">{{ programa.nombre_carrera }}</div>
                                            <div class="text-sm opacity-50">{{ programa.cod_carrera }}</div>
                                            <div v-if="programa.version" class="text-xs opacity-50">Versión: {{ programa.version }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="badge" :class="{
                                            'badge-primary': programa.tipo === 'maestria',
                                            'badge-secondary': programa.tipo === 'doctorado',
                                            'badge-accent': programa.tipo === 'diplomado',
                                            'badge-info': programa.tipo === 'especialidad'
                                        }">
                                            {{ programa.tipo }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="badge badge-outline" :class="{
                                            'badge-success': programa.modalidad === 'presencial',
                                            'badge-warning': programa.modalidad === 'virtual',
                                            'badge-info': programa.modalidad === 'semipresencial'
                                        }">
                                            {{ programa.modalidad }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-sm">
                                            {{ programa.coordinador?.nombre_doc || 'Sin asignar' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="badge" :class="{
                                            'badge-success': programa.estado === 'activo',
                                            'badge-warning': programa.estado === 'inactivo',
                                            'badge-info': programa.estado === 'futuro',
                                            'badge-error': programa.estado === 'cerrado'
                                        }">
                                            {{ programa.estado }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="dropdown dropdown-end">
                                            <div tabindex="0" role="button" class="btn btn-ghost btn-sm">
                                                <HIcon name="EllipsisVerticalIcon" class="h-5 w-5" />
                                            </div>
                                            <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                                                <li>
                                                    <Link :href="route('programas.show', programa.id)">
                                                        <HIcon name="EyeIcon" class="h-4 w-4" />
                                                        Ver Detalles
                                                    </Link>
                                                </li>
                                                <li v-if="permisos.puede_editar">
                                                    <Link :href="route('programas.edit', programa.id)">
                                                        <HIcon name="PencilSquareIcon" class="h-4 w-4" />
                                                        Editar
                                                    </Link>
                                                </li>
                                                <li v-if="permisos.puede_eliminar">
                                                    <button @click="eliminarPrograma(programa)" class="text-error">
                                                        <HIcon name="TrashIcon" class="h-4 w-4" />
                                                        Eliminar
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="programas.data.length === 0">
                                    <td colspan="6" class="text-center py-8">
                                        <div class="text-lg">No se encontraron programas.</div>
                                        <div class="text-base-content/60">
                                            No hay programas registrados para la gestión actual ({{ gestionActiva.nombre }}) o que coincidan con su búsqueda.
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <Pagination v-if="gestionActiva && programas.data.length > 0" :links="programas.links" />
                </div>
            </div>
        </div>
    </AppLayout>
</template> 