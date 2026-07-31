<template>
  <div class="nav">
    <ul class="nav navbar-nav">
      <li class="nav-item">

        <h4
          class="brand-text mt-1 mr-1"
          style="color:#8075f1;"
        >
          {{ denumireSocCurenta }}
        </h4>
      </li>
    </ul>
  </div>

</template>

<script>
// In antet a ramas doar denumirea societatii curente: selectorul de luna si cel
// de gestiune au fost eliminate. Valorile lor raman in store pentru modulele
// care le folosesc.
import store from '@/store'

export default {
  data() {
    return {
      denumireSocCurenta: store.state.app.societateaCurenta
        ? JSON.parse(store.state.app.societateaCurenta).denumire
        : '',
    }
  },
}
</script>

<style lang="scss" scoped>
@import '~@core/scss/base/bootstrap-extended/include';

ul
{
  list-style: none;
  padding: 0;
  margin: 0;
}
p {
  margin: 0;
}

.nav-bookmar-content-overlay {
    position: fixed;
    opacity: 0;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    -webkit-transition: all 0.7s;
    transition: all 0.7s;
    z-index: -1;

    &:not(.show) {
      pointer-events: none;
    }

    &.show {
      cursor: pointer;
      z-index: 10;
      opacity: 1;
    }
}
</style>
