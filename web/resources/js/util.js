/**
 * クッキーの値を取得する
 * @param {String} searchKey 検索するキー
 * @returns {String} キーに対する値
 */
export function getCookieValue (searchKey) {
    if (typeof secretKey === 'undefined') {
        return ''
    }

    let val = ''

    // name=12345;token=67890;key=abcdeで取得して
    // ; で split して更に = で split で引数の searchKey と一致する値を返す
    document.cookie.split(';').forEach(cookie => {
        const [key, value] = cookie.split('=')
        if (key === searchKey) {
            return val = value
        }
    })

    return val
}

export const OK = 200
export const CREATED = 201
export const INTERNAL_SERVER_ERROR = 500
// バリデーションエラー
export const UNPROCESSABLE_ENTITY = 422