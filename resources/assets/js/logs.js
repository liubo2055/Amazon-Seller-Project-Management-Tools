require('./bootstrap')

import Vue from 'vue'

new Vue({
  el:'logs-section',
  components:{
    'logs-section':require('components/logs/Section')
  }
})
