<template>
  <div class="file-input">
    <template v-if="!uploaded&&!initialImageUrl&&!initialDownloadUrl">
      <div
        class="fileinput fileinput-new"
        data-provides="fileinput"
        v-show="!uploading"
      >
        <span class="btn btn-default btn-file">
          <span class="fileinput-new">{{ _ix('Select file','File input') }}</span>
          <span class="fileinput-exists">{{ _ix('Change','File input') }}</span>
          <input
            ref="file"
            type="file"
            @change="uploadOnChange&&upload()"
          >
        </span>
        <template v-if="!uploadOnChange">
          <button
            class="btn btn-warning fileinput-exists"
            data-dismiss="fileinput"
            type="button"
            @click="reset()"
          >
            {{ _ix('Reset','File input') }}
          </button>
          <button
            class="btn btn-primary fileinput-exists"
            type="button"
            @click="upload()"
          >
            {{ error?_ix('Retry','File input'):_ix('Upload','File input') }}
          </button>
          <span class="fileinput-filename"></span>
        </template>
      </div>
      <div class="progress" v-show="progress">
        <div
          class="progress-bar progress-bar-info"
          ref="progress"
          :style="{
            width:progress+'%'
          }"
        ></div>
      </div>
      <template v-if="error">
        <div class="alert alert-warning">
          <i class="fa fa-exclamation-triangle"></i>
          {{ _ix('An error has occurred while trying to upload the file','File input') }}
        </div>
      </template>
    </template>
    <template v-if="uploaded">
      <p class="uploaded" :title="sprintf(_ix('File %s uploaded','File input'),name)">
        {{ sprintf(_ix('File %s uploaded','File input'),name) }}
      </p>
      <img
        class="preview-image"
        v-if="preview&&imageUrl"
        :src="imageUrl"
      >
      <a
        class="btn btn-info"
        target="_blank"
        v-if="downloadUrl"
        :href="downloadUrl"
      >
        {{ _ix('Download','File input') }}
      </a>
      <button
        class="btn btn-warning"
        type="button"
        v-if="replaceable"
        @click="replace()"
      >
        {{ _ix('Clear','File input') }}
      </button>
    </template>
    <template v-if="initialImageUrl">
      <img class="preview-image" :src="initialImageUrl">
      <button
        class="btn btn-warning"
        type="button"
        v-if="replaceable"
        @click="clear(true)"
      >
        {{ _ix('Clear','File input') }}
      </button>
    </template>
    <template v-if="initialDownloadUrl">
      <a
        class="btn btn-info"
        target="_blank"
        v-if="initialDownloadUrl"
        :href="initialDownloadUrl"
      >
        {{ _ix('Download','File input') }}
      </a>
      <button
        class="btn btn-warning"
        type="button"
        v-if="replaceable"
        @click="clear(false)"
      >
        {{ _ix('Clear','File input') }}
      </button>
    </template>
  </div>
</template>
<script>
  export default {
    props:{
      url:{
        type:String,
        required:true
      },
      uploadOnChange:{
        type:Boolean,
        required:true
      },
      preview:{
        type:Boolean,
        required:true
      },
      replaceable:{
        type:Boolean,
        required:true
      },
      value:{
        type:Object
      }
    },
    data(){
      return {
        uploading:false,
        error:false,
        progress:0,
        uploaded:false,
        code:null,
        name:null,
        imageUrl:null,
        downloadUrl:null,
        deleteUrl:null,
        initialImageUrl:this.value?this.value.imageUrl||null:null,
        initialDownloadUrl:this.value?this.value.downloadUrl||null:null
      }
    },
    watch:{
      value(value){
        this.initialImageUrl=value.imageUrl||null
        this.initialDownloadUrl=value.downloadUrl||null

        this.resetUpload()
        this.uploaded=false
      }
    },
    methods:{
      reset(){
        this.uploading=false
        this.error=false
        this.progress=0
      },
      resetUpload(){
        this.code=null
        this.name=null
        this.imageUrl=null
        this.downloadUrl=null
        this.deleteUrl=null
      },
      upload(){
        this.reset()
        this.uploading=true

        const params=new FormData()
        params.append('file',this.$refs.file.files[0])

        const vm=this
        const config={
          onUploadProgress(e){
            vm.progress=Math.round((e.loaded*100)/e.total)
          }
        }

        axios.post(this.url,params,config)
          .then(response=>{
            this.reset()

            const data=response.data
            this.code=data.code
            this.name=data.name
            this.imageUrl=data.imageUrl
            this.downloadUrl=data.downloadUrl
            this.deleteUrl=data.deleteUrl

            this.$emit('upload',{
              code:data.code
            })

            this.uploaded=true
          })
          .catch(()=>{
            this.reset()
            this.error=true
          })
      },
      replace(){
        axios.delete(this.deleteUrl)
          .then(()=>{
            this.reset()
            this.resetUpload()

            this.$emit('upload',{
              code:null
            })

            this.uploaded=false
          })
      },
      clear(image){
        if(!image)
          this.initialDownloadUrl=null
        else
          this.initialImageUrl=null

        this.$emit('upload',{
          code:null
        })
      }
    }
  }
</script>
