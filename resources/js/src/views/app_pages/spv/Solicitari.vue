<template>
  <div>
    <b-card title="Solicitări documente SPV">
      <!-- Toate se termină la aceeași linie: câmpurile și butoanele rândului
           sunt aliniate la bază, nu întinse pe înălțimea celui mai înalt. -->
      <b-row class="mb-3 align-items-end">
        <b-col md="3">
          <div class="d-flex align-items-center justify-content-between">
            <label class="mb-0">
              Firme
              <span
                v-if="firme.length"
                class="text-muted"
              >· {{ firme.length }} {{ firme.length === 1 ? 'aleasă' : 'alese' }}</span>
            </label>
            <!-- Un birou cere adesea același document pentru toate firmele lui -->
            <b-button
              size="sm"
              variant="flat-primary"
              class="py-0 px-50"
              :disabled="!societati.length"
              @click="alegeToate"
            >
              <small>{{ firme.length === societati.length ? 'niciuna' : 'toate' }}</small>
            </b-button>
          </div>
          <v-select
            v-model="firme"
            multiple
            label="eticheta"
            :options="societati"
            :loading="societatiInCurs"
            placeholder="Alegeți firmele..."
            class="select-firme"
          >
            <template #no-options>
              {{ societatiInCurs ? 'Se încarcă...' : 'Nicio entitate înrolată. Sincronizați-le mai întâi.' }}
            </template>
          </v-select>
        </b-col>
        <b-col md="3">
          <label>Tip document</label>
          <b-form-select
            v-model="tipDocument"
            :options="tipuri"
          />
        </b-col>
        <b-col
          v-if="cere('an')"
          md="2"
        >
          <label>An</label>
          <b-form-input
            v-model.number="an"
            type="number"
            min="2000"
            max="2100"
          />
        </b-col>
        <b-col
          v-if="cere('luna')"
          md="2"
        >
          <label>Luna</label>
          <b-form-input
            v-model.number="luna"
            type="number"
            min="1"
            max="12"
          />
        </b-col>
        <b-col
          v-if="cere('motiv')"
          md="3"
        >
          <label>Motivul solicitării</label>
          <b-form-input
            v-model="motiv"
            placeholder="Ex: obținere credit"
          />
        </b-col>
        <b-col
          v-if="cere('numar_inregistrare')"
          md="3"
        >
          <label>Număr înregistrare declarație</label>
          <b-form-input
            v-model="numarInregistrare"
            placeholder="Ex: INTERNT-1160245317-2026"
          />
        </b-col>
        <b-col
          v-if="cere('cui_pui')"
          md="2"
        >
          <label>CUI punct de lucru (opțional)</label>
          <b-form-input v-model="cuiPui" />
        </b-col>
        <b-col
          md="2"
          class="d-flex align-items-end"
        >
          <b-button
            variant="primary"
            :disabled="!poateTrimite || trimiteInCurs"
            @click="trimite"
          >
            <b-spinner
              v-if="trimiteInCurs"
              small
              class="mr-1"
            />
            Solicită
          </b-button>
        </b-col>

        <!-- Preluarea răspunsurilor stă la capătul rândului: e celălalt capăt
             al aceleiași treburi — ce ai cerut și ce a venit. -->
        <b-col
          md="auto"
          class="ml-auto d-flex justify-content-end align-items-end"
        >
          <!-- Bifele și butonul pornesc din același colț din stânga: altfel
               textul lor, mai lat decât butonul, ar ieși în afara lui. -->
          <div class="d-flex flex-column align-items-start">
            <div
              class="pentru-tiparire"
              title="Trimite la imprimanta dumneavoastră răspunsurile aduse în această sesiune"
            >
              <b-form-checkbox
                v-model="tiparire"
                size="sm"
                class="comutator-primar"
              >
                <small
                  class="text-nowrap"
                  :class="tiparire ? 'text-primary' : 'text-muted'"
                >
                  Imprimare răspunsuri preluate
                </small>
              </b-form-checkbox>
            </div>

            <!-- Filigranul ajută la sortarea teancului: într-un vraf de fișe rol
                 de la zeci de firme, altfel nu se mai știe care a cui. -->
            <div
              class="pentru-tiparire"
              title="Aplică watermark cu denumirea firmei pe documentele de imprimat"
            >
              <b-form-checkbox
                v-model="filigran"
                size="sm"
                class="comutator-primar"
                :disabled="!tiparire"
              >
                <small
                  class="text-nowrap"
                  :class="filigran && tiparire ? 'text-primary' : 'text-muted'"
                >
                  Aplică watermark
                </small>
              </b-form-checkbox>
            </div>

            <!-- Apăsarea preia acum; din săgeată se alege manual sau automat -->
            <b-dropdown
              split
              variant="outline-primary"
              :disabled="preiaInCurs"
              @click="preia()"
            >
              <template #button-content>
                <b-spinner
                  v-if="preiaInCurs"
                  small
                  class="mr-1"
                />
                <feather-icon
                  v-else
                  icon="DownloadIcon"
                  size="15"
                  class="mr-1"
                />
                Preia răspunsurile{{ automat.activ ? ' automat' : '' }}
                <b-badge
                  v-if="inAsteptare"
                  variant="warning"
                  class="ml-1"
                >
                  {{ inAsteptare }}
                </b-badge>
              </template>

              <b-dropdown-item
                :active="!automat.activ"
                @click="automat.activ = false"
              >
                Preia răspunsurile
              </b-dropdown-item>
              <b-dropdown-item
                :active="automat.activ"
                @click="automat.activ = true"
              >
                Preia răspunsurile automat
              </b-dropdown-item>
            </b-dropdown>
          </div>
        </b-col>
      </b-row>

      <b-row class="mb-3 align-items-center">
        <!-- Intervalul și ultima preluare stau sub butonul lor, dar în afara
             rândului de mai sus: acolo ar fi împins butoanele din linie. -->
        <b-col
          md="auto"
          class="ml-auto automat-raspunsuri text-right"
        >
          <div
            v-if="automat.activ"
            class="d-flex align-items-center justify-content-end"
          >
            <small class="text-primary mr-1">la</small>
            <b-form-select
              v-model.number="automat.minute"
              size="sm"
              class="lista-minute"
              :options="optiuniMinute"
            />
          </div>

          <small class="text-muted">
            <span v-if="ultimaPreluare">
              Ultima preluare: {{ ultimaPreluare }} · {{ raspunsuriAduse }}
            </span>
            <span v-else>Nicio preluare încă</span>
          </small>
        </b-col>
      </b-row>

      <!--
        Cât timp se lucrează se spune la ce răspuns s-a ajuns: o preluare de
        zeci de documente ține minute, iar o rotiță singură nu deosebește
        lucrul de împotmolire.
      -->
      <div
        v-if="preiaInCurs && mersul"
        class="text-muted mb-3"
      >
        {{ mersul }}
        <b-progress
          v-if="deAdus"
          :value="aduse"
          :max="deAdus"
          variant="primary"
          height="6px"
          class="mt-1"
        />
      </div>

      <b-alert
        v-if="eroare"
        show
        variant="danger"
        class="mb-3"
      >
        {{ eroare }}
      </b-alert>

      <b-table
        :items="solicitariFiltrate"
        :per-page="pePagina"
        :current-page="pagina"
        :fields="campuri"
        :busy="listaInCurs"
        responsive
        striped
        small
        show-empty
        empty-text="Nu există solicitări pentru filtrul selectat."
      >
        <!-- Filtrele stau sub numele coloanei, ca la Mesaje ANAF, și lucrează
             pe ce e deja adus, deci răspund pe loc. -->
        <template #head(cif)="date">
          <div>{{ date.label }}</div>
          <b-form-input
            v-model="filtre.cif"
            size="sm"
            class="filtru-coloana"
            placeholder="CIF"
          />
        </template>

        <template #head(den_firma)="date">
          <div>{{ date.label }}</div>
          <b-form-input
            v-model="filtre.den_firma"
            size="sm"
            class="filtru-coloana"
            placeholder="Denumire"
          />
        </template>

        <template #head(tip_document)="date">
          <div>{{ date.label }}</div>
          <b-form-input
            v-model="filtre.tip_document"
            size="sm"
            class="filtru-coloana"
            placeholder="Tip"
          />
        </template>

        <template #head(data_solicitarii)="date">
          <div>{{ date.label }}</div>
          <b-form-input
            v-model="filtre.data_solicitarii"
            size="sm"
            class="filtru-coloana"
            placeholder="Data"
          />
        </template>

        <template #head(data_afisare)="date">
          <div>{{ date.label }}</div>
          <b-form-input
            v-model="filtre.data_afisare"
            size="sm"
            class="filtru-coloana"
            placeholder="Data"
          />
        </template>

        <template #head(stare)="date">
          <div>{{ date.label }}</div>
          <b-form-select
            v-model="filtre.stare"
            size="sm"
            class="filtru-coloana"
            :options="[
              { value: '', text: 'toate' },
              { value: 'preluata', text: 'răspuns primit' },
              { value: 'trimisa', text: 'în așteptare' },
            ]"
          />
        </template>

        <template #head(obs)="date">
          <div>{{ date.label }}</div>
          <b-form-input
            v-model="filtre.obs"
            size="sm"
            class="filtru-coloana"
            placeholder="Observații"
          />
        </template>

        <template #head(certificat_nume)="date">
          <div>{{ date.label }}</div>
          <b-form-input
            v-model="filtre.certificat_nume"
            size="sm"
            class="filtru-coloana"
            placeholder="Certificat"
          />
        </template>

        <template #table-busy>
          <div class="text-center my-2">
            <b-spinner class="align-middle mr-1" />
            Se încarcă solicitările...
          </div>
        </template>

        <!-- Denumirea vine din Entități înrolate; dacă CIF-ul nu mai e acolo,
             rămâne cea reținută la solicitare și se atrage atenția. -->
        <template #cell(den_firma)="rand">
          <div class="d-flex align-items-center">
            <span>{{ rand.item.den_firma || '-' }}</span>
            <feather-icon
              v-if="!rand.item.inrolata"
              icon="AlertTriangleIcon"
              size="14"
              class="text-warning ml-50 flex-shrink-0"
              :title="'CIF-ul ' + rand.item.cif + ' nu se află în Entități înrolate cu denumire. '
                + 'Denumirea afișată este cea reținută la solicitare.'"
            />
          </div>
        </template>

        <template #cell(stare)="rand">
          <b-badge :variant="rand.item.stare === 'preluata' ? 'success' : 'secondary'">
            {{ rand.item.stare === 'preluata' ? 'Răspuns primit' : 'În așteptare' }}
          </b-badge>
        </template>

        <template #cell(obs)="rand">
          <span :class="clasaObs(rand.item.obs)">{{ rand.item.obs || '-' }}</span>
        </template>

        <template #cell(actiuni)="rand">
          <b-button
            v-if="areDocument(rand.item)"
            size="sm"
            variant="outline-secondary"
            class="mr-1"
            @click="deschide(rand.item)"
          >
            Deschide
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

      <paginare
        v-model="pagina"
        :per-page.sync="pePagina"
        :total="solicitariFiltrate.length"
      />
    </b-card>
  </div>
