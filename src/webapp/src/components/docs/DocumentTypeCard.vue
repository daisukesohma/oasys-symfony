<template>
    <div class="document-type-card"
         :class="{'current-view' : active}"
         @click="$event => $emit('click', $event)">
        <img :src="iconSrc" alt="">
        <div class="document-type-card--content">
            <h5>{{title}}</h5>
            <p>{{number}} fichiers</p>
        </div>
    </div>
</template>
<script>
export default {
    name:"DocumentTypeCard",
    props:{
        icon: {
            type: String,
            required: true,
        },
        title:{
            type: String,
            required: true,
        },
        number:{
            type: Number,
            required: true,
        },
        active: {
            type: Boolean,
            required: false,
        },
    },
    computed: {
        iconSrc(){
            return require(`@/assets/img/${this.icon}.svg`)
        }
    },
}
</script>