<template>
  <popup
    :save-button="Boolean(saveUrl)"
    :save-error="saveError"
    :title="_ix('Edit project','Projects')"
    @close="$emit('close')"
    @save="save()"
  >
    <div class="form-group required-field not-required">
      <label>{{ _ix('Product title','Projects') }}</label>
      <text-input
        v-model="project.productTitle"
        :length="255"
      ></text-input>
      <validation-error
        path="productTitle"
        :errors="validationErrors"
      ></validation-error>
    </div>
    <div class="form-group required-field not-required">
      <label>{{ _ix('Product description','Projects') }}</label>
      <text-input
        v-model="project.productDescription"
        :length="255"
      ></text-input>
      <validation-error
        path="productDescription"
        :errors="validationErrors"
      ></validation-error>
    </div>
    <div class="form-group required-field">
      <label>{{ _ix('Project price','Projects') }}</label>
      <text-input
        type="number"
        v-model="project.projectPrice"
        :max="99999.99"
        :min="0.01"
      ></text-input>
      <validation-error
        path="projectPrice"
        :errors="validationErrors"
      ></validation-error>
    </div>
    <div class="form-group required-field not-required">
      <label>{{ _ix('Notes','Projects') }}</label>
      <textarea-input v-model="project.notes"></textarea-input>
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

  export default {
    components:{
      Popup,
      ValidationError,
      TextInput,
      Checkbox,
      Dropdown,
      TextareaInput
    },
    props:{
      loadUrl:{
        type:String,
        required:true
      }
    },
    data(){
      return {
        project:{},
        saveUrl:null,
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
        this.saveUrl=data.saveUrl
      },
      save(){
        this.saveError=false
        this.validationErrors={}

        axios.post(this.saveUrl,this.project)
          .then(()=>{
            this.$emit('close')
            this.$emit('save')
          })
          .catch(error=>{
            this.saveError=true
            this.validationErrors=error.response.data.errors||{}
          })
      }
    }
  }
</script>
