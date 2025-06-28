<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import { UserGroupIcon, AcademicCapIcon, DocumentTextIcon } from '@heroicons/vue/24/outline';

const props = defineProps({
  query: String,
  results: Object,
});

const resultTypes = [
    { key: 'docentes', title: 'Docentes', icon: UserGroupIcon, routeName: 'docentes.show', titleKey: 'nombre_doc' },
    { key: 'programas', title: 'Programas', icon: AcademicCapIcon, routeName: 'programas.show', titleKey: 'nombre_carrera' },
    { key: 'certificaciones', title: 'Certificaciones', icon: DocumentTextIcon, routeName: 'certificaciones.show', titleKey: 'numero' },
    { key: 'tesis', title: 'Tesis', icon: DocumentTextIcon, routeName: 'tesis.show', titleKey: 'titulo' },
    { key: 'usuarios', title: 'Usuarios', icon: UserGroupIcon, routeName: 'users.index', titleKey: 'name' },
];

</script>

<template>
  <AppLayout>
    <template #title>
      Resultados de la Búsqueda para: <span class="text-primary">"{{ query }}"</span>
    </template>

    <div v-if="Object.values(results).every(res => res.length === 0)" class="flex justify-center py-10">
        <div class="card bg-base-100 shadow-xl max-w-md">
          <div class="card-body items-center text-center">
            <h2 class="card-title">Sin resultados</h2>
            <p class="text-base-content/70">Intenta con otros términos de búsqueda.</p>
          </div>
        </div>
    </div>

    <div class="space-y-8">
        <div v-for="type in resultTypes" :key="type.key">
            <div v-if="results[type.key] && results[type.key].length > 0" class="card bg-base-100 shadow-xl">
                <div class="card-body">
                    <h2 class="card-title">
                        <component :is="type.icon" class="h-6 w-6"/>
                        {{ type.title }}
                        <div class="badge badge-secondary">{{ results[type.key].length }}</div>
                    </h2>
                    <div class="overflow-x-auto">
                        <table class="table">
                            <tbody>
                                <tr v-for="item in results[type.key]" :key="item.id" class="hover">
                                    <td>
                                        <Link :href="route(type.routeName, item.id)">
                                            {{ item[type.titleKey] }}
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </AppLayout>
</template> 