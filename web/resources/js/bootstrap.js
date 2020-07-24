// クッキー取得関数読み込み
import { getCookieValue } from './util'

window.axios = require('axios')

// Ajaxリクエストであることを示すヘッダーを付与
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

window.axios.interceptors.request.use(config => {
    // クッキーからトークンを取り出してヘッダーに添付する
    // これによってLaravelはフォームではなくヘッダーをみてCSRFトークンチェックを行ってくれる
    config.headers['X-XSRF-TOKEN'] = getCookieValue('X-XSRF-TOKEN')

    return config
})
