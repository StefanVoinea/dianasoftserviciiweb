<template>
  <div>
    <b-card title="Entități cu drept de semnătură">
      <p class="text-muted">
        Lista este preluată de la ANAF — sunt CIF-urile pentru care certificatul de pe token
        are drepturi în SPV. Denumirile se completează din vectorul fiscal și din documentul
        de date de identificare, apoi sunt folosite la mesajele și solicitările SPV.
      </p>

      <b-row class="mb-3">
        <b-col md="3">
          <b-button
            variant="primary"
            :disabled="sincronizareInCurs"
            @click="sincronizeaza"
          >
            <b-spinner
              v-if="sincronizareInCurs"
              small
              class="mr-1"
            />
            Inițializează / actualizează lista
          </b-button>
        </b-col>
        <b-col md="3">
          <b-button
            variant="outline-primary"
            :disabled="solicitareInCurs"
            @click="solicita"
          >
            <b-spinner
              v-if="solicitareInCurs"
              small
              class="mr-1"
            />
            Solicită datele lipsă din SPV
          </b-button>
        </b-col>
        <b-col
          md="6"
          class="d-flex align-items-center"
        >
          <b-form-checkbox
            v-model="doarActive"
            @change="incarcaLista"
          >
            Doar cele active
          </b-form-checkbox>
        </b-col>
      </b-row>

      <b-alert
        v-if="eroare"
        show
        variant="danger"
      >
        {{ eroare }}
      </b-alert>
      <b-alert
        v-if="info"
        show
        variant="info"
        class="py-2"
      >
        {{ info }}
      </b-alert>

      <b-table
        :items="societatiFiltrate"
        :per-page="pePagina"
        :current-page="pagina"
        :fields="campuri"
        :busy="listaInCurs"
        responsive
        striped
        small
        show-empty
        empty-text="Nicio entitate pentru filtrul ales."
      >
        <!-- Filtrele stau sub numele coloanei și lucrează pe lista deja adusă -->
        <template #head(cif)="date">
          <div>{{ date.label }}</div>
          <b-form-input
            v-model="filtre.cif"
            size="sm"
            class="filtru-coloana"
            placeholder="CIF"
          />
        </template>

        <template #head(denumire)="date">
          <div>{{ date.label }}</div>
          <b-form-input
            v-model="filtre.denumire"
            size="sm"
            class="filtru-coloana"
            placeholder="Denumire"
          />
        </template>

        <template #head(tip)="date">
          <div>{{ date.label }}</div>
          <b-form-select
            v-model="filtre.tip"
            size="sm"
            class="filtru-coloana"
            :options="[
              { value: '', text: 'toate' },
              { value: 'pj', text: 'persoane juridice' },
              { value: 'pf', text: 'persoane fizice' },
            ]"
          />
        </template>

        <template #table-busy>
          <div class="text-center my-2">
            <b-spinner class="align-middle mr-1" />
            Se încarcă...
          </div>
        </template>

        <template #cell(denumire)="rand">
          <span v-if="rand.item.denumire">{{ rand.item.denumire }}</span>
          <span
            v-else
            class="text-muted"
          >necunoscută — solicitați datele din SPV</span>
          <b-badge
            v-if="rand.item.denumire_sursa"
            variant="light"
            class="ml-1"
          >
            {{ etichetaSursa(rand.item.denumire_sursa) }}
          </b-badge>
        </template>

        <template #cell(tip)="rand">
          {{ rand.item.tip === 'pf' ? 'Persoană fizică' : 'Persoană juridică' }}
        </template>

        <template #cell(activ)="rand">
          <b-badge :variant="rand.item.activ ? 'success' : 'secondary'">
            {{ rand.item.activ ? 'Activă' : 'Fără drepturi' }}
          </b-badge>
        </template>

        <template #cell(date)="rand">
          <div :class="rand.item.vector_la ? 'text-success' : 'text-muted'">
            Vector fiscal: {{ rand.item.vector_la || 'lipsă' }}
          </div>
          <div :class="rand.item.date_identificare_la ? 'text-success' : 'text-muted'">
            Date identificare: {{ rand.item.date_identificare_la || 'lipsă' }}
          </div>
        </template>

        <template #cell(certificat)="rand">
          <div v-if="rand.item.certificat">
            {{ rand.item.certificat }}
            <div class="small text-muted">
              expiră {{ rand.item.certificat_expira }}
            </div>
          </div>
          <span
            v-else
            class="text-muted"
          >-</span>
        </template>

        <template #cell(actiuni)="rand">
          <b-button
            size="sm"
            variant="outline-primary"
            @click="editeaza(rand.item)"
          >
            Redenumește
          </b-button>
        </template>
      </b-table>

      <paginare
        v-model="pagina"
        :per-page.sync="pePagina"
        :total="societatiFiltrate.length"
      />
    </b-card>

    <b-modal
      v-model="formularVizibil"
      title="Denumire entitate"
      ok-title="Salvează"
      cancel-title="Renunță"
      @ok="salveaza"
    >
      <p class="text-muted small">
        Denumirea introdusă manual are prioritate față de cea preluată din documentele SPV.
      </p>
      <b-form-input v-model="formular.denumire" />
    </b-modal>
  </div>
