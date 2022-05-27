<template>
  <row
    :columns="columns"
    :row="row"
  >
    <template
      slot="code"
      slot-scope="{value}"
    >
      <p>{{ value.code }}</p>
      <p>
        <a
          rel="noreferrer"
          target="_blank"
          :href="value.url"
        >
          {{ value.asin }}
        </a>
      </p>
      <p v-if="value.private">
        <span class="badge badge-warning">{{ _ix('Private','To-Dos') }}</span>
      </p>
    </template>
    <template
      slot="price"
      slot-scope="{value}"
    >
      <p>{{ value.price }}</p>
      <p>{{ sprintf(_ix('Shipping method: %s','To-Dos'),value.shipping) }}</p>
    </template>
    <template
      slot="title"
      slot-scope="{value}"
    >
      <p>{{ value.title }}</p>
      <p>{{ value.description }}</p>
    </template>
    <template
      slot="seller"
      slot-scope="{value}"
    >
      <p v-if="value.name">{{ value.name }}</p>
      <p v-if="value.phone">
        <i class="fa fa-phone"></i>
        <a :href="sprintf('tel:%s',value.phone)">
          {{ sprintf(_ix('%s','To-Dos'),value.phone) }}
        </a>
      </p>
      <p v-if="value.storefrontUrl">
        <a
          rel="noreferrer"
          target="_blank"
          :href="value.storefrontUrl"
        >
          {{ value.storefrontName }}
        </a>
      </p>
      <p v-if="!value.storefrontUrl">{{ value.storefrontName }}</p>
      <p>{{ value.id }}</p>
    </template>
    <template
      slot="notesKeywords"
      slot-scope="{value}"
    >
      <p v-if="value.notes">{{ value.notes }}</p>
      <div
        class="todo-keywords"
        v-if="value.keywords"
      >
        <span
          class="todo-keyword"
          v-for="keyword in value.keywords"
        >
          {{ keyword }}
        </span>
      </div>
    </template>
    <template
      slot="status"
      slot-scope="{value}"
    >
      <p>{{ value.status }}</p>
      <p
        class="text-danger"
        v-if="value.limitReached"
      >
        {{ _ix('Daily limit reached','To-Dos') }}
      </p>
    </template>
    <template
      slot="progress"
      slot-scope="{value}"
    >
      <progress-bars
        v-if="value!==null"
        :values="value"
      />
      <div v-if="value!==null">
        {{ sprintf(_ix('Completed: %u, unassigned: %u, others: %u, failed/deleted: %u','To-Dos'),
        value.completed,
        value.unassigned,
        value.others,
        value.failed) }}
      </div>
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
  import ProgressBars from './ProgressBars'
  import Operations from './Operations'

  export default {
    components:{
      Row,
      ProgressBars,
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
