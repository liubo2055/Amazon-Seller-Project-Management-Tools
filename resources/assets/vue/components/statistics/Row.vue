<template>
  <row
    :columns="columns"
    :row="row"
  >
    <template
      slot="row"
      slot-scope="{value}"
    >
      <p>{{ value.row }}</p>
      <p v-if="value.private">
        <span class="badge badge-warning">{{ _ix('Private','Statistics') }}</span>
      </p>
    </template>
    <template
      slot="asinMarketplace"
      slot-scope="{value}"
    >
      <p>
        <a
          target="_blank"
          :href="value.url"
        >
          {{ value.asin }}
        </a>
      </p>
      <p>{{ value.marketplace }}</p>
    </template>
    <template
      slot="seller"
      slot-scope="{value}"
    >
      <p>{{ value.name }}</p>
      <p>{{ value.id }}</p>
    </template>
    <template
      slot="storefront"
      slot-scope="{value}"
    >
      <a
        rel="noreferrer"
        target="_blank"
        :href="value.url"
      >
        {{ value.name }}
      </a>
    </template>
    <user-info
      slot="freelancer"
      slot-scope="{value}"
      :user="value"
    />
    <template
      slot="completion"
      slot-scope="{value}"
    >
      <p v-if="value.id">{{ value.id }}</p>
      <p v-if="value.url">
        <a
          target="_blank"
          :href="value.url"
        >
          {{ value.url }}
        </a>
      </p>
    </template>
    <template
      slot="dates"
      slot-scope="{value}"
    >
      <p>{{ sprintf(_ix('Created at %s','Statistics'),value.created) }}</p>
      <p v-if="value.completionIdSubmitted">{{ sprintf(_ix('Completion ID submitted at %s','Statistics'),value.completionIdSubmitted) }}</p>
      <p v-if="value.completed">{{ sprintf(_ix('Completed at %s','Statistics'),value.completed) }}</p>
    </template>
  </row>
</template>
<script>
  import Row from 'common/tables/rows/Row'
  import UserInfo from 'common/components/UserInfo'

  export default {
    components:{
      UserInfo,
      Row
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
