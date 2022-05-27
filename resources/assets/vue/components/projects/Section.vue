<template>
  <div>
    <view-popup
      v-if="viewUrl"
      :load-url="viewUrl"
      @close="view(null)"
    ></view-popup>
    <submit-id-popup
      v-if="submitIdSaveUrl"
      :save-url="submitIdSaveUrl"
      @close="submitId(null)"
      @save="reloadTable()"
    ></submit-id-popup>
    <submit-url-popup
      v-if="submitUrlSaveUrl"
      :save-url="submitUrlSaveUrl"
      @close="submitUrl(null)"
      @save="reloadTable()"
    ></submit-url-popup>
    <edit-todo-attrs-popup
      v-if="editTodoAttrsUrl"
      :load-url="editTodoAttrsUrl"
      @close="editTodoAttrs(null)"
      @save="reloadTable()"
    ></edit-todo-attrs-popup>
    <edit-project-attrs-popup
      v-if="editProjectAttrsUrl"
      :load-url="editProjectAttrsUrl"
      :upload-url="urls.upload"
      @close="editProjectAttrs(null)"
      @save="reloadTable()"
    ></edit-project-attrs-popup>
    <view-similar-popup
      v-if="viewSimilarUrl"
      :columns="similarProjectsColumns"
      :load-url="viewSimilarUrl"
      @close="viewSimilar(null)"
    ></view-similar-popup>
    <filters
      :filter-fields="filterFields"
      @change="filters=$event.filters"
      @initialize="initialize($event.filters)"
    ></filters>
    <table-comp
      :columns="columns"
      :csrf="csrf"
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
  import ViewPopup from './ViewPopup'
  import SubmitIdPopup from './SubmitIdPopup'
  import SubmitUrlPopup from './SubmitUrlPopup'
  import EditTodoAttrsPopup from './EditTodoAttrsPopup'
  import EditProjectAttrsPopup from './editProjectAttrsPopup/EditProjectAttrsPopup'
  import ViewSimilarPopup from './ViewSimilarPopup'
  import TableComp from './Table'

  export default {
    components:{
      Filters,
      ViewPopup,
      SubmitIdPopup,
      SubmitUrlPopup,
      EditTodoAttrsPopup,
      EditProjectAttrsPopup,
      ViewSimilarPopup,
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
      },
      similarProjectsColumns:{
        type:Array,
        required:true
      }
    },
    data(){
      return {
        viewUrl:null,
        submitIdSaveUrl:null,
        submitUrlSaveUrl:null,
        editTodoAttrsUrl:null,
        editProjectAttrsUrl:null,
        viewSimilarUrl:null,
        loadTable:false,
        filters:{}
      }
    },
    methods:{
      rowEvent(event){
        // url is the URL to perform an operation or load a popup; saveUrl is the URL to submit a popup to
        const {
          action,
          url,
          saveUrl
        }=event

        switch(action){
          case 'accept':
          case 'complete':
          case 'fail':
          case 'cancel':
          case 'restore':
            this.action(url)
            break
          case 'delete':
            this.action(url,'delete')
            break
          case 'view':
            this.view(url)
            break
          case 'submitId':
            this.submitId(saveUrl)
            break
          case 'submitUrl':
            this.submitUrl(saveUrl)
            break
          case 'editTodoAttrs':
            this.editTodoAttrs(url)
            break
          case 'editProjectAttrs':
            this.editProjectAttrs(url)
            break
          // This event is generated from the status column
          case 'similar':
            this.viewSimilar(url)
            break
        }
      },
      view(url){
        this.viewUrl=url
      },
      submitId(saveUrl){
        this.submitIdSaveUrl=saveUrl
      },
      submitUrl(saveUrl){
        this.submitUrlSaveUrl=saveUrl
      },
      editTodoAttrs(url){
        this.editTodoAttrsUrl=url
      },
      editProjectAttrs(url){
        this.editProjectAttrsUrl=url
      },
      viewSimilar(url){
        this.viewSimilarUrl=url
      },
      action(url,method='post'){
        axios[method](url)
          .then(()=>{
            this.reloadTable()
          })
      },
      reloadTable(){
        this.loadTable=true
      },
      initialize(filters){
        this.filters=filters
        this.loadTable=true
      }
    }
  }
</script>
