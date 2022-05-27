<template>
  <input
    class="checkbox"
    :checked="value"
    :name="name"
    :type="radio?'radio':'checkbox'"
    @change="$emit('input',$event.target.checked)"
  >
</template>
<script>
  export default {
    props:{
      value:{
        type:Boolean
      },
      radio:{
        type:Boolean
      },
      // Required for radio buttons
      name:{
        type:String
      }
    }
  }
</script>
