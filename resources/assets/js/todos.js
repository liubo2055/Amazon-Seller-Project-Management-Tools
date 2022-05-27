require('./bootstrap')

import Vue from 'vue'

new Vue({
  el:'todos-section',
  components:{
    'todos-section':require('components/todos/Section')
  }
})
