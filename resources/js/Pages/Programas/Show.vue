<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
  programa: Object,
  permisos: Object,
});
</script>

<template>
  <AppLayout>
    <Head title="Detalles de Programa" />
    <template #title>Programa: {{ programa.nombre_carrera }}</template>

    <div class="card bg-base-100 shadow-xl max-w-lg">
      <div class="card-body space-y-2">
        <p><strong>Código:</strong> {{ programa.cod_carrera }}</p>
        <p><strong>Nombre:</strong> {{ programa.nombre_carrera }}</p>
        <p><strong>Tipo:</strong> {{ programa.tipo }}</p>
        <p><strong>Modalidad:</strong> {{ programa.modalidad }}</p>
        <p><strong>Estado:</strong> {{ programa.estado }}</p>
        <p><strong>Coordinador:</strong> {{ programa.coordinador?.nombre_doc || '—' }}</p>
        <p><strong>Gestión:</strong> {{ programa.gestion?.nombre || '—' }}</p>

        <div class="card-actions justify-end mt-4">
          <Link href="/programas" class="btn btn-ghost">Volver</Link>
          <Link v-if="permisos.puede_eliminar" :href="`/programas/${programa.id}`" method="delete" as="button" class="btn btn-error">Eliminar</Link>
          <Link v-if="permisos.puede_editar" :href="`/programas/${programa.id}/edit`" class="btn btn-primary">Editar</Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template> 