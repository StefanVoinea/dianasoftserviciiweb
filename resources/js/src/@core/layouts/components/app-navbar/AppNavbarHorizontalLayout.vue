<template>
  <div class="navbar-container d-flex content align-items-center">

    <!-- Nav Menu Toggler -->
    <ul class="nav navbar-nav d-xl-none">
      <li class="nav-item">
        <b-link
          class="nav-link"
          @click="toggleVerticalMenuActive"
        >
          <feather-icon
            icon="MenuIcon"
            size="21"
          />
        </b-link>
      </li>
    </ul>

    <!-- Left Col -->
    <div class="bookmark-wrapper align-items-center flex-grow-1 d-none d-lg-flex">
      <!-- <bookmarks /> -->
      <module />
    </div>

    <!-- Right Col -->
    <!-- Lângă utilizator au rămas doar notificările: sarcinile, calendarul și
         schimbarea temei erau lucruri care nu se folosesc aici. -->
    <b-navbar-nav class="nav align-items-center ml-auto">
      <skin-dropdown />
      <notification-dropdown />
      <user-dropdown />
    </b-navbar-nav>
  </div>
</template>

<script>
import {
  BLink, BNavbarNav,
} from 'bootstrap-vue'
import NotificationDropdown from './components/NotificationDropdown.vue'
import SkinDropdown from './components/SkinDropdown.vue'
import UserDropdown from './components/UserDropdown.vue'
import Module from './components/Module.vue'

export default {
  components: {
    BLink,

    // Navbar Components
    BNavbarNav,
    NotificationDropdown,
    SkinDropdown,
    UserDropdown,
    Module,
  },
  props: {
    toggleVerticalMenuActive: {
      type: Function,
      default: () => {},
    },
  },

}
</script>
