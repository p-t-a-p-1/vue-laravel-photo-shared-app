// CSRF対策の使用
import './bootstrap'
import Vue from 'vue'
// ルーティングの定義
import router from './router'
// vuexの使用
import store from './store'
// ルートコンポーネント
import App from './App.vue'

const createApp = async () => {
    // Vueインスタンス生成前にユーザーチェックをしログインしているか確認
    await store.dispatch('auth/currentUser')

    new Vue({
        el: '#app',
        router,
        store,
        components: { App },
        template: '<App />',
    })
}

createApp()
