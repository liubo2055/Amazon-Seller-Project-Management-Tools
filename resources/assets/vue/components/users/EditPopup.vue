<template>
  <popup
    :save-button="Boolean(saveUrl)"
    :save-error="saveError"
    :title="title"
    @close="$emit('close')"
    @save="save()"
  >
    <div class="row">
      <div class="col-sm-6">
        <div class="form-group required-field">
          <label>{{ _ix('Email','Users') }}</label>
          <text-input
            type="email"
            v-model="email"
            :focus="true"
            :length="255"
          />
          <validation-error
            path="email"
            :errors="validationErrors"
          />
        </div>
        <div
          class="form-group required-field"
          :class="{
            'not-required':!create
          }"
        >
          <label>{{ _ix('Password','Users') }}</label>
          <text-input
            type="password"
            v-model="password"
            :length="16"
          />
          <validation-error
            path="password"
            :errors="validationErrors"
          />
        </div>
        <div
          class="form-group required-field"
          :class="{
            'not-required':!create
          }"
        >
          <label>{{ _ix('Password (confirmation)','Users') }}</label>
          <text-input
            type="password"
            v-model="passwordConfirmation"
            :length="16"
          />
        </div>
        <div class="form-group required-field">
          <label>{{ _ix('Role','Users') }}</label>
          <dropdown
            v-model="role"
            :options="roles"
          />
          <validation-error
            path="role"
            :errors="validationErrors"
          />
        </div>
        <div
          class="form-group required-field not-required"
          v-if="role===freelancerRole"
        >
          <label>{{ _ix('Creator user (for private freelancers)','Users') }}</label>
          <autocomplete
            v-model="creatorUser"
            :disabled="onlyPrivateFreelancers"
            :options="filteredCreatorUsers"
          />
          <validation-error
            path="creatorUser"
            :errors="validationErrors"
          />
        </div>
        <div class="form-group required-field">
          <label>{{ _ix('Name','Users') }}</label>
          <text-input
            v-model="name"
            :length="255"
          />
          <validation-error
            path="name"
            :errors="validationErrors"
          />
        </div>
        <div class="form-group required-field">
          <label>{{ _ix('Cell phone number','Users') }}</label>
          <text-input
            v-model="phone"
            :length="255"
          />
          <validation-error
            path="phone"
            :errors="validationErrors"
          />
        </div>
        <div class="form-group required-field">
          <label>{{ _ix('Timezone','Users') }}</label>
          <dropdown
            v-model="timezone"
            :options="timezones"
          />
          <validation-error
            path="timezone"
            :errors="validationErrors"
          />
        </div>
        <div class="form-group required-field">
          <label>{{ _ix('Language','Users') }}</label>
          <dropdown
            v-model="locale"
            :options="locales"
          />
          <validation-error
            path="locale"
            :errors="validationErrors"
          />
        </div>
      </div>
      <div class="col-sm-6">
        <div class="form-group required-field">
          <label>{{ _ix('QQ','Users') }}</label>
          <text-input
            v-model="qq"
            :length="255"
          />
          <validation-error
            path="qq"
            :errors="validationErrors"
          />
        </div>
        <div class="form-group required-field not-required">
          <label>{{ _ix('Wechat ID','Users') }}</label>
          <text-input
            v-model="wechatId"
            :length="255"
          />
          <validation-error
            path="wechatId"
            :errors="validationErrors"
          />
        </div>
        <div class="form-group required-field not-required">
          <label>{{ _ix('Alipay ID','Users') }}</label>
          <text-input
            v-model="alipayId"
            :length="255"
          />
          <validation-error
            path="alipayId"
            :errors="validationErrors"
          />
        </div>
        <div class="form-group required-field not-required">
          <label>{{ _ix('Company','Users') }}</label>
          <text-input
            v-model="companyName"
            :length="255"
          />
          <validation-error
            path="companyName"
            :errors="validationErrors"
          />
        </div>
        <div class="form-group required-field not-required">
          <label>{{ _ix('Website URL','Users') }}</label>
          <text-input
            v-model="companyUrl"
            :length="255"
          />
          <p class="text-info">{{ _ix('The URL must start with http or https; example: http://example.com','Users') }}</p>
          <validation-error
            path="companyUrl"
            :errors="validationErrors"
          />
        </div>
        <div class="form-group required-field not-required">
          <label>{{ _ix('Register date','Users') }}</label>
          <p>{{ registerDate }}</p>
        </div>
      </div>
    </div>
  </popup>
</template>
<script>
  import Popup from 'common/components/Popup'
  import ValidationError from 'common/components/ValidationError'
  import TextInput from 'common/controls/TextInput'
  import Dropdown from 'common/controls/Dropdown'
  import Autocomplete from 'common/controls/autocomplete/Autocomplete'

  export default {
    components:{
      Autocomplete,
      Popup,
      ValidationError,
      TextInput,
      Dropdown
    },
    props:{
      loadUrl:{
        type:String,
        required:true
      },
      roles:{
        type:Object,
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
      creatorUsers:{
        type:Array,
        required:true
      },
      freelancerRole:{
        type:String,
        required:true
      },
      onlyPrivateFreelancers:{
        type:Boolean,
        required:true
      }
    },
    data(){
      return {
        saveUrl:null,
        id:null,
        email:null,
        password:null,
        passwordConfirmation:null,
        role:null,
        creatorUser:null,
        name:null,
        qq:null,
        wechatId:null,
        locale:null,
        timezone:null,
        phone:null,
        companyName:null,
        companyUrl:null,
        registerDate:null,
        notes:null,
        alipayId:null,
        create:false,
        saveError:false,
        validationErrors:{}
      }
    },
    computed:{
      title(){
        if(this.create)
          return _ix('Create user','Users')
        else
          return _ix('Edit user','Users')
      },
      filteredCreatorUsers(){
        // Exclude the user being edited
        return this.creatorUsers.filter(creatorUser=>creatorUser.id!==this.id)
      }
    },
    created(){
      axios.post(this.loadUrl)
        .then(response=>{
          this.loadFromData(response.data)
          this.create= !response.data.id
        })
    },
    methods:{
      loadFromData(data){
        this.saveUrl=data.url
        this.id=data.id
        this.email=data.email
        this.role=data.role
        this.creatorUser=data.creatorUser
        this.name=data.name
        this.qq=data.qq
        this.wechatId=data.wechatId
        this.locale=data.locale
        this.timezone=data.timezone
        this.phone=data.phone
        this.companyName=data.companyName
        this.companyUrl=data.companyUrl
        this.registerDate=data.registerDate
        this.notes=data.notes
        this.alipayId=data.alipayId
      },
      save(){
        this.saveError=false
        this.validationErrors={}

        const params={
          email:this.email,
          password:this.password,
          passwordConfirmation:this.passwordConfirmation,
          role:this.role,
          creatorUser:this.creatorUser,
          name:this.name,
          qq:this.qq,
          wechatId:this.wechatId,
          locale:this.locale,
          timezone:this.timezone,
          phone:this.phone,
          companyName:this.companyName,
          companyUrl:this.companyUrl,
          alipayId:this.alipayId,
          notes:this.notes
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
