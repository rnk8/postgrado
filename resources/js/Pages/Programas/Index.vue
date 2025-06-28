<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import { ref } from 'vue';
import HIcon from '@/Components/HIcon.vue';

const props = defineProps({
    programas: Object,
    filters: Object,
    permisos: Object,
});

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
                    
                    <div class="overflow-x-auto">
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
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <Pagination :links="programas.links" />
                </div>
            </div>
        </div>
    </AppLayout>
</template> 