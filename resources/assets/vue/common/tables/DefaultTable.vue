<template>
  <div>
    <filters
      :filter-fields="filterFields"
      @change="change($event.filters)"
    ></filters>
    <table-base
      :default-order="defaultOrder"
      :columns="columns"
      :filters="filters"
      :initial-size="initialSize"
      :row-component="Row"
      :url="url"
    ></table-base>
  </div>
</template>
<script>
  import TableBase from './TableBase'
  import Filters from 'common/filters/Filters'
  import Row from './rows/Row'

  export default {
    components:{
      Filters,
      TableBase
    },
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
      defaultOrder:{
        type:String,
        required:true
      },
      filterFields:{
        type:Array
      }
    },
    data(){
      return {
        Row,
        filters:{}
      }
    },
    methods:{
      change(filters){
        this.filters=filters

        this.$emit('filter',filters)

        if(typeof window.tableFilter==='function')
          window.tableFilter(filters)
      }
    }
  }
</script>
