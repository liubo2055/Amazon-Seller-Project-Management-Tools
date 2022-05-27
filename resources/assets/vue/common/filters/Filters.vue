<template>
  <div class="filters">
    <div :class="{
      row:rowColumnsClass
    }">
      <filter-comp
        v-for="field in filterFields"
        :checkboxes="field.checkboxes"
        :component="field.component"
        :component-data="field.componentData"
        :help-text="field.helpText"
        :initial-value="field.value"
        :key="field.name"
        :length="field.length"
        :max="field.max"
        :min="field.min"
        :name="field.name"
        :options="field.options"
        :radios="field.radios"
        :range="field.range"
        :row-columns-class="rowColumnsClass"
        :subtitles="field.subtitles"
        :title="field.title"
        :type="field.type"
        @change="change(field.name,$event.value)"
      ></filter-comp>
    </div>
    <div
      class="row"
      v-if="filterButton"
    >
      <div :class="{
        'col-sm-3':rowColumnsClass
      }">
        <button
          class="btn btn-primary"
          type="button"
          @click="filter"
        >
          {{ filterButtonText||_ix('Filter','Filter') }}
        </button>
      </div>
    </div>
    <hr>
  </div>
</template>
<script>
  import FilterComp from './Filter'

  export default {
    components:{FilterComp},
    props:{
      filterFields:{
        type:Array,
        required:true
      },
      filterButton:{
        type:Boolean
      },
      filterButtonText:{
        type:String
      },
      rowColumnsClass:{
        type:Boolean,
        default:true
      },
      shouldFilter:{
        type:Function
      }
    },
    data(){
      return {
        filters:{},
        filtered:false
      }
    },
    mounted(){
      const filters={}
      this.filterFields.forEach(field=>{
        if(field.value){
          filters[field.name]=field.value
        }
      })

      this.filters=filters
      this.$emit('initialize',{filters})
    },
    methods:{
      change(name,value){
        if(value!==null)
          this.filters=Object.assign({},this.filters,{
            [name]:value
          })
        else
          this.filters=_.omit(this.filters,name)

        this.$emit('update',{
          filters:this.filters,
          name
        })

        // If there's a filter button, don't emit the event unless it has already been clicked
        if(!this.filterButton||this.filtered)
          if(!this.shouldFilter||this.shouldFilter(this.filters,name))
            this.filter(name)
      },
      filter(name){
        this.$emit('change',{
          filters:this.filters,
          name
        })

        this.filtered=true
      }
    }
  }
</script>
