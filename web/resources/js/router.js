import Vue from 'vue'
import VueRouter from 'vue-router'

// コンポーネントをインポート
import PhotoList from './pages/PhotoList.vue'
import PhotoDetail from './pages/PhotoDetail.vue'
import Login from './pages/Login.vue'

import store from './store'

import SystemError from './pages/errors/System.vue'

// VueRouterプラグインを使用し、<RouterView /> コンポーネントを使うことができる
Vue.use(VueRouter)

// パスとコンポーネントのマッピング
const routes = [
    {
        path: '/',
        component: PhotoList,
        props: route => {
            const page = route.query.page
            // 不正な値は1とする
            return { page: /^[1-9][0-9]*$/.test(page) ? page * 1 : 1 }
        }
    },
    {
        // idは写真IDが動的に変わる
        path: '/photos/:id',
        component: PhotoDetail,
        props: true // 写真IDをpropsとして受け取ることができる
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
    scrollBehavior () {
        return {x: 0, y: 0}
    },
    routes
})

// VueRouterインスタンスをエクスポート
// app.jsでインポートするため
export default router