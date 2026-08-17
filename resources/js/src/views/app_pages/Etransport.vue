<template>
  <div>
    <b-tabs content-class="pt-1">
      <b-tab
        title="Declarații UIT"
        active
      >
        <declaratii-uit />
      </b-tab>

      <b-tab title="Notificări">
        <b-row class="mb-2">
          <b-col
            md="6"
            class="mb-2"
          >
            <b-card
              class="h-100 border mb-0"
              body-class="p-2"
            >
              <h6 class="mb-2">
                Preia notificările din e-Transport
              </h6>

              <b-row no-gutters>
                <b-col
                  cols="7"
                  class="pr-1"
                >
                  <b-form-input
                    v-model="cif"
                    size="sm"
                    placeholder="CIF declarant"
                  />
                </b-col>
                <b-col cols="5">
                  <b-form-input
                    v-model.number="zile"
                    type="number"
                    min="1"
                    max="60"
                    size="sm"
                    placeholder="Zile"
                  />
                </b-col>
              </b-row>

              <div class="d-flex align-items-center mt-2">
                <b-button
                  variant="primary"
                  size="sm"
                  :disabled="!cif || sincronizareInCurs"
                  @click="sincronizeaza"
                >
                  <b-spinner
                    v-if="sincronizareInCurs"
                    small
                    class="mr-1"
                  />
                  Preia
                </b-button>
                <small class="text-muted ml-2">
                  Maximum 60 de zile. Se preiau stările finale și notificările cu erori.
                </small>
              </div>
            </b-card>
          </b-col>

          <b-col
            md="6"
            class="mb-2"
          >
            <b-card
              class="h-100 border mb-0"
              body-class="p-2"
            >
              <h6 class="mb-2">
                Depune o declarație de transport
              </h6>

              <b-row no-gutters>
                <b-col
                  cols="5"
                  class="pr-1"
                >
                  <b-form-input
                    v-model="cifDepunere"
                    size="sm"
                    placeholder="CIF declarant"
                  />
                </b-col>
                <b-col cols="7">
                  <b-form-file
                    v-model="fisier"
                    size="sm"
                    accept=".xml"
                    placeholder="Declarația XML..."
                    browse-text="Alege"
                  />
                </b-col>
              </b-row>

              <div class="d-flex align-items-center mt-2">
                <b-button
                  variant="outline-primary"
                  size="sm"
                  :disabled="!fisier || !cifDepunere || depunereInCurs"
                  @click="depune"
                >
                  <b-spinner
                    v-if="depunereInCurs"
                    small
                    class="mr-1"
                  />
                  Depune
                </b-button>
                <small class="text-muted ml-2">
                  La succes ANAF întoarce indexul de încărcare și UIT-ul.
                </small>
              </div>
            </b-card>
          </b-col>
        </b-row>

        <!--
      Legătura cu ANAF se poate face în două feluri: prin certificatul de pe token
      (programul local) sau prin autorizare OAuth2. Cardul arată mereu ce se
      folosește acum și permite autorizarea OAuth2.
    -->
        <b-card
          class="border mb-2"
          body-class="p-2"
        >
          <b-row
            no-gutters
            class="align-items-center"
          >
            <b-col md="7">
              <h6 class="mb-1">
                Autorizare OAuth2 la ANAF
                <b-badge
                  :variant="mod === 'oauth' ? 'light-primary' : 'light-secondary'"
                  class="ml-1"
                >
                  {{ mod === 'oauth' ? 'mod activ' : 'mod inactiv — se folosește certificatul digital' }}
                </b-badge>
              </h6>

              <small
                v-if="!oauthConfigurat"
                class="text-danger d-block"
              >
                Aplicația nu are datele de client primite de la ANAF (CLIENT_ANAF_ID și CLIENT_ANAF_SECRET).
              </small>
              <small
                v-else-if="!cif"
                class="text-muted d-block"
              >
                Completați CIF-ul de mai sus, apoi autorizați aplicația la ANAF.
              </small>
              <small
                v-else-if="autorizare.autorizat"
                class="text-success d-block"
              >
                Autorizat pentru {{ cif }} până la {{ autorizare.expira_la }}
                <span v-if="autorizare.zile_ramase !== null">({{ autorizare.zile_ramase }} zile rămase)</span>
              </small>
              <small
                v-else
                class="text-muted d-block"
              >
                Nicio autorizare pentru {{ cif }}.
                <span v-if="mod === 'oauth'">Apelurile vor fi refuzate de ANAF.</span>
              </small>

              <small
                v-if="oauthRedirect"
                class="text-muted d-block"
              >
                Adresa de retur declarată la ANAF: {{ oauthRedirect }}
              </small>
            </b-col>
            <b-col
              md="5"
              class="text-md-right"
            >
              <b-button
                variant="outline-primary"
                size="sm"
                :disabled="!cif || !oauthConfigurat"
                @click="autorizeaza"
              >
                Autorizează la ANAF
              </b-button>
              <b-button
                variant="outline-secondary"
                size="sm"
                class="ml-1"
                :disabled="!cif"
                @click="verificaAutorizarea"
              >
                Verifică
              </b-button>
            </b-col>
          </b-row>
        </b-card>

        <b-alert
          v-if="info"
          show
          variant="info"
          class="py-2"
        >
          {{ info }}
        </b-alert>

        <b-alert
          v-if="eroare"
          show
          variant="danger"
          class="py-2"
        >
          {{ eroare }}
        </b-alert>

        <b-card
          class="border"
          body-class="p-2"
        >
          <b-row
            no-gutters
            class="mb-2 align-items-center"
          >
            <b-col
              md="3"
              class="pr-1"
            >
              <b-form-input
                v-model="filtre.uit"
                size="sm"
                placeholder="Caută după UIT"
                @change="incarcaLista"
              />
            </b-col>
            <b-col
              md="3"
              class="pr-1"
            >
              <b-form-input
                v-model="filtre.cif"
                size="sm"
                placeholder="Caută după CIF declarant"
                @change="incarcaLista"
              />
            </b-col>
            <b-col md="3">
              <b-form-checkbox
                v-model="filtre.doar_erori"
                @change="incarcaLista"
              >
                Doar cele cu erori
              </b-form-checkbox>
            </b-col>
          </b-row>

          <b-table
            :items="notificari"
            :fields="campuri"
            :busy="listaInCurs"
            responsive
            striped
            small
            class="mb-0"
            show-empty
            empty-text="Nicio notificare. Apăsați „Preia” pentru a le aduce de la ANAF."
          >
            <template #table-busy>
              <div class="text-center my-2">
                <b-spinner class="align-middle mr-1" />
                Se încarcă...
              </div>
            </template>

            <template #cell(uit)="rand">
              <div>{{ rand.item.uit || '-' }}</div>
              <b-badge :variant="rand.item.are_erori ? 'danger' : 'success'">
                {{ rand.item.are_erori ? 'cu erori' : 'validă' }}
              </b-badge>
              <b-badge
                variant="light"
                class="ml-1"
              >
                {{ rand.item.tip_eticheta }}
              </b-badge>
            </template>

            <template #cell(transport)="rand">
              <div class="small">
                {{ rand.item.operatiune || '-' }}
              </div>
              <div class="small text-muted">
                transport {{ rand.item.data_transp || '-' }}
              </div>
            </template>

            <template #cell(parteneri)="rand">
              <div class="small">
                {{ rand.item.partener || '-' }}
              </div>
              <div class="small text-muted">
                transportator: {{ rand.item.transportator || '-' }}
              </div>
            </template>

            <template #cell(mesaje)="rand">
              <div
                v-for="(mesaj, index) in rand.item.mesaje || []"
                :key="index"
                class="small"
                :class="mesaj.tip === 'ERR' ? 'text-danger' : 'text-muted'"
              >
                {{ mesaj.mesaj }}
              </div>
              <span
                v-if="!(rand.item.mesaje || []).length"
                class="small text-muted"
              >—</span>
            </template>

            <template #cell(actiuni)="rand">
              <b-button
                v-if="rand.item.id_incarcare"
                v-b-tooltip.hover.top.window.v-light
                size="sm"
                variant="outline-secondary"
                class="btn-icon"
                title="Verifică starea la ANAF"
                @click="verificaStarea(rand.item)"
              >
                <feather-icon icon="RefreshCwIcon" />
              </b-button>
            </template>
          </b-table>
        </b-card>

        <b-modal
          v-model="stareVizibila"
          title="Starea declarației la ANAF"
          ok-only
          ok-title="Închide"
          size="lg"
        >
          <pre class="small mb-0">{{ stareRaspuns }}</pre>
        </b-modal>
      </b-tab>
    </b-tabs>
  </div>
