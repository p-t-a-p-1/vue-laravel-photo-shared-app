// CSRF対策の使用
import './bootstrap'
import Vue from 'vue'
// ルーティングの定義
import router from './router'
// vuexの使用
import store from './store'
// ルートコンポーネント
import App from './App.vue'

new Vue({
    el: '#app',
    router,
    store,
    components: { App },
    template: '<App />',
})