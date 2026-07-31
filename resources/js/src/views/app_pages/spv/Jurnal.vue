<template>
  <div>
    <b-card title="Jurnal de activitate">
      <p class="text-muted">
        Ce a făcut fiecare utilizator în modulul ANAF: citiri de mesaje, solicitări,
        semnări și depuneri de declarații, modificări de date. Operațiile eșuate apar marcate.
      </p>

      <b-row class="mb-3">
        <b-col md="2">
          <label>Utilizator</label>
          <b-form-select
            v-model="filtre.user_id"
            :options="optiuniUtilizatori"
            @change="incarcaLista"
          />
        </b-col>
        <b-col md="3">
          <label>Acțiune</label>
          <b-form-select
            v-model="filtre.actiune"
            :options="optiuniActiuni"
            @change="incarcaLista"
          />
        </b-col>
        <b-col md="2">
          <label>De la</label>
          <b-form-input
            v-model="filtre.de_la"
            type="date"
            @change="incarcaLista"
          />
        </b-col>
        <b-col md="2">
          <label>Până la</label>
          <b-form-input
            v-model="filtre.pana_la"
            type="date"
            @change="incarcaLista"
          />
        </b-col>
        <b-col md="3">
          <label>Caută în descriere</label>
          <b-form-input
            v-model="filtre.cautare"
            placeholder="Ex: D300 sau 15208744"
            @change="incarcaLista"
          />
        </b-col>
      </b-row>

      <b-row class="mb-3">
        <b-col
          md="3"
          class="d-flex align-items-center"
        >
          <b-form-checkbox
            v-model="filtre.doar_esecuri"
            @change="incarcaLista"
          >
            Doar operațiile eșuate
          </b-form-checkbox>
        </b-col>
        <b-col
          md="3"
          class="d-flex align-items-center"
        >
          <b-button
            variant="outline-secondary"
            size="sm"
            class="mr-1"
            @click="resetFiltre"
          >
            Șterge filtrele
          </b-button>

          <!-- Se exportă ce se vede: aceleași filtre, aceeași ordine -->
          <b-button
            variant="outline-success"
            size="sm"
            :disabled="!intrari.length || exportInCurs"
            @click="exporta"
          >
            <b-spinner
              v-if="exportInCurs"
              small
              class="mr-50"
            />
            <feather-icon
              v-else
              icon="FileTextIcon"
              size="14"
              class="mr-50"
            />Export xls
          </b-button>
        </b-col>
      </b-row>

      <b-alert
        v-if="eroare"
        show
        variant="danger"
      >
        {{ eroare }}
      </b-alert>

      <b-table
        :items="intrari"
        :per-page="pePagina"
        :current-page="pagina"
        :fields="campuri"
        :busy="listaInCurs"
        responsive
        striped
        small
        show-empty
        empty-text="Nicio activitate înregistrată pentru filtrele alese."
      >
        <template #table-busy>
          <div class="text-center my-2">
            <b-spinner class="align-middle mr-1" />
            Se încarcă jurnalul...
          </div>
        </template>

        <template #cell(descriere)="rand">
          <span :class="rand.item.reusit ? '' : 'text-danger'">{{ rand.item.descriere }}</span>
          <b-badge
            v-if="!rand.item.reusit"
            variant="danger"
            class="ml-1"
          >
            eșuat
          </b-badge>
        </template>

        <template #cell(email)="rand">
          {{ rand.item.email || '-' }}
        </template>

        <template #cell(cif)="rand">
          {{ rand.item.cif || '-' }}
        </template>
      </b-table>

      <paginare
        v-model="pagina"
        :per-page.sync="pePagina"
        :total="intrari.length"
      />

      <p class="text-muted small">
        Se afișează cele mai recente {{ intrari.length }} înregistrări.
      </p>
    </b-card>
  </div>
</template>

<script>
export default {
  name: 'SpvJurnal',
  data() {
    return {
      intrari: [],
      utilizatori: [],
      actiuni: {},
      eroare: '',
      pagina: 1,
      pePagina: 25,
      exportInCurs: false,
      listaInCurs: false,
      filtre: {
        user_id: null, actiune: null, de_la: '', pana_la: '', cautare: '', doar_esecuri: false,
      },
      campuri: [
        { key: 'cand', label: 'Când' },
        { key: 'utilizator', label: 'Utilizator' },
        { key: 'email', label: 'Email' },
        { key: 'actiune_eticheta', label: 'Acțiune' },
        { key: 'descriere', label: 'Detalii' },
        { key: 'cif', label: 'CIF' },
        { key: 'ip', label: 'IP' },
      ],
    }
  },
  computed: {
    optiuniUtilizatori() {
      return [{ value: null, text: 'Toți' }].concat(
        this.utilizatori.map(u => ({ value: u.user_id, text: u.user_nume || `#${u.user_id}` })),
      )
    },
    optiuniActiuni() {
      return [{ value: null, text: 'Toate' }].concat(
        Object.keys(this.actiuni).map(cheie => ({ value: cheie, text: this.actiuni[cheie] })),
      )
    },
  },
  created() {
    this.incarcaLista()
  },
  methods: {
    resetFiltre() {
      this.filtre = {
        user_id: null, actiune: null, de_la: '', pana_la: '', cautare: '', doar_esecuri: false,
      }
      this.incarcaLista()
    },
    /** Filtrele scrise, fără cele lăsate goale. */
    parametri() {
      const params = {}

      Object.keys(this.filtre).forEach(cheie => {
        const valoare = this.filtre[cheie]
        if (valoare !== null && valoare !== '' && valoare !== false) {
          params[cheie] = valoare
        }
      })

      return params
    },
    /**
     * Descarcă în Excel exact ce se vede: aceleași filtre, aceeași ordine.
     *
     * Fișierul vine ca blob, nu printr-un link direct: ruta cere tokenul, iar
     * un `window.open` nu-l poartă cu el.
     */
    exporta() {
      this.eroare = ''
      this.exportInCurs = true

      this.$http.get('/anaf-jurnal/export', { params: this.parametri(), responseType: 'blob' })
        .then(raspuns => {
          const url = window.URL.createObjectURL(new Blob([raspuns.data]))
          const legatura = document.createElement('a')

          legatura.href = url
          legatura.download = `jurnal_activitate_${new Date().toISOString().slice(0, 10)}.xlsx`
          document.body.appendChild(legatura)
          legatura.click()
          document.body.removeChild(legatura)

          setTimeout(() => window.URL.revokeObjectURL(url), 60000)
        })
        .catch(() => {
          this.eroare = 'Exportul nu a putut fi creat.'
        })
        .finally(() => {
          this.exportInCurs = false
        })
    },
    incarcaLista() {
      this.listaInCurs = true

      this.$http.get('/anaf-jurnal', { params: this.parametri() })
        .then(raspuns => {
          this.intrari = raspuns.data.data || []
          this.utilizatori = raspuns.data.utilizatori || []
          this.actiuni = raspuns.data.actiuni || {}
        })
        .catch(err => {
          this.eroare = err.response && err.response.data && err.response.data.message
            ? err.response.data.message
            : 'Nu s-a putut încărca jurnalul'
        })
        .finally(() => {
          this.listaInCurs = false
        })
    },
  },
}
</script>
