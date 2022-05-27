<template>
  <popup
    :save-button="showImportButton"
    :save-button-text="_ix('Import','Users')"
    :save-error="importError"
    :saving="importing"
    :title="_ix('Import users','Users')"
    @close="$emit('close')"
    @save="importUsers()"
  >
    <div class="form-group">
      <label>{{ _ix('File','Users') }}</label>
      <file-input
        :preview="false"
        :replaceable="true"
        :upload-on-change="false"
        :url="uploadUrl"
        @upload="fileCode=$event.code"
      ></file-input>
      <p class="text-info">{{ _ix('Allowed formats: Excel 2007 (xlsx), Excel 97-2003 (xls), CSV','Users') }}</p>
      <a :href="templateSheetUrl">{{ _ix('Download template','Users') }}</a>
    </div>
    <preview
      v-if="showPreview"
      :reading="reading"
      :users="extractedUsers"
    ></preview>
    <result
      v-if="showResult"
      :error="importError"
      :importing="importing"
      :result="importResult"
    ></result>
  </popup>
</template>
<script>
  import Popup from 'common/components/Popup'
  import FileInput from 'common/controls/FileInput'
  import Preview from './Preview'
  import Result from './Result'

  export default {
    components:{
      Popup,
      FileInput,
      Preview,
      Result
    },
    props:{
      uploadUrl:{
        type:String,
        required:true
      },
      importUrl:{
        type:String,
        required:true
      },
      previewUrl:{
        type:String,
        required:true
      },
      templateSheetUrl:{
        type:String,
        required:true
      }
    },
    data(){
      return {
        fileCode:null,
        reading:false,
        extractedUsers:[],
        importing:false,
        importResult:null,
        importError:false,
        showPreview:false,
        showResult:false,
        showImportButton:false,
        reloadRequired:false
      }
    },
    watch:{
      fileCode(fileCode){
        this.showPreview=false
        this.showResult=false
        this.showImportButton=false

        if(fileCode)
          this.loadFile()
      }
    },
    mounted(){
      $(this.$refs.modal).on('hidden.bs.modal',()=>{
        if(this.reloadRequired)
          this.$emit('save')
      })
    },
    methods:{
      loadFile(){
        this.showPreview=true
        this.showResult=false
        this.showImportButton=false

        this.extractedUsers=[]
        this.reading=true

        const params={
          code:this.fileCode
        }
        axios.post(this.previewUrl,params)
          .then(response=>{
            this.extractedUsers=response.data
            this.reading=false
            this.showImportButton=true
          })
          .catch(()=>{
            this.reading=false
          })
      },
      importUsers(){
        this.showPreview=false
        this.showResult=true

        this.importing=true
        this.importResult=null
        this.importError=false

        const params={
          code:this.fileCode
        }
        axios.post(this.importUrl,params)
          .then(response=>{
            this.importResult=response.data.result
            this.importing=false
            this.showImportButton=false
            this.reloadRequired=true
          })
          .catch(()=>{
            this.importing=false
            this.importError=true
          })
      }
    }
  }
</script>
