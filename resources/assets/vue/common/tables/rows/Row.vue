<template>
  <tr :class="classes||[]">
    <td
      v-for="column in columns"
      v-if="showCell(column)"
      :class="[column.class||'']"
      :rowspan="columnRowspan(column)"
    >
      <slot
        :id="row.meta.id"
        :name="column.code"
        :value="row[column.code]"
      >
        {{ row[column.code] }}
      </slot>
    </td>
  </tr>
</template>
<script>
  export default {
    props:{
      columns:{
        type:Array,
        required:true
      },
      row:{
        type:Object,
        required:true
      },
      classes:{},
      rowspan:{
        type:Number
      },
      rowspanColumns:{
        type:Array
      }
    },
    methods:{
      showCell(column){
        const skipColumns=this.row.meta.skipColumns||[]

        return skipColumns.indexOf(column.code)=== -1
      },
      columnRowspan(column){
        if(!this.rowspan|| !this.rowspanColumns)
          return 1

        return this.rowspanColumns.indexOf(column.code)=== -1?1:this.rowspan
      }
    }
  }
</script>
