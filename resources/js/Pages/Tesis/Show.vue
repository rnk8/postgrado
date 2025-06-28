<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
const props = defineProps({ tesis: Object });
</script>

<template>
  <AppLayout>
    <Head title="Detalles de Tesis" />
    <template #title>Detalles de Tesis</template>

    <div class="card bg-base-100 shadow-xl">
      <div class="card-body space-y-2">
        <p><strong>Título:</strong> {{ tesis.titulo }}</p>
        <p><strong>Registro Estudiante:</strong> {{ tesis.nro_registro_est }}</p>
        <p><strong>Nombre Estudiante:</strong> {{ tesis.nombre_est }}</p>
        <p><strong>Fecha Defensa:</strong> {{ tesis.fecha_defensa_tfg || '—' }}</p>
        <p><strong>Nota Defensa:</strong> {{ tesis.nota_defensa_tfg || '—' }}</p>
        <p><strong>Programa:</strong> {{ tesis.programa?.nombre_carrera || '—' }}</p>
        <p><strong>Tutor:</strong> {{ tesis.tutor?.nombre || '—' }}</p>
        <p><strong>Estado:</strong> {{ tesis.estado }}</p>
        <div class="card-actions justify-end mt-4">
          <Link href="/tesis" class="btn btn-ghost">Volver</Link>
        </div>
      </div>
    </div>
  </AppLayout>
</template> 