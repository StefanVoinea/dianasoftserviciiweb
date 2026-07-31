<template>

  <div class="navbar-header d-xl-block d-none">
    <ul class="nav navbar-nav">
      <li class="nav-item">
        <b-link
          class="navbar-brand"
          to="/"
        >
          <!-- Doar sigla: numele aplicației și versiunea au lăsat locul
               modulelor, care sunt drumul spre lucru, nu o informație. -->
          <span class="brand-logo">
            <b-img
              :src="appLogoImage"
              :alt="appName"
              :title="appName + ' ' + versiune"
            />
          </span>
        </b-link>
      </li>
    </ul>
  </div>
 
</template>

<script>
import { BLink, BImg } from 'bootstrap-vue'
import { $themeConfig } from '@themeConfig'

export default {
  components: {
    BLink,
    BImg,
  },
  setup() {
    // App Name

    const { appName, appLogoImage } = $themeConfig.app
    const versiune= window.versiune
    return {
      appName,
      appLogoImage,
      versiune,
    }
  },
}
</script>

<style>

</style>
