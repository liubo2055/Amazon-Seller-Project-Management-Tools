<template>
  <div>
    <div
      class="alert alert-info"
      v-if="importing"
    >
      <i class="fa fa-spinner fa-pulse"></i>
      {{ _ix('Importing file...','Users') }}
    </div>
    <div
      class="alert alert-danger"
      v-if="error"
    >
      {{ _ix('An error has occurred while trying to import the file','Users') }}
    </div>
    <div
      class="alert alert-success"
      v-if="result"
    >
      {{ result }}
    </div>
  </div>
</template>
<script>
  export default {
    props:{
      importing:{
        type:Boolean,
        required:true
      },
      error:{
        type:Boolean,
        required:true
      },
      result:{
        type:String
      }
    }
  }
</script>
