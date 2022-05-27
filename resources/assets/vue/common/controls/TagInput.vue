<template>
  <input-tag
    :tags="value||[]"
    @update:tags="$emit('input',$event)"
  ></input-tag>
</template>
<script>
  import InputTag from 'vue-input-tag'

  export default {
    components:{InputTag},
    props:{
      value:{
        type:Array
      }
    }
  }
</script>
