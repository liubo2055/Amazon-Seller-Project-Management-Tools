<template>
  <div :class="{
    'col-sm-3':rowColumnsClass,
    filter:true
  }">
    <p class="text-primary filter-title">{{ title }}</p>
    <div class="row filter-control">
      <template v-if="!component">
        <div
          class="col-sm-12"
          v-if="checkboxes"
        >
          <label v-for="(text,checkboxName) in checkboxes">
            <checkbox
              :value="initiallyChecked(checkboxName)"
              @input="toggleCheckboxValue(checkboxName,$event)"
            ></checkbox>
            {{ text }}
          </label>
          <help-text :text="helpText"></help-text>
        </div>
        <div
          class="col-sm-12"
          v-if="radios"
        >
          <label v-for="(text,radioName) in radios">
            <checkbox
              :name="name"
              :radio="true"
              :value="initialValue===radioName"
              @input="value=radioName"
            ></checkbox>
            {{ text }}
          </label>
          <help-text :text="helpText"></help-text>
        </div>
        <div
          class="col-sm-12"
          v-if="options"
        >
          <dropdown
            :options="options"
            :value="firstInitialValue"
            @change="value=$event.value"
          ></dropdown>
          <help-text :text="helpText"></help-text>
        </div>
        <template v-if="!options&&!checkboxes&&!radios">
          <div :class="[range?'col-sm-6':'col-sm-12']">
            <p
              class="filter-subtitle"
              v-if="range&&subtitles"
            >
              {{ subtitles[0] }}
            </p>
            <text-input
              v-if="type!=='date'"
              :length="length"
              :min="min"
              :max="firstMax"
              :type="type"
              :value="firstInitialValue"
              @change="value=$event.value"
            ></text-input>
            <date-input
              v-if="type==='date'"
              :min="min"
              :max="firstMax"
              :value="firstInitialValue"
              @input="value=$event"
            ></date-input>
            <help-text :text="firstHelpText"></help-text>
          </div>
          <div
            class="col-sm-6"
            v-if="range"
          >
            <p
              class="filter-subtitle"
              v-if="subtitles"
            >
              {{ subtitles[1] }}
            </p>
            <text-input
              v-if="type!=='date'"
              :length="length"
              :min="secondMin"
              :max="max"
              :type="type"
              :value="secondInitialValue"
              @change="secondValue=$event.value"
            ></text-input>
            <date-input
              v-if="type==='date'"
              :min="secondMin"
              :max="max"
              :value="secondInitialValue"
              @input="secondValue=$event"
            ></date-input>
            <help-text :text="secondHelpText"></help-text>
          </div>
        </template>
      </template>
      <template v-if="component">
        <div class="col-sm-12">
          <component
            v-if="component"
            :data="componentData"
            :is="component"
            @change="value=$event.value"
            @changeSecond="secondValue=$event.value"
          ></component>
        </div>
      </template>
    </div>
  </div>
</template>
<script>
  import TextInput from 'common/controls/TextInput'
  import DateInput from 'common/controls/DateInput'
  import Dropdown from 'common/controls/Dropdown'
  import Checkbox from 'common/controls/Checkbox'
  import HelpText from './HelpText'

  export default {
    components:{
      TextInput,
      DateInput,
      Dropdown,
      Checkbox,
      HelpText
    },
    props:{
      title:{
        type:String,
        required:true
      },
      name:{
        type:String,
        required:true
      },
      range:{
        type:Boolean
      },
      type:{
        type:String
      },
      length:{
        type:Number
      },
      min:{
        type:Number
      },
      max:{
        type:Number
      },
      options:{},
      checkboxes:{
        type:[
          Object,
          Array
        ]
      },
      radios:{
        type:[
          Object,
          Array
        ]
      },
      initialValue:{},
      helpText:{},
      rowColumnsClass:{
        type:Boolean,
        default:true
      },
      subtitles:{
        type:Array
      },
      component:{
        type:Object
      },
      componentData:{
        type:Object
      }
    },
    data(){
      return {
        value:this.initialValue||(this.checkboxes?[]:null),
        secondValue:null
      }
    },
    computed:{
      secondMin(){
        if(this.value!==null)
          return this.value
        else
          return this.min
      },
      firstMax(){
        if(this.secondValue!==null)
          return this.secondValue
        else
          return this.max
      },
      firstInitialValue(){
        if(this.initialValue!==undefined)
          if(this.range)
            return this.initialValue[0]
          else
            return this.initialValue
        else
          return null
      },
      secondInitialValue(){
        if(this.initialValue!==undefined&&this.range)
          return this.initialValue[1]
        else
          return null
      },
      firstHelpText(){
        if(this.helpText!==undefined)
          if(this.range)
            return this.helpText[0]
          else
            return this.helpText
        else
          return null
      },
      secondHelpText(){
        if(this.helpText!==undefined&&this.range)
          return this.helpText[1]
        else
          return null
      }
    },
    watch:{
      value(){
        this.change()
      },
      secondValue(){
        this.change()
      },
      initialValue(initialValue){
        if(!this.range)
        // Used when available checkboxes are changed dynamically
          this.value=initialValue
        else{
          this.value=initialValue[0]||null
          this.secondValue=initialValue[1]||null
        }
      }
    },
    methods:{
      change(){
        let value
        if(!this.range)
          value=this.value
        else if(this.value===null&&this.secondValue===null)
          value=null
        else
          value=[
            this.value,
            this.secondValue
          ]

        this.$emit('change',{value})
      },
      toggleCheckboxValue(name,checked){
        if(checked&&this.value.indexOf(name)=== -1)
          this.value.push(name)
        else if(!checked)
          this.value=_.without(this.value,name)
      },
      initiallyChecked(name){
        if(Array.isArray(this.initialValue))
          return this.initialValue.indexOf(name)!== -1
        else
          return false
      }
    }
  }
</script>