</template>

<script>
import DeclaratiiUit from './dianasoft/etransport/DeclaratiiUit.vue'

export default {
  name: 'Etransport',
  components: { DeclaratiiUit },
  data() {
    return {
      cif: '',
      zile: 30,
      cifDepunere: '',
      fisier: null,
      notificari: [],
      filtre: { uit: '', cif: '', doar_erori: false },
      info: '',
      eroare: '',
      listaInCurs: false,
      sincronizareInCurs: false,
      depunereInCurs: false,
      stareVizibila: false,
      stareRaspuns: '',
      mod: 'certificat',
      oauthConfigurat: false,
      oauthRedirect: '',
      autorizare: { autorizat: false, expira_la: null, zile_ramase: null },
      campuri: [
        { key: 'uit', label: 'UIT / stare' },
        { key: 'cod_decl', label: 'Declarant' },
        { key: 'transport', label: 'Operațiune' },
        { key: 'parteneri', label: 'Parteneri' },
        { key: 'vehicul', label: 'Vehicul' },
        { key: 'mesaje', label: 'Mesaje ANAF' },
        { key: 'data_creare', label: 'Creată la' },
        { key: 'actiuni', label: '' },
      ],
    }
  },
  created() {
    document.title = `${window.app_name} -> Dispecer e-Transport`

    // Cererile merg autentificate și în contextul societății selectate.
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

    const certificat = window.localStorage.getItem('anaf_certificat_activ')
    if (certificat) {
      this.$http.defaults.headers.common['X-Certificat-Id'] = certificat
    }

    this.incarcaLista()
    this.verificaAutorizarea()
  },
  methods: {
    /** Autorizarea se face la ANAF, într-o filă nouă, cu certificatul digital. */
    autorizeaza() {
      this.eroare = ''

      this.$http.get('/anaf-etransport/oauth/url', { params: { cif: this.cif } })
        .then(raspuns => {
          window.open(raspuns.data.url, '_blank')
          this.info = 'Finalizați autorizarea în fila deschisă, apoi apăsați „Verifică”.'
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Adresa de autorizare nu a putut fi obținută')
        })
    },
    verificaAutorizarea() {
      const params = this.cif ? { cif: this.cif } : {}

      this.$http.get('/anaf-etransport/oauth/stare', { params })
        .then(raspuns => {
          this.mod = raspuns.data.mod
          this.oauthConfigurat = raspuns.data.configurat
          this.oauthRedirect = raspuns.data.redirect_uri
          this.autorizare = raspuns.data.data
        })
        .catch(() => {
          // starea autorizării nu blochează restul paginii
        })
    },
    incarcaLista() {
      this.listaInCurs = true
      const params = {}
      if (this.filtre.uit) params.uit = this.filtre.uit
      if (this.filtre.cif) params.cif = this.filtre.cif
      if (this.filtre.doar_erori) params.doar_erori = 1

      this.$http.get('/anaf-etransport', { params })
        .then(raspuns => {
          this.notificari = raspuns.data.data || []
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Nu s-au putut încărca notificările')
        })
        .finally(() => {
          this.listaInCurs = false
        })
    },
    sincronizeaza() {
      this.eroare = ''
      this.info = ''
      this.sincronizareInCurs = true

      this.$http.post('/anaf-etransport/sincronizeaza', { cif: this.cif, zile: this.zile })
        .then(raspuns => {
          const r = raspuns.data.data
          this.info = `${r.preluate} notificări primite, ${r.noi} noi, ${r.cu_erori} cu erori.`
          this.incarcaLista()
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Preluarea notificărilor a eșuat')
        })
        .finally(() => {
          this.sincronizareInCurs = false
        })
    },
    depune() {
      this.eroare = ''
      this.info = ''
      this.depunereInCurs = true

      const formular = new FormData()
      formular.append('cif', this.cifDepunere)
      formular.append('fisier', this.fisier)

      this.$http.post('/anaf-etransport/depune', formular, { headers: { 'Content-Type': 'multipart/form-data' } })
        .then(raspuns => {
          this.info = `Declarație depusă, index de încărcare ${raspuns.data.index_incarcare}.`
          this.fisier = null
          this.incarcaLista()
        })
        .catch(err => {
          const date = err.response && err.response.data
          const erori = date && date.erori ? date.erori : []

          this.eroare = erori.length
            ? erori.map(e => e.errorMessage || e.mesaj || e).join(' | ')
            : this.mesajEroare(err, 'Depunerea a eșuat')
        })
        .finally(() => {
          this.depunereInCurs = false
        })
    },
    verificaStarea(notificare) {
      this.eroare = ''

      this.$http.get('/anaf-etransport/stare', { params: { id_incarcare: notificare.id_incarcare } })
        .then(raspuns => {
          this.stareRaspuns = JSON.stringify(raspuns.data.data, null, 2)
          this.stareVizibila = true
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Starea nu a putut fi verificată')
        })
    },
    mesajEroare(err, implicit) {
      return err.response && err.response.data && err.response.data.message
        ? err.response.data.message
        : implicit
    },
  },
}
</script>
