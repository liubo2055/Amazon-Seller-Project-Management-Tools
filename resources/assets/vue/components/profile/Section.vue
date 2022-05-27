<template>
  <div>
    <div class="row">
      <div class="col-sm-6">
        <div class="form-group required-field">
          <label>{{ _ix('Name','Profile') }}</label>
          <text-input
            v-model="name"
            :focus="true"
            :length="255"
          ></text-input>
          <validation-error
            path="name"
            :errors="validationErrors"
          ></validation-error>
        </div>
        <div class="form-group required-field">
          <label>{{ _ix('Email','Profile') }}</label>
          <text-input
            type="email"
            v-model="email"
            :length="255"
          ></text-input>
          <validation-error
            path="email"
            :errors="validationErrors"
          ></validation-error>
        </div>
        <div class="form-group required-field not-required">
          <label>{{ _ix('Password','Profile') }}</label>
          <text-input
            type="password"
            v-model="password"
            :length="16"
          ></text-input>
          <validation-error
            path="password"
            :errors="validationErrors"
          ></validation-error>
        </div>
        <div class="form-group required-field not-required">
          <label>{{ _ix('Password (confirmation)','Profile') }}</label>
          <text-input
            type="password"
            v-model="passwordConfirmation"
            :length="16"
          ></text-input>
        </div>
        <div class="form-group required-field">
          <label>{{ _ix('QQ','Profile') }}</label>
          <text-input
            v-model="qq"
            :length="255"
          ></text-input>
          <validation-error
            path="qq"
            :errors="validationErrors"
          ></validation-error>
        </div>
        <div class="form-group required-field">
          <label>{{ _ix('Cell phone number','Profile') }}</label>
          <text-input
            v-model="phone"
            :length="255"
          ></text-input>
          <validation-error
            path="phone"
            :errors="validationErrors"
          ></validation-error>
        </div>
        <div class="form-group required-field">
          <label>{{ _ix('Timezone','Profile') }}</label>
          <dropdown
            v-model="timezone"
            :options="timezones"
          ></dropdown>
          <validation-error
            path="timezone"
            :errors="validationErrors"
          ></validation-error>
        </div>
        <div class="form-group required-field">
          <label>{{ _ix('Language','Profile') }}</label>
          <dropdown
            v-model="locale"
            :options="locales"
          ></dropdown>
          <validation-error
            path="locale"
            :errors="validationErrors"
          ></validation-error>
        </div>
        <div class="form-group required-field not-required">
          <label>{{ _ix('Wechat ID','Profile') }}</label>
          <text-input
            v-model="wechatId"
            :length="255"
          ></text-input>
          <validation-error
            path="wechatId"
            :errors="validationErrors"
          ></validation-error>
        </div>
        <div class="form-group required-field not-required">
          <label>{{ _ix('Alipay ID','Profile') }}</label>
          <text-input
            v-model="alipayId"
            :length="255"
          ></text-input>
          <validation-error
            path="alipayId"
            :errors="validationErrors"
          ></validation-error>
        </div>
        <div class="form-group required-field not-required">
          <label>{{ _ix('Company','Profile') }}</label>
          <text-input
            v-model="companyName"
            :length="255"
          ></text-input>
          <validation-error
            path="companyName"
            :errors="validationErrors"
          ></validation-error>
        </div>
        <div class="form-group required-field not-required">
          <label>{{ _ix('Website URL','Profile') }}</label>
          <text-input
            v-model="companyUrl"
            :length="255"
          ></text-input>
          <p class="text-info">{{ _ix('The URL must start with http or https; example: http://example.com','Profile') }}</p>
          <validation-error
            path="companyUrl"
            :errors="validationErrors"
          ></validation-error>
        </div>
      </div>
      <div class="col-sm-6">
        <div class="form-group required-field not-required">
          <label>{{ _ix('Profile picture','Profile') }}</label>
          <file-input
            :preview="true"
            :replaceable="true"
            :upload-on-change="false"
            :url="uploadUrl"
            :value="{
              imageUrl:this.imageUrl
            }"
            @upload="changeFileCode('image',$event.code)"
          ></file-input>
        </div>
        <div v-if="showStorefronts">
          <div class="form-group required-field">
            <h4>{{ _ix('Storefronts','Profile') }}</h4>
            <storefronts
              v-model="storefronts"
              :errors="validationErrors"
            ></storefronts>
          </div>
        </div>
      </div>
    </div>
    <button
      class="btn btn-primary btn-lg"
      type="button"
      @click="save()"
    >
      <template v-if="!saving">
        {{ _ix('Save','Profile') }}
      </template>
      <template v-if="saving">
        <i class="fa fa-circle-o-notch fa-spin fa-fw"></i>
        {{ _ix('Saving...','Profile') }}
      </template>
    </button>
    <p
      class="text-success"
      v-if="saved"
    >
      <i class="fa fa-check"></i>
      {{ _ix('Profile saved','Profile') }}
    </p>
    <p
      class="text-danger"
      v-if="saveError"
    >
      <i class="fa fa-exclamation-triangle"></i>
      {{ _ix('Save error','Profile') }}
    </p>
  </div>
