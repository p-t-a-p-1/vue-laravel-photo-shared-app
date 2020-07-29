/**
 * Vuex（状態管理ライブラリ）のauthストアの設定
 *
 * アクション → コミットでミューテーション呼び出し → ステート更新
 */

import { OK, UNPROCESSABLE_ENTITY } from '../util'

// データの入れ物
const state = {
    user: null, // ログイン中のユーザーデータ
    apiStatus: null, // API呼び出しが成功したか失敗したかを表す
    loginErrorMessages: null // エラーメッセージ
}

// ステートの内容から算出される値（ユーザーがログイン中であるかどうか）
// ステートをもとに演算した結果が欲しい時にゲッターを使う
const getters = {
    // ログインチェックに使用
    check: state => !! state.user,
    // ログインユーザーの名前（nullの場合は空）
    username: state => state.user ? state.user.name : ''
}

// ステートを同期処理で更新するためのメソッド
const mutations = {
    setUser (state, user) {
        state.user = user
    },
    setApiStatus (state, status) {
        state.apiStatus = status
    },
    setLoginErrorMessages (state, messages) {
        state.loginErrorMessages = messages
    }
}

// ステートを非同期処理で更新するためのメソッド
// APIとの通信などの非同期処理を行った後にミューテーションを呼び出してステートを更新する
const actions = {
    async register (context, data) {
        const response = await axios.post('/api/register', data)
        context.commit('setUser', response.data)
    },
    async login (context, data) {
        // 最初は null
        context.commit('setApiStatus', null)
        // API通信が成功した場合も失敗した場合も response にレスポンスオブジェクトを代入
        const response = await axios.post('/api/login', data).catch(err => err.response || err)

        if (response.status === OK) {
            // 成功したらtrue
            context.commit('setApiStatus', true)
            context.commit('setUser', response.data)
            return false
        }

        // 失敗したらfalse
        context.commit('setApiStatus', false)

        if (response.status === UNPROCESSABLE_ENTITY) {
            // ページコンポーネント内でエラーの表示を行う必要があるので
            // error/setCodeミューテーションを呼び出さない
            // 代わりにloginErrorMesagesにメッセージをセットする
            context.commit('setLoginErrorMessages', response.data.errors)
        } else {
            // あるストアモジュールから別のモジュールのミューテーションをcommitする場合は{ root: true }が必要
            context.commit('error/setCode', response.status, { root: true })
        }



    },
    async logout (context) {
        const response = await axios.post('/api/logout')
        // userステートをnullにする
        context.commit('setUser', null)
    },
    async currentUser(context) {
        const response = await axios.get('/api/user')
        const user = response.data || null;
        context.commit('setUser', user)
    }
}

// 名前空間で分けておく
// モジュールに分けた時にステートやミューテーションの名前が被ってもモジュール名で区別できる
export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions
}