<template>
  <div>
    <button
      class="btn btn-primary"
      type="button"
      v-if="urls.new"
      @click="$emit('create',urls.new)"
    >
      {{ _ix('Create to-do','To-Dos') }}
    </button>
    <button
      class="btn btn-primary"
      type="button"
      v-if="urls.newPrivate"
      @click="$emit('create',urls.newPrivate)"
    >
      {{ _ix('Create private to-do','To-Dos') }}
    </button>
  </div>
</template>
<script>
  export default {
    props:{
      urls:{
        type:Object,
        required:true
      }
    }
  }
</script>