</template>
<script>
  import ValidationError from 'common/components/ValidationError'
  import TextInput from 'common/controls/TextInput'
  import Dropdown from 'common/controls/Dropdown'
  import Checkbox from 'common/controls/Checkbox'
  import FileInput from 'common/controls/FileInput'
  import Storefronts from './Storefronts'

  export default {
    components:{
      ValidationError,
      TextInput,
      Dropdown,
      Checkbox,
      FileInput,
      Storefronts
    },
    props:{
      saveUrl:{
        type:String,
        required:true
      },
      profile:{
        type:Object,
        required:true
      },
      uploadUrl:{
        type:String,
        required:true
      },
      timezones:{
        type:Object,
        required:true
      },
      locales:{
        type:Object,
        required:true
      },
      showStorefronts:{
        type:Boolean,
        required:true
      }
    },
    data(){
      return {
        email:null,
        password:null,
        passwordConfirmation:null,
        name:null,
        qq:null,
        wechatId:null,
        locale:null,
        timezone:null,
        phone:null,
        companyName:null,
        companyUrl:null,
        alipayId:null,
        imageUrl:null,
        storefronts:[],
        fileCodes:{},
        saving:false,
        saved:null,
        saveError:false,
        validationErrors:{}
      }
    },
    created(){
      this.email=this.profile.email
      this.name=this.profile.name
      this.qq=this.profile.qq
      this.wechatId=this.profile.wechatId
      this.locale=this.profile.locale
      this.timezone=this.profile.timezone
      this.phone=this.profile.phone
      this.companyName=this.profile.companyName
      this.companyUrl=this.profile.companyUrl
      this.alipayId=this.profile.alipayId
      this.imageUrl=this.profile.imageUrl
      if(this.showStorefronts)
        this.storefronts=this.profile.storefronts||[]
    },
    methods:{
      changeFileCode(attr,code){
        this.fileCodes[attr]=code
      },
      save(){
        this.saving=true
        this.saved=false
        this.saveError=false
        this.validationErrors={}

        const params={
          email:this.email,
          password:this.password,
          passwordConfirmation:this.passwordConfirmation,
          name:this.name,
          qq:this.qq,
          wechatId:this.wechatId,
          locale:this.locale,
          timezone:this.timezone,
          phone:this.phone,
          companyName:this.companyName,
          companyUrl:this.companyUrl,
          alipayId:this.alipayId,
          imageUrl:this.imageUrl,
          fileCodes:this.fileCodes
        }
        if(this.showStorefronts)
          params.storefronts=this.storefronts
        axios.post(this.saveUrl,params)
          .then(response=>{
            this.saving=false
            this.saved=true
            this.saveError=false
            this.validationErrors={}

            this.imageUrl=response.data.imageUrl
            this.fileCodes={}
          })
          .catch(error=>{
            this.saving=false
            this.saved=false
            this.saveError=true
            this.validationErrors=error.response.data.errors||{}
          })
      }
    }
  }
</script>
