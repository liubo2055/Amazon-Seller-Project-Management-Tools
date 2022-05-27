<template>
  <div>
    <edit-popup
      v-if="editUrl"
      :guess-url="urls.guess"
      :load-url="editUrl"
      :marketplaces="marketplaces"
      @close="edit(null)"
      @save="reloadTable()"
    />
    <payment-popup
      v-if="paymentsUrl"
      :load-url="paymentsUrl"
      @close="viewPayments(null)"
    />
    <header-comp
      :urls="urls"
      @create="edit($event)"
    />
    <filters
      :filter-fields="filterFields"
      @change="filters=$event.filters"
      @initialize="initialize($event.filters)"
    />
    <table-comp
      :columns="columns"
      :filters="filters"
      :initial-size="initialSize"
      :load="loadTable"
      :url="urls.list"
      @loaded="loadTable=false"
      @rowEvent="rowEvent($event)"
    />
  </div>
</template>
<script>
  import Filters from 'common/filters/Filters'
  import EditPopup from './EditPopup'
  import PaymentPopup from './PaymentPopup'
  import HeaderComp from './Header'
  import TableComp from './Table'

  export default {
    components:{
      Filters,
      EditPopup,
      PaymentPopup,
      HeaderComp,
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
      marketplaces:{
        type:Object,
        required:true
      },
      filterFields:{
        type:Array,
        required:true
      }
    },
    data(){
      return {
        editUrl:null,
        paymentsUrl:null,
        loadTable:false,
        filters:{}
      }
    },
    methods:{
      rowEvent(event){
        const {
          action,
          url
        }=event

        switch(action){
          case 'edit':
          case 'clone':
            this.edit(url)
            break
          case 'payments':
            this.viewPayments(url)
            break
          case 'complete':
          case 'reopen':
          case 'confirm':
            this.action(url)
            break
          case 'pay':
            this.pay(url)
            break
          case 'delete':
            this.action(url,'delete')
            break
        }
      },
      edit(editUrl){
        this.editUrl=editUrl
      },
      viewPayments(paymentsUrl){
        this.paymentsUrl=paymentsUrl
      },
      action(url,method='post'){
        axios[method](url)
          .then(()=>{
            this.reloadTable()
          })
      },
      pay(url){
        axios.post(url)
          .then(response=>{
            const url=response.data.url
            if(url)
              location.href=url
            else
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
