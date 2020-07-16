import Vue from 'vue'
import VueRouter from 'vue-router'

// コンポーネントをインポート
import PhotoList from './pages/PhotoList.vue'
import Login from './pages/Login.vue'

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
        component: Login
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