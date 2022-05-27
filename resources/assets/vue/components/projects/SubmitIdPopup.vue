<template>
  <popup
    :save-button="Boolean(saveUrl)"
    :save-error="saveError"
    :title="_ix('Submit completion ID','Projects')"
    @close="$emit('close')"
    @save="save()"
  >
    <div class="form-group required-field">
      <label>{{ _ix('Completion ID','Projects') }}</label>
      <text-input
        v-model="completionId"
        :focus="true"
        :length="20"
      ></text-input>
      <validation-error
        path="completionId"
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
        completionId:null,
        saveError:false,
        validationErrors:{}
      }
    },
    methods:{
      save(){
        this.saveError=false
        this.validationErrors={}

        const params={
          completionId:this.completionId
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
