import Vue from 'vue'
// ルーティングの定義をインポート
import router from './router'
// ルートコンポーネントをインポート
import App from './App.vue'

new Vue({
    el: '#app',
    router,
    components: { App },
    template: '<App />'
})