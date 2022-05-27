require('./bootstrap')

import Vue from 'vue'

new Vue({
  el:'projects-section',
  components:{
    'projects-section':require('components/projects/Section')
  }
})
