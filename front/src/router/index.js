import Vue from 'vue'
import Router from 'vue-router'
import Login from '@/views/login/login'
import home_user from '@/components/home_user'
import home_ad from '@/components/home_ad'
import { Header } from 'element-ui'
import ad_book_search from '@/views/ad/ad_book_search'
import ad_changepwd from '@/views/ad/changepwd'
import ad_bookadd from '@/views/ad/ad_bookadd'
import ad_user_search from '@/views/ad/ad_user_search'
import ad_useradd from '@/views/ad/ad_user_add'
import user_changepwd from '@/views/user/changepwd'
import user_book_search from '@/views/user/user_search'
import user_book from '@/views/user/mybook' 
import user_hotbook from '@/views/user/hotbook'
import ad_history from '@/views/ad/historyborrow'
import ad_now from '@/views/ad/nowborrow'
import user_history from '@/views/user/historyborrow'
Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/',
      name: 'login', // 路由名称
      component: Login // 组件对象
    },
    {
      path: '/home_user',
      name: 'home_user',
      component: home_user,
      children:[{
        path:'changepwd',
        name:'home_user',
        component:user_changepwd
      },
      {
        path:'booksearch',
        name:'home_user',
        component:user_book_search
      },
      {
        path:'mybook',
        name:'home_user',
        component:user_book
      },
      {
        path:'history',
        name:'home_user',
        component:user_history
      },
      {
        path:'hotbook',
        name:'home_user',
        component:user_hotbook
      }
    ]
    },
    {
      path: '/home_ad',
      name: 'home_ad',
      component: home_ad,
      children:[
        {
          path:'booksearch',
          name:'home_ad',
          component:ad_book_search
        },
        {
          path:'changepwd',
          name:'home_ad',
          component:ad_changepwd
        },
        {
          path:'bookadd',
          name:'home_ad',
          component:ad_bookadd
        },
        {
          path:'usersearch',
          name:'home_ad',
          component:ad_user_search
        },
        {
          path:'useradd',
          name:'home_ad',
          component:ad_useradd
        },
        {
          path:'historyborrow',
          name:'home_ad',
          component:ad_history
        },
        {
          path:'nowborrow',
          name:'home_ad',
          component:ad_now
        }
      ]
    }
  ]
})
