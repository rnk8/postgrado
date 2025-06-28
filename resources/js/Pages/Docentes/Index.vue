<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import HIcon from '@/Components/HIcon.vue';
import { ref, watch } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import { throttle } from 'lodash';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    docentes: Object,
    filters: Object,
    permisos: Object,
});

const search = ref(props.filters.search || '');

watch(search, throttle((value) => {
    router.get(route('docentes.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 300));

const deleteDocente = (id) => {
    if (confirm('¿Estás seguro de que quieres eliminar este docente?')) {
        router.delete(route('docentes.destroy', id), {
            preserveScroll: true
        });
    }
}

function performSearch() {
    router.get(route('docentes.index'), { search: search.value }, {
        preserveState: true,
        replace: true,
    });
}

function getInitials(name) {
    return name.split(' ').map(word => word[0]).join('').toUpperCase().slice(0, 2);
}

function eliminarDocente(docente) {
    if (confirm(`¿Está seguro de eliminar al docente "${docente.nombre_doc}"? Esta acción no se puede deshacer.`)) {
        router.delete(route('docentes.destroy', docente.id));
    }
}
</script>

<template>
    <AppLayout>
        <template #title>
            Gestión de Docentes
        </template>

        <div class="space-y-6">
            <!-- Filtros y Búsqueda -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <div class="flex flex-col lg:flex-row gap-4 items-end">
                        <!-- Búsqueda -->
                        <div class="form-control flex-1">
                            <label class="label">
                                <span class="label-text">Buscar docente</span>
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
                            :href="route('docentes.create')"
                            class="btn btn-primary"
                        >
                            <HIcon name="PlusIcon" class="h-5 w-5" />
                            Nuevo Docente
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Tabla de Docentes -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title">Lista de Docentes</h2>
                    
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Docente</th>
                                    <th>Contacto</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="docente in docentes.data" :key="docente.id" class="hover">
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div class="avatar placeholder">
                                                <div class="bg-neutral text-neutral-content rounded-full w-12">
                                                    <span class="text-xl">{{ getInitials(docente.nombre_doc) }}</span>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="font-bold">{{ docente.nombre_doc }}</div>
                                                <div class="text-sm opacity-50">{{ docente.cod_doc }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-sm">
                                            <div v-if="docente.email">{{ docente.email }}</div>
                                            <div v-if="docente.telefono" class="opacity-50">{{ docente.telefono }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="badge" :class="{
                                            'badge-success': docente.estado === 'activo',
                                            'badge-warning': docente.estado === 'inactivo'
                                        }">
                                            {{ docente.estado }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="dropdown dropdown-end">
                                            <div tabindex="0" role="button" class="btn btn-ghost btn-sm">
                                                <HIcon name="EllipsisVerticalIcon" class="h-5 w-5" />
                                            </div>
                                            <ul tabindex="0" class="dropdown-content z-[1] menu p-2 shadow bg-base-100 rounded-box w-52">
                                                <li>
                                                    <Link :href="route('docentes.show', docente.id)">
                                                        <HIcon name="EyeIcon" class="h-4 w-4" />
                                                        Ver Detalles
                                                    </Link>
                                                </li>
                                                <li v-if="permisos.puede_editar">
                                                    <Link :href="route('docentes.edit', docente.id)">
                                                        <HIcon name="PencilSquareIcon" class="h-4 w-4" />
                                                        Editar
                                                    </Link>
                                                </li>
                                                <li v-if="permisos.puede_eliminar">
                                                    <button @click="eliminarDocente(docente)" class="text-error">
                                                        <HIcon name="TrashIcon" class="h-4 w-4" />
                                                        Eliminar
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <Pagination :links="docentes.links" />
                </div>
            </div>
        </div>
    </AppLayout>
</template> 