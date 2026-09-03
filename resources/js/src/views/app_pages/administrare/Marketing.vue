<template>
  <div>
    <b-alert
      v-if="mesaj"
      show
      variant="success"
      class="py-1"
      dismissible
      @dismissed="mesaj = ''"
    >
      {{ mesaj }}
    </b-alert>

    <b-alert
      v-if="eroare"
      show
      variant="danger"
      class="py-1"
      dismissible
      @dismissed="eroare = ''"
    >
      {{ eroare }}
    </b-alert>

    <b-row class="mb-1">
      <!-- Aducerea listei -->
      <b-col md="5">
        <b-card
          class="h-100 border mb-0"
          body-class="p-1"
        >
          <h6 class="mb-50">
            Încarcă lista de firme
          </h6>

          <small class="text-muted d-block mb-1">
            Fișier Excel cu coloanele listelor CECCAR: județ, denumire firmă, CUI, telefon,
            e-mail. Rândurile fără adresă se lasă deoparte. Firmele care există deja își
            înnoiesc datele, dar <strong>nu</strong> și dezabonarea: aceea e hotărârea lor.
          </small>

          <b-form-file
            v-model="fisier"
            size="sm"
            accept=".xlsx,.xls,.csv"
            placeholder="Alegeți fișierul…"
            :disabled="importInCurs"
            @input="importa"
          />

          <div
            v-if="importInCurs"
            class="mt-50 d-flex align-items-center"
          >
            <b-spinner
              small
              class="mr-50"
            />
            <small class="text-muted">Se citește fișierul…</small>
          </div>
        </b-card>
      </b-col>

      <!-- Cate sunt si in ce stare -->
      <b-col md="7">
        <b-card
          class="h-100 border mb-0"
          body-class="p-1"
        >
          <div class="d-flex align-items-center justify-content-between mb-1">
            <h6 class="mb-0">
              În evidență
            </h6>

            <!-- Masura cinstita a unei campanii: cati din cei carora li s-a
                 scris au apasat butonul. -->
            <small
              v-if="procentRaspuns !== null"
              class="text-muted"
            >
              au răspuns <strong>{{ procentRaspuns }}%</strong> dintre cei cărora li s-a scris
            </small>
          </div>

          <b-row class="text-center">
            <b-col
              v-for="cifra in cifre"
              :key="cifra.cheie"
              cols="4"
              md="2"
            >
              <div
                class="cursor-pointer py-25"
                @click="filtre.stare = cifra.cheie === 'toate' ? '' : cifra.cheie; incarca()"
              >
                <h4
                  class="mb-0"
                  :class="cifra.culoare"
                >
                  {{ sumar[cifra.cheie] || 0 }}
                </h4>
                <small class="text-muted">{{ cifra.eticheta }}</small>
              </div>
            </b-col>
          </b-row>
        </b-card>
      </b-col>
    </b-row>

    <!-- Cautarea si alegerea -->
    <b-card
      class="border mb-1"
      body-class="p-1"
    >
      <b-row no-gutters>
        <b-col
          md="4"
          class="pr-md-1 mb-50 mb-md-0"
        >
          <b-form-input
            v-model="filtre.cauta"
            size="sm"
            placeholder="Caută după denumire, e-mail sau CUI"
            debounce="400"
            @update="incarca()"
          />
        </b-col>
        <b-col
          md="3"
          class="pr-md-1 mb-50 mb-md-0"
        >
          <b-form-select
            v-model="filtre.judet"
            size="sm"
            :options="optiuniJudete"
            @change="incarca()"
          />
        </b-col>
        <b-col
          md="3"
          class="pr-md-1 mb-50 mb-md-0"
        >
          <b-form-select
            v-model="filtre.stare"
            size="sm"
            :options="optiuniStare"
            @change="incarca()"
          />
        </b-col>
        <b-col
          md="2"
          class="text-md-right"
        >
          <b-button
            variant="primary"
            size="sm"
            :disabled="!alesi.length"
            @click="deschideScrisoarea"
          >
            Scrie celor {{ alesi.length }} aleși
          </b-button>
        </b-col>
      </b-row>
    </b-card>

    <b-card
      class="border mb-0"
      body-class="p-0"
    >
      <b-table
        :items="contacte"
        :fields="campuri"
        :busy="seIncarca"
        responsive
        hover
        small
        class="mb-0"
        empty-text="Nicio firmă în listă. Încărcați un fișier."
        show-empty
      >
        <template #head(alege)>
          <b-form-checkbox
            :checked="totiAlesi"
            :disabled="!contactePotFiAlese.length"
            @change="alegeTot"
          />
        </template>

        <template #cell(alege)="rand">
          <b-form-checkbox
            :checked="alesi.includes(rand.item.id)"
            :disabled="!rand.item.abonat"
            @change="comuta(rand.item.id)"
          />
        </template>

        <template #cell(denumire)="rand">
          <div class="font-weight-bold">
            {{ rand.item.denumire }}
          </div>
          <small class="text-muted">{{ rand.item.email }}</small>
        </template>

        <template #cell(trimise)="rand">
          <span :class="rand.item.cate_trimiteri ? '' : 'text-muted'">
            {{ rand.item.cate_trimiteri || '—' }}
          </span>
        </template>

        <template #cell(ultima)="rand">
          <span :class="rand.item.ultima_trimitere_la ? '' : 'text-muted'">
            {{ dataScurta(rand.item.ultima_trimitere_la) }}
          </span>
        </template>

        <template #cell(demo)="rand">
          <div v-if="rand.item.demo_cerut_la">
            <b-badge variant="light-warning">
              {{ dataScurta(rand.item.demo_cerut_la) }}
            </b-badge>
            <small
              v-if="rand.item.demo_persoana || rand.item.demo_telefon"
              class="d-block text-muted"
            >
              {{ [rand.item.demo_persoana, rand.item.demo_telefon].filter(Boolean).join(' · ') }}
            </small>
          </div>
          <span
            v-else
            class="text-muted"
          >—</span>
        </template>

        <template #cell(stare)="rand">
          <b-badge
            v-if="!rand.item.abonat"
            variant="light-secondary"
          >
            dezabonat
          </b-badge>
          <b-badge
            v-else-if="rand.item.cate_trimiteri"
            variant="light-success"
          >
            {{ rand.item.cate_trimiteri }} trimise
          </b-badge>
          <b-badge
            v-else
            variant="light-primary"
          >
            nescris
          </b-badge>
        </template>
      </b-table>

      <div
        v-if="pagini > 1"
        class="d-flex justify-content-center py-1"
      >
        <b-pagination
          v-model="pagina"
          :total-rows="total"
          :per-page="100"
          size="sm"
          class="mb-0"
          @change="incarca($event)"
        />
      </div>
    </b-card>

    <!-- Scrisoarea -->
    <b-modal
      v-model="scrisoareaVizibila"
      title="Scrisoare către firmele alese"
      size="lg"
      ok-title="Trimite"
      cancel-title="Renunță"
      :ok-disabled="!scrisoare.subiect || !scrisoare.text || trimiteInCurs"
      @ok.prevent="trimite"
    >
      <b-form-group label="Subiect">
        <b-form-input
          v-model="scrisoare.subiect"
          placeholder="De pildă: SPV Curier — declarațiile ANAF, de pe telefon"
        />
      </b-form-group>

      <b-form-group label="Textul scrisorii">
        <b-form-textarea
          v-model="scrisoare.text"
          rows="12"
          placeholder="Bună ziua, {nume},&#10;&#10;…"
        />
        <small class="text-muted">
          Se pot folosi: <code>{nume}</code> (denumirea fără SRL), <code>{firma}</code>,
          <code>{cui}</code>, <code>{judet}</code>. Legătura de dezabonare se adaugă
          singură în fiecare scrisoare — nu trebuie scrisă aici.
        </small>
      </b-form-group>

      <b-form-group label="Numele campaniei (facultativ)">
        <b-form-input
          v-model="scrisoare.campanie"
          placeholder="spv-curier-septembrie"
        />
      </b-form-group>

      <b-alert
        v-if="previzualizare"
        show
        variant="secondary"
        class="py-1"
      >
        <small class="d-block text-muted mb-50">Așa ajunge la {{ previzualizare.catre }}:</small>
        <div style="white-space: pre-line;">
          {{ previzualizare.text }}
        </div>
      </b-alert>

      <b-button
        variant="flat-secondary"
        size="sm"
        :disabled="!scrisoare.text || !alesi.length"
        @click="vezi"
      >
        Vezi cum arată pentru primul ales
      </b-button>

      <div class="mt-1">
        <small class="text-muted">
          Se trimit <strong>{{ alesi.length }}</strong> scrisori. Cei care s-au dezabonat
          între timp sunt săriți fără să întrebe nimeni.
        </small>
      </div>
    </b-modal>
  </div>
