<template>
  <div>
    <a
      class="btn btn-link"
      v-if="operations.projects"
      :href="operations.projects.url"
    >
      {{ _ix('View projects','To-Dos') }}
    </a>
    <button
      class="btn btn-link"
      type="button"
      v-if="operations.edit"
      @click="$emit('rowEvent',{
        action:'edit',
        url:operations.edit.url
      })"
    >
      {{ _ix('Edit','To-Dos') }}
    </button>
    <button
      class="btn btn-link"
      type="button"
      v-if="operations.clone"
      @click="$emit('rowEvent',{
        action:'clone',
        url:operations.clone.url
      })"
    >
      {{ _ix('Clone','To-Dos') }}
    </button>
    <button
      class="btn btn-link"
      type="button"
      v-if="operations.payments"
      @click="$emit('rowEvent',{
        action:'payments',
        url:operations.payments.url
      })"
    >
      {{ _ix('View payments','To-Dos') }}
    </button>
    <button
      class="btn btn-link"
      type="button"
      v-if="operations.complete"
      @click="$emit('rowEvent',{
        action:'complete',
        url:operations.complete.url
      })"
    >
      {{ _ix('Complete','To-Dos') }}
    </button>
    <button
      class="btn btn-link"
      type="button"
      v-if="operations.reopen"
      @click="$emit('rowEvent',{
        action:'reopen',
        url:operations.reopen.url
      })"
    >
      {{ _ix('Reopen','To-Dos') }}
    </button>
    <button
      class="btn btn-link"
      type="button"
      v-if="operations.confirm"
      @click="$emit('rowEvent',{
        action:'confirm',
        url:operations.confirm.url
      })"
    >
      {{ _ix('Confirm','To-Dos') }}
    </button>
    <button
      class="btn btn-link"
      type="button"
      v-if="operations.pay"
      @click="$emit('rowEvent',{
        action:'pay',
        url:operations.pay.url
      })"
    >
      {{ _ix('Pay again','To-Dos') }}
    </button>
    <button
      class="btn btn-link"
      type="button"
      v-if="operations.delete"
      @click="$emit('rowEvent',{
        action:'delete',
        url:operations.delete.url
      })"
    >
      {{ _ix('Delete','To-Dos') }}
    </button>
  </div>
</template>
<script>
  export default {
    props:{
      operations:{
        type:[
          Object,
          Array
        ],
        required:true
      }
    }
  }
</script>
