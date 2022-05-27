<template>
  <div>
    <filters
      :filter-fields="filterFields"
      @change="filters=$event.filters"
      @initialize="initialize($event.filters)"
    ></filters>
    <export-buttons
      :csrf="csrf"
      :filters="filtersJson"
      :url="urls.download"
    ></export-buttons>
    <table-comp
      :columns="columns"
      :filters="filters"
      :initial-size="initialSize"
      :load="loadTable"
      :url="urls.list"
      @loaded="loadTable=false"
      @rowEvent="rowEvent($event)"
    ></table-comp>
  </div>
</template>
<script>
  import Filters from 'common/filters/Filters'
  import TableComp from './Table'
  import ExportButtons from './ExportButtons'

  export default {
    components:{
      Filters,
      ExportButtons,
      TableComp
    },
    props:{
      urls:{
        type:Object,
        required:true
      },
      columns:{
        type:Array,
        required:true
      },
      initialSize:{
        type:Number,
        required:true
      },
      filterFields:{
        type:Array,
        required:true
      },
      csrf:{
        type:String,
        required:true
      }
    },
    data(){
      return {
        loadTable:false,
        filters:{}
      }
    },
    computed:{
      filtersJson(){
        return JSON.stringify(this.filters)
      }
    },
    methods:{
      initialize(filters){
        this.filters=filters
        this.loadTable=true
      }
    }
  }
</script>
