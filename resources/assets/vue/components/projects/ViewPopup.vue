<template>
  <popup
    :title="_ix('View project','Projects')"
    @close="$emit('close')"
  >
    <div class="row">
      <div class="col-sm-4">
        <h5>{{ _ix('Row','Projects') }}</h5>
      </div>
      <div class="col-sm-8">{{ project.row }}</div>
    </div>
    <div class="row">
      <div class="col-sm-4">
        <h5>{{ _ix('Product title','Projects') }}</h5>
      </div>
      <div class="col-sm-8">{{ project.productTitle }}</div>
    </div>
    <div class="row">
      <div class="col-sm-4">
        <h5>{{ _ix('Product description','Projects') }}</h5>
      </div>
      <div class="col-sm-8">{{ project.productDescription }}</div>
    </div>
    <div class="row">
      <div class="col-sm-4">
        <h5>{{ _ix('Project title','Projects') }}</h5>
      </div>
      <div class="col-sm-8">{{ project.projectTitle }}</div>
    </div>
    <div class="row">
      <div class="col-sm-4">
        <h5>{{ _ix('Project description','Projects') }}</h5>
      </div>
      <div class="col-sm-8">{{ project.projectDescription }}</div>
    </div>
    <div class="row">
      <div class="col-sm-4">
        <h5>{{ _ix('Store description','Projects') }}</h5>
      </div>
      <div class="col-sm-8">{{ project.storeDescription }}</div>
    </div>
    <div class="row">
      <div class="col-sm-4">
        <h5>{{ _ix('Project price','Projects') }}</h5>
      </div>
      <div class="col-sm-8">{{ project.projectCurrency?sprintf('%s %s',
        project.projectCurrency,
        project.projectPrice):'' }}
      </div>
    </div>
    <div class="row">
      <div class="col-sm-4">
        <h5>{{ _ix('Status','Projects') }}</h5>
      </div>
      <div class="col-sm-8">{{ project.status }}</div>
    </div>
    <div class="row">
      <div class="col-sm-4">
        <h5>{{ _ix('Storefront name','Projects') }}</h5>
      </div>
      <div class="col-sm-8">{{ project.storefrontName }}</div>
    </div>
    <div class="row">
      <div class="col-sm-4">
        <h5>{{ _ix('Storefront URL','Projects') }}</h5>
      </div>
      <div class="col-sm-8">
        <a
          target="_blank"
          v-if="project.storefrontUrl"
          :href="project.storefrontUrl"
        >
          {{ project.storefrontUrl }}
        </a>
      </div>
    </div>
    <div class="row">
      <div class="col-sm-4">
        <h5>{{ _ix('Creation date','Projects') }}</h5>
      </div>
      <div class="col-sm-8">{{ project.creation }}</div>
    </div>
    <div class="row">
      <div class="col-sm-4">
        <h5>{{ _ix('Completion ID submit date','Projects') }}</h5>
      </div>
      <div class="col-sm-8">{{ project.completionIdSubmission }}</div>
    </div>
    <div class="row">
      <div class="col-sm-4">
        <h5>{{ _ix('Completion date','Projects') }}</h5>
      </div>
      <div class="col-sm-8">{{ project.completion }}</div>
    </div>
    <div class="row">
      <div class="col-sm-4">
        <h5>{{ _ix('Images','Projects') }}</h5>
      </div>
      <div class="col-sm-8 images">
        <img
          class="image"
          v-for="url in images"
          :src="url"
        >
      </div>
    </div>
    <div class="row">
      <div class="col-sm-4">
        <h5>{{ _ix('Media','Projects') }}</h5>
      </div>
      <div class="col-sm-8 medias">
        <p
          class="media"
          v-for="media in medias"
        >
          {{ media }}
        </p>
      </div>
    </div>
  </popup>
</template>
<script>
  import Popup from 'common/components/Popup'

  export default {
    components:{Popup},
    props:{
      loadUrl:{
        type:String,
        required:true
      }
    },
    data(){
      return {
        project:{},
        images:[],
        medias:[]
      }
    },
    created(){
      axios.post(this.loadUrl)
        .then(response=>{
          this.loadFromData(response.data)
        })
    },
    methods:{
      loadFromData(data){
        this.project=data.project
        this.images=data.images
        this.medias=data.medias
      }
    }
  }
</script>
