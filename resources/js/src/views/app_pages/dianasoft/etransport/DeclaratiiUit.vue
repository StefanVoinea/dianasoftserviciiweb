<template>
  <div>
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

    <!-- Lista declaratiilor -->
    <b-card
      v-if="!declaratia"
      class="border"
      body-class="p-2"
    >
      <div class="d-flex align-items-center mb-2">
        <b-button
          variant="primary"
          size="sm"
          @click="deschideNoua"
        >
          <feather-icon
            icon="PlusIcon"
            class="mr-25"
          />
          Declarație nouă
        </b-button>

        <b-form-select
          v-model="filtruStare"
          :options="optiuniFiltruStare"
          size="sm"
          class="ml-2"
          style="width: auto"
          @change="incarcaLista"
        />
      </div>

      <b-table
        :items="declaratii"
        :fields="campuriLista"
        :busy="listaInCurs"
        responsive
        striped
        small
        show-empty
        class="mb-0"
        empty-text="Nicio declarație. Începeți cu „Declarație nouă”."
      >
        <template #table-busy>
          <div class="text-center my-2">
            <b-spinner class="align-middle mr-1" />
            Se încarcă...
          </div>
        </template>

        <template #cell(stare)="rand">
          <b-badge :variant="culoareStare(rand.item.stare)">
            {{ rand.item.stare_eticheta }}
          </b-badge>
          <div
            v-if="rand.item.uit"
            class="small font-weight-bold mt-25"
          >
            {{ rand.item.uit }}
          </div>
        </template>

        <template #cell(operatiune)="rand">
          <div class="small">
            {{ rand.item.operatiune || '-' }}
          </div>
          <div class="small text-muted">
            {{ rand.item.partener || '' }}
          </div>
        </template>

        <template #cell(actiuni)="rand">
          <b-button
            v-b-tooltip.hover.top.window.v-light
            size="sm"
            variant="outline-secondary"
            class="btn-icon"
            :title="rand.item.poate_fi_modificata ? 'Deschide' : 'Vezi detaliile'"
            @click="deschide(rand.item.id)"
          >
            <feather-icon :icon="rand.item.poate_fi_modificata ? 'Edit2Icon' : 'EyeIcon'" />
          </b-button>
          <b-button
            v-if="rand.item.index_incarcare && !rand.item.uit"
            v-b-tooltip.hover.top.window.v-light
            size="sm"
            variant="outline-secondary"
            class="btn-icon ml-25"
            title="Verifică starea la ANAF"
            @click="verificaDinLista(rand.item.id)"
          >
            <feather-icon icon="RefreshCwIcon" />
          </b-button>
          <b-button
            v-if="rand.item.poate_fi_modificata"
            v-b-tooltip.hover.top.window.v-light
            size="sm"
            variant="outline-danger"
            class="btn-icon ml-25"
            title="Șterge ciorna"
            @click="sterge(rand.item.id)"
          >
            <feather-icon icon="Trash2Icon" />
          </b-button>
        </template>
      </b-table>
    </b-card>

    <!-- Formularul declaratiei -->
    <div v-else>
      <div class="d-flex align-items-center mb-2">
        <b-button
          variant="outline-secondary"
          size="sm"
          @click="inchideFormularul"
        >
          <feather-icon
            icon="ArrowLeftIcon"
            class="mr-25"
          />
          Înapoi la listă
        </b-button>

        <b-badge
          :variant="culoareStare(declaratia.stare)"
          class="ml-2"
        >
          {{ declaratia.stare_eticheta || 'Ciornă nouă' }}
        </b-badge>

        <h5
          v-if="declaratia.uit"
          class="mb-0 ml-2"
        >
          UIT: <span class="text-success font-weight-bolder">{{ declaratia.uit }}</span>
        </h5>
      </div>

      <!-- Import fisiere: doar pentru utilizatorii cu formate de fisiere cunoscute -->
      <b-card
        v-if="editabila && importPermis"
        class="border mb-2"
        body-class="p-2"
      >
        <h6 class="mb-1">
          Import linii din fișierele furnizorului
        </h6>
        <b-row
          no-gutters
          class="align-items-center"
        >
          <b-col md="6">
            <b-form-file
              v-model="fisiereDeImportat"
              multiple
              size="sm"
              accept=".xlsx,.xls,.ods,.txt"
              placeholder="Excel cu detaliile facturii sau raport text..."
              browse-text="Alege"
            />
          </b-col>
          <b-col
            md="6"
            class="pl-md-1 d-flex align-items-center mt-1 mt-md-0"
          >
            <b-form-checkbox v-model="grupate">
              grupează pe cod vamal
            </b-form-checkbox>
            <b-button
              variant="primary"
              size="sm"
              class="ml-2"
              :disabled="!fisiereDeImportat.length || importInCurs"
              @click="importaFisierele"
            >
              <b-spinner
                v-if="importInCurs"
                small
                class="mr-1"
              />
              Importă
            </b-button>
          </b-col>
        </b-row>
        <small
          v-if="declaratia.fisiere_importate && declaratia.fisiere_importate.length"
          class="text-muted d-block mt-1"
        >
          Importate: {{ declaratia.fisiere_importate.join(', ') }}
        </small>
      </b-card>

      <!-- Operatiune si partener -->
      <b-card
        class="border mb-2"
        body-class="p-2"
      >
        <b-row>
          <b-col md="3">
            <label class="small mb-0">CIF declarant*</label>
            <b-form-input
              v-model="declaratia.cif_declarant"
              size="sm"
              :disabled="!editabila"
            />
          </b-col>
          <b-col md="5">
            <label class="small mb-0">Tip operațiune*</label>
            <b-form-select
              v-model="declaratia.tip_operatiune"
              :options="optiuniTipOperatiune"
              size="sm"
              :disabled="!editabila"
              @change="tipOperatiuneSchimbat"
            />
          </b-col>
          <b-col md="4">
            <label class="small mb-0">Referință internă</label>
            <b-form-input
              v-model="declaratia.referinta_interna"
              size="sm"
              :disabled="!editabila"
            />
          </b-col>
        </b-row>

        <b-row class="mt-1">
          <b-col md="3">
            <label class="small mb-0">Țara partenerului*</label>
            <b-form-select
              v-model="declaratia.partener_tara"
              :options="optiuniTari"
              size="sm"
              :disabled="!editabila"
            />
          </b-col>
          <b-col md="3">
            <label class="small mb-0">Cod partener (TVA)</label>
            <b-form-input
              v-model="declaratia.partener_cod"
              size="sm"
              :disabled="!editabila"
            />
          </b-col>
          <b-col md="6">
            <label class="small mb-0">Denumire partener*</label>
            <b-form-input
              v-model="declaratia.partener_denumire"
              size="sm"
              :disabled="!editabila"
            />
          </b-col>
        </b-row>
      </b-card>

      <!-- Bunuri transportate -->
      <b-card
        class="border mb-2"
        body-class="p-2"
      >
        <div class="d-flex align-items-center mb-1">
          <h6 class="mb-0">
            Bunuri transportate
          </h6>
          <b-button
            v-if="editabila"
            variant="outline-primary"
            size="sm"
            class="ml-2"
            @click="adaugaLinie"
          >
            <feather-icon icon="PlusIcon" />
            Linie nouă
          </b-button>
        </div>

        <datalist id="coduri-vamale-lista">
          <option
            v-for="cod in coduriVamaleGasite"
            :key="cod.cod"
            :value="cod.cod"
          >
            {{ cod.denumire_scurta || cod.denumire }}
          </option>
        </datalist>

        <div class="table-responsive mb-0">
          <table class="table table-sm table-striped">
            <thead>
              <tr class="small">
                <th style="min-width: 110px">
                  Scop
                </th>
                <th style="min-width: 110px">
                  Cod vamal
                </th>
                <th style="min-width: 220px">
                  Denumire*
                </th>
                <th style="min-width: 80px">
                  Cantitate*
                </th>
                <th style="min-width: 90px">
                  UM
                </th>
                <th style="min-width: 90px">
                  Kg net
                </th>
                <th style="min-width: 90px">
                  Kg brut*
                </th>
                <th style="min-width: 100px">
                  Valoare {{ declaratia.valuta }}
                </th>
                <th style="min-width: 100px">
                  Valoare lei
                </th>
                <th v-if="editabila" />
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(linie, index) in declaratia.linii"
                :key="index"
              >
                <td>
                  <b-form-select
                    v-model="linie.scop_operatiune"
                    :options="optiuniScop"
                    size="sm"
                    :disabled="!editabila"
                  />
                </td>
                <td>
                  <b-form-input
                    v-model="linie.cod_tarifar"
                    size="sm"
                    list="coduri-vamale-lista"
                    :disabled="!editabila"
                    @input="cautaCodVamal($event)"
                    @change="codVamalAles(linie)"
                  />
                </td>
                <td>
                  <b-form-input
                    v-model="linie.denumire"
                    size="sm"
                    :disabled="!editabila"
                  />
                </td>
                <td>
                  <b-form-input
                    v-model.number="linie.cantitate"
                    type="number"
                    step="0.01"
                    size="sm"
                    :disabled="!editabila"
                  />
                </td>
                <td>
                  <b-form-select
                    v-model="linie.um"
                    :options="optiuniUm"
                    size="sm"
                    :disabled="!editabila"
                  />
                </td>
                <td>
                  <b-form-input
                    v-model.number="linie.greutate_neta"
                    type="number"
                    step="0.001"
                    size="sm"
                    :disabled="!editabila"
                  />
                </td>
                <td>
                  <b-form-input
                    v-model.number="linie.greutate_bruta"
                    type="number"
                    step="0.001"
                    size="sm"
                    :disabled="!editabila"
                  />
                </td>
                <td>
                  <b-form-input
                    v-model.number="linie.valoare"
                    type="number"
                    step="0.01"
                    size="sm"
                    :disabled="!editabila"
                    @change="recalculeazaLeii"
                  />
                </td>
                <td>
                  <b-form-input
                    v-model.number="linie.valoare_lei"
                    type="number"
                    step="0.01"
                    size="sm"
                    :disabled="!editabila"
                  />
                </td>
                <td v-if="editabila">
                  <b-button
                    variant="outline-danger"
                    size="sm"
                    class="btn-icon"
                    @click="declaratia.linii.splice(index, 1)"
                  >
                    <feather-icon icon="Trash2Icon" />
                  </b-button>
                </td>
              </tr>
            </tbody>
            <tfoot>
              <tr class="small font-weight-bold">
                <td colspan="3">
                  Total
                </td>
                <td>{{ total('cantitate') }}</td>
                <td />
                <td>{{ total('greutate_neta') }}</td>
                <td>{{ total('greutate_bruta') }}</td>
                <td>{{ total('valoare') }}</td>
                <td>{{ total('valoare_lei') }}</td>
                <td v-if="editabila" />
              </tr>
            </tfoot>
          </table>
        </div>

        <b-row
          v-if="editabila"
          class="align-items-end mt-1"
        >
          <b-col md="2">
            <label class="small mb-0">Valuta fișierelor</label>
            <b-form-select
              v-model="declaratia.valuta"
              :options="['EUR', 'RON', 'USD', 'HUF', 'GBP', 'CHF']"
              size="sm"
            />
          </b-col>
          <b-col md="3">
            <label class="small mb-0">Curs lei</label>
            <b-input-group size="sm">
              <b-form-input
                v-model.number="declaratia.curs"
                type="number"
                step="0.0001"
              />
              <b-input-group-append>
                <b-button
                  variant="outline-primary"
                  @click="iaCursulBnr"
                >
                  Curs BNR
                </b-button>
              </b-input-group-append>
            </b-input-group>
          </b-col>
          <b-col
            md="4"
            class="mt-1 mt-md-0"
          >
            <label class="small mb-0">Kg brut total (de pe DDT/CMR)</label>
            <b-input-group size="sm">
              <b-form-input
                v-model.number="greutateBrutaTotala"
                type="number"
                step="0.001"
              />
              <b-input-group-append>
                <b-button
                  variant="outline-primary"
                  @click="imparteGreutateaBruta"
                >
                  Împarte pe linii
                </b-button>
              </b-input-group-append>
            </b-input-group>
            <small class="text-muted">
              proporțional cu greutatea netă, când fișierul nu are kg brut pe linie
            </small>
          </b-col>
          <b-col
            md="3"
            class="mt-1 mt-md-0"
          >
            <b-button
              variant="outline-primary"
              size="sm"
              block
              @click="recalculeazaLeii(true)"
            >
              Recalculează valorile în lei
            </b-button>
          </b-col>
        </b-row>
      </b-card>

      <!-- Date transport -->
      <b-card
        class="border mb-2"
        body-class="p-2"
      >
        <h6 class="mb-1">
          Date transport
        </h6>
        <b-row>
          <b-col md="2">
            <label class="small mb-0">Nr. vehicul*</label>
            <b-form-input
              v-model="declaratia.nr_vehicul"
              size="sm"
              placeholder="B111ABC"
              :disabled="!editabila"
            />
          </b-col>
          <b-col md="2">
            <label class="small mb-0">Nr. remorcă 1</label>
            <b-form-input
              v-model="declaratia.nr_remorca1"
              size="sm"
              :disabled="!editabila"
            />
          </b-col>
          <b-col md="2">
            <label class="small mb-0">Nr. remorcă 2</label>
            <b-form-input
              v-model="declaratia.nr_remorca2"
              size="sm"
              :disabled="!editabila"
            />
          </b-col>
          <b-col md="3">
            <label class="small mb-0">Data transportului*</label>
            <b-form-input
              v-model="declaratia.data_transport"
              type="date"
              size="sm"
              :disabled="!editabila"
              @change="iaCursulBnr(true)"
            />
          </b-col>
        </b-row>
        <b-row class="mt-1">
          <b-col md="2">
            <label class="small mb-0">Țara transportator*</label>
            <b-form-select
              v-model="declaratia.transportator_tara"
              :options="optiuniTari"
              size="sm"
              :disabled="!editabila"
            />
          </b-col>
          <b-col md="3">
            <label class="small mb-0">Cod transportator (CIF)</label>
            <b-form-input
              v-model="declaratia.transportator_cod"
              size="sm"
              :disabled="!editabila"
            />
          </b-col>
          <b-col md="7">
            <label class="small mb-0">Denumire transportator*</label>
            <b-form-input
              v-model="declaratia.transportator_denumire"
              size="sm"
              :disabled="!editabila"
            />
          </b-col>
        </b-row>
      </b-card>

      <!-- Traseul -->
      <b-row>
        <b-col md="6">
          <loc-traseu
            v-model="declaratia.loc_start"
            titlu="Începutul traseului"
            :fel="felTraseu.start"
            :judete="nomenclatoare.judete"
            :ptf="nomenclatoare.ptf"
            :birouri="nomenclatoare.birouri_vamale"
            :editabila="editabila"
          />
        </b-col>
        <b-col md="6">
          <loc-traseu
            v-model="declaratia.loc_final"
            titlu="Sfârșitul traseului"
            :fel="felTraseu.final"
            :judete="nomenclatoare.judete"
            :ptf="nomenclatoare.ptf"
            :birouri="nomenclatoare.birouri_vamale"
            :editabila="editabila"
          />
        </b-col>
      </b-row>

      <!-- Documente transport -->
      <b-card
        class="border mb-2"
        body-class="p-2"
      >
        <div class="d-flex align-items-center mb-1">
          <h6 class="mb-0">
            Documente transport
          </h6>
          <b-button
            v-if="editabila"
            variant="outline-primary"
            size="sm"
            class="ml-2"
            @click="declaratia.documente.push({ tip: 20, numar: '', data: '', observatii: '' })"
          >
            <feather-icon icon="PlusIcon" />
            Adaugă
          </b-button>
        </div>

        <b-row
          v-for="(document, index) in declaratia.documente"
          :key="index"
          class="align-items-end mb-50"
        >
          <b-col md="3">
            <label class="small mb-0">Tip*</label>
            <b-form-select
              v-model="document.tip"
              :options="optiuniTipDocument"
              size="sm"
              :disabled="!editabila"
            />
          </b-col>
          <b-col md="3">
            <label class="small mb-0">Număr</label>
            <b-form-input
              v-model="document.numar"
              size="sm"
              :disabled="!editabila"
            />
          </b-col>
          <b-col md="3">
            <label class="small mb-0">Dată*</label>
            <b-form-input
              v-model="document.data"
              type="date"
              size="sm"
              :disabled="!editabila"
            />
          </b-col>
          <b-col md="2">
            <label class="small mb-0">Observații</label>
            <b-form-input
              v-model="document.observatii"
              size="sm"
              :disabled="!editabila"
            />
          </b-col>
          <b-col
            v-if="editabila"
            md="1"
          >
            <b-button
              variant="outline-danger"
              size="sm"
              class="btn-icon"
              @click="declaratia.documente.splice(index, 1)"
            >
              <feather-icon icon="Trash2Icon" />
            </b-button>
          </b-col>
        </b-row>
      </b-card>

      <div
        v-if="editabila"
        class="d-flex mb-2"
      >
        <b-button
          variant="outline-primary"
          :disabled="salvareInCurs"
          @click="salveaza()"
        >
          <b-spinner
            v-if="salvareInCurs"
            small
            class="mr-1"
          />
          Salvează ciorna
        </b-button>
        <b-button
          variant="primary"
          class="ml-2"
          :disabled="depunereInCurs"
          @click="depune"
        >
          <b-spinner
            v-if="depunereInCurs"
            small
            class="mr-1"
          />
          Depune la ANAF pentru UIT
        </b-button>
      </div>

      <div
        v-else-if="declaratia.index_incarcare && !declaratia.uit"
        class="mb-2"
      >
        <b-button
          variant="outline-primary"
          @click="verifica"
        >
          <feather-icon
            icon="RefreshCwIcon"
            class="mr-25"
          />
          Verifică starea la ANAF
        </b-button>
      </div>
    </div>
  </div>
