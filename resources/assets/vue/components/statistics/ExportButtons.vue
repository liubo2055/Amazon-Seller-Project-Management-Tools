<template>
  <form
    method="POST"
    :action="url"
    v-if="url"
  >
    <input
      name="_token"
      type="hidden"
      :value="csrf"
    >
    <input
      name="filters"
      type="hidden"
      :value="filters"
    >
    <button
      class="btn btn-primary"
      name="separate"
      value="false"
    >
      <i class="fa fa-file-excel-o"></i>
      {{ _ix('Download','Statistics') }}
    </button>
    <button
      class="btn btn-primary"
      name="separate"
      value="true"
    >
      <i class="fa fa-file-excel-o"></i>
      {{ _ix('Download with separate sheets','Statistics') }}
    </button>
  </form>
</template>
<script>
  export default {
    props:{
      url:{
        type:String,
        required:true
      },
      filters:{
        type:String,
        required:true
      },
      csrf:{
        type:String,
        required:true
      }
    }
  }
</script>
