import Vue from 'vue'
import VueRouter from 'vue-router'

// コンポーネントをインポート
import PhotoList from './pages/PhotoList.vue'
import Login from './pages/Login.vue'

import store from './store'

import SystemError from './pages/errors/System.vue'

// VueRouterプラグインを使用し、<RouterView /> コンポーネントを使うことができる
Vue.use(VueRouter)

// パスとコンポーネントのマッピング
const routes = [
    {
        path: '/',
        component: PhotoList
    },
    {
        path: '/login',
        component: Login,
        // /loginにアクセス後、ペーぞコンポーネントが切り替わる直前に呼び出される
        // to...アクセスされようとしてるルートのルートオブジェクト
        // from...アクセス元のルート
        // next...ページの移動先を決める関数
        beforeEnter (to, from, next) {
            if (store.getters['auth/check']) {
                // 引数のページに切り替わる
                next('/')
            } else {
                // 引数なしはそのままページのコンポーネントが切り替わる
                next()
            }
        }
    },
    {
        path: '/500',
        component: SystemError
    }
]

// VueRouterインスタンスを作成
const router = new VueRouter({
    mode: 'history',
    routes
})

// VueRouterインスタンスをエクスポート
// app.jsでインポートするため
export default router