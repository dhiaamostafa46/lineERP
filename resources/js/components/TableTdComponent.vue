<template>
  <span>{{ message }}</span>
</template>

<script setup>
defineProps({
  message: {
    type: String,
    required: true,
  },
});
</script>
