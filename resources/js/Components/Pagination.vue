<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  links: {
    type: Array,
    required: true,
  },
});
</script>

<template>
  <div v-if="links && links.length > 1" class="flex justify-center mt-6">
    <div class="join">
      <component
        v-for="(link, idx) in links"
        :key="idx"
        :is="link.url ? Link : 'span'"
        v-bind="link.url ? { href: link.url } : {}"
        class="join-item btn"
        :class="{
          'btn-active': link.active,
          'btn-disabled': !link.url
        }"
        v-html="link.label"
      />
    </div>
  </div>
</template>

<style scoped>
/**** No additional styling, rely on DaisyUI trimmings ****/
</style> 