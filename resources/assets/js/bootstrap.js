require('babel-polyfill')

import Vue from 'vue'
import _ix from './i18n/gettext'
import numeral from 'numeral'
import {sprintf} from 'sprintf-js'
import stringsMixin from 'common/mixins/strings'

window._=require('lodash')

window._ix=_ix
window.numeral=numeral
window.sprintf=sprintf

Vue.config.productionTip=false
Vue.mixin(stringsMixin)

window.axios=require('axios')
window.axios.defaults.headers.common={
  'X-CSRF-TOKEN':window.Xproject.csrfToken,
  'X-Requested-With':'XMLHttpRequest'
}
