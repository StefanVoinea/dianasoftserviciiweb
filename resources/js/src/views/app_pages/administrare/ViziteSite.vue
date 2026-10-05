<template>
  <div>
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

    <!-- Cifrele: cate vizite, de la cate adrese, cat au stat -->
    <b-card
      class="border mb-1"
      body-class="p-1"
    >
      <div class="d-flex align-items-center justify-content-between mb-1">
        <h6 class="mb-0">
          Vizitele pe spvcurier.ro
        </h6>
        <b-button
          size="sm"
          variant="outline-primary"
          :disabled="seIncarca"
          @click="incarca(pagina)"
        >
          Reîncarcă
        </b-button>
      </div>

      <b-row class="text-center">
        <b-col
          v-for="cifra in cifre"
          :key="cifra.cheie"
          cols="12"
          md="4"
        >
          <div class="py-25">
            <h4 class="mb-0 text-primary">
              {{ cifra.vizite }}
              <small class="text-muted">vizite</small>
            </h4>
            <small class="text-muted">
              {{ cifra.eticheta }} · {{ cifra.adrese }} adrese IP ·
              în medie {{ durata(cifra.durata_medie) }}
            </small>
          </div>
        </b-col>
      </b-row>
    </b-card>

    <!-- Ce se arata -->
    <b-card
      class="border mb-1"
      body-class="p-1"
    >
      <b-row
        no-gutters
        class="align-items-center"
      >
        <b-col
          md="3"
          class="pr-md-1 mb-50 mb-md-0"
        >
          <b-form-select
            v-model="zile"
            :options="optiuniZile"
            size="sm"
            @change="incarca()"
          />
        </b-col>
        <b-col
          md="4"
          class="pr-md-1 mb-50 mb-md-0"
        >
          <b-form-radio-group
            v-model="fel"
            :options="optiuniFel"
            buttons
            button-variant="outline-primary"
            size="sm"
          />
        </b-col>
        <b-col md="5">
          <span
            v-if="ip"
            class="small"
          >
            Numai adresa <strong>{{ ip }}</strong> —
            <b-link @click="alegeAdresa('')">arată toate</b-link>
          </span>
        </b-col>
      </b-row>
    </b-card>

    <!-- Vizitele, una cate una -->
    <b-card
      v-if="fel === 'vizite'"
      class="border"
      no-body
    >
      <b-table
        :items="vizite"
        :fields="campuriVizite"
        :busy="seIncarca"
        responsive
        hover
        small
        class="mb-0"
        empty-text="Nicio vizită în intervalul ales."
        show-empty
      >
        <template #cell(ip)="rand">
          <b-link
            v-b-tooltip.hover
            :title="rand.item.user_agent"
            @click="alegeAdresa(rand.item.ip)"
          >
            {{ rand.item.ip }}
          </b-link>
        </template>

        <template #cell(firma)="rand">
          <span v-if="rand.item.firma">
            {{ rand.item.firma }}
            <small class="text-muted d-block">{{ rand.item.email }}</small>
          </span>
          <span
            v-else
            class="text-muted"
          >—</span>
        </template>

        <template #cell(durata_secunde)="rand">
          {{ durata(rand.item.durata_secunde) }}
        </template>

        <template #cell(pagini)="rand">
          {{ rand.item.pagini.map(numelePaginii).join(' → ') }}
        </template>

        <template #cell(referrer)="rand">
          <span :class="rand.item.referrer ? '' : 'text-muted'">
            {{ rand.item.referrer || 'direct' }}
          </span>
        </template>
      </b-table>

      <div
        v-if="total > 100"
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

    <!-- Adunate pe adresa: cine revine si cat sta de tot -->
    <b-card
      v-else
      class="border"
      no-body
    >
      <b-table
        :items="peAdresa"
        :fields="campuriAdrese"
        :busy="seIncarca"
        responsive
        hover
        small
        class="mb-0"
        empty-text="Nicio vizită în intervalul ales."
        show-empty
      >
        <template #cell(ip)="rand">
          <b-link @click="alegeAdresa(rand.item.ip)">
            {{ rand.item.ip }}
          </b-link>
        </template>

        <template #cell(firma)="rand">
          <span :class="rand.item.firma ? '' : 'text-muted'">
            {{ rand.item.firma || '—' }}
          </span>
        </template>

        <template #cell(durata_secunde)="rand">
          {{ durata(rand.item.durata_secunde) }}
        </template>
      </b-table>
    </b-card>

    <small class="text-muted d-block mt-50">
      O vizită e o deschidere a paginii. Durata numără doar timpul cât pagina a
      fost în față, nu cât a stat într-o filă uitată. Firma apare când
      vizitatorul a venit din butonul „Prezentarea aplicației" al unei scrisori
      de marketing.
    </small>
  </div>
