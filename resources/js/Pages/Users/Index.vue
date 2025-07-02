<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import { useForm, Link, router } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import HIcon from '@/Components/HIcon.vue';

const props = defineProps({
    users: Object,
    roles: Array,
    permisos: Object,
});

const editingUser = ref(null);
const showModal = ref(false);
const editModal = ref(null);
const selectedUser = ref(null);
const selectedRoles = ref([]);
const updating = ref(false);
const editedName = ref('');
const editedEmail = ref('');

const form = useForm({
    roles: [],
});

const openEditModal = (user) => {
    selectedUser.value = user;
    selectedRoles.value = user.roles.map(role => role.name);
    editedName.value = user.name;
    editedEmail.value = user.email;
    editModal.value.showModal();
};

const closeModal = () => {
    showModal.value = false;
    editingUser.value = null;
    form.reset();
};

const submit = () => {
    form.put(route('users.update', editingUser.value.id), {
        onSuccess: () => closeModal(),
    });
};

const closeEditModal = () => {
    selectedUser.value = null;
    selectedRoles.value = [];
    editModal.value.close();
};

const updateUserRoles = () => {
    if (!selectedUser.value) return;
    
    updating.value = true;
    
    router.put(route('users.update', selectedUser.value.id), {
        roles: selectedRoles.value,
        name: editedName.value,
        email: editedEmail.value,
    }, {
        onSuccess: () => {
            closeEditModal();
            updating.value = false;
        },
        onError: () => {
            updating.value = false;
        },
    });
};

const eliminarUsuario = (user) => {
    if (confirm(`¿Está seguro de eliminar al usuario "${user.name}"? Esta acción no se puede deshacer.`)) {
        router.delete(route('users.destroy', user.id));
    }
};

const roleColors = {
    administrador: 'badge-primary',
    director: 'badge-secondary',
    secretario: 'badge-accent',
};

function getInitials(name) {
    return name.split(' ').map(word => word[0]).join('').toUpperCase().slice(0, 2);
}

function formatDate(date) {
    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
}
</script>

<template>
    <AppLayout>
        <template #title>
            Gestión de Usuarios
        </template>

        <div class="space-y-6">
            <!-- Tabla de Usuarios -->
            <div class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title">Lista de Usuarios</h2>
                    
                    <div class="flex justify-end mb-4">
                        <Link v-if="permisos.puede_crear" :href="route('register')" class="btn btn-primary">
                            <HIcon name="PlusIcon" class="h-5 w-5" />
                            Nuevo Usuario
                        </Link>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Email</th>
                                    <th>Roles</th>
                                    <th>Verificado</th>
                                    <th>Creado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="user in users.data" :key="user.id" class="hover">
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div class="avatar placeholder">
                                                <div class="bg-neutral text-neutral-content rounded-full w-12">
                                                    <span class="text-xl">{{ getInitials(user.name) }}</span>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="font-bold">{{ user.name }}</div>
                                                <div class="text-sm opacity-50">ID: {{ user.id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-sm">{{ user.email }}</div>
                                    </td>
                                    <td>
                                        <div class="flex flex-wrap gap-1">
                                            <div
                                                v-for="role in user.roles"
                                                :key="role.id"
                                                class="badge badge-outline badge-sm"
                                                :class="{
                                                    'badge-primary': role.name === 'admin',
                                                    'badge-secondary': role.name === 'director',
                                                    'badge-accent': role.name === 'secretario'
                                                }"
                                            >
                                                {{ role.name }}
                                            </div>
                                            <div v-if="user.roles.length === 0" class="text-sm opacity-50">Sin roles</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="badge" :class="{'badge-success': user.email_verified_at, 'badge-warning': !user.email_verified_at}">
                                            {{ user.email_verified_at ? 'Sí' : 'No' }}
                                        </div>
                                    </td>
                                    <td>{{ formatDate(user.created_at) }}</td>
                                    <td>
                                        <div class="flex gap-2">
                                            <button
                                                v-if="permisos.puede_editar_roles || permisos.puede_editar"
                                                @click="openEditModal(user)"
                                                class="btn btn-ghost btn-sm"
                                            >
                                                <HIcon name="PencilSquareIcon" class="h-4 w-4" />
                                                Editar
                                            </button>
                                            <button
                                                v-if="permisos.puede_eliminar"
                                                @click="eliminarUsuario(user)"
                                                class="btn btn-ghost btn-sm text-error"
                                            >
                                                <HIcon name="TrashIcon" class="h-4 w-4" />
                                                Eliminar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <Pagination :links="users.links" />
                </div>
            </div>
        </div>

        <!-- Modal de Edición de Roles -->
        <dialog ref="editModal" class="modal">
            <div class="modal-box">
                <h3 class="font-bold text-lg">Editar Roles de Usuario</h3>
                
                <div v-if="selectedUser" class="py-4">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="avatar placeholder">
                            <div class="bg-neutral text-neutral-content rounded-full w-12">
                                <span class="text-xl">{{ getInitials(selectedUser.name) }}</span>
                            </div>
                        </div>
                        <div>
                            <div class="font-bold">{{ selectedUser.name }}</div>
                            <div class="text-sm opacity-50">{{ selectedUser.email }}</div>
                        </div>
                    </div>

                    <div class="form-control mb-4">
                        <label class="label"><span class="label-text">Nombre</span></label>
                        <input v-model="editedName" type="text" class="input input-bordered w-full" :disabled="!permisos.puede_editar" />
                    </div>
                    <div class="form-control mb-4">
                        <label class="label"><span class="label-text">Email</span></label>
                        <input v-model="editedEmail" type="email" class="input input-bordered w-full" :disabled="!permisos.puede_editar" />
                    </div>

                    <div class="form-control" :class="{ 'opacity-50': !permisos.puede_editar_roles }">
                        <label class="label">
                            <span class="label-text">Roles disponibles</span>
                        </label>
                        <div class="space-y-2">
                            <label
                                v-for="role in roles"
                                :key="role"
                                class="label cursor-pointer justify-start gap-4"
                            >
                                <input
                                    v-model="selectedRoles"
                                    type="checkbox"
                                    :value="role"
                                    class="checkbox checkbox-primary"
                                    :disabled="!permisos.puede_editar_roles"
                                />
                                <span class="label-text capitalize">{{ role }}</span>
                            </label>
                        </div>
                        <div v-if="!permisos.puede_editar_roles" class="text-xs text-base-content/60 mt-2">
                            No tiene permiso para modificar roles.
                        </div>
                    </div>
                </div>

                <div class="modal-action">
                    <button @click="closeEditModal" class="btn btn-ghost">Cancelar</button>
                    <button @click="updateUserRoles" class="btn btn-primary" :disabled="updating">
                        <span v-if="updating" class="loading loading-spinner loading-sm"></span>
                        Actualizar Roles
                    </button>
                </div>
            </div>
            <form method="dialog" class="modal-backdrop">
                <button @click="closeEditModal">close</button>
            </form>
        </dialog>
    </AppLayout>
</template>