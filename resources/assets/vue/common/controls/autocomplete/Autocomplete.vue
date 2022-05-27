<template>
  <v-autocomplete
    v-model="currentValue"
    :component-item="Item"
    :disabled="disabled"
    :get-label="getLabel"
    :items="items"
    :min-len="1"
    @change="change($event)"
    @item-selected="select($event)"
    @update-items="updateItems($event)"
  />
</template>
<script>
  import Vue from 'vue'
  import Autocomplete from 'v-autocomplete'
  import Item from './Item'

  Vue.use(Autocomplete)

  export default {
    props:{
      options:{
        type:Array,
        required:true
      },
      disabled:{
        type:Boolean
      },
      value:{}
    },
    data(){
      return {
        Item,
        items:this.options,
        currentValue:this.initialValue()
      }
    },
    watch:{
      value(){
        this.currentValue=this.initialValue()
      }
    },
    methods:{
      initialValue(){
        return _.first(this.options.filter(option=>option.id===this.value))||null
      },
      getLabel(item){
        return item?item.name:null
      },
      change:_.debounce(function(text){
        this.currentValue=_.first(this.options.filter(option=>option.name===text||option.description===text))||null

        // When clearing the selection, the event will be emitted from here
        if(!this.currentValue)
          this.$emit('input',null)
      },300),
      select(item){
        this.$emit('input',item?item.id:null)
      },
      updateItems(text){
        if(typeof text!=='string')
          text=''
        const regexp=new RegExp(text.toLowerCase())

        this.items=this.options.filter(item=>{
          if(regexp.test(item.name.toLowerCase()))
            return true
          else
            return item.description&&regexp.test(item.description.toLowerCase())
        })
      }
    }
  }
</script>
<style>
  .v-autocomplete{
    position: relative;
  }

  .v-autocomplete .v-autocomplete-input-group .v-autocomplete-input{
    background-color: #eeeeee;
    border: 1px solid #157977;
    box-shadow: none;
    outline: none;
    padding: 5px;
    width: calc(100% - 32px);
    -webkit-box-shadow: none;
  }

  .v-autocomplete .v-autocomplete-input-group.v-autocomplete-selected .v-autocomplete-input{
    background-color: #f2fff2;
    color: green;
  }

  .v-autocomplete .v-autocomplete-list{
    border: 1px solid #157977;
    border-left-width: 0;
    border-right-width: 0;
    max-height: 400px;
    overflow-y: auto;
    position: absolute;
    text-align: left;
    width: 100%;
    z-index: 1;
  }

  .v-autocomplete .v-autocomplete-list .v-autocomplete-list-item{
    background-color: White;
    border: 1px solid #157977;
    border-top-width: 0;
    cursor: pointer;
    padding: 10px;
  }

  .v-autocomplete .v-autocomplete-list .v-autocomplete-list-item:hover{
    background-color: #eeeeee;
  }

  .v-autocomplete .v-autocomplete-list .v-autocomplete-list-item:last-child{
    border-bottom: none;
  }

  .v-autocomplete .v-autocomplete-list .v-autocomplete-list-item.v-autocomplete-item-active{
    background-color: #f3f6fa;
  }

  .v-autocomplete .v-autocomplete-list .v-autocomplete-list-item abbr{
    display: block;
    font-family: sans-serif;
    font-size: 0.80em;

    opacity: 0.80;
  }
</style>
