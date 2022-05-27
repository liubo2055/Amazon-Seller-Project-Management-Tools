<template>
  <popup
    :title="_ix('View similar projects','Projects')"
    @close="$emit('close')"
  >
    <div v-for="(attr,index) in attrs">
      <hr v-if="index>0">
      <h3>{{ sprintf(_ix('Projects with the same %s: "%s"','Projects'),
        attr.name,
        attr.value) }}</h3>
      <table-comp
        :columns="columns"
        :initial-size="10"
        :load="true"
        :url="attr.url"
      ></table-comp>
    </div>
  </popup>
</template>
<script>
  import Popup from 'common/components/Popup'
  import TableComp from './Table'

  export default {
    components:{
      Popup,
      TableComp
    },
    props:{
      loadUrl:{
        type:String,
        required:true
      },
      columns:{
        type:Array,
        required:true
      }
    },
    data(){
      return {
        attrs:[]
      }
    },
    created(){
      axios.post(this.loadUrl)
        .then(response=>{
          this.attrs=response.data
        })
    }
  }
</script>
