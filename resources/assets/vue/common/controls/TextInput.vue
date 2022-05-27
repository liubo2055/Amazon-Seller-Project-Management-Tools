<template>
  <input
    class="form-control"
    ref="text"
    :disabled="disabled"
    :maxlength="length"
    :placeholder="placeholder"
    :type="type||'text'"
    :value="currentValue"
    @blur="blur()"
    @change="change($event.target.value)"
  >
</template>
<script>
  export default {
    props:{
      focus:{
        type:Boolean
      },
      type:{
        type:String
      },
      length:{
        type:Number
      },
      min:{
        type:Number
      },
      max:{
        type:Number
      },
      value:{},
      placeholder:{
        type:String
      },
      language:{
        type:String
      },
      disabled:{
        type:Boolean
      }
    },
    data(){
      return {
        focused:false,
        currentValue:this.value
      }
    },
    watch:{
      value(value){
        this.currentValue=value
      },
      language(){
        this.currentValue=this.value
      }
    },
    mounted(){
      if(this.value!==undefined)
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

        if(this.type==='number')
          if((value+'').match(/^[0-9.]+$/)){
            value=parseFloat(value)

            let limit=null
            if(this.min!==null&&value<this.min)
              limit=this.min
            else if(this.max!==null&&value>this.max)
              limit=this.max

            if(limit!==null){
              value= ~~limit
              this.$refs.text.value=limit
            }
          }
          else
            value=null

        this.currentValue=value

        if(!initial)
          this.$emit('input',value)
        this.$emit('change',{
          value,
          initial
        })
      }
    }
  }
</script>
