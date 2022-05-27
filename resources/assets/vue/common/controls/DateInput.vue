<template>
  <input
    class="form-control"
    ref="input"
    :maxlength="10"
    :placeholder="placeholder"
    :value="value"
    @blur="blur()"
    @change="change()"
  >
</template>
<script>
  import Pikaday from 'pikaday'
  import moment from 'moment'

  export default {
    props:{
      focus:{
        type:Boolean
      },
      min:{
        type:String
      },
      max:{
        type:String
      },
      value:{},
      placeholder:{
        type:String
      }
    },
    data(){
      return {
        focused:false,
        pikaday:null
      }
    },
    watch:{
      min(min){
        this.pikaday.setMinDate(moment(min,'YYYY-MM-DD').toDate())
      },
      max(max){
        this.pikaday.setMaxDate(moment(max,'YYYY-MM-DD').toDate())
      }
    },
    mounted(){
      const vm=this
      this.pikaday=new Pikaday({
        field:this.$refs.input,
        format:'YYYY-MM-DD',
        firstDay:1,
        minDate:moment(this.min,'YYYY-MM-DD').toDate(),
        maxDate:moment(this.max,'YYYY-MM-DD').toDate(),
        onSelect(){
          vm.change()
        }
      })

      if(this.focus){
        this.$refs.input.focus()
        this.focused=true
      }
    },
    methods:{
      blur(){
        if(!this.focus||this.focused)
          this.$emit('blur')
      },
      change(){
        let value
        if(this.$refs.input.value===''||this.pikaday===null)
          value=null
        else
          value=this.pikaday.toString('YYYY-MM-DD')

        this.$emit('input',value)
      }
    }
  }
</script>