</template>

<script>
import LocTraseu from './LocTraseu.vue'

/** Ciorna goala, cum arata inainte de import sau completare. */
const declaratieGoala = () => ({
  id: null,
  stare: 'ciorna',
  stare_eticheta: 'Ciornă',
  poate_fi_modificata: true,
  cif_declarant: '',
  referinta_interna: '',
  tip_operatiune: 10,
  partener_tara: null,
  partener_cod: '',
  partener_denumire: '',
  nr_vehicul: '',
  nr_remorca1: '',
  nr_remorca2: '',
  transportator_tara: 'RO',
  transportator_cod: '',
  transportator_denumire: '',
  data_transport: '',
  loc_start: { tip: 'ptf' },
  loc_final: { tip: 'adresa' },
  documente: [],
  linii: [],
  valuta: 'EUR',
  curs: null,
  fisiere_importate: [],
  index_incarcare: null,
  uit: null,
})

export default {
  name: 'DeclaratiiUit',
  components: { LocTraseu },
  data() {
    return {
      declaratii: [],
      declaratia: null,
      nomenclatoare: {
        tipuri_operatiune: {},
        scopuri: {},
        scopuri_pe_operatiune: {},
        traseu_pe_operatiune: {},
        judete: {},
        ptf: {},
        birouri_vamale: {},
        tipuri_document: {},
        unitati_masura: {},
        tari: {},
      },
      coduriVamaleGasite: [],
      importPermis: false,
      fisiereDeImportat: [],
      grupate: true,
      greutateBrutaTotala: null,
      filtruStare: '',
      info: '',
      eroare: '',
      listaInCurs: false,
      importInCurs: false,
      salvareInCurs: false,
      depunereInCurs: false,
      campuriLista: [
        { key: 'stare', label: 'Stare / UIT' },
        { key: 'cif_declarant', label: 'Declarant' },
        { key: 'operatiune', label: 'Operațiune / partener' },
        { key: 'vehicul', label: 'Vehicul' },
        { key: 'data_transport', label: 'Transport' },
        { key: 'nr_linii', label: 'Linii' },
        { key: 'valoare_lei', label: 'Valoare lei' },
        { key: 'creata_la', label: 'Creată la' },
        { key: 'actiuni', label: '' },
      ],
    }
  },
  computed: {
    editabila() {
      return !this.declaratia || this.declaratia.poate_fi_modificata
    },
    optiuniTipOperatiune() {
      return this.perechi(this.nomenclatoare.tipuri_operatiune)
    },
    optiuniTari() {
      return this.perechi(this.nomenclatoare.tari)
    },
    optiuniUm() {
      return this.perechi(this.nomenclatoare.unitati_masura, cod => cod)
    },
    optiuniTipDocument() {
      return this.perechi(this.nomenclatoare.tipuri_document)
    },
    optiuniFiltruStare() {
      return [
        { value: '', text: 'Toate stările' },
        { value: 'ciorna', text: 'Ciorne' },
        { value: 'depusa', text: 'Depuse' },
        { value: 'validata', text: 'Validate (cu UIT)' },
        { value: 'respinsa', text: 'Respinse' },
      ]
    },
    /** Scopurile permise la tipul de operatiune ales. */
    optiuniScop() {
      const tip = this.declaratia ? this.declaratia.tip_operatiune : null
      const permise = (this.nomenclatoare.scopuri_pe_operatiune || {})[tip] || []

      return permise.map(cod => ({ value: cod, text: this.nomenclatoare.scopuri[cod] || cod }))
    },
    /** Ce fel de loc cere traseul la operatiunea aleasa (ptf / birou_vamal / adresa). */
    felTraseu() {
      const tip = this.declaratia ? this.declaratia.tip_operatiune : null

      return (this.nomenclatoare.traseu_pe_operatiune || {})[tip] || { start: 'adresa', final: 'adresa' }
    },
  },
  created() {
    this.incarcaNomenclatoarele()
    this.incarcaLista()
  },
  methods: {
    perechi(obiect, text) {
      return Object.keys(obiect || {}).map(cod => ({
        value: Number.isNaN(Number(cod)) ? cod : Number(cod),
        text: text ? `${cod} — ${obiect[cod]}` : obiect[cod],
      }))
    },
    culoareStare(stare) {
      return {
        ciorna: 'light-secondary',
        depusa: 'light-warning',
        validata: 'light-success',
        respinsa: 'light-danger',
      }[stare] || 'light-secondary'
    },
    mesajEroare(err, implicit) {
      return err.response && err.response.data && err.response.data.message
        ? err.response.data.message
        : implicit
    },
    incarcaNomenclatoarele() {
      this.$http.get('/anaf-etransport/declaratii/nomenclatoare')
        .then(raspuns => {
          this.nomenclatoare = raspuns.data
          this.importPermis = !!raspuns.data.import_permis
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Nomenclatoarele nu s-au putut încărca')
        })
    },
    incarcaLista() {
      this.listaInCurs = true
      const params = this.filtruStare ? { stare: this.filtruStare } : {}

      this.$http.get('/anaf-etransport/declaratii', { params })
        .then(raspuns => {
          this.declaratii = raspuns.data.data || []
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Declarațiile nu s-au putut încărca')
        })
        .finally(() => {
          this.listaInCurs = false
        })
    },
    deschideNoua() {
      this.info = ''
      this.eroare = ''
      this.declaratia = declaratieGoala()
      this.tipOperatiuneSchimbat()
    },
    deschide(id) {
      this.info = ''
      this.eroare = ''

      this.$http.get(`/anaf-etransport/declaratii/${id}`)
        .then(raspuns => {
          this.declaratia = raspuns.data.data
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Declarația nu s-a putut deschide')
        })
    },
    inchideFormularul() {
      this.declaratia = null
      this.incarcaLista()
    },
    sterge(id) {
      this.$bvModal.msgBoxConfirm('Ștergeți ciorna?', { okTitle: 'Șterge', cancelTitle: 'Renunță' })
        .then(confirmat => {
          if (!confirmat) return

          this.$http.delete(`/anaf-etransport/declaratii/${id}`)
            .then(() => this.incarcaLista())
            .catch(err => {
              this.eroare = this.mesajEroare(err, 'Ștergerea a eșuat')
            })
        })
    },
    tipOperatiuneSchimbat() {
      if (!this.declaratia) return

      const fel = this.felTraseu

      // Traseul isi schimba felul dupa operatiune; ce era completat pentru alt fel nu se pastreaza.
      if ((this.declaratia.loc_start || {}).tip !== fel.start) {
        this.declaratia.loc_start = { tip: fel.start }
      }
      if ((this.declaratia.loc_final || {}).tip !== fel.final) {
        this.declaratia.loc_final = { tip: fel.final }
      }

      // Scopul implicit al liniilor: primul permis la operatiunea aleasa.
      const permise = (this.nomenclatoare.scopuri_pe_operatiune || {})[this.declaratia.tip_operatiune] || []
      this.declaratia.linii.forEach(linie => {
        if (!permise.includes(linie.scop_operatiune)) {
          // eslint-disable-next-line no-param-reassign
          linie.scop_operatiune = permise[0] || 9999
        }
      })
    },
    adaugaLinie() {
      const permise = (this.nomenclatoare.scopuri_pe_operatiune || {})[this.declaratia.tip_operatiune] || []

      this.declaratia.linii.push({
        cod_tarifar: '',
        denumire: '',
        scop_operatiune: permise[0] || 9999,
        cantitate: null,
        um: 'H87',
        greutate_neta: null,
        greutate_bruta: null,
        valoare: null,
        valoare_lei: null,
      })
    },
    importaFisierele() {
      this.eroare = ''
      this.importInCurs = true

      const formular = new FormData()
      this.fisiereDeImportat.forEach(fisier => formular.append('fisiere[]', fisier))
      formular.append('grupate', this.grupate ? 1 : 0)

      this.$http.post('/anaf-etransport/declaratii/importa', formular, { headers: { 'Content-Type': 'multipart/form-data' } })
        .then(raspuns => {
          this.aplicaImportul(raspuns.data)
          this.fisiereDeImportat = []
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Importul a eșuat')
        })
        .finally(() => {
          this.importInCurs = false
        })
    },
    aplicaImportul(rezultat) {
      const permise = (this.nomenclatoare.scopuri_pe_operatiune || {})[this.declaratia.tip_operatiune] || []
      const scop = permise[0] || 9999

      const linii = (rezultat.linii || []).map(linie => ({
        ...linie,
        scop_operatiune: scop,
        valoare_lei: null,
      }))

      this.declaratia.linii = this.declaratia.linii.concat(linii)
      this.declaratia.fisiere_importate = (this.declaratia.fisiere_importate || []).concat(rezultat.fisiere || [])

      const antet = rezultat.antet || {}
      if (antet.valuta) this.declaratia.valuta = antet.valuta
      if (antet.partener_denumire && !this.declaratia.partener_denumire) {
        this.declaratia.partener_denumire = antet.partener_denumire
      }
      if (antet.partener_cod && !this.declaratia.partener_cod) {
        this.declaratia.partener_cod = antet.partener_cod
      }
      if (antet.document_numar) {
        this.declaratia.documente.push({
          tip: 20,
          numar: antet.document_numar,
          data: antet.document_data || '',
          observatii: '',
        })
      }

      this.recalculeazaLeii()

      const avertismente = rezultat.avertismente || []
      this.info = `${linii.length} linii importate.${avertismente.length ? ` ${avertismente.join(' ')}` : ''}`
    },
    cautaCodVamal(termen) {
      if (!termen || termen.length < 2) return

      this.$http.get('/anaf-etransport/declaratii/coduri-vamale', { params: { q: termen } })
        .then(raspuns => {
          this.coduriVamaleGasite = raspuns.data.data || []
        })
        .catch(() => {
          // autocomplete-ul nu blocheaza formularul
        })
    },
    codVamalAles(linie) {
      const gasit = this.coduriVamaleGasite.find(cod => cod.cod === linie.cod_tarifar)

      if (gasit) {
        // eslint-disable-next-line no-param-reassign
        linie.denumire = gasit.denumire
      }
    },
    iaCursulBnr(tacut) {
      if (!this.declaratia || !this.editabila) return

      if (this.declaratia.valuta === 'RON') {
        this.declaratia.curs = 1
        this.recalculeazaLeii()

        return
      }

      const data = this.declaratia.data_transport || new Date().toISOString().slice(0, 10)

      this.$http.get('/anaf-etransport/declaratii/curs', { params: { valuta: this.declaratia.valuta, data } })
        .then(raspuns => {
          if (raspuns.data.curs) {
            this.declaratia.curs = raspuns.data.curs
            this.recalculeazaLeii()
          } else if (tacut !== true) {
            this.eroare = raspuns.data.message || 'Nu s-a găsit curs BNR'
          }
        })
        .catch(err => {
          if (tacut !== true) this.eroare = this.mesajEroare(err, 'Cursul BNR nu s-a putut lua')
        })
    },
    /** valoare (in valuta) x curs = valoare lei; scrie doar unde e de scris. */
    recalculeazaLeii(peste) {
      const curs = this.declaratia.valuta === 'RON' ? 1 : this.declaratia.curs

      if (!curs) return

      this.declaratia.linii.forEach(linie => {
        if (linie.valoare !== null && linie.valoare !== '' && (peste === true || !linie.valoare_lei)) {
          // eslint-disable-next-line no-param-reassign
          linie.valoare_lei = Math.round(linie.valoare * curs * 100) / 100
        }
      })
    },
    /** Imparte kg brut total pe linii, proportional cu kg net. */
    imparteGreutateaBruta() {
      const totalNet = this.declaratia.linii.reduce((suma, linie) => suma + (linie.greutate_neta || 0), 0)

      if (!this.greutateBrutaTotala || !totalNet) {
        this.eroare = 'Completați kg brut total și greutățile nete pe linii.'

        return
      }

      this.declaratia.linii.forEach(linie => {
        // eslint-disable-next-line no-param-reassign
        linie.greutate_bruta = Math.round(((linie.greutate_neta || 0) / totalNet) * this.greutateBrutaTotala * 1000) / 1000
      })
    },
    total(camp) {
      const suma = (this.declaratia.linii || []).reduce((acumulat, linie) => acumulat + (Number(linie[camp]) || 0), 0)

      return Math.round(suma * 1000) / 1000
    },
    salveaza(dupa) {
      this.eroare = ''
      this.salvareInCurs = true

      const cale = this.declaratia.id
        ? `/anaf-etransport/declaratii/${this.declaratia.id}`
        : '/anaf-etransport/declaratii'

      return this.$http.post(cale, this.declaratia)
        .then(raspuns => {
          this.declaratia = raspuns.data.data

          if (dupa) return dupa()

          this.info = 'Ciorna a fost salvată.'

          return null
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Salvarea a eșuat')
        })
        .finally(() => {
          this.salvareInCurs = false
        })
    },
    depune() {
      this.info = ''
      this.eroare = ''

      this.salveaza(() => {
        this.depunereInCurs = true

        return this.$http.post(`/anaf-etransport/declaratii/${this.declaratia.id}/depune`)
          .then(raspuns => {
            this.declaratia = raspuns.data.data
            this.info = this.declaratia.uit
              ? `Declarația a fost validată. UIT: ${this.declaratia.uit}`
              : `Declarația a fost depusă, index de încărcare ${this.declaratia.index_incarcare}. Verificați starea pentru UIT.`
          })
          .catch(err => {
            const date = err.response && err.response.data
            const erori = date && date.erori ? date.erori : []

            this.eroare = erori.length
              ? erori.map(e => e.errorMessage || e.mesaj || e).join(' | ')
              : this.mesajEroare(err, 'Depunerea a eșuat')

            if (date && date.data) this.declaratia = date.data
          })
          .finally(() => {
            this.depunereInCurs = false
          })
      })
    },
    verifica() {
      this.eroare = ''

      this.$http.post(`/anaf-etransport/declaratii/${this.declaratia.id}/verifica`)
        .then(raspuns => {
          this.declaratia = raspuns.data.data
          this.info = this.declaratia.uit
            ? `Declarația are UIT: ${this.declaratia.uit}`
            : 'Declarația e încă în prelucrare la ANAF.'
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Starea nu s-a putut verifica')
        })
    },
    verificaDinLista(id) {
      this.$http.post(`/anaf-etransport/declaratii/${id}/verifica`)
        .then(() => this.incarcaLista())
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Starea nu s-a putut verifica')
        })
    },
  },
}
</script>
