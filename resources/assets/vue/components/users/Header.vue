<template>
  <div>
    <button
      class="btn btn-primary"
      type="button"
      @click="$emit('create')"
    >
      {{ _ix('Create user','Users') }}
    </button>
    <button
      class="btn btn-default"
      type="button"
      v-if="showImportButton"
      @click="$emit('import')"
    >
      {{ _ix('Import users','Users') }}
    </button>
  </div>
</template>
<script>
  export default {
    props:{
      showImportButton:{
        type:Boolean,
        required:true
      }
    }
  }
</script>
