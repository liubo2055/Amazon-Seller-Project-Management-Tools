<template>
  <div class="slider-input">
    <div ref="slider"></div>
  </div>
</template>
<script>
  import noUiSlider from 'nouislider'
  import wNumb from 'wnumb'

  export default {
    props:{
      min:{
        type:Number,
        default:0
      },
      max:{
        type:Number,
        default:1000000
      },
      step:{
        type:Number,
        default:1
      }
    },
    mounted(){
      const slider=this.$refs.slider

      noUiSlider.create(slider,{
        start:[
          this.min,
          this.max
        ],
        range:{
          min:this.min,
          max:this.max
        },
        step:this.step,
        connect:true,
        tooltips:true,
        format:wNumb({
          decimals:0,
          thousand:','
        })
      })

      const vm=this
      slider.noUiSlider.on('set',function(values,handle,unencoded){
        let [
          min,
          max
        ]=unencoded

        vm.$emit('input',[
          ~~min,
          ~~max
        ])
      })
    }
  }
</script>