</template>

<script>
/**
 * Vizitele paginii de prezentare spvcurier.ro: câți au intrat, de la ce adresă
 * IP și cât au stat. Pagina însăși le spune aplicației cât e deschisă; aici
 * doar se citesc.
 */
import {
  BAlert, BButton, BCard, BCol, BFormRadioGroup, BFormSelect, BLink,
  BPagination, BRow, BTable, VBTooltip,
} from 'bootstrap-vue'

export default {
  name: 'ViziteSite',
  components: {
    BAlert,
    BButton,
    BCard,
    BCol,
    BFormRadioGroup,
    BFormSelect,
    BLink,
    BPagination,
    BRow,
    BTable,
  },
  directives: {
    'b-tooltip': VBTooltip,
  },
  data() {
    return {
      seIncarca: false,
      eroare: '',
      zile: 30,
      fel: 'vizite',
      ip: '',
      pagina: 1,
      total: 0,
      vizite: [],
      peAdresa: [],
      sumar: {},
      optiuniZile: [
        { value: 1, text: 'Astăzi' },
        { value: 7, text: 'Ultimele 7 zile' },
        { value: 30, text: 'Ultimele 30 de zile' },
        { value: 90, text: 'Ultimele 90 de zile' },
        { value: 365, text: 'Ultimul an' },
      ],
      optiuniFel: [
        { value: 'vizite', text: 'Vizită cu vizită' },
        { value: 'adrese', text: 'Adunate pe adresă IP' },
      ],
      campuriVizite: [
        { key: 'cand', label: 'Când' },
        { key: 'ip', label: 'Adresa IP' },
        { key: 'firma', label: 'Cine' },
        { key: 'durata_secunde', label: 'Cât a stat' },
        { key: 'pagini', label: 'Ce a deschis' },
        { key: 'dispozitiv', label: 'De pe' },
        { key: 'referrer', label: 'De unde a venit' },
      ],
      campuriAdrese: [
        { key: 'ip', label: 'Adresa IP' },
        { key: 'firma', label: 'Cine' },
        { key: 'vizite', label: 'Vizite' },
        { key: 'durata_secunde', label: 'Cât a stat de tot' },
        { key: 'prima', label: 'Prima vizită' },
        { key: 'ultima', label: 'Ultima vizită' },
      ],
      numePagini: {
        home: 'Pornire',
        spv: 'Cum funcționează',
        tarife: 'Plan tarifar',
        intrebari: 'Întrebări',
        securitate: 'Securitate',
        confidentialitate: 'Confidențialitate',
        termeni: 'Termeni',
        cookies: 'Cookies',
        demo: 'Formularul demo',
      },
    }
  },
  computed: {
    cifre() {
      const etichete = { azi: 'astăzi', sapte_zile: 'în 7 zile', treizeci_zile: 'în 30 de zile' }

      return Object.keys(etichete).map(cheie => ({
        cheie,
        eticheta: etichete[cheie],
        vizite: 0,
        adrese: 0,
        durata_medie: 0,
        ...(this.sumar[cheie] || {}),
      }))
    },
  },
  created() {
    this.incarca()
  },
  methods: {
    incarca(pagina) {
      this.seIncarca = true
      this.pagina = pagina || 1

      return this.$http.get('/administrare/vizite-site', {
        params: { zile: this.zile, ip: this.ip, page: this.pagina },
      })
        .then(raspuns => {
          const date = raspuns.data

          this.vizite = date.data || []
          this.peAdresa = date.pe_adresa || []
          this.sumar = date.sumar || {}
          this.total = date.total || 0
        })
        .catch(() => {
          this.eroare = 'Vizitele nu au putut fi aduse.'
        })
        .finally(() => {
          this.seIncarca = false
        })
    },
    /** Vizitele unei singure adrese, ori iar toate. */
    alegeAdresa(ip) {
      this.ip = ip
      this.fel = 'vizite'
      this.incarca()
    },
    /** Secundele, spuse omenește: „45 s", „3 min 20 s", „1 h 05 min". */
    durata(secunde) {
      const s = Number(secunde) || 0

      if (s < 60) return `${s} s`

      if (s < 3600) return `${Math.floor(s / 60)} min ${String(s % 60).padStart(2, '0')} s`

      return `${Math.floor(s / 3600)} h ${String(Math.floor((s % 3600) / 60)).padStart(2, '0')} min`
    },
    numelePaginii(cheie) {
      return this.numePagini[cheie] || cheie
    },
  },
}
</script>
