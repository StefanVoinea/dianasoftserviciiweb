<template>
  <div>
    <b-card title="Vector fiscal">
      <b-tabs v-model="tabActiv">
        <b-tab title="Vector declarat">
          <b-row class="my-3">
            <b-col md="3">
              <b-form-input
                v-model="filtruCui"
                placeholder="Filtrează după CUI"
                @change="incarcaLista"
              />
            </b-col>
            <b-col md="3">
              <b-button
                variant="primary"
                @click="deschideFormular(null)"
              >
                Adaugă contribuabil
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
            :items="vectori"
            :fields="campuriVector"
            :busy="listaInCurs"
            responsive
            striped
            small
            show-empty
            empty-text="Nu există contribuabili în vectorul fiscal."
          >
            <template #table-busy>
              <div class="text-center my-2">
                <b-spinner class="align-middle mr-1" />Se încarcă...
              </div>
            </template>

            <template #cell(actiuni)="rand">
              <b-button
                size="sm"
                variant="outline-primary"
                class="mr-1"
                @click="deschideFormular(rand.item)"
              >
                Modifică
              </b-button>
              <b-button
                size="sm"
                variant="outline-danger"
                @click="sterge(rand.item)"
              >
                Șterge
              </b-button>
            </template>
          </b-table>
        </b-tab>

        <b-tab
          title="Vector din SPV"
          @click="incarcaSpv"
        >
          <b-row class="my-3">
            <b-col md="3">
              <b-form-input
                v-model="filtruCuiSpv"
                placeholder="Filtrează după CUI"
                @change="incarcaSpv"
              />
            </b-col>
          </b-row>
          <b-table
            :items="vectorSpv"
            :fields="campuriSpv"
            responsive
            striped
            small
            show-empty
            empty-text="Nu există date. Solicitați documentul „Vector Fiscal” din pagina Solicitări SPV."
          />
        </b-tab>

        <b-tab
          title="Situație depuneri"
          @click="incarcaSituatie"
        >
          <b-row class="my-3">
            <b-col md="2">
              <label>Luna</label>
              <b-form-input
                v-model.number="perioada.luna"
                type="number"
                min="1"
                max="12"
              />
            </b-col>
            <b-col md="2">
              <label>Anul</label>
              <b-form-input
                v-model.number="perioada.anul"
                type="number"
                min="2000"
                max="2100"
              />
            </b-col>
            <b-col
              md="2"
              class="d-flex align-items-end"
            >
              <b-button
                variant="primary"
                @click="incarcaSituatie"
              >
                Generează
              </b-button>
            </b-col>
          </b-row>

          <b-table
            :items="situatie"
            :fields="campuriSituatie"
            responsive
            striped
            small
            show-empty
            empty-text="Nu există obligații declarative pentru perioada selectată."
          >
            <template #cell(obligatii)="rand">
              <b-badge
                v-for="obligatie in rand.item.obligatii"
                :key="obligatie.tip"
                :variant="obligatie.depusa ? 'success' : 'danger'"
                class="mr-1 mb-1"
              >
                {{ obligatie.tip }} ({{ obligatie.periodicitate }})
              </b-badge>
            </template>
          </b-table>
        </b-tab>
      </b-tabs>
    </b-card>

    <b-modal
      v-model="formularVizibil"
      :title="formular.id ? 'Modifică vectorul fiscal' : 'Adaugă contribuabil'"
      ok-title="Salvează"
      cancel-title="Renunță"
      size="lg"
      @ok="salveaza"
    >
      <b-row>
        <b-col md="4">
          <label>CUI</label>
          <b-form-input v-model="formular.cui" />
        </b-col>
        <b-col md="8">
          <label>Denumire</label>
          <b-form-input v-model="formular.denumire" />
        </b-col>
      </b-row>
      <hr>
      <b-row>
        <b-col
          v-for="tip in tipuriDeclaratii"
          :key="tip"
          md="3"
          class="mb-2"
        >
          <label>{{ tip }}</label>
          <b-form-select
            v-model="formular[tip]"
            :options="optiuniPeriodicitate"
          />
        </b-col>
      </b-row>
    </b-modal>
  </div>
