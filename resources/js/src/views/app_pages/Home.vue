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
      <div
        v-for="modul in vizibile"
        :key="modul.cheie"
        class="col-12 col-md-6 col-lg-4"
      >
        <div class="card h-100">
          <div class="card-body">
            <!-- Unde modulul are siglă, ea ține loc de titlu: poartă și numele,
                 și culoarea lui. -->
            <img
              v-if="modul.sigla"
              :src="modul.sigla"
              :alt="'DianaSoft → ' + modul.nume"
              class="mb-2 d-block sigla-card"
            >
            <h4
              v-else
              class="mb-2"
            >
              {{ modul.nume }}
            </h4>
            <p class="text-muted mb-3">
              {{ modul.descriere }}
            </p>
            <button
              class="btn"
              :class="modul.culoare ? 'text-white' : 'btn-primary'"
              :style="modul.culoare ? { backgroundColor: modul.culoare, borderColor: modul.culoare } : null"
              @click="mergiLa(modul)"
            >
              Accesează
            </button>
          </div>
        </div>
      </div>

      <!-- Administrarea nu e un modul vândut: e unealta noastră, și se arată
           numai celui care are voie acolo. -->
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
              Accesează
            </button>
          </div>
        </div>
      </div>

      <!-- Un cont fără niciun modul nu rămâne în fața unei pagini goale, fără
           să înțeleagă de ce. -->
      <div
        v-if="!vizibile.length && !superAdmin"
        class="col-12"
      >
        <div class="card">
          <div class="card-body text-center py-3">
            <h4 class="mb-1">
              Nu aveți încă niciun modul
            </h4>
            <p class="text-muted mb-0">
              Modulele se dau de administratorul firmei dumneavoastră, dintre
              cele cuprinse în abonament.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

import { contextul, moduleStiute } from '@/libs/module'

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
      /*
       * Modulele vândute, cu cheia din abonament — aceeași după care se face și
       * antetul, ca omul să vadă în amândouă locurile același lucru.
       */
      module: [
        {
          cheie: 'spv',
          nume: 'SPV Curier',
          descriere: 'Deschide mesajele ANAF și verificările SPV direct din dashboard.',
          ruta: 'spv',
          culoare: '#22406f',
          // eslint-disable-next-line global-require, import/no-unresolved
          sigla: require('@/assets/images/sigle/spv-curier-orizontal.svg'),
        },
        {
          cheie: 'etransport',
          nume: 'Dispecer e-Transport',
          descriere: 'Declară transporturile de bunuri, urmărește UIT-urile și starea notificărilor la ANAF.',
          ruta: 'etransport',
        },
        {
          cheie: 'portal_just',
          nume: 'Grefier alert',
          descriere: 'Caută dosare, părți și termene în ECRIS și vezi ședințele instanțelor.',
          ruta: 'portal-just',
        },
      ],
      /*
       * Modulele date contului acesta. „null" înseamnă că încă nu se știe —
       * atunci se arată toate, ca la antet: ascunderea de aici e înlesnire, nu
       * pază, iar cererile către un modul nedat sunt oprite oricum de server.
       */
      alese: moduleStiute(),
    }
  },
  computed: {
    asset_path() {
      return window.asset_path
    },
    /** Modulele pe care le are contul; cât timp nu se știe, se arată toate. */
    vizibile() {
      if (this.alese === null) return this.module

      return this.module.filter(modul => this.alese.indexOf(modul.cheie) !== -1)
    },
  },

  created() {
    document.title = `${window.app_name}->Home`

    if (!this.$store.getters['app/loggedIn']) {
      this.$store.dispatch('app/destroyToken')
      this.$router.push({ name: 'auth-login' })
    }

    /*
     * Serverul spune cine e și ce module are: drepturile nu se țin în browser.
     * Se folosește același răspuns pe care îl cer și antetul, și paznicul
     * rutelor — o singură cerere pe încărcare de pagină, nu una de fiecare.
     */
    contextul()
      .then(context => {
        this.superAdmin = Boolean(context.super_admin)

        // Un server mai vechi nu trimite deloc câmpul: lipsa lui nu e o
        // interdicție, e o necunoscută.
        this.alese = Array.isArray(context.module) ? context.module : null
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
    mergiLa(modul) {
      this.$router.push({ name: modul.ruta })
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
/* Sigla ține loc de titlu, deci stă la înălțimea unui titlu. */
.sigla-card {
  height: 32px;
  width: auto;
  max-width: 100%;
}

/* Mesajul e scris de om, cu randuri cu tot: se pastreaza asa cum l-a scris. */
.mesaj-notificare {
  white-space: pre-wrap;
}
</style>
