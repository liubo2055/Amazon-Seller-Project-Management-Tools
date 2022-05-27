<template>
  <select
    class="form-control"
    ref="select"
    :disabled="disabled"
    @blur="blur()"
    @change="change($event.target.value)"
  >
    <option
      selected
      v-if="!disablePlaceholder"
      value=""
    >
      ...
    </option>
    <option
      v-for="(name,valueItem) in options"
      :selected="value!==null&&isValue(valueItem,value)"
      :value="valueItem"
    >
      {{ name }}
    </option>
  </select>
</template>
<script>
  export default {
    props:{
      focus:{
        type:Boolean
      },
      options:{
        type:[
          Object,
          Array
        ],
        required:true
      },
      disablePlaceholder:{
        type:Boolean
      },
      boolean:{
        type:Boolean
      },
      value:{},
      disabled:{
        type:Boolean
      }
    },
    data(){
      return {
        focused:false
      }
    },
    mounted(){
      if(this.value!==null)
        this.change(this.value,true)

      if(this.focus){
        this.$refs.text.focus()
        this.focused=true
      }
    },
    methods:{
      blur(){
        if(!this.focus||this.focused)
          this.$emit('blur')
      },
      change(value,initial){
        initial=initial||false

        if(value==='')
          value=null

        // If this.boolean is true, this.options must contain "false" and "true" as keys
        if(this.boolean)
          switch(value){
            case 'true':
              value=true
              break
            case 'false':
              value=false
              break
            default:
              value=null
          }

        if(!initial)
          this.$emit('input',value)
        this.$emit('change',{
          value,
          initial
        })
      },
      isValue(valueFromOptions,value){
        if(!this.boolean)
          return valueFromOptions==value
        else
          return value?valueFromOptions==='true':valueFromOptions==='false'
      }
    }
  }
</script>
