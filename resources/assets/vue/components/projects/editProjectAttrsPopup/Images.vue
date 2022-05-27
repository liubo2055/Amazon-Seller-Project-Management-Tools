<template>
  <div class="row">
    <div
      class="col-sm-3"
      v-for="(image,index) in images"
      :key="image.id"
    >
      <image-comp
        :image-url="image.url"
        :upload-url="uploadUrl"
        @upload="change(index,$event.code)"
      ></image-comp>
      <button
        class="btn btn-warning btn-xs"
        type="button"
        @click="$emit('delete',{index})"
      >
        {{ _ix('Delete','Projects') }}
      </button>
    </div>
    <div class="col-sm-3">
      <button
        class="btn btn-primary"
        type="button"
        @click="$emit('add')"
      >
        {{ _ix('Add image','Projects') }}
      </button>
    </div>
  </div>
</template>
<script>
  import ImageComp from './Image'

  export default {
    components:{ImageComp},
    props:{
      images:{
        type:Array,
        required:true
      },
      uploadUrl:{
        type:String,
        required:true
      }
    },
    methods:{
      change(index,code){
        this.$emit('change',{
          index,
          code
        })
      }
    }
  }
</script>
