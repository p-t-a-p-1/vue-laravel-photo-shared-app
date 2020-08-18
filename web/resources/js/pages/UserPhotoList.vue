<template>
    <div class="photo-list">
        <h2>{{ this.userName }}の写真一覧</h2>
        <div class="grid">
            <Photo
                class="grid__item"
                v-for="photo in photos"
                :key="photo.id"
                :item="photo"
                @like="onLikeClick"
            />
        </div>
        <Pagination :current-page="currentPage" :last-page="lastPage" />
    </div>
</template>

<script>
import { OK } from '../util'
import Photo from '../components/Photo.vue'
import Pagination from '../components/Pagination.vue'

export default {
    components: {
        Photo,
        Pagination
    },
    props: {
        // router.jsで props: true としているので動的なidをpropsとして受け渡しできる
        user_id: {
            type: String,
            required: true
        },
        page: {
            type: Number,
            required: true,
            default: 1
        }
    },
    data () {
        return {
            photos: [],
            currentPage: 0,
            lastPage: 0,
            userName: '',
        }
    },
    methods: {
        async fetchPhotos () {

            const response = await axios.get(`/api/photos/user/${this.user_id}/?page=${this.page}`)
            console.log(response.data)
            if (response.status !== OK) {
                this.$store.commit('error/setCode', response.status)
                return false
            }

            this.photos = response.data.data
            this.currentPage = response.data.current_page
            this.lastPage = response.data.last_page
            this.userName = response.data.data[0].owner.name
        },
        onLikeClick({ id, liked }) {
            if (! this.$store.getters['auth/check']) {
                alert('いいね機能を使うにはログインしてください')
                return false
            }

            if (liked) {
                this.unlike(id)
            } else {
                this.like(id)
            }
        },
        async like (id) {
            const response = await axios.put(`/api/photos/${id}/like`)

            if (response.status !== OK) {
                this.$store.commit('error/setCode', response.status)
                return false
            }

            this.photos = this.photos.map(photo => {
                if (photo.id === response.data.photo_id) {
                    photo.likes_count += 1
                    photo.liked_by_user = true
                }
                return photo
            })
        },
        async unlike (id) {
            const response = await axios.delete(`/api/photos/${id}/like`)

            if (response.status !== OK) {
                this.$store.commit('error/setCode', response.status)
                return false
            }

            this.photos = this.photos.map(photo => {
                if (photo.id === response.data.photo_id) {
                    photo.likes_count -= 1
                    photo.liked_by_user = false
                }
                return photo
            })
        }
    },
    watch: {
        // ページが変わった時にfetchPhotosを実行するように
        $route: {
            // 監視ハンドラ
            async handler () {
                await this.fetchPhotos()
            },
            immediate: true // コンポーネントが生成されたタイミングでも実行（2ページ目以降）
        }
    }
}

</script>