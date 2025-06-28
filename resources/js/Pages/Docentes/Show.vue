<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
  docente: Object,
  permisos: Object,
});
</script>

<template>
  <AppLayout>
    <Head title="Detalles de Docente" />
    <template #title>Docente: {{ docente.nombre_doc }}</template>

    <div class="card bg-base-100 shadow-xl max-w-lg">
      <div class="card-body space-y-2">
        <p><strong>Código:</strong> {{ docente.cod_doc }}</p>
        <p><strong>Nombre:</strong> {{ docente.nombre_doc }}</p>
        <p><strong>Género:</strong> {{ docente.genero_doc }}</p>
        <p><strong>Email:</strong> {{ docente.email || '—' }}</p>
        <p><strong>Teléfono:</strong> {{ docente.telefono || '—' }}</p>
        <p><strong>Estado:</strong> {{ docente.estado }}</p>
        <p><strong>Gestión:</strong> {{ docente.gestion?.nombre || '—' }}</p>

        <div class="card-actions justify-end mt-4">
          <Link href="/docentes" class="btn btn-ghost">Volver</Link>
          <Link v-if="permisos.puede_eliminar" :href="`/docentes/${docente.id}`" method="delete" as="button" class="btn btn-error">Eliminar</Link>
          <Link v-if="permisos.puede_editar" :href="`/docentes/${docente.id}/edit`" class="btn btn-primary">Editar</Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template> 