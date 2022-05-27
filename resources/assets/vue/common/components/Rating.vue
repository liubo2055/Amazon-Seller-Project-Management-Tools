<template>
  <div
    :class="{
      'rating-input':true,
      'rating-input-readonly':readonly,
      'rating-input-hover':currentRatingHover!==null
    }"
    :title="decNumber(value)"
  >
    <span
      v-for="i in 5"
      @click="change(i)"
      @mouseout="hover(null)"
      @mouseover="hover(i)"
    >
      <i :class="[
        'fa',
        starClass(i)
      ]"></i>
    </span>
  </div>
</template>
<script>
  export default {
    props:{
      value:{
        type:Number
      },
      readonly:{
        type:Boolean
      }
    },
    data(){
      return {
        currentRating:this.value||0,
        currentRatingHover:null
      }
    },
    watch:{
      value(value){
        this.currentRating=value
      }
    },
    methods:{
      change(rating){
        if(!this.readonly){
          this.currentRatingHover=null
          this.$emit('input',rating)
        }
      },
      hover(rating){
        if(!this.readonly)
          this.currentRatingHover=rating
      },
      starClass(index){
        const rating=this.currentRatingHover||this.currentRating

        if(index<=rating)
          return 'fa-star'
        // For example, if index is 2 and rating is 1.5, print a half-filled star
        else if(index-0.5<=rating)
          return 'fa-star-half-o'
        else
          return 'fa-star-o'
      }
    }
  }
</script>