</template>

<script>
export default {
  name: 'VectorFiscal',
  data() {
    return {
      tabActiv: 0,
      vectori: [],
      vectorSpv: [],
      situatie: [],
      tipuriDeclaratii: [],
      optiuniPeriodicitate: [{ value: null, text: '-' }],
      filtruCui: '',
      filtruCuiSpv: '',
      eroare: '',
      listaInCurs: false,
      formularVizibil: false,
      formular: {},
      perioada: { luna: new Date().getMonth() + 1, anul: new Date().getFullYear() },
      campuriSpv: [
        { key: 'cui', label: 'CUI' },
        { key: 'cod_imp', label: 'Cod obligație' },
        { key: 'semnificatie', label: 'Semnificație' },
        { key: 'perfisc', label: 'Periodicitate' },
        { key: 'data_inceput', label: 'Data început' },
        { key: 'data_sfarsit', label: 'Data sfârșit' },
      ],
      campuriSituatie: [
        { key: 'cui', label: 'CUI' },
        { key: 'denumire', label: 'Denumire' },
        { key: 'obligatii', label: 'Obligații (verde = depusă)' },
        { key: 'lipsa', label: 'Nedepuse' },
      ],
    }
  },
  computed: {
    campuriVector() {
      return [
        { key: 'cui', label: 'CUI' },
        { key: 'denumire', label: 'Denumire' },
        ...this.tipuriDeclaratii.map(tip => ({ key: tip, label: tip })),
        { key: 'actiuni', label: 'Acțiuni' },
      ]
    },
  },
  created() {
    document.title = `${window.app_name} -> Vector fiscal`
    this.incarcaLista()
  },
  methods: {
    incarcaLista() {
      this.listaInCurs = true
      const params = {}
      if (this.filtruCui) params.cui = this.filtruCui

      this.$http.get('/vector-fiscal', { params })
        .then(raspuns => {
          this.vectori = raspuns.data.data || []
          this.tipuriDeclaratii = raspuns.data.declaratii || []
          this.optiuniPeriodicitate = [{ value: null, text: '-' }].concat(
            (raspuns.data.periodicitati || []).map(p => ({ value: p, text: p })),
          )
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Nu s-a putut încărca vectorul fiscal')
        })
        .finally(() => {
          this.listaInCurs = false
        })
    },
    incarcaSpv() {
      const params = {}
      if (this.filtruCuiSpv) params.cui = this.filtruCuiSpv

      this.$http.get('/vector-fiscal/spv', { params })
        .then(raspuns => {
          this.vectorSpv = raspuns.data.data || []
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Nu s-au putut încărca datele din SPV')
        })
    },
    incarcaSituatie() {
      this.$http.get('/vector-fiscal/situatie', { params: this.perioada })
        .then(raspuns => {
          this.situatie = raspuns.data.data || []
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Nu s-a putut genera situația')
        })
    },
    deschideFormular(vector) {
      this.formular = vector ? { ...vector } : { cui: '', denumire: '' }
      this.formularVizibil = true
    },
    salveaza() {
      this.eroare = ''
      const cerere = this.formular.id
        ? this.$http.put(`/vector-fiscal/${this.formular.id}`, this.formular)
        : this.$http.post('/vector-fiscal', this.formular)

      cerere
        .then(() => {
          this.notifica('Vectorul fiscal a fost salvat', 'success')
          this.incarcaLista()
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Salvarea a eșuat')
        })
    },
    sterge(vector) {
      this.$http.delete(`/vector-fiscal/${vector.id}`)
        .then(() => {
          this.notifica('Contribuabilul a fost șters', 'success')
          this.incarcaLista()
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Ștergerea a eșuat')
        })
    },
    mesajEroare(err, implicit) {
      return err.response && err.response.data && err.response.data.message
        ? err.response.data.message
        : implicit
    },
    notifica(mesaj, variant) {
      this.$bvToast.toast(mesaj, { title: 'Vector fiscal', variant, solid: true })
    },
  },
}
</script>
