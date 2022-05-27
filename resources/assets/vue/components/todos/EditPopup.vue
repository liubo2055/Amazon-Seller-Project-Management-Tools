<template>
  <popup
    :save-button="saveButtonEnabled"
    :save-button-text="saveButtonText"
    :save-error="saveError"
    :saving="saving"
    :title="title"
    @close="$emit('close')"
    @save="save()"
  >
    <div class="row">
      <div class="col-sm-6">
        <div
          class="form-group required-field"
          v-if="!hideCode"
        >
          <label>{{ _ix('ID','To-Dos') }}</label>
          <text-input
            v-model="todo.code"
            :disabled="isDisabled('code')"
            :focus="true"
            :length="20"
          ></text-input>
          <validation-error
            path="code"
            :errors="validationErrors"
          ></validation-error>
        </div>
        <div class="form-group required-field">
          <label>{{ _ix('Shipping method','To-Dos') }}</label>
          <div>
            <label>
              <checkbox
                v-model="todo.fba"
                :radio="true"
              ></checkbox>
              {{ _ix('FBA','To-Dos') }}
            </label>
            <label>
              <checkbox
                v-model="fbm"
                :radio="true"
              ></checkbox>
              {{ _ix('FBM','To-Dos') }}
            </label>
          </div>
          <validation-error
            path="fba"
            :errors="validationErrors"
          ></validation-error>
        </div>
        <div class="form-group required-field not-required">
          <label>{{ _ix('Keywords','To-Dos') }}</label>
          <tag-input
            v-model="todo.keywords"
            :disabled="isDisabled('keywords')"
          ></tag-input>
          <validation-error
            path="keywords"
            :errors="validationErrors"
          ></validation-error>
        </div>
        <div class="form-group required-field">
          <label>{{ _ix('Product URL','To-Dos') }}</label>
          <text-input
            v-model="todo.productUrl"
            :disabled="isDisabled('productUrl')"
            :length="255"
            @input="guessFromProductUrl($event)"
          ></text-input>
          <validation-error
            path="productUrl"
            :errors="validationErrors"
          ></validation-error>
        </div>
        <div class="form-group required-field">
          <label>{{ _ix('Product ASIN','To-Dos') }}</label>
          <text-input
            v-model="todo.productAsin"
            :disabled="isDisabled('productAsin')"
            :length="10"
          ></text-input>
          <validation-error
            path="productAsin"
            :errors="validationErrors"
          ></validation-error>
        </div>
        <div class="form-group required-field">
          <label>{{ _ix('Marketplace','To-Dos') }}</label>
          <dropdown
            v-model="todo.marketplace"
            :disabled="isDisabled('marketplace')"
            :options="marketplaces"
            @input="updatePrice()"
          ></dropdown>
          <validation-error
            path="marketplace"
            :errors="validationErrors"
          ></validation-error>
        </div>
        <div class="form-group required-field">
          <label>{{ _ix('Product price','To-Dos') }}</label>
          <text-input
            type="number"
            v-model="todo.productPrice"
            :max="99999.99"
            :min="0.01"
            @input="updatePrice()"
          ></text-input>
          <validation-error
            path="productPrice"
            :errors="validationErrors"
          ></validation-error>
        </div>
        <div class="form-group required-field not-required">
          <label>{{ _ix('Product title','To-Dos') }}</label>
          <text-input
            v-model="todo.productTitle"
            :length="255"
          ></text-input>
          <validation-error
            path="productTitle"
            :errors="validationErrors"
          ></validation-error>
        </div>
        <div class="form-group required-field not-required">
          <label>{{ _ix('Product description','To-Dos') }}</label>
          <text-input
            v-model="todo.productDescription"
            :length="255"
          ></text-input>
          <validation-error
            path="productDescription"
            :errors="validationErrors"
          ></validation-error>
        </div>
      </div>
      <div class="col-sm-6">
        <div class="form-group required-field not-required">
          <label>{{ _ix('Seller name','To-Dos') }}</label>
          <text-input
            v-model="todo.sellerName"
            :disabled="isDisabled('sellerName')"
            :length="255"
          ></text-input>
          <p
            class="text-info"
            v-if="todo.originalSellerName"
          >
            {{ sprintf(_ix('Original seller name: %s','To-Dos'),todo.originalSellerName) }}
          </p>
          <validation-error
            path="sellerName"
            :errors="validationErrors"
          ></validation-error>
        </div>
        <div
          :class="{
          'form-group':true,
          'required-field':true,
          'not-required':!storefrontUrlRequired
        }"
        >
          <label>{{ _ix('Storefront URL','To-Dos') }}</label>
          <text-input
            v-if="storefronts===null"
            v-model="todo.storefrontUrl"
            :disabled="isDisabled('storefrontUrl')"
            :length="255"
            @input="guessFromStorefrontUrl($event)"
          ></text-input>
          <dropdown
            v-if="storefronts!==null"
            v-model="todo.storefrontUrl"
            :disabled="isDisabled('storefrontUrl')"
            :options="storefrontUrls"
            @input="guessFromStorefrontUrl($event)"
          ></dropdown>
          <validation-error
            path="storefrontUrl"
            :errors="validationErrors"
          ></validation-error>
        </div>
        <div class="form-group required-field">
          <label>{{ _ix('Storefront name','To-Dos') }}</label>
          <text-input
            v-if="storefronts===null"
            v-model="todo.storefrontName"
            :disabled="isDisabled('storefrontName')"
            :length="255"
          ></text-input>
          <dropdown
            v-if="storefronts!==null"
            v-model="todo.storefrontName"
            :disabled="isDisabled('storefrontName')"
            :options="storefrontNames"
          ></dropdown>
          <validation-error
            path="storefrontName"
            :errors="validationErrors"
          ></validation-error>
        </div>
        <div class="form-group required-field not-required">
          <label>{{ _ix('Seller ID','To-Dos') }}</label>
          <text-input
            v-model="todo.sellerId"
            :disabled="isDisabled('sellerId')"
            :length="20"
          ></text-input>
          <validation-error
            path="sellerId"
            :errors="validationErrors"
          ></validation-error>
        </div>
        <div class="form-group required-field">
          <label>{{ _ix('Daily limit for projects','To-Dos') }}</label>
          <text-input
            type="number"
            v-model="todo.dailyLimit"
            :min="1"
          ></text-input>
          <validation-error
            path="dailyLimit"
            :errors="validationErrors"
          ></validation-error>
        </div>
        <div class="form-group required-field">
          <label>{{ _ix('Number of similar projects','To-Dos') }}</label>
          <text-input
            type="number"
            v-model="todo.projects"
            :disabled="isDisabled('projects')"
            :min="1"
            @input="updatePrice()"
          ></text-input>
          <validation-error
            path="projects"
            :errors="validationErrors"
          ></validation-error>
        </div>
        <div class="form-group required-field">
          <label>{{ _ix('Project title & description','To-Dos') }}</label>
          <dropdown
            v-model="todo.projectTitleDescription"
            :boolean="true"
            :disabled="isDisabled('projectTitleDescription')"
            :options="{
              'true':_ix('Yes','To-Dos'),
              'false':_ix('No','To-Dos')
            }"
          ></dropdown>
          <validation-error
            path="projectTitleDescription"
            :errors="validationErrors"
          ></validation-error>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-12">
        <div class="form-group required-field not-required">
          <label>{{ _ix('Notes','To-Dos') }}</label>
          <textarea-input v-model="todo.notes"></textarea-input>
        </div>
      </div>
    </div>
    <div
      class="price-footer"
      slot="footer"
      v-if="price"
    >
      <p>
        <b>{{ sprintf(_ix('Price: RMB %s','To-Dos'),decNumber(price)) }}</b>
      </p>
      <p
        class="price-formula"
        v-if="priceBreakdown"
      >
        {{ _ix('Price formula:','To-Dos') }}
        {{ intNumber(priceBreakdown.projects) }}
        * (
        {{ decNumber(priceBreakdown.variable) }}
        *
        <span class="price">{{ decNumber(priceBreakdown.productPrice) }}</span>
        +
        {{ decNumber(priceBreakdown.fixed) }}
        )
      </p>
      <div
        class="text-right"
        v-if="huistoreGatewayFunds!==null"
      >
        {{ _ix('Pay using:','To-Dos') }}
        <label>
          <checkbox
            radio
            v-model="alipayGateway"
          />
          {{ _ix('Alipay','To-Dos') }}
        </label>
        <label>
          <checkbox
            radio
            v-model="huistoreGateway"
            :disabled="huistoreGatewayDisabled"
          />
          {{ _ix('huistore.com balance','To-Dos') }}
        </label>
      </div>
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
  import TagInput from 'common/controls/TagInput'

  export default {
    components:{
      TagInput,
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
      },
      guessUrl:{
        type:String,
        required:true
      },
      marketplaces:{
        type:Object,
        required:true
      }
    },
    data(){
      return {
        todo:{},
        disabledFields:[],
        paymentRequired:false,
        huistoreGatewayFunds:null,
        saveUrl:null,
        storefrontUrlRequired:false,
        hideCode:true,
        priceUrl:null,
        storefronts:null,
        codeHash:null,
        price:null,
        priceBreakdown:null,
        huistoreGateway:false,
        huistoreGatewayDisabled:false,
        create:false,
        saving:false,
        saveError:false,
        validationErrors:{}
      }
    },
    computed:{
      title(){
        if(this.create)
          return _ix('Create to-do','To-Dos')
        else
          return _ix('Edit to-do','To-Dos')
      },
      fbm:{
        get(){
          return !this.todo.fba
        },
        set(fbm){
          this.todo.fba= !fbm
        }
      },
      storefrontUrls(){
        const urls={}
        this.storefronts.forEach(storefront=>{
          urls[storefront.url]=storefront.marketplaceSellerId
        })

        return urls
      },
      storefrontNames(){
        if(this.todo.storefrontUrl)
          for(let i in this.storefronts){
            const storefront=this.storefronts[i]
            const {
              url,
              names
            }=storefront
            if(this.todo.storefrontUrl===url){
              const namesForUrl={}
              names.forEach(name=>{
                namesForUrl[name]=name
              })
              return namesForUrl
            }
          }

        return {}
      },
      saveButtonEnabled(){
        if(!this.saveUrl)
          return false
        if(!this.paymentRequired)
          return true
        else
          return Boolean(this.price)
      },
      saveButtonText(){
        if(this.paymentRequired)
          if(!this.huistoreGateway)
            return _ix('Pay now','To-Dos')
          else
            return _ix('Pay now with balance','To-Dos')
        else
          return null
      },
      alipayGateway:{
        get(){
          return !this.huistoreGateway
        },
        set(alipayGateway){
          this.huistoreGateway= !alipayGateway
        }
      }
    },
    watch:{
      price(price){
        if(this.huistoreGatewayFunds==null)
          return

        this.huistoreGatewayDisabled=price>this.huistoreGatewayFunds

        if(this.huistoreGatewayDisabled)
          this.huistoreGateway=false
      }
    },
    created(){
      axios.post(this.loadUrl)
        .then(response=>{
          this.loadFromData(response.data)
          this.create= !response.data.todo.id

          this.updatePrice()
        })
    },
    methods:{
      loadFromData(data){
        this.todo=data.todo
        this.disabledFields=data.disabledFields
        this.paymentRequired=data.paymentRequired
        this.huistoreGatewayFunds=data.huistoreGatewayFunds
        this.saveUrl=data.saveUrl
        this.storefrontUrlRequired=data.storefrontUrlRequired
        this.hideCode=data.hideCode
        this.priceUrl=data.priceUrl
        this.storefronts=data.storefronts||null
        this.codeHash=data.codeHash
      },
      save(){
        this.saving=true
        this.saveError=false
        this.validationErrors={}

        const params=Object.assign({},this.todo,{
          price:this.price,
          codeHash:this.codeHash,
          huistoreGateway:this.huistoreGateway
        })
        axios.post(this.saveUrl,params)
          .then(response=>{
            if(response.data.url)
              location.href=response.data.url
            else{
              this.saving=false

              this.$emit('close')
              this.$emit('save')
            }
          })
          .catch(error=>{
            this.saving=false
            this.saveError=true
            this.validationErrors=error.response.data.errors||{}
          })
      },
      updatePrice(){
        if(!this.paymentRequired)
          return

        this.$nextTick(()=>{
          const params={
            projects:this.todo.projects,
            productPrice:this.todo.productPrice,
            marketplace:this.todo.marketplace
          }
          axios.post(this.priceUrl,params)
            .then(response=>{
              this.price=response.data.price
              this.priceBreakdown=response.data.priceBreakdown
            })
        })
      },
      guessFromProductUrl(productUrl){
        const params={
          input:'product',
          productUrl
        }
        axios.post(this.guessUrl,params)
          .then(response=>{
            const {
              asin,
              marketplace
            }=response.data

            this.$set(this.todo,'productAsin',asin)
            this.$set(this.todo,'marketplace',marketplace)

            this.updatePrice()
          })
      },
      guessFromStorefrontUrl(storefrontUrl){
        const params={
          input:'store',
          storefrontUrl
        }
        axios.post(this.guessUrl,params)
          .then(response=>{
            const {
              sellerId,
              marketplace
            }=response.data

            this.$set(this.todo,'sellerId',sellerId)
            this.$set(this.todo,'marketplace',marketplace)

            this.updatePrice()
          })
      },
      isDisabled(field){
        return this.disabledFields.indexOf(field)!== -1
      }
    }
  }
</script>
