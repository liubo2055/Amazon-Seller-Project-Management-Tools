<template>
  <div
    class="validation-error"
    v-if="matchingErrors.length"
  >
    <div
      class="text text-danger"
      v-for="error in matchingErrors"
    >
      <i class="fa fa-exclamation-triangle"></i>
      {{ error }}
    </div>
  </div>
</template>
<script>
  export default {
    props:{
      errors:{
        type:Object,
        required:true
      },
      path:{
        type:[
          String,
          Array
        ],
        required:true
      }
    },
    computed:{
      matchingErrors(){
        let key
        if(Array.isArray(this.path))
          key=this.path.join('.')
        else
          key=this.path

        return this.errors[key]||[]
      }
    }
  }
</script>
