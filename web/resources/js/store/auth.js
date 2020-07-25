/**
 * Vuex（状態管理ライブラリ）のストアの設定
 */

// データの入れ物（ログイン中のユーザーデータ）
const state = {
    user: null
}

// ステートの内容から算出される値（ユーザーがログイン中であるかどうか）
const getters = {}

// ステートを同期処理で更新するためのメソッド
const mutations = {
    setUser (state, user) {
        state.user = user
    }
}

// ステートを非同期処理で更新するためのメソッド
// APIとの通信などの非同期処理を行った後にミューテーションを呼び出してステートを更新する
const actions = {}

// 名前空間で分けておく
// モジュールに分けた時にステートやミューテーションの名前が被ってもモジュール名で区別できる
export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions
}