<script setup>
import AppLayout from '../../Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const menu = computed(() => page.props.menu || []);
</script>

<template>
  <AppLayout>
    <Head title="Menú Dinámico" />

    <template #title>Menú Dinámico</template>

    <div class="card bg-base-100 shadow-xl">
      <div class="card-body p-4">
        <ul class="space-y-1">
          <li v-for="item in menu" :key="item.id">
            <strong>{{ item.titulo }}</strong>
            <ul class="pl-4">
              <li v-for="child in item.children" :key="child.id">
                {{ child.titulo }}
              </li>
            </ul>
          </li>
          <li v-if="menu.length === 0" class="text-gray-500">No hay elementos de menú.</li>
        </ul>
      </div>
    </div>
  </AppLayout>
</template> 