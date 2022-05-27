<template>
  <table :class="{
    loading,
    table:true
  }">
    <colgroup>
      <col
        v-for="column in columns"
        :style="{
          width:column.width+'%'
        }"
      >
    </colgroup>
    <thead>
    <tr>
      <th
        v-for="column in columns"
        :class="{
          order:!column.notSortable,
          [column.class||'']:true
        }"
        @click="!column.notSortable&&setOrder(column.code)"
      >
        {{ column.name }}
        <span
          v-if="column.code===order"
          :class="[
            'glyphicon',
            ascOrder?'glyphicon-menu-up':'glyphicon-menu-down'
          ]"
        ></span>
      </th>
    </tr>
    </thead>
    <tbody>
    <tr v-if="!rows.length&&!loading">
      <td :colspan="columns.length">
        <span>{{ _ix('No items found','Table') }}</span>
      </td>
    </tr>
    <tr v-if="loading">
      <td :colspan="columns.length">
        <span>
          <i class="fa fa-spinner fa-pulse"></i>
          {{ _ix('Loading items...','Table') }}
        </span>
      </td>
    </tr>
    <component
      v-for="row in rows"
      :columns="columns"
      :component-data="rowComponentData"
      :is="rowComponent"
      :key="row.meta.id"
      :row="row"
      @rowEvent="$emit('rowEvent',$event)"
    ></component>
    </tbody>
    <tfoot>
    <tr>
      <td :colspan="columns.length">
        <pagination
          v-if="pages"
          :page="page"
          :pages="pages"
          @page="setPage($event.page)"
        ></pagination>
        <p>{{ sprintf(_ix('%s items found','Table'),intNumber(items)) }}</p>
        <pagination-size
          :initial-size="initialSize"
          @size="setSize($event.size)"
        ></pagination-size>
      </td>
    </tr>
    </tfoot>
  </table>
</template>
<script>
  import Pagination from './pagination/Pagination'
  import PaginationSize from './pagination/PaginationSize'

  export default {
    components:{
      Pagination,
      PaginationSize
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
      rowComponent:{
        type:Object,
        required:true
      },
      rowComponentData:{
        type:Object
      },
      filters:{
        type:Object
      },
      extraLoadingParams:{
        type:Object
      },
      load:{
        type:Boolean,
        required:true
      }
    },
    data(){
      return {
        state:{
          pages:0,
          items:0,
          rows:[]
        },
        loading:false,
        page:1,
        order:this.defaultOrder.split('|')[0],
        ascOrder:this.defaultOrder.split('|')[1]==='asc',
        extraRows:[],
        size:this.initialSize
      }
    },
    computed:{
      pages(){
        return this.state.pages
      },
      items(){
        return this.state.items
      },
      rows(){
        return this.extraRows.concat(this.loadedRows)
      },
      loadedRows(){
        return this.state.rows
      }
    },
    watch:{
      load(load){
        if(load)
          this.reload()
      },
      loading(loading){
        if(!loading)
          return

        axios.post(this.url,this.loadParams())
          .then(response=>{
            this.state=response.data
            this.loading=false
            this.$emit('loaded')
          })
      },
      filters(){
        this.reload()
      }
    },
    mounted(){
      if(this.load)
        this.reload()
    },
    methods:{
      loadParams(){
        const commonParams={
          page:this.page,
          size:this.size,
          order:this.order,
          ascOrder:this.ascOrder,
          filters:this.filters
        }

        const extraParamas=this.extraLoadingParams||{}

        return Object.assign({},commonParams,extraParamas)
      },
      setOrder(column){
        if(this.order===column)
          this.ascOrder= !this.ascOrder
        else{
          this.order=column
          this.ascOrder=false
        }
        this.reload()
      },
      setPage(page){
        if(this.page!==page){
          this.page=page
          this.reload()
        }
      },
      setSize(size){
        this.size=size
        this.page=1
        this.reload()
      },
      addExtraRow(row){
        this.extraRows.push(row)
      },
      deleteExtraRows(){
        this.extraRows=[]
      },
      reload(){
        this.loading=true
      }
    }
  }
</script>
