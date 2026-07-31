<template>
  <div>
    <b-card no-body>
      <b-tabs
        v-model="tabActiv"
        card
      >
        <!-- Se afișează doar taburile pentru care utilizatorul are drept.
             pt-0: conținutul începe imediat sub bara de taburi, la fel în toate. -->
        <b-tab
          v-for="tab in taburiVizibile"
          :key="tab.cheie"
          :title="tab.titlu"
          class="pt-0"
          lazy
        >
          <component :is="tab.componenta" />
        </b-tab>
      </b-tabs>

      <div
        v-if="!taburiVizibile.length"
        class="p-3 text-muted"
      >
        Nu aveți drepturi pentru nicio operațiune din acest modul.
      </div>
    </b-card>
  </div>
</template>

<script>
import SocietatiTab from './spv/Societati.vue'
import DeclaratiiTab from './spv/Declaratii.vue'
import MesajeTab from './spv/Mesaje.vue'
import SolicitariTab from './spv/Solicitari.vue'
import CertificateTab from './spv/Certificate.vue'
import JurnalTab from './spv/Jurnal.vue'
import UtilizatoriTab from './spv/Utilizatori.vue'

/*
 * Drepturile pe operațiuni sunt dezactivate: accesul ține doar de societatea
 * selectată. Pentru reactivare se completează „drepturi” (ex. ['verificareMesajeSpv'])
 * și se pune middleware-ul corespunzător pe rutele din routes/api.php.
 */
const TABURI = [
  {
    cheie: 'certificate', titlu: 'Certificate digitale', componenta: 'CertificateTab', drepturi: [],
  },
  {
    cheie: 'entitati', titlu: 'Entități înrolate', componenta: 'SocietatiTab', drepturi: [],
  },
  {
    cheie: 'declaratii', titlu: 'Declarații fiscale', componenta: 'DeclaratiiTab', drepturi: [],
  },
  {
    cheie: 'mesaje', titlu: 'Mesaje ANAF', componenta: 'MesajeTab', drepturi: [],
  },
  {
    cheie: 'solicitari', titlu: 'Solicitări ANAF', componenta: 'SolicitariTab', drepturi: [],
  },
  {
    cheie: 'jurnal', titlu: 'Jurnal activitate', componenta: 'JurnalTab', drepturi: [],
  },
  // Doar administratorul firmei; ceilalți nici nu văd fila.
  {
    cheie: 'utilizatori', titlu: 'Utilizatori', componenta: 'UtilizatoriTab', drepturi: [], doarAdministrator: true,
  },
]

export default {
  name: 'Spv',
  components: {
    SocietatiTab, DeclaratiiTab, MesajeTab, SolicitariTab, CertificateTab, JurnalTab, UtilizatoriTab,
  },
  data() {
    return {
      tabActiv: 0,
      administrator: false,
    }
  },
  computed: {
    taburiVizibile() {
      return TABURI.filter(tab => {
        if (tab.doarAdministrator && !this.administrator) return false

        return tab.drepturi.length === 0 || tab.drepturi.some(drept => this.poate(drept))
      })
    },
  },
  watch: {
    // Tabul rămâne în adresă, ca să poată fi trimis ca link sau reîncărcat.
    tabActiv(index) {
      const tab = this.taburiVizibile[index]
      if (!tab) return

      document.title = `${window.app_name} -> SPV Curier: ${tab.titlu}`

      if (this.$route.query.tab !== tab.cheie) {
        this.$router.replace({ query: { ...this.$route.query, tab: tab.cheie } }).catch(() => {})
      }
    },
  },
  created() {
    // Toate cererile modulului merg autentificate și în contextul societății
    // selectate: serverul le folosește ca să returneze doar datele acelui client.
    const token = window.localStorage.getItem('accessToken')
    if (token) {
      this.$http.defaults.headers.common.Authorization = `Bearer ${token}`
    }

    const societate = window.localStorage.getItem('societateaCurenta')
    if (societate) {
      try {
        this.$http.defaults.headers.common.AuthorizationHeader = JSON.parse(societate).id
      } catch (e) {
        // societate salvată într-un format vechi — se cere reautentificarea
      }
    }

    // Certificatul ales însoțește cererile, ca acestea să ajungă la calculatorul
    // pe care este conectat tokenul respectiv.
    const certificat = window.localStorage.getItem('anaf_certificat_activ')
    if (certificat) {
      this.$http.defaults.headers.common['X-Certificat-Id'] = certificat
    }

    // Fila „Utilizatori” o vede doar administratorul firmei. Răspunsul se cere
    // de la server, nu din localStorage: dreptul se poate schimba între timp.
    this.$http.get('/context')
      .then(({ data }) => {
        this.administrator = data.data.administrator_client || data.data.super_admin
      })
      .catch(() => {
        this.administrator = false
      })

    // Tabul se identifică prin nume, nu prin poziție: pozițiile diferă de la un
    // utilizator la altul, în funcție de drepturi.
    const cerut = this.taburiVizibile.findIndex(tab => tab.cheie === this.$route.query.tab)
    this.tabActiv = cerut >= 0 ? cerut : 0

    const activ = this.taburiVizibile[this.tabActiv]
    if (activ) {
      document.title = `${window.app_name} -> SPV Curier: ${activ.titlu}`
    }
  },
  methods: {
    // Proprietarul societății are toate drepturile, ca și pe server.
    poate(drept) {
      if (window.localStorage.getItem('userRole') === 'owner') return true

      return this.$userpermitt.can(drept)
    },
  },
}
</script>
