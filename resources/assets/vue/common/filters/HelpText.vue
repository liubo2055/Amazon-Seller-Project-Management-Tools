<template>
  <p
    class="filter-help-text"
    v-if="text"
  >
    {{ text }}
  </p>
</template>
<script>
  export default {
    props:{
      text:{
        type:String
      }
    }
  }
</script>
