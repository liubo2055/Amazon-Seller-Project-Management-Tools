require('./bootstrap')

import Vue from 'vue'

new Vue({
  el:'statistics-section',
  components:{
    'statistics-section':require('components/statistics/Section')
  }
})