</template>

<script>
export default {
  name: 'SpvSocietati',
  data() {
    return {
      societati: [],
      doarActive: false,
      eroare: '',
      info: '',
      pagina: 1,
      pePagina: 25,
      filtre: {
        cif: '',
        denumire: '',
        tip: '',
      },
      listaInCurs: false,
      sincronizareInCurs: false,
      solicitareInCurs: false,
      formularVizibil: false,
      formular: {},
      campuri: [
        { key: 'cif', label: 'CIF' },
        { key: 'denumire', label: 'Denumire' },
        { key: 'tip', label: 'Tip' },
        { key: 'activ', label: 'Stare' },
        { key: 'date', label: 'Date preluate din SPV' },
        { key: 'certificat', label: 'Certificat' },
        { key: 'actiuni', label: 'Acțiuni' },
      ],
    }
  },
  computed: {
    /** Entitățile care trec de filtrele scrise pe coloane. */
    societatiFiltrate() {
      const contine = (valoare, cautat) => String(valoare || '')
        .toLowerCase()
        .indexOf(cautat.toLowerCase()) !== -1

      return this.societati.filter(societate => {
        if (this.filtre.tip && societate.tip !== this.filtre.tip) return false

        return ['cif', 'denumire'].every(cheie => {
          const cautat = (this.filtre[cheie] || '').trim()

          return cautat === '' || contine(societate[cheie], cautat)
        })
      })
    },
  },
  created() {
    this.incarcaLista()
  },
  methods: {
    etichetaSursa(sursa) {
      return { vector: 'vector fiscal', date_identificare: 'date identificare', manual: 'manual' }[sursa] || sursa
    },
    incarcaLista() {
      this.listaInCurs = true
      // Filtrarea pe CIF, denumire si tip se face pe coloanele tabelului.
      const params = {}
      if (this.doarActive) params.doar_active = 1

      this.$http.get('/anaf-societati', { params })
        .then(raspuns => {
          this.societati = raspuns.data.data || []
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Nu s-a putut încărca lista de entități')
        })
        .finally(() => {
          this.listaInCurs = false
        })
    },
    sincronizeaza() {
      this.eroare = ''
      this.info = ''
      this.sincronizareInCurs = true

      this.$http.post('/anaf-societati/sincronizeaza')
        .then(raspuns => {
          const r = raspuns.data.data
          const parti = [`${r.gasite} CIF-uri în certificat`]
          if (r.noi) parti.push(`${r.noi} noi`)
          if (r.dezactivate) parti.push(`${r.dezactivate} fără drepturi acum`)
          this.info = parti.join(', ')
          this.incarcaLista()
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Sincronizarea a eșuat')
        })
        .finally(() => {
          this.sincronizareInCurs = false
        })
    },
    solicita() {
      this.eroare = ''
      this.info = ''
      this.solicitareInCurs = true

      this.$http.post('/anaf-societati/solicita')
        .then(raspuns => {
          const r = raspuns.data.data
          const parti = []
          if (r.trimise) parti.push(`${r.trimise} solicitări trimise`)
          if (r.reinterpretate) parti.push(`${r.reinterpretate} documente reinterpretate`)
          if (r.sarite) parti.push(`${r.sarite} sărite (deja cerute azi sau persoane fizice)`)
          this.info = parti.length ? parti.join(', ') : 'Nu era nimic de solicitat.'

          if (r.erori && r.erori.length) {
            this.eroare = r.erori.join(' | ')
          }

          this.incarcaLista()
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Solicitarea a eșuat')
        })
        .finally(() => {
          this.solicitareInCurs = false
        })
    },
    editeaza(societate) {
      this.formular = { ...societate }
      this.formularVizibil = true
    },
    salveaza() {
      this.$http.put(`/anaf-societati/${this.formular.id}`, { denumire: this.formular.denumire })
        .then(() => {
          this.incarcaLista()
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Salvarea a eșuat')
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

<style scoped>
/* Casutele de filtru din antet: scunde, ca sa nu ingroase capul tabelului. */
.filtru-coloana {
  height: 1.6rem;
  padding: 0 0.35rem;
  font-size: 0.75rem;
  font-weight: 400;
  margin-top: 0.15rem;
}
</style>
