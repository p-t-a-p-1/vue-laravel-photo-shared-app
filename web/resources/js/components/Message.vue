<template>
    <div class="message" v-show="message">
        {{ message }}
    </div>
</template>

<script>
import { mapState } from 'vuex'

export default {
    computed: {
        // mapState内では state === this.$store.state
        ...mapState({
            message: state => state.message.content
        })
    }
}
</script>