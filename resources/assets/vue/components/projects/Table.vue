<template>
  <div>
    <table-base
      default-order="dates|desc"
      :columns="columns"
      :filters="filters"
      :initial-size="initialSize"
      :load="load"
      :row-component="Row"
      :row-component-data="{csrf}"
      :url="url"
      @loaded="$emit('loaded')"
      @rowEvent="$emit('rowEvent',$event)"
    ></table-base>
  </div>
</template>
<script>
  import TableBase from 'common/tables/TableBase'
  import Row from './Row'

  export default {
    components:{TableBase},
    props:{
      columns:{
        type:Array,
        required:true
      },
      url:{
        type:String,
        required:true
      },
      initialSize:{
        type:Number,
        required:true
      },
      load:{
        type:Boolean,
        required:true
      },
      filters:{
        type:Object,
        required:true
      },
      csrf:{
        type:String
      }
    },
    data(){
      return {Row}
    }
  }
</script>
