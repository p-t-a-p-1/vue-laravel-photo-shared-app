/**
 * メッセージ管理用にmessageストアの定義
 */

// データの入れ物
const state = {
    content: ''
}

// ステートを同期処理で更新するためのメソッド
const mutations = {
    setContent (state, { content, timeout }) {
        state.content = content

        if (typeof timeout === 'undefined') {
            timeout = 3000
        }

        setTimeout(() => (state.content = ''), timeout)
    }
}

export default {
    namespaced: true,
    state,
    mutations
}