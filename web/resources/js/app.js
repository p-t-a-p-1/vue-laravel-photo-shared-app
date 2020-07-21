import Vue from 'vue'
// ルーティングの定義
import router from './router'
// ルートコンポーネント
import App from './App.vue'

new Vue({
    el: '#app',
    router,
    components: { App },
    template: '<App />',
})