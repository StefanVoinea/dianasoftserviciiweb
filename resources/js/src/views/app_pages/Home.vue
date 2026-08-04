<template>
  <div class="container-fluid">
    <!-- Înștiințările primite, până sunt citite -->
    <div
      v-if="notificari.length"
      class="row"
    >
      <div class="col-12">
        <b-alert
          v-for="notificare in notificari"
          :key="notificare.id"
          show
          :variant="variantaNotificare(notificare)"
          class="d-flex align-items-start py-1 px-2"
        >
          <feather-icon
            :icon="iconaNotificare(notificare)"
            size="18"
            class="mr-1 mt-25 flex-shrink-0"
          />
          <div class="flex-grow-1">
            <div class="font-weight-bolder">
              {{ notificare.titlu }}
            </div>
            <div
              class="mesaj-notificare"
              v-text="notificare.mesaj"
            />
            <small class="text-muted">{{ notificare.primita_la }}</small>
          </div>
          <b-button
            size="sm"
            variant="flat-secondary"
            class="flex-shrink-0"
            @click="amCitit(notificare)"
          >
            Am citit
          </b-button>
        </b-alert>
      </div>
    </div>

    <div class="row">
      <div class="col-12 col-md-6 col-lg-4">
        <div class="card h-100">
          <div class="card-body">
            <!-- Sigla tine loc de titlu: poarta numele si culoarea modulului. -->
            <img
              :src="siglaSpv"
              alt="DianaSoft → SPV Curier"
              class="mb-2 d-block"
              style="height: 32px; width: auto; max-width: 100%;"
            >
            <p class="text-muted mb-3">
              Deschide mesajele ANAF și verificările SPV direct din dashboard.
            </p>
            <button
              class="btn text-white"
              style="background-color: #22406f; border-color: #22406f;"
              @click="goToSpv"
            >
              Acceseaza
            </button>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-4">
        <div class="card h-100">
          <div class="card-body">
            <h4 class="mb-2">
              Dispecer e-Transport
            </h4>
            <p class="text-muted mb-3">
              Declară transporturile de bunuri, urmărește UIT-urile și starea notificărilor la ANAF.
            </p>
            <button
              class="btn btn-primary"
              @click="goToEtransport"
            >
              Acceseaza
            </button>
          </div>
        </div>
      </div>

      <div
        v-if="superAdmin"
        class="col-12 col-md-6 col-lg-4"
      >
        <div class="card h-100 border-primary">
          <div class="card-body">
            <h4 class="mb-2">
              Administrare clienți
            </h4>
            <p class="text-muted mb-3">
              Firme, conturi, module, tarife și perioade de probă.
            </p>
            <button
              class="btn btn-primary"
              @click="goToAdministrare"
            >
              Acceseaza
            </button>
          </div>
        </div>
      </div>

      <div class="col-12 col-md-6 col-lg-4">
        <div class="card h-100">
          <div class="card-body">
            <h4 class="mb-2">
              Grefier alert
            </h4>
            <p class="text-muted mb-3">
              Caută dosare, părți și termene în ECRIS și vezi ședințele instanțelor.
            </p>
            <button
              class="btn btn-primary"
              @click="goToPortalJust"
            >
              Acceseaza
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

export default {

  name: 'Home',

  data() {
    return {
      tblRecords: [],
      showLoading: false,
      // Cardul de administrare apare doar pentru contul care are voie acolo
      superAdmin: false,
      // Înștiințările necitite, arătate până sunt confirmate
      notificari: [],
      // eslint-disable-next-line global-require, import/no-unresolved
      siglaSpv: require('@/assets/images/sigle/spv-curier-orizontal.svg'),
    }
  },
  computed: {
    asset_path() {
      return window.asset_path
    },
  },

  created() {
    document.title = `${window.app_name}->Home`

    if (!this.$store.getters['app/loggedIn']) {
      this.$store.dispatch('app/destroyToken')
      this.$router.push({ name: 'auth-login' })
    }

    // Serverul spune cine e: dreptul de administrare nu se ține în browser.
    this.$http.get('/context')
      .then(({ data }) => {
        this.superAdmin = data.data.super_admin
      })
      .catch(() => {
        this.superAdmin = false
      })

    this.incarcaNotificari()
  },
  methods: {
    incarcaNotificari() {
      this.$http.get('/notificari')
        .then(({ data }) => {
          this.notificari = data.data.filter(notificare => !notificare.citita)
        })
        .catch(() => {
          this.notificari = []
        })
    },
    /** Confirmată, dispare de aici; rămâne scrisă pentru evidență. */
    amCitit(notificare) {
      this.$http.post(`/notificari/${notificare.id}/citita`)
        .then(() => {
          this.notificari = this.notificari.filter(alta => alta.id !== notificare.id)
        })
    },
    variantaNotificare(notificare) {
      if (notificare.importanta === 'urgenta') return 'danger'

      return notificare.importanta === 'avertizare' ? 'warning' : 'info'
    },
    iconaNotificare(notificare) {
      if (notificare.importanta === 'urgenta') return 'AlertOctagonIcon'

      return notificare.importanta === 'avertizare' ? 'AlertTriangleIcon' : 'InfoIcon'
    },
    goToAdministrare() {
      this.$router.push({ name: 'administrare' })
    },
    goToSpv() {
      this.$router.push({ name: 'spv' })
    },
    goToEtransport() {
      this.$router.push({ name: 'etransport' })
    },
    goToPortalJust() {
      this.$router.push({ name: 'portal-just' })
    },
    handleErrors(error) {
      if (error.status === 401) {
        // Sesiunea a expirat: se șterge tokenul și se cere reautentificarea.
        this.$store.dispatch('app/destroyToken')
          .finally(() => {
            this.$router.push({ name: 'auth-login' })
          })
      } else {
        // this.showLoading=false

        // this.$vs.notify({
        //     title: "Eroare la conectarea la server!",
        //     text: error.data.error,
        //     iconPack: "feather",
        //     icon: "icon-alert-circle",
        //     color: "danger"
        // })
      }
    },

  },

}

</script>

<style scoped>
/* Mesajul e scris de om, cu randuri cu tot: se pastreaza asa cum l-a scris. */
.mesaj-notificare {
  white-space: pre-wrap;
}
</style>
