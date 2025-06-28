<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import { usePage, Link } from '@inertiajs/vue3';

const page = usePage();
const gestion = page.props.gestion;
const estadisticas = page.props.estadisticas;
const permisos = page.props.permisos;
</script>

<template>
  <AppLayout>
    <template #title>Gestión {{ gestion.nombre }}</template>

    <div class="card bg-base-100 shadow-xl mb-4">
      <div class="card-body">
        <h2 class="card-title">Datos Básicos</h2>
        <p><strong>Descripción:</strong> {{ gestion.descripcion || '—' }}</p>
        <p><strong>Fecha Inicio:</strong> {{ gestion.fecha_inicio }}</p>
        <p><strong>Fecha Fin:</strong> {{ gestion.fecha_fin }}</p>
        <p><strong>Estado:</strong> {{ gestion.estado }}</p>
        <p><strong>Actual:</strong> {{ gestion.es_actual ? 'Sí' : 'No' }}</p>
      </div>
    </div>

    <div class="card bg-base-100 shadow-xl">
      <div class="card-body">
        <h2 class="card-title">Resumen</h2>
        <ul class="list-disc ml-6 text-sm">
          <li>Docentes: {{ estadisticas.docentes.total }}</li>
          <li>Programas: {{ estadisticas.programas.total }}</li>
          <li>Certificaciones: {{ estadisticas.certificaciones.total }}</li>
          <li>Tesis: {{ estadisticas.tesis.total }}</li>
        </ul>
      </div>
    </div>

    <div class="card-actions justify-end mt-4">
      <Link v-if="permisos.puede_activar && !gestion.es_actual" :href="`/gestiones/${gestion.id}/activar`" method="put" as="button" class="btn btn-success">Activar</Link>
      <Link v-if="permisos.puede_editar" :href="`/gestiones/${gestion.id}/edit`" class="btn btn-primary">Editar</Link>
    </div>
  </AppLayout>
</template> 