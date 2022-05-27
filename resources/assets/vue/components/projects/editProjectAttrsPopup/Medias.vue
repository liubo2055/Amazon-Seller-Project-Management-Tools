<template>
  <div class="row">
    <div
      class="col-sm-3"
      v-for="(media,index) in medias"
      :key="media.id"
    >
      <textarea-input
        :length="255"
        :value="media.content"
        @input="change(index,$event)"
      ></textarea-input>
      <button
        class="btn btn-warning btn-xs"
        type="button"
        @click="$emit('delete',{index})"
      >
        {{ _ix('Delete','Projects') }}
      </button>
    </div>
    <div class="col-sm-3">
      <button
        class="btn btn-primary"
        type="button"
        @click="$emit('add')"
      >
        {{ _ix('Add media','Projects') }}
      </button>
    </div>
  </div>
</template>
<script>
  import TextareaInput from 'common/controls/TextareaInput'

  export default {
    components:{TextareaInput},
    props:{
      medias:{
        type:Array,
        required:true
      }
    },
    methods:{
      change(index,content){
        this.$emit('change',{
          index,
          content
        })
      }
    }
  }
</script>
