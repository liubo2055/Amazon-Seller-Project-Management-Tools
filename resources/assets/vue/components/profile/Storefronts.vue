<template>
  <div class="storefronts">
    <div
      class="storefront"
      v-for="(storefront,index) in value"
    >
      <div class="row">
        <div class="col-sm-1">
          <button
            class="btn btn-link"
            type="button"
            @click="remove(index)"
          >
            <i class="fa fa-minus-circle"></i>
          </button>
        </div>
        <div class="col-sm-11">
          <div class="form-group required-field">
            <label>{{ _ix('URL','Profile') }}</label>
            <text-input
              :length="255"
              :value="storefront.url"
              @input="update(index,'url',$event)"
            ></text-input>
            <validation-error
              :errors="visibleErrors"
              :path="[
                'storefronts',
                index,
                'url'
              ]"
            ></validation-error>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-1"></div>
        <div class="col-sm-11">
          <div class="form-group required-field">
            <label>{{ _ix('Names','Profile') }}</label>
            <storefront-names
              :error-path-base="[
                'storefronts',
                index,
                'names'
              ]"
              :errors="visibleErrors"
              :value="storefront.names"
              @input="update(index,'names',$event)"
            ></storefront-names>
          </div>
        </div>
      </div>
    </div>
    <button
      class="btn btn-default"
      type="button"
      @click="add()"
    >
      <i class="fa fa-plus-circle"></i>
      {{ _ix('Add','Profile') }}
    </button>
  </div>
</template>
<script>
  import ValidationError from 'common/components/ValidationError'
  import TextInput from 'common/controls/TextInput'
  import StorefrontNames from './StorefrontNames'

  export default {
    components:{
      ValidationError,
      TextInput,
      StorefrontNames
    },
    props:{
      value:{
        type:Array,
        required:true
      },
      errors:{
        type:Object,
        required:true
      }
    },
    data(){
      return {
        visibleErrors:this.errors
      }
    },
    watch:{
      errors(errors){
        this.visibleErrors=errors
      }
    },
    methods:{
      add(){
        const storefront={
          url:'',
          names:['']
        }

        this.change(this.value.concat(storefront))
      },
      remove(index){
        const storefronts=this.value
        storefronts.splice(index,1)

        this.change(storefronts)
      },
      update(index,attr,value){
        const storefronts=this.value
        storefronts[index][attr]=value

        this.change(storefronts)
      },
      change(storefronts){
        this.$emit('input',storefronts)

        this.visibleErrors={}
      }
    }
  }
</script>
