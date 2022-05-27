<template>
  <row
    :classes="row.meta.waitingForDescription?['waiting-for-description']:[]"
    :columns="columns"
    :row="row"
  >
    <div
      slot="rowAsinSeller"
      slot-scope="{value}"
    >
      <p>{{ value.row }}</p>
      <p>
        <a
          target="_blank"
          :href="value.url"
        >
          {{ value.asin }}
        </a>
      </p>
      <p>{{ value.marketplace }}</p>
      <p v-if="value.sellerName">{{ value.sellerName }}</p>
      <p v-if="value.sellerPhone">
        <i class="fa fa-phone"></i>
        <a :href="sprintf('tel:%s',value.sellerPhone)">
          {{ sprintf(_ix('%s','Projects'),value.sellerPhone) }}
        </a>
      </p>
      <p>
        <a
          rel="noreferrer"
          target="_blank"
          :href="value.storefrontUrl"
        >
          {{ value.storefrontName }}
        </a>
      </p>
      <p>{{ value.sellerId }}</p>
      <p v-if="value.private">
        <span class="badge badge-warning">{{ _ix('Private','Projects (to-dos)') }}</span>
      </p>
    </div>
    <div
      slot="todoTitleDescription"
      slot-scope="{value}"
    >
      <p v-if="value.productTitle">
        <span class="label label-info">{{ _ix('Title','Projects (to-dos)') }}</span>
        {{ value.productTitle }}
      </p>
      <p v-if="value.productDescription">
        <span class="label label-info">{{ _ix('Description','Projects (to-dos)') }}</span>
        {{ value.productDescription }}
      </p>
    </div>
    <div
      slot="projectTitleDescription"
      slot-scope="{value}"
    >
      <p v-if="value.projectTitle">
        <span class="label label-info">{{ _ix('Title','Projects (projects)') }}</span>
        {{ value.projectTitle }}
      </p>
      <p v-if="value.projectDescription">
        <span class="label label-info">{{ _ix('Description','Projects (projects)') }}</span>
        {{ value.projectDescription }}
      </p>
      <p v-if="value.storeDescription">
        <span class="label label-info">{{ _ix('Store description','Projects') }}</span>
        {{ value.storeDescription }}
      </p>
    </div>
    <template
      slot="imageMedia"
      slot-scope="{value}"
    >
      <form
        method="POST"
        :action="value.downloadUrl"
        v-if="value.downloadUrl"
      >
        <input
          name="_token"
          type="hidden"
          :value="componentData.csrf"
        >
        <button class="btn btn-default btn-sm">
          <i class="fa fa-download"></i>
          {{ _ix('Download all','Projects') }}
        </button>
      </form>
      <p>{{ sprintf(_ix('%u images','Projects'),value.images) }}</p>
      <p>{{ sprintf(_ix('%u medias','Projects'),value.medias) }}</p>
    </template>
    <template
      slot="status"
      slot-scope="{value}"
    >
      <p>{{ value.status }}</p>
      <p
        class="text-danger"
        v-if="value.idDeadline"
      >
        {{ sprintf(_ix('You must submit the completion ID before %s','Projects'),value.idDeadline) }}
      </p>
      <p
        class="similar-projects"
        v-if="value.similar"
        @click="$emit('rowEvent',{
          action:'similar',
          url:value.similarUrl
        })"
      >
        {{ _ix('You have already accepted','Projects') }}
        <span class="similar-projects-value">{{ intNumber(value.similar.storefrontName) }}</span>
        {{ _ix('projects of this store name,','Projects') }}
        <span class="similar-projects-value">{{ intNumber(value.similar.sellerId) }}</span>
        {{ _ix('projects of this seller ID,','Projects') }}
        <span class="similar-projects-value">{{ intNumber(value.similar.asin) }}</span>
        {{ _ix('projects of this ASIN','Projects') }}
      </p>
    </template>
    <template
      slot="completion"
      slot-scope="{value}"
    >
      <p v-if="value.id">
        <span v-if="!value.url">{{ value.id }}</span>
        <a
          target="_blank"
          v-if="value.url"
          :href="value.url"
        >
          {{ value.id }}
        </a>
      </p>
      <user-info
        v-if="value.freelancer"
        :label="_ix('Freelancer','Projects')"
        :user="value.freelancer"
      />
    </template>
    <template
      slot="dates"
      slot-scope="{value}"
    >
      <p>{{ sprintf(_ix('Created at %s','Projects'),value.created) }}</p>
      <p v-if="value.completionIdSubmitted">{{ sprintf(_ix('Completion ID submitted at %s','Projects'),value.completionIdSubmitted) }}</p>
      <p v-if="value.completed">{{ sprintf(_ix('Completed at %s','Projects'),value.completed) }}</p>
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
  import UserInfo from '../../common/components/UserInfo'

  export default {
    components:{
      UserInfo,
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
      },
      componentData:{
        type:Object,
        required:true
      }
    }
  }
</script>
