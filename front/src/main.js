// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './router'
import axios from 'axios'
Vue.config.productionTip = false
Vue.prototype.$axios = axios;
import ElementUI, { Message, TabPane } from 'element-ui'
import 'element-ui/lib/theme-chalk/index.css'
Vue.use(ElementUI)
/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  render:h=>h(App),
  components: { App },
  template: '<App/>'
})

import Router from 'vue-router'

const originalPush = Router.prototype.push
Router.prototype.push = function push(location, onResolve, onReject) {
  if (onResolve || onReject) return originalPush.call(this, location, onResolve, onReject)
  return originalPush.call(this, location).catch(err => err)
}

router.beforeEach((to, from, next) => {
  if (to.name !== 'login' && sessionStorage.getItem('userid')==null )
  {
    Message.error("请先登录")
    next({ name: 'login' })
  } 
  else next()
})

router.beforeEach((to, from, next) => {
  if(to.name=='home_user' && sessionStorage.getItem('flag')=='2')
  {
    Message.error("请先登录")
    next({ name: 'login' })
  } 
  else next()
})

router.beforeEach((to, from, next) => {
  if(to.name=='home_ad' && sessionStorage.getItem('flag')=='1')
  {
    Message.error("请先登录")
    next({ name: 'login' })
  } 
  else next()
})