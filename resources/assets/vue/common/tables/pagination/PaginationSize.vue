<template>
  <div class="form-inline">
    {{ _ix('Show','Table') }}
    <dropdown
      :options="options"
      :disable-placeholder="true"
      :value="size"
      @change="setSize(~~$event.value,$event.initial)"
    ></dropdown>
    {{ _ix('per page','Table') }}
  </div>
</template>
<script>
  import Dropdown from 'common/controls/Dropdown'

  export default {
    components:{Dropdown},
    props:{
      initialSize:{
        type:Number,
        required:true
      }
    },
    data(){
      return {
        size:this.initialSize
      }
    },
    computed:{
      options(){
        const sizes=[
          10,
          20,
          50,
          100,
          200
        ]
        const options={}

        for(let i=0; i<sizes.length; i++)
          options[sizes[i]]=sizes[i]

        return options
      }
    },
    methods:{
      setSize(size,initial){
        if(!initial)
          this.$emit('size',{size})
      }
    }
  }
</script>
