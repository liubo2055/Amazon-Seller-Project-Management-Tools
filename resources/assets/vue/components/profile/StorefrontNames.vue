<template>
  <div>
    <div
      class="row"
      v-for="(name,index) in value"
    >
      <div class="col-sm-2">
        <button
          class="btn btn-link"
          type="button"
          v-if="value.length>1"
          @click="remove(index)"
        >
          <i class="fa fa-minus-circle"></i>
        </button>
      </div>
      <div class="col-sm-10">
        <text-input
          :length="255"
          :value="name"
          @input="update(index,$event)"
        ></text-input>
        <validation-error
          :errors="errors"
          :path="errorPath(index)"
        ></validation-error>
      </div>
    </div>
    <button
      class="btn btn-default"
      type="button"
      @click="add()"
    >
      <i class="fa fa-plus-circle"></i>
      {{ _ix('Add','Profile') }}
    </button>
  </div>
</template>
<script>
  import ValidationError from 'common/components/ValidationError'
  import TextInput from 'common/controls/TextInput'

  export default {
    components:{
      ValidationError,
      TextInput
    },
    props:{
      value:{
        type:Array,
        required:true
      },
      errors:{
        type:Object,
        required:true
      },
      errorPathBase:{
        type:Array,
        required:true
      }
    },
    methods:{
      add(){
        const names=this.value
        names.push('')

        this.$emit('input',names)
      },
      remove(index){
        const names=this.value
        names.splice(index,1)

        this.$emit('input',names)
      },
      update(index,value){
        const names=this.value
        names[index]=value

        this.$emit('input',names)
      },
      errorPath(index){
        return this.errorPathBase.concat(index)
      }
    }
  }
</script>
