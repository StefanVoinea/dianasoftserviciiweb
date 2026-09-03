<template>
  <b-nav-item-dropdown
    class="dropdown-skin mr-25"
    right
  >
    <template #button-content>
      <feather-icon
        v-b-tooltip.hover.bottom="'Înfățișarea aplicației'"
        class="text-body"
        icon="DropletIcon"
        size="21"
      />
    </template>

    <b-dropdown-header class="pb-50">
      <span class="font-weight-bolder">Înfățișarea</span>
    </b-dropdown-header>

    <b-dropdown-item
      v-for="skin in skinuri"
      :key="skin.id"
      class="d-flex"
      @click="alege(skin.id)"
    >
      <div class="d-flex align-items-start">
        <feather-icon
          :icon="skin.icon"
          size="16"
          class="mr-75 mt-25"
          :class="skin.id === skinCurent ? 'text-primary' : 'text-muted'"
        />
        <div>
          <div :class="skin.id === skinCurent ? 'font-weight-bolder' : ''">
            {{ skin.nume }}
          </div>
          <small class="text-muted">{{ skin.descriere }}</small>
        </div>
        <feather-icon
          v-if="skin.id === skinCurent"
          icon="CheckIcon"
          size="16"
          class="ml-75 mt-25 text-primary"
        />
      </div>
    </b-dropdown-item>
  </b-nav-item-dropdown>
</template>

<script>
import {
  BNavItemDropdown, BDropdownItem, BDropdownHeader, VBTooltip,
} from 'bootstrap-vue'
import { skinuri } from '@/libs/ds-skinuri'

/**
 * Alegerea înfățișării, aceleași skinuri ca în aplicația DV Auto.
 *
 * Stă în bara de sus, nu în personalizatorul Vuexy: acela e stins în această
 * aplicație (themeConfig: customizer false), iar o înfățișare pe care omul n-o
 * poate schimba n-are niciun rost.
 */
export default {
  components: {
    BNavItemDropdown,
    BDropdownItem,
    BDropdownHeader,
  },
  directives: {
    'b-tooltip': VBTooltip,
  },
  data() {
    return {
      skinuri,
    }
  },
  computed: {
    skinCurent() {
      return this.$store.state.appConfig.layout.dsSkin
    },
  },
  methods: {
    alege(id) {
      this.$store.commit('appConfig/UPDATE_DS_SKIN', id)
    },
  },
}
</script>
