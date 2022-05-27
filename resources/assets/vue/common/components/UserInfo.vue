<template>
  <div class="user-info">
    <button
      class="btn btn-link user-info-name"
      @click="showDetails=!showDetails"
    >
      <span
        class="label label-info"
        v-if="label"
      >
        {{ label }}
      </span>
      &nbsp;
      <span v-if="!bold">{{ user.name }}</span>
      <b v-if="bold">{{ user.name }}</b>
    </button>
    <template v-if="showDetails">
      <p v-if="user.qq">
        <i class="fa fa-qq"></i>
        {{ user.qq }}
      </p>
      <p v-if="user.phone">
        <i class="fa fa-phone"></i>
        {{ user.phone }}
      </p>
      <p v-if="user.email">
        <i class="fa fa-envelope"></i>
        {{ user.email }}
      </p>
    </template>
  </div>
</template>
<script>
  export default {
    props:{
      user:{
        type:Object,
        required:true
      },
      label:{
        type:String
      },
      bold:{
        type:Boolean
      }
    },
    data(){
      return {
        showDetails:false
      }
    }
  }
</script>
