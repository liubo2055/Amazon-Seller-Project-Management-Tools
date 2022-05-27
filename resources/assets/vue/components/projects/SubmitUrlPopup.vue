<template>
  <popup
    :save-button="Boolean(saveUrl)"
    :save-error="saveError"
    :title="_ix('Submit completion URL','Projects')"
    @close="$emit('close')"
    @save="save()"
  >
    <div class="form-group required-field">
      <label>{{ _ix('Completion URL','Projects') }}</label>
      <text-input
        type="url"
        v-model="completionUrl"
        :focus="true"
        :length="255"
      ></text-input>
      <validation-error
        path="completionUrl"
        :errors="validationErrors"
      ></validation-error>
    </div>
  </popup>
</template>
<script>
  import Popup from 'common/components/Popup'
  import ValidationError from 'common/components/ValidationError'
  import TextInput from 'common/controls/TextInput'

  export default {
    components:{
      Popup,
      ValidationError,
      TextInput
    },
    props:{
      saveUrl:{
        type:String,
        required:true
      }
    },
    data(){
      return {
        completionUrl:null,
        saveError:false,
        validationErrors:{}
      }
    },
    methods:{
      save(){
        this.saveError=false
        this.validationErrors={}

        const params={
          completionUrl:this.completionUrl
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
      }
    }
  }
</script>
