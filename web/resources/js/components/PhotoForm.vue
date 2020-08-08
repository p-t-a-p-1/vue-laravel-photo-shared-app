<template>
    <div v-show="value" class="photo-form">
        <h2 class="title">Submit a photo</h2>
        <div v-show="loading" class="panel">
            <Loader>Senging your photo...</Loader>
        </div>
        <form v-show="! loading" class="form" @submit.prevent="submit">
            <div class="errors" v-if="errors">
                <ul v-if="errors.photo">
                    <li v-for="msg in errors.photo" :key="msg">{{ msg }}</li>
                </ul>
            </div>
            <input class="form__item" type="file" @change="onFileChange">
            <output class="form__output" v-if="preview">
                <img :src="preview" alt="" />
            </output>
            <div class="form__button">
                <button type="submit" class="button button--inverse">submit</button>
            </div>
        </form>
    </div>
</template>

<script>
import { CREATED, UNPROCESSABLE_ENTITY } from '../util'
import Loader from './Loader.vue'

// このコンポーネントの表示/非表示を親コンポーネント側で制御できるように
// valueを渡す
export default {
    components: {
        Loader
    },
    props: {
        value: {
            type: Boolean,
            required: true
        },
    },
    data () {
        return {
            loading: false,
            preview: null,
            photo: null, // 選択中のファイルを格納
            errors: null,
        }
    },
    methods: {
        // フォームでファイルが選択されたら実行される
        onFileChange (event) {
            // 何もしなかったら処理中断
            if (event.target.files.length === 0) {
                return false
            }

            // ファイルが画像でない場合は処理中断
            if (! event.target.files[0].type.match('image.*')) {
                return false
            }

            // FileReaderクラスのインスタンスを取得
            const reader = new FileReader()

            // ファイルを読み込み終わったタイミングで実行する
            reader.onload = e => {
                // previewに読み込み結果（データURL）を代入
                // previewに値が入ると<output>につけたv-ifがtrueと判定される
                // <output>内部の<img>のsrc属性はpreviewの値を参照してるので
                // 結果として画像が表示される
                this.preview = e.target.result
            }

            // ファイルを読み込む
            // 読み込まれたファイルはデータURL形式で受け取れる
            reader.readAsDataURL(event.target.files[0])

            // photoに選択中のファイルを追加
            this.photo = event.target.files[0]
        },
        // 入力欄の値とプレビュー表示をクリアするメソッド
        reset () {
            this.preview = ''
            this.photo = null
            // this.$el はコンポーネントそのもののDOM要素
            this.$el.querySelector('input[type="file"]').value = null
        },
        async submit () {

            // ローディング表示
            this.loading = true

            // Ajaxでファイルを送るためにはFormData APIを使用する
            const formData = new FormData()
            formData.append('photo', this.photo)
            const response = await axios.post('/api/photos', formData)

            // 通信が終わったらローディングを非表示
            this.loading = false

            // バリデーションエラー
            // 値をクリアしたりフォームを閉じたりしないためクリアの前に処理書く
            if (response.status === UNPROCESSABLE_ENTITY) {
                this.errors = response.data.errors
                return false
            }

            // 入力値をクリア
            this.reset()
            // NavbarのshowRoomがfalseになる
            // PhotoFormのvalueもfalseになるので非表示になる仕組み
            this.$emit('input', false)

            // 投稿以外のステータスコードの場合
            if (response.status !== CREATED) {
                this.$store.commit('error/setCode', response.status)
                return false
            }

            // メッセージ登録
            this.$store.commit('message/setContent', {
                content: '写真が投稿されました！',
                timeout: 6000
            })

            // 送信した写真IDの詳細ページに遷移
            this.$router.push(`/photos/${response.data.id}`)
        }
    }
}
</script>