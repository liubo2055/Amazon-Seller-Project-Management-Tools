require('./bootstrap')

import Vue from 'vue'

new Vue({
  el:'profile-section',
  components:{
    'profile-section':require('components/profile/Section')
  }
})
