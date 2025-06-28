<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
  certificacion: Object,
});
</script>

<template>
  <AppLayout>
    <Head title="Detalles de Certificación" />

    <template #title>
      Certificación: {{ certificacion.numero }}
    </template>

    <div class="card bg-base-100 shadow-xl">
      <div class="card-body space-y-2">
        <p><strong>Número:</strong> {{ certificacion.numero }}</p>
        <p><strong>Registro Estudiante:</strong> {{ certificacion.nro_registro_est }}</p>
        <p><strong>Nombre Estudiante:</strong> {{ certificacion.nombre_est }}</p>
        <p><strong>Programa:</strong> {{ certificacion.programa?.nombre_carrera || '—' }}</p>
        <p><strong>Nota:</strong> {{ certificacion.nota || '—' }}</p>
        <p><strong>Nota Defensa:</strong> {{ certificacion.nota_defensa_tfg || '—' }}</p>
        <p><strong>Promedio:</strong> {{ certificacion.promedio || '—' }}</p>
        <p><strong>Estado:</strong> {{ certificacion.estado }}</p>
        <p><strong>Fecha Emisión:</strong> {{ certificacion.fecha_emision || '—' }}</p>
        <div class="card-actions justify-end mt-4">
          <Link href="/certificaciones" class="btn btn-ghost">Volver</Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template> 