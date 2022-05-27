<template>
  <div>
    <button
      class="btn btn-link"
      type="button"
      v-if="operations.edit"
      @click="$emit('rowEvent',{
        action:'edit',
        url:operations.edit.url
      })"
    >
      {{ _ix('Edit','Users') }}
    </button>
    <button
      class="btn btn-link"
      type="button"
      v-if="operations.block"
      @click="$emit('rowEvent',{
        action:'block',
        url:operations.block.url
      })"
    >
      {{ _ix('Block','Users') }}
    </button>
    <button
      class="btn btn-link"
      type="button"
      v-if="operations.unblock"
      @click="$emit('rowEvent',{
        action:'unblock',
        url:operations.unblock.url
      })"
    >
      {{ _ix('Unblock','Users') }}
    </button>
  </div>
</template>
<script>
  export default {
    props:{
      operations:{
        type:Object,
        required:true
      }
    }
  }
</script>
