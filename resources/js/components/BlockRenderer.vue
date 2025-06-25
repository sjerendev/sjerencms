<template>
    <component :is="blockComponents[block.type]" 
              v-if="blockComponents[block.type]" 
              :block="block.data"
              :is-first-block="isFirstBlock" />
</template>

<script setup>
import FullImage from './blocks/FullImage.vue'
import ImageText from './blocks/ImageText.vue'
import FullText from './blocks/FullText.vue'
import Carousel from './blocks/Carousel.vue'
import ThreeColumnCards from './blocks/ThreeColumnCards.vue'
import Video from './blocks/Video.vue'
import CallToAction from './blocks/CallToAction.vue'
import HeadlineText from './blocks/HeadlineText.vue'
import PricingTables from './blocks/pricing_tables.vue'
import FaqAccordion from './blocks/FaqAccordion.vue'
import TeamMembers from './blocks/TeamMembers.vue'
import LogoShowcase from './blocks/LogoShowcase.vue'
import ContactForm from './blocks/ContactForm.vue'
import IconCards from './blocks/IconCards.vue'
import BlogPosts from './blocks/BlogPosts.vue'
import ServiceCards from './blocks/ServiceCards.vue'

const blockComponents = {
    full_image: FullImage,
    image_text: ImageText,
    full_text: FullText,
    carousel: Carousel,
    three_column_cards: ThreeColumnCards,
    video: Video,
    call_to_action: CallToAction,
    headline_text: HeadlineText,
    pricing_tables: PricingTables,
    faq_accordion: FaqAccordion,
    team_members: TeamMembers,
    logo_showcase: LogoShowcase,
    contact_form: ContactForm,
    icon_cards: IconCards,
    blog_posts: BlogPosts,
    service_cards: ServiceCards
}

defineProps({
    block: {
        type: Object,
        required: true
    },
    isFirstBlock: {
        type: Boolean,
        default: false
    }
})
</script>
