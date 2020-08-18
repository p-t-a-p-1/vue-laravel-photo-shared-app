<template>
    <div class="photo">
        <figure class="photo__wrapper">
            <img class="photo__image" :src="item.url" :alt="`Photo by ${item.owner.name}`" />
        </figure>
        <RouterLink
            class="photo__overlay"
            :to="`/photos/${item.id}`"
            :title="`View the photo by ${item.owner.name}`"
        >
            <div class="photo__controls">
                <button
                    class="photo__action photo__action--like"
                    :class="{ 'photo__action--liked' : item.liked_by_user }"
                    title="Like photo"
                    @click.prevent="like"
                >
                    <i class="icon ion-md-heart"></i>{{ item.likes_count }}
                </button>
                <!-- @click.stopでダウンロードリンク押した際に写真詳細リンク遷移するのを防ぐ -->
                <a
                    class="photo__action"
                    title="Download photo"
                    @click.stop
                    :href="`/photos/${item.id}/download`">
                    <i class="icon ion-md-arrow-round-down"></i>
                </a>
            </div>
            <a
                class="photo__username"
                :href="`/photos/user/${item.user_id}`"
                @click.stop
            >{{ item.owner.name }}</a>

        </RouterLink>
    </div>
</template>

<style lang="scss">
.photo__username {
    background: #fff;
    border: 0;
    border-radius: 0.25rem;
    color: #222;
    cursor: pointer;
    display: inline-table;
    font-family: inherit;
    font-size: 1rem;
    line-height: 1;
    margin-left: 0.25rem;
    opacity: 0.8;
    outline: none;
    padding: 0.5em 0.75em;
    text-decoration: none;
    &:hover {
        opacity: 1.0;
    }
}
</style>

<script>
export default {
    props: {
        item: {
            type: Object,
            required: true
        }
    },
    methods: {
        like () {
            this.$emit('like', {
                id: this.item.id,
                liked: this.item.liked_by_user
            })
        }
    }
}
</script>