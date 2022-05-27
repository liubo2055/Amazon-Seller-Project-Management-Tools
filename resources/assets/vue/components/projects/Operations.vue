<template>
  <div>
    <button
      class="btn btn-link"
      type="button"
      v-if="operations.view"
      @click="$emit('rowEvent',{
        action:'view',
        url:operations.view.url
      })"
    >
      {{ _ix('View details','Projects') }}
    </button>
    <button
      class="btn btn-link"
      type="button"
      v-if="operations.accept&&!operations.accept.limitReached"
      @click="$emit('rowEvent',{
        action:'accept',
        url:operations.accept.url
      })"
    >
      {{ _ix('Accept','Projects') }}
    </button>
    <div
      class="text text-danger"
      v-if="operations.accept&&operations.accept.limitReached"
    >
      {{ _ix('Daily limit reached','Projects') }}
    </div>
    <button
      class="btn btn-link"
      type="button"
      v-if="operations.submitId"
      @click="$emit('rowEvent',{
        action:'submitId',
        saveUrl:operations.submitId.saveUrl
      })"
    >
      {{ _ix('Submit completion ID','Projects') }}
    </button>
    <button
      class="btn btn-link"
      type="button"
      v-if="operations.submitUrl"
      @click="$emit('rowEvent',{
        action:'submitUrl',
        saveUrl:operations.submitUrl.saveUrl
      })"
    >
      {{ _ix('Submit completion URL','Projects') }}
    </button>
    <button
      class="btn btn-link"
      type="button"
      v-if="operations.editTodoAttrs"
      @click="$emit('rowEvent',{
        action:'editTodoAttrs',
        url:operations.editTodoAttrs.url
      })"
    >
      {{ _ix('Edit','Projects') }}
    </button>
    <button
      class="btn btn-link"
      type="button"
      v-if="operations.editProjectAttrs"
      @click="$emit('rowEvent',{
        action:'editProjectAttrs',
        url:operations.editProjectAttrs.url
      })"
    >
      {{ _ix('Submit description','Projects') }}
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
      {{ _ix('Confirm','Projects') }}
    </button>
    <button
      class="btn btn-link"
      type="button"
      v-if="operations.fail"
      @click="$emit('rowEvent',{
        action:'fail',
        url:operations.fail.url
      })"
    >
      {{ _ix('Fail','Projects') }}
    </button>
    <button
      class="btn btn-link"
      type="button"
      v-if="operations.cancel"
      @click="$emit('rowEvent',{
        action:'cancel',
        url:operations.cancel.url
      })"
    >
      {{ _ix('Cancel','Projects') }}
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
      {{ _ix('Delete','Projects') }}
    </button>
    <button
      class="btn btn-link"
      type="button"
      v-if="operations.restore"
      @click="$emit('rowEvent',{
        action:'restore',
        url:operations.restore.url
      })"
    >
      {{ _ix('Restore','Projects') }}
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
