<template>
  <div
    class="modal fade"
    ref="modal"
    tabindex="-1"
  >
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <button
            class="close"
            data-dismiss="modal"
            type="button"
            :disabled="saving"
          >
            <span>&times;</span>
          </button>
          <h4 class="modal-title">{{ title }}</h4>
        </div>
        <div class="modal-body">
          <slot></slot>
        </div>
        <div class="modal-footer">
          <slot name="footer"></slot>
          <button
            class="btn btn-default"
            data-dismiss="modal"
            type="button"
            :disabled="saving"
          >
            {{ _ix('Close','Popup') }}
          </button>
          <button
            class="btn btn-primary"
            type="button"
            v-if="saveButton"
            :disabled="saving"
            @click="$emit('save')"
          >
            <i
              class="fa fa-circle-o-notch fa-spin fa-fw"
              v-if="saving"
            ></i>
            {{ saveButtonText||_ix('Save','Popup') }}
          </button>
          <p
            class="text-danger"
            v-if="saveError"
          >
            <i class="fa fa-exclamation-triangle"></i>
            {{ _ix('Save error','Popup') }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
  export default {
    props:{
      title:{
        type:String,
        required:true
      },
      saveButton:{
        type:Boolean
      },
      saveError:{
        type:Boolean
      },
      saveButtonText:{
        type:String
      },
      saving:{
        type:Boolean
      }
    },
    mounted(){
      $(this.$refs.modal).modal({
        backdrop:'static',
        keyboard:false
      })
      $(this.$refs.modal).on('hidden.bs.modal',()=>{
        this.$emit('close')
      })
    },
    beforeDestroy(){
      $(this.$refs.modal).modal('hide')
    }
  }
</script>
