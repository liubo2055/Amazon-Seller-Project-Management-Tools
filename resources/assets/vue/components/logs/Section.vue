<template>
  <div>
    <filters
      :filter-fields="viewFilterFields"
      @change="change($event.filters)"
    ></filters>
    <div
      class="log-buttons"
      v-if="selection"
    >
      <form
        method="POST"
        :action="downloadUrl"
      >
        <button
          class="btn btn-primary"
          type="button"
          @click="load()"
        >
          <i class="fa fa-repeat"></i>
          {{ _ix('Reload','Log Viewer') }}
        </button>
        <input
          name="_token"
          type="hidden"
          :value="csrf"
        >
        <input
          name="file"
          type="hidden"
          :value="file"
        >
        <button class="btn btn-default">
          <i class="fa fa-download"></i>
          {{ _ix('Download','Log Viewer') }}
        </button>
      </form>
    </div>
    <view-comp
      v-if="selection"
      :html="html"
    ></view-comp>
  </div>
</template>
<script>
  import Filters from 'common/filters/Filters'
  import ViewComp from './View'

  export default {
    components:{
      Filters,
      ViewComp
    },
    props:{
      loadUrl:{
        type:String,
        required:true
      },
      filterFields:{
        type:Array,
        required:true
      },
      filterFieldsWithoutSearch:{
        type:Array,
        required:true
      },
      downloadUrl:{
        type:String,
        required:true
      },
      csrf:{
        type:String,
        required:true
      }
    },
    data(){
      return {
        html:'',
        file:null,
        view:null
      }
    },
    computed:{
      selection(){
        return this.file!==null&&this.view!==null
      },
      viewFilterFields(){
        if(this.view!=='search')
          return this.filterFieldsWithoutSearch
        else
          return this.filterFields
      }
    },
    methods:{
      change(filters){
        this.file=filters.file||null
        this.view=filters.view||null
        this.search=filters.search||null

        this.load()
      },
      load(){
        if(!this.selection)
          return

        const params={
          file:this.file,
          view:this.view,
          search:this.search
        }

        axios.post(this.loadUrl,params)
          .then(response=>{
            this.html=response.data
          })
      }
    }
  }
</script>