</template>

<script>
import vSelect from 'vue-select'
import citesteFluxul, { areFlux } from '@/libs/flux'

export default {
  name: 'SpvSolicitariTab',
  components: { vSelect },
  data() {
    return {
      // Firmele alese, luate din Entități înrolate
      firme: [],
      societati: [],
      societatiInCurs: false,
      tipDocument: null,
      an: null,
      luna: null,
      motiv: '',
      numarInregistrare: '',
      cuiPui: '',
      tipuri: [],
      // tip => parametrii suplimentari ceruti de ANAF
      parametriTip: {},
      solicitari: [],
      eroare: '',
      pagina: 1,
      pePagina: 25,

      // Filtre pe coloanele tabelului; lucreaza pe ce e deja adus
      filtre: {
        cif: '',
        den_firma: '',
        tip_document: '',
        data_solicitarii: '',
        data_afisare: '',
        stare: '',
        obs: '',
        certificat_nume: '',
      },
      listaInCurs: false,
      trimiteInCurs: false,
      preiaInCurs: false,
      // Mersul lucrului, cat timp se preia: „3 din 7 — Fisa rol 15208744"
      mersul: '',
      aduse: 0,
      deAdus: 0,

      // Preluarea repetată, ca la recipise: ANAF răspunde după un timp
      automat: { activ: false, minute: 10 },
      optiuniMinute: [
        { value: 5, text: '5 minute' },
        { value: 10, text: '10 minute' },
        { value: 15, text: '15 minute' },
        { value: 30, text: '30 minute' },
        { value: 60, text: '60 minute' },
      ],
      cronometru: null,
      ultimaPreluare: '',
      ultimaPreluareNumar: 0,
      tiparire: false,
      filigran: false,
      campuri: [
        { key: 'cif', label: 'CIF' },
        { key: 'den_firma', label: 'Denumire' },
        { key: 'tip_document', label: 'Tip document' },
        { key: 'data_solicitarii', label: 'Solicitat la' },
        { key: 'data_afisare', label: 'Răspuns la' },
        { key: 'stare', label: 'Stare' },
        { key: 'obs', label: 'Observații' },
        { key: 'certificat_nume', label: 'Certificat' },
        { key: 'actiuni', label: 'Acțiuni' },
      ],
    }
  },
  computed: {
    listaCui() {
      return this.firme.map(firma => firma.cif)
    },
    /** Solicitările care trec de filtrele scrise pe coloane. */
    solicitariFiltrate() {
      const contine = (valoare, cautat) => String(valoare || '')
        .toLowerCase()
        .indexOf(cautat.toLowerCase()) !== -1

      return this.solicitari.filter(solicitare => {
        if (this.filtre.stare && solicitare.stare !== this.filtre.stare) return false

        return Object.keys(this.filtre)
          .filter(cheie => cheie !== 'stare')
          .every(cheie => {
            const cautat = (this.filtre[cheie] || '').trim()

            return cautat === '' || contine(solicitare[cheie], cautat)
          })
      })
    },
    /** Câte solicitări încă așteaptă răspuns de la ANAF. */
    inAsteptare() {
      return this.solicitari.filter(s => s.stare !== 'preluata').length
    },
    raspunsuriAduse() {
      if (!this.ultimaPreluareNumar) return 'fără răspunsuri noi'

      return this.ultimaPreluareNumar === 1 ? 'un răspuns' : `${this.ultimaPreluareNumar} răspunsuri`
    },
    poateTrimite() {
      if (this.listaCui.length === 0 || !this.tipDocument) return false

      // cui_pui e optional; restul parametrilor ceruti de ANAF sunt obligatorii
      return (this.parametriTip[this.tipDocument] || [])
        .filter(p => p !== 'cui_pui')
        .every(p => {
          const valoare = {
            an: this.an, luna: this.luna, motiv: this.motiv, numar_inregistrare: this.numarInregistrare,
          }[p]
          return valoare !== null && valoare !== '' && valoare !== undefined
        })
    },
  },
  watch: {
    // Fără imprimare, filigranul n-are pe ce sta: se stinge odată cu ea, ca
    // bifa să nu rămână aprinsă degeaba.
    tiparire(activa) {
      if (!activa) this.filigran = false
    },
    // Orice schimbare a setării o ține minte și repornește cronometrul.
    'automat.activ': function reporneste() {
      this.salveazaSetarea()
      this.reglaCronometrul()
    },
    'automat.minute': function reporneste() {
      this.salveazaSetarea()
      this.reglaCronometrul()
    },
  },
  created() {
    this.incarcaSetarea()
    this.incarcaLista()
    this.incarcaSocietati()
    this.reglaCronometrul()
  },
  beforeDestroy() {
    // Fără asta, cronometrul ar cere răspunsuri și după plecarea din filă.
    this.opresteCronometrul()
  },
  methods: {
    /**
     * Firmele din Entități înrolate — doar cele active: pentru celelalte, SPV
     * respinge oricum solicitarea.
     */
    incarcaSocietati() {
      this.societatiInCurs = true

      this.$http.get('/anaf-societati', { params: { doar_active: 1 } })
        .then(({ data }) => {
          this.societati = data.data.map(societate => ({
            cif: societate.cif,
            eticheta: societate.denumire ? `${societate.denumire} (${societate.cif})` : societate.cif,
          }))
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Entitățile înrolate nu au putut fi încărcate')
        })
        .finally(() => {
          this.societatiInCurs = false
        })
    },
    alegeToate() {
      this.firme = this.firme.length === this.societati.length ? [] : [...this.societati]
    },
    clasaObs(obs) {
      if (!obs) return ''
      return obs.indexOf('ATENȚIE') === 0 ? 'text-danger font-weight-bold' : 'text-muted'
    },
    cere(parametru) {
      return (this.parametriTip[this.tipDocument] || []).indexOf(parametru) !== -1
    },
    /*
     * Răspunsul are document de arătat?
     *
     * De acum el stă în arhiva de pe calculatorul clientului (arhiva_cale), nu
     * pe server. Cele preluate înainte sunt încă pe server (cale_fisier), deci
     * se ține seama de amândouă.
     */
    areDocument(solicitare) {
      return Boolean(solicitare.arhiva_cale || solicitare.cale_fisier)
    },
    // Documentele stau pe discul privat, deci se cer prin API (cu token) si se
    // deschid dintr-un blob local, nu printr-un link direct catre /storage.
    deschide(solicitare) {
      this.eroare = ''

      this.$http.get(`/spv/solicitari/${solicitare.id}/fisier`, { responseType: 'blob' })
        .then(raspuns => {
          const url = window.URL.createObjectURL(new Blob([raspuns.data], { type: 'application/pdf' }))
          window.open(url, '_blank')
          setTimeout(() => window.URL.revokeObjectURL(url), 60000)
        })
        .catch(() => {
          this.eroare = 'Documentul nu a putut fi deschis.'
        })
    },
    /** Întoarce promisiunea: preluarea are nevoie de lista proaspătă. */
    incarcaLista() {
      this.listaInCurs = true
      // Filtrarea se face pe coloanele tabelului, dupa ce lista e adusa intreaga.
      return this.$http.get('/spv/solicitari')
        .then(raspuns => {
          this.solicitari = raspuns.data.data || []
          this.parametriTip = raspuns.data.tipuri || {}
          this.tipuri = Object.keys(this.parametriTip).map(tip => ({ value: tip, text: tip }))
          if (!this.tipDocument && this.tipuri.length) {
            this.tipDocument = this.tipuri[0].value
          }
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Nu s-au putut încărca solicitările')
        })
        .finally(() => {
          this.listaInCurs = false
        })
    },
    trimite() {
      this.eroare = ''
      this.trimiteInCurs = true

      this.$http.post('/spv/solicitari', {
        cui: this.listaCui,
        tip_document: this.tipDocument,
        an: this.an || null,
        luna: this.luna || null,
        motiv: this.motiv || null,
        numar_inregistrare: this.numarInregistrare || null,
        cui_pui: this.cuiPui || null,
      })
        .then(raspuns => {
          const trimise = (raspuns.data.data || []).length
          this.notifica(`${trimise} solicitare/solicitări trimise către SPV`, 'success')
          if (raspuns.data.erori && raspuns.data.erori.length) {
            this.eroare = raspuns.data.erori.join(' | ')
          }
          this.firme = []
          this.incarcaLista()
        })
        .catch(err => {
          const date = err.response && err.response.data
          this.eroare = date && date.erori && date.erori.length
            ? date.erori.join(' | ')
            : this.mesajEroare(err, 'Solicitarea a eșuat')
        })
        .finally(() => {
          this.trimiteInCurs = false
        })
    },
    /**
     * @param {boolean} tacut pornit de cronometru: nu deranjează cu mesaje când
     *                        n-a venit nimic nou
     */
    preia(tacut = false) {
      this.eroare = ''
      this.preiaInCurs = true
      this.mersul = ''
      this.aduse = 0
      this.deAdus = 0

      // Ce era deja preluat înainte: la sfârșit se tipăresc doar noutățile.
      const inainte = this.solicitari.filter(s => this.areDocument(s)).map(s => s.id)

      return this.cereRaspunsurile()
        .then(rezultat => {
          this.ultimaPreluare = this.acum()
          this.ultimaPreluareNumar = rezultat.preluate || 0
          window.localStorage.setItem('solicitari_ultima_preluare', JSON.stringify({
            la: this.ultimaPreluare,
            preluate: this.ultimaPreluareNumar,
          }))

          if (!tacut || rezultat.preluate > 0) {
            this.notifica(
              `Verificate: ${rezultat.verificate}, răspunsuri noi: ${rezultat.preluate}`,
              rezultat.preluate > 0 ? 'success' : 'info',
            )
          }

          if (rezultat.erori && rezultat.erori.length) {
            this.eroare = rezultat.erori.join(' | ')
          }

          return this.incarcaLista().then(() => {
            if (!this.tiparire) return null

            const noi = this.solicitari
              .filter(s => this.areDocument(s) && inainte.indexOf(s.id) === -1)
              .map(s => s.id)

            return noi.length ? this.tipareste(noi) : null
          })
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Preluarea răspunsurilor a eșuat')
        })
        .finally(() => {
          this.preiaInCurs = false
          this.mersul = ''
          this.aduse = 0
          this.deAdus = 0
        })
    },
    /**
     * Cere răspunsurile, spunând la fiecare al câtelea e din câte.
     *
     * Răspunsul curge — câte un rând pe măsură ce se lucrează —, așa că omul
     * vede mersul, nu doar o rotiță: fiecare document are pauza cerută de ANAF
     * și drumul până la tokenul clientului, iar din afară lucrul și
     * împotmolirea arată la fel.
     *
     * Se numără cele care chiar au ce aduce, nu toate solicitările în
     * așteptare: „3 din 3" spune adevărul, „3 din 120" ar părea că s-a oprit.
     *
     * @returns {Promise<object>} rezultatul, în forma răspunsului obișnuit
     */
    cereRaspunsurile() {
      // Browserele fără fetch cu flux rămân pe calea dinainte, fără numărătoare.
      if (!areFlux()) {
        return this.$http.post('/spv/solicitari/preia').then(raspuns => raspuns.data.data)
      }

      let rezultat = {
        verificate: 0,
        preluate: 0,
        ramase: 0,
        erori: [],
      }

      return citesteFluxul('spv/solicitari/preia/flux', pas => {
        if (pas.tip === 'inceput') {
          this.deAdus = pas.total
          this.aduse = 0

          if (pas.total) this.mersul = `0 din ${pas.total} răspunsuri aduse...`

          return
        }

        if (pas.tip === 'pas') {
          this.aduse = pas.facute

          const care = pas.ce ? ` — ${pas.ce}` : ''
          const cazut = pas.reusit ? '' : ' (nu s-a putut aduce)'

          this.mersul = `${pas.facute} din ${pas.total} răspunsuri aduse${care}${cazut}`

          return
        }

        if (pas.tip === 'gata') rezultat = pas
      }).then(() => rezultat)
    },
    /**
     * Trimite documentele la imprimanta utilizatorului.
     *
     */
    tipareste(ids) {
      return this.$http.post('/spv/solicitari/tipareste', { id: ids, filigran: this.filigran })
        .then(({ data }) => {
          this.notifica(
            `${data.data.documente} documente trimise la „${data.data.imprimanta}”`,
            'success',
          )
        })
        .catch(err => {
          const motiv = this.mesajEroare(err, 'programul local nu a răspuns.')

          this.eroare = `Răspunsurile au fost preluate, dar tipărirea nu a reușit: ${motiv}`
        })
    },
    /** Momentul de acum, scris ca peste tot în modul: zz.ll.aaaa hh:mm:ss. */
    acum() {
      const d = new Date()
      const doua = n => String(n).padStart(2, '0')

      return `${doua(d.getDate())}.${doua(d.getMonth() + 1)}.${d.getFullYear()} `
        + `${doua(d.getHours())}:${doua(d.getMinutes())}:${doua(d.getSeconds())}`
    },
    incarcaSetarea() {
      try {
        const salvat = JSON.parse(window.localStorage.getItem('solicitari_preluare_automat'))

        if (salvat && typeof salvat.minute === 'number' && salvat.minute > 0) {
          const permise = this.optiuniMinute.map(o => o.value)
          const minute = permise.indexOf(salvat.minute) !== -1
            ? salvat.minute
            : permise.reduce((a, b) => (Math.abs(b - salvat.minute) < Math.abs(a - salvat.minute) ? b : a))

          this.automat = { activ: !!salvat.activ, minute }
        }

        const ultima = JSON.parse(window.localStorage.getItem('solicitari_ultima_preluare'))

        if (ultima) {
          this.ultimaPreluare = ultima.la || ''
          this.ultimaPreluareNumar = ultima.preluate || 0
        }
      } catch (e) {
        // setare veche sau stricată — se rămâne pe valorile implicite
      }
    },
    salveazaSetarea() {
      window.localStorage.setItem('solicitari_preluare_automat', JSON.stringify(this.automat))
    },
    opresteCronometrul() {
      if (this.cronometru) {
        window.clearInterval(this.cronometru)
        this.cronometru = null
      }
    },
    reglaCronometrul() {
      this.opresteCronometrul()

      const minute = Number(this.automat.minute)

      if (!this.automat.activ || !minute || minute < 1) {
        return
      }

      this.cronometru = window.setInterval(() => {
        // Se întreabă ANAF-ul doar când chiar e ceva de așteptat, și nu peste o
        // preluare pornită de mână sau peste ea însăși.
        if (!this.preiaInCurs && this.inAsteptare) {
          this.preia(true)
        }
      }, minute * 60 * 1000)
    },
    sterge(solicitare) {
      this.$http.delete(`/spv/solicitari/${solicitare.id}`)
        .then(() => {
          this.notifica('Solicitarea a fost ștearsă', 'success')
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
      this.$bvToast.toast(mesaj, { title: 'Solicitări SPV', variant, solid: true })
    },
  },
}
</script>

<style lang="scss">
@import "@core/scss/vue/libs/vue-select.scss";

/* Lista de intervale: doar cat sa incapa "60 minute", scunda ca sa nu ingroase randul. */
.lista-minute {
  width: 6.5rem;
  height: 1.6rem;
  padding: 0 1.2rem 0 0.4rem;
  line-height: 1.2;
  background-position: right 0.35rem center;
}

/* Casutele de filtru din antet: scunde, ca sa nu ingroase capul tabelului. */
.filtru-coloana {
  height: 1.6rem;
  padding: 0 0.35rem;
  font-size: 0.75rem;
  font-weight: 400;
  margin-top: 0.15rem;
}

/* Bifele stau lipite de buton, discret, fara chenar. */
.pentru-tiparire {
  margin-bottom: 0.15rem;
}

/* Bifa incepe din coltul din stanga al butonului, nu mai la stanga de el. */
.pentru-tiparire ::v-deep .custom-control {
  padding-left: 1.35rem;
}

.pentru-tiparire ::v-deep .custom-control-label::before,
.pentru-tiparire ::v-deep .custom-control-label::after {
  left: -1.35rem;
}

.pentru-tiparire small {
  font-size: 0.75rem;
}

.automat-raspunsuri small {
  font-size: 0.72rem;
}

/* Multe firme alese ar impinge tot randul in jos; lista se deruleaza in loc. */
.select-firme .vs__selected-options {
  max-height: 7rem;
  overflow-y: auto;
}
</style>
