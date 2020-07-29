/**
 * エラー情報を扱うerrorストアの定義
 */

const state = {
    code: null // エラーのステータスコード
}

const mutations = {
    setCode (state, code) {
        state.code = code
    }
}

export default {
    namespaced: true,
    state,
    mutations
}