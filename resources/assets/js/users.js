require('./bootstrap')

import Vue from 'vue'

new Vue({
  el:'users-section',
  components:{
    'users-section':require('components/users/Section')
  }
})
