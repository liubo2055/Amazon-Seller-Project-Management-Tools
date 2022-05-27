<template>
  <div>
    <file-input
      :preview="true"
      :replaceable="false"
      :upload-on-change="true"
      :url="uploadUrl"
      :value="value"
      @upload="$emit('upload',$event)"
    ></file-input>
  </div>
</template>
<script>
  import FileInput from 'common/controls/FileInput'

  export default {
    components:{FileInput},
    props:{
      uploadUrl:{
        type:String,
        required:true
      },
      imageUrl:{
        type:String
      }
    },
    data(){
      return {
        yyy:Math.random()
      }
    },
    computed:{
      value(){
        if(this.imageUrl)
          return {
            imageUrl:this.imageUrl
          }
        else
          return null
      }
    }
  }
</script>
