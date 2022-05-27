<template>
  <popup
    :save-button="Boolean(saveUrl)"
    :save-error="saveError"
    :title="_ix('Submit description','Projects')"
    @close="$emit('close')"
    @save="save()"
  >
    <div class="form-group required-field not-required">
      <label>{{ _ix('Project title','Projects') }}</label>
      <text-input
        v-model="project.projectTitle"
        :length="255"
      ></text-input>
      <validation-error
        path="projectTitle"
        :errors="validationErrors"
      ></validation-error>
    </div>
    <div class="form-group required-field not-required">
      <label>{{ _ix('Project description','Projects') }}</label>
      <textarea-input v-model="project.projectDescription"></textarea-input>
      <validation-error
        path="projectDescription"
        :errors="validationErrors"
      ></validation-error>
    </div>
    <div
      class="form-group required-field not-required"
      v-if="showStoreDescription"
    >
      <label>{{ _ix('Store description','Projects') }}</label>
      <text-input
        v-model="project.storeDescription"
        :length="255"
      ></text-input>
      <validation-error
        path="storeDescription"
        :errors="validationErrors"
      ></validation-error>
    </div>
    <div class="form-group required-field not-required">
      <label>{{ _ix('Images','Projects') }}</label>
      <images
        :images="images"
        :upload-url="uploadUrl"
        @add="addImage()"
        @change="changeImage($event.index,$event.code)"
        @delete="deleteImage($event.index)"
      ></images>
    </div>
    <div class="form-group required-field not-required">
      <label>{{ _ix('Media','Projects') }}</label>
      <medias
        :medias="medias"
        @add="addMedia()"
        @change="changeMedia($event.index,$event.content)"
        @delete="deleteMedia($event.index)"
      ></medias>
    </div>
  </popup>
</template>
<script>
  import Popup from 'common/components/Popup'
  import ValidationError from 'common/components/ValidationError'
  import TextInput from 'common/controls/TextInput'
  import Checkbox from 'common/controls/Checkbox'
  import Dropdown from 'common/controls/Dropdown'
  import TextareaInput from 'common/controls/TextareaInput'
  import Images from './Images'
  import Medias from './Medias'

  export default {
    components:{
      Popup,
      ValidationError,
      TextInput,
      Checkbox,
      Dropdown,
      TextareaInput,
      Images,
      Medias
    },
    props:{
      loadUrl:{
        type:String,
        required:true
      },
      uploadUrl:{
        type:String,
        required:true
      }
    },
    data(){
      return {
        project:{},
        images:[],
        medias:[],
        saveUrl:null,
        showStoreDescription:false,
        newId:-1,
        saveError:false,
        validationErrors:{}
      }
    },
    created(){
      axios.post(this.loadUrl)
        .then(response=>{
          this.loadFromData(response.data)
        })
    },
    methods:{
      loadFromData(data){
        this.project=data.project
        this.images=data.images
        this.medias=data.medias
        this.saveUrl=data.saveUrl
        this.showStoreDescription=data.showStoreDescription
      },
      save(){
        this.saveError=false
        this.validationErrors={}

        const params={
          project:this.project,
          images:this.images,
          medias:this.medias
        }
        axios.post(this.saveUrl,params)
          .then(()=>{
            this.$emit('close')
            this.$emit('save')
          })
          .catch(error=>{
            this.saveError=true
            this.validationErrors=error.response.data.errors||{}
          })
      },
      addImage(){
        this.images.push({
          id:this.newId--,
          url:null
        })
      },
      changeImage(index,code){
        const image=this.images[index]
        image.code=code

        this.$set(this.images,index,image)
      },
      deleteImage(index){
        this.images.splice(index,1)
      },
      addMedia(){
        this.medias.push({
          id:this.newId--,
          url:null
        })
      },
      changeMedia(index,content){
        const media=this.medias[index]
        media.content=content

        this.$set(this.medias,index,media)
      },
      deleteMedia(index){
        this.medias.splice(index,1)
      }
    }
  }
</script>
