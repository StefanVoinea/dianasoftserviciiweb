<template>
  <div class="d-flex">
    <b-button
       @click="doCopy"
      v-ripple.400="'rgba(186, 191, 199, 0.15)'"
      variant="flat-primary"
      class="btn-icon" >
       <feather-icon icon="CopyIcon" />
    </b-button>
  </div>
</template>

<script>
import { BFormInput, BFormGroup, BButton } from 'bootstrap-vue'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import Ripple from 'vue-ripple-directive'

export default {
  props:{
  textToCopy:String
  },
  components: {
    BFormInput,
    BFormGroup,
    BButton,
    // eslint-disable-next-line vue/no-unused-components
    ToastificationContent,
  },
  directives: {
    Ripple,
  },
  name:"copytoclipboard",
  data() {
    return {
      message: this.textToCopy,
    }
  },
  watch:{
      textToCopy(){
        this.message = this.textToCopy
      }
    },
  methods: {
     doCopy() {
      const container = document.querySelector('.v-dialog')
      this.$copyText(this.message, container)
      },
    onCopy() {
      // console.log(this.message)

      this.$toast({
        component: ToastificationContent,
        props: {
          title: 'Text copied',
          icon: 'BellIcon',
        },
      })
    },
    onError() {
      this.$toast({
        component: ToastificationContent,
        props: {
          title: 'Failed to copy texts!',
          icon: 'BellIcon',
        },
      })
    },
  },
}
</script>