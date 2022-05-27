<template>
  <div class="progress todo-progress">
    <div
      class="progress-bar todo-progress-bar-completed"
      :style="{
        width:width('completed')
      }"
    ></div>
    <div
      class="progress-bar todo-progress-bar-unassigned"
      :style="{
        width:width('unassigned')
      }"
    ></div>
    <div
      class="progress-bar progress-bar-warning"
      :style="{
        width:width('others')
      }"
    ></div>
    <div
      class="progress-bar progress-bar-danger"
      :style="{
        width:width('failed')
      }"
    ></div>
  </div>
</template>
<script>
  export default {
    props:{
      values:{
        type:Object,
        required:true
      }
    },
    computed:{
      total(){
        return this.values.completed+this.values.unassigned+this.values.others+this.values.failed
      }
    },
    methods:{
      width(item){
        // Precondition: total is at least 1
        return (100*this.values[item]/this.total)+'%'
      }
    }
  }
</script>