</template>

<script>
import {
  BRow, BCol, BCard, BTable, BBadge, BButton, BFormInput, BFormSelect, BFormFile,
  BFormCheckbox, BFormGroup, BFormTextarea, BModal, BAlert, BSpinner, BPagination,
} from 'bootstrap-vue'

/**
 * Firmele cărora li se poate scrie despre aplicațiile noastre.
 *
 * Lista se încarcă dintr-un fișier, se filtrează, se aleg firme și li se scrie.
 * Dezabonarea nu se atinge de aici: ea se face de către om, din scrisoare, iar
 * cine a ieșit se vede în listă și nu mai poate fi ales.
 */
export default {
  components: {
    BRow,
    BCol,
    BCard,
    BTable,
    BBadge,
    BButton,
    BFormInput,
    BFormSelect,
    BFormFile,
    BFormCheckbox,
    BFormGroup,
    BFormTextarea,
    BModal,
    BAlert,
    BSpinner,
    BPagination,
  },
  data() {
    return {
      contacte: [],
      judete: [],
      sumar: {},
      total: 0,
      pagina: 1,
      pagini: 1,
      seIncarca: false,
      fisier: null,
      importInCurs: false,
      trimiteInCurs: false,
      mesaj: '',
      eroare: '',
      alesi: [],
      filtre: { cauta: '', judet: '', stare: '' },
      scrisoareaVizibila: false,
      scrisoare: { subiect: '', text: '', campanie: '' },
      previzualizare: null,
      campuri: [
        { key: 'alege', label: '', thStyle: { width: '40px' } },
        { key: 'denumire', label: 'Firma' },
        { key: 'judet', label: 'Județ' },
        { key: 'cui', label: 'CUI' },
        { key: 'telefon', label: 'Telefon' },
        { key: 'trimise', label: 'Scrisori', class: 'text-center' },
        { key: 'ultima', label: 'Ultima scrisoare' },
        { key: 'demo', label: 'A cerut demo' },
        { key: 'stare', label: 'Stare' },
      ],
      cifre: [
        { cheie: 'toate', eticheta: 'în listă', culoare: '' },
        { cheie: 'abonati', eticheta: 'abonați', culoare: 'text-success' },
        { cheie: 'nescrisi', eticheta: 'nescriși', culoare: 'text-primary' },
        { cheie: 'dezabonati', eticheta: 'dezabonați', culoare: 'text-secondary' },
        { cheie: 'demo', eticheta: 'au cerut demo', culoare: 'text-warning' },
        { cheie: 'fara_raspuns', eticheta: 'fără răspuns', culoare: 'text-muted' },
      ],
    }
  },
  computed: {
    optiuniJudete() {
      return [{ value: '', text: 'Toate județele' }].concat(this.judete.map(j => ({ value: j, text: j })))
    },
    optiuniStare() {
      return [
        { value: '', text: 'Toate stările' },
        { value: 'abonati', text: 'Doar abonați' },
        { value: 'nescrisi', text: 'Doar cărora nu li s-a scris' },
        { value: 'dezabonati', text: 'Doar dezabonați' },
      ]
    },
    /**
     * Câți dintre cei cărora li s-a scris au apăsat butonul.
     *
     * E singura măsură cinstită a unei campanii: deschiderile se numără prost,
     * o apăsare nu.
     */
    procentRaspuns() {
      const scrisi = (this.sumar.demo || 0) + (this.sumar.fara_raspuns || 0)

      if (!scrisi) return null

      return Math.round(((this.sumar.demo || 0) / scrisi) * 1000) / 10
    },

    /** Numai cei abonați pot fi aleși; ceilalți nici nu se bifează. */
    contactePotFiAlese() {
      return this.contacte.filter(c => c.abonat)
    },
    totiAlesi() {
      return this.contactePotFiAlese.length > 0
        && this.contactePotFiAlese.every(c => this.alesi.includes(c.id))
    },
  },
  created() {
    this.incarca()
  },
  methods: {
    incarca(pagina) {
      this.seIncarca = true
      this.pagina = pagina || 1

      const parametri = {
        pagina: this.pagina,
        page: this.pagina,
        cauta: this.filtre.cauta,
        judet: this.filtre.judet,
        stare: this.filtre.stare,
      }

      return this.$http.get('/marketing/contacte', { params: parametri })
        .then(raspuns => {
          const date = raspuns.data

          this.contacte = date.data || []
          this.judete = date.judete || []
          this.sumar = date.sumar || {}
          this.total = date.total || 0
          this.pagini = date.pagini || 1
        })
        .catch(() => {
          this.eroare = 'Lista nu a putut fi adusă.'
        })
        .finally(() => {
          this.seIncarca = false
        })
    },

    importa(fisier) {
      if (!fisier) return

      this.eroare = ''
      this.mesaj = ''
      this.importInCurs = true

      const date = new FormData()
      date.append('fisier', fisier, fisier.name)

      this.$http.post('/marketing/import', date, { headers: { 'Content-Type': 'multipart/form-data' } })
        .then(raspuns => {
          this.mesaj = raspuns.data.message
          this.fisier = null

          return this.incarca()
        })
        .catch(err => {
          this.eroare = (err.response && err.response.data && err.response.data.message)
            || 'Fișierul nu a putut fi citit.'
          this.fisier = null
        })
        .finally(() => {
          this.importInCurs = false
        })
    },

    /** „2026-09-03 14:22:10" devine „03.09.2026". */
    dataScurta(valoare) {
      if (!valoare) return '—'

      const data = new Date(String(valoare).replace(' ', 'T'))

      if (Number.isNaN(data.getTime())) return String(valoare).slice(0, 10)

      const doua = numar => String(numar).padStart(2, '0')

      return `${doua(data.getDate())}.${doua(data.getMonth() + 1)}.${data.getFullYear()}`
    },

    comuta(id) {
      const unde = this.alesi.indexOf(id)

      if (unde === -1) this.alesi.push(id)
      else this.alesi.splice(unde, 1)
    },

    alegeTot(bifat) {
      const deAici = this.contactePotFiAlese.map(c => c.id)

      if (bifat) {
        deAici.forEach(id => {
          if (!this.alesi.includes(id)) this.alesi.push(id)
        })
      } else {
        this.alesi = this.alesi.filter(id => !deAici.includes(id))
      }
    },

    deschideScrisoarea() {
      this.previzualizare = null
      this.scrisoareaVizibila = true
    },

    vezi() {
      this.$http.post('/marketing/previzualizare', {
        contact_id: this.alesi[0],
        text: this.scrisoare.text,
      })
        .then(raspuns => {
          this.previzualizare = raspuns.data
        })
        .catch(() => {
          this.eroare = 'Previzualizarea nu a mers.'
        })
    },

    trimite() {
      this.trimiteInCurs = true
      this.eroare = ''

      this.$http.post('/marketing/trimite', {
        contacte: this.alesi,
        subiect: this.scrisoare.subiect,
        text: this.scrisoare.text,
        campanie: this.scrisoare.campanie,
      })
        .then(raspuns => {
          this.mesaj = raspuns.data.message
          this.scrisoareaVizibila = false
          this.alesi = []

          return this.incarca(this.pagina)
        })
        .catch(err => {
          this.eroare = (err.response && err.response.data && err.response.data.message)
            || 'Scrisorile nu au putut fi puse la trimitere.'
        })
        .finally(() => {
          this.trimiteInCurs = false
        })
    },
  },
}
</script>
