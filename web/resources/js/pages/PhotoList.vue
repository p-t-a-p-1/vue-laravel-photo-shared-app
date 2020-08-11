<template>
    <div class="photo-list">
        <div class="grid">
            <Photo
                class="grid__item"
                v-for="photo in photos"
                :key="photo.id"
                :item="photo"
            />
        </div>
    </div>
</template>

<script>
import { OK } from '../util'
import Photo from '../components/Photo.vue'

export default {
    components: {
        Photo,
    },
    data () {
        return {
            photos: []
        }
    },
    methods: {
        async fetchPhotos () {
            const response = await axios.get('/api/photos')

            if (response.status !== OK) {
                this.$store.commit('error/setCode', response.status)
                return false
            }

            this.photos = response.data.data
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