<template>
  <row
    :columns="columns"
    :row="row"
  >
    <template
      slot="name"
      slot-scope="{value}"
    >
      <template v-if="!value.private">{{ value.name }}</template>
      <template v-else>
        <p>
          <span class="label label-info">{{ _ix('Freelancer','Users') }}</span>
          {{ value.name }}
        </p>
        <p>
          <span class="label label-info">{{ _ix('Employer','Users') }}</span>
          {{ value.creatorUser }}
        </p>
        <p>
          <span class="badge badge-warning">{{ _ix('Private','Users') }}</span>
        </p>
      </template>
    </template>
    <operations
      slot="operations"
      slot-scope="{value}"
      :operations="value"
      @rowEvent="$emit('rowEvent',$event)"
    />
  </row>
</template>
<script>
  import Row from 'common/tables/rows/Row'
  import Operations from './Operations'

  export default {
    components:{
      Row,
      Operations
    },
    props:{
      columns:{
        type:Array,
        required:true
      },
      row:{
        type:Object,
        required:true
      }
    }
  }
</script>
