<script setup>
import { computed } from 'vue';
import * as OutlineIcons from '@heroicons/vue/24/outline';
import * as SolidIcons from '@heroicons/vue/24/solid';

const props = defineProps({
  name: { type: String, required: true },
  solid: { type: Boolean, default: false },
  class: { type: String, default: 'h-5 w-5' }
});

const IconComponent = computed(() => {
  if (props.solid && SolidIcons[props.name]) {
    return SolidIcons[props.name];
  }
  return OutlineIcons[props.name] || OutlineIcons['QuestionMarkCircleIcon'];
});
</script>

<template>
  <component :is="IconComponent" :class="props.class"/>
</template> 