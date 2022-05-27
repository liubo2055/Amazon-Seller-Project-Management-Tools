<template>
  <div>
    <span>{{ item.name }}</span>
    <abbr v-if="item.description">{{ item.description }}</abbr>
  </div>
</template>
<script>
  export default {
    props:{
      item:{
        type:Object,
        required:true
      }
    }
  }
</script>
