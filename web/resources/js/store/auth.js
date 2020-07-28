/**
 * Vuex（状態管理ライブラリ）のauthストアの設定
 *
 * アクション → コミットでミューテーション呼び出し → ステート更新
 */
// データの入れ物（ログイン中のユーザーデータ）
const state = {
    user: null
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
        const response = await axios.post('/api/login', data)
        context.commit('setUser', response.data)
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