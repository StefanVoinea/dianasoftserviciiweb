<template>
  <div>
    <b-alert
      :show="eroare !== ''"
      variant="danger"
      class="mb-2"
      dismissible
      @dismissed="eroare = ''"
    >
      <div class="alert-body">
        {{ eroare }}
      </div>
    </b-alert>

    <b-alert
      :show="info !== ''"
      variant="info"
      class="mb-2"
      dismissible
      @dismissed="info = ''"
    >
      <div class="alert-body">
        {{ info }}
      </div>
    </b-alert>

    <b-tabs
      v-model="tabActiv"
      class="portal-just"
      lazy
    >
      <!-- Căutarea dosarelor: cel puțin un criteriu principal e obligatoriu -->
      <b-tab title="Dosare">
        <b-card
          class="border mb-2"
          body-class="p-2"
        >
          <b-row>
            <b-col md="3">
              <label class="small mb-0">Număr dosar</label>
              <b-form-input
                v-model="cautare.numar_dosar"
                size="sm"
                placeholder="ex. 1234/3/2024"
                @keyup.enter="cautaDosare"
              />
            </b-col>
            <b-col md="3">
              <label class="small mb-0">Obiect</label>
              <b-form-input
                v-model="cautare.obiect"
                size="sm"
                placeholder="ex. pretenții"
                @keyup.enter="cautaDosare"
              />
            </b-col>
            <b-col md="3">
              <label class="small mb-0">Nume parte</label>
              <b-form-input
                v-model="cautare.nume_parte"
                size="sm"
                placeholder="persoană sau societate"
                @keyup.enter="cautaDosare"
              />
            </b-col>
            <b-col md="3">
              <label class="small mb-0">Instanța</label>
              <b-form-input
                v-model="filtruInstanta"
                size="sm"
                class="mb-1"
                placeholder="filtrează instanțele…"
              />
              <b-form-select
                v-model="cautare.institutie"
                size="sm"
                :options="optiuniInstante"
              />
            </b-col>
          </b-row>

          <b-row class="mt-1">
            <b-col md="3">
              <label class="small mb-0">Data dosarului de la</label>
              <datacalendaristica
                v-model="cautare.data_start"
                name="data_start"
              />
            </b-col>
            <b-col md="3">
              <label class="small mb-0">până la</label>
              <datacalendaristica
                v-model="cautare.data_stop"
                name="data_stop"
              />
            </b-col>
            <b-col md="3">
              <label class="small mb-0">Modificat de la</label>
              <datacalendaristica
                v-model="cautare.modificat_de"
                name="modificat_de"
              />
            </b-col>
            <b-col md="3">
              <label class="small mb-0">Modificat până la</label>
              <datacalendaristica
                v-model="cautare.modificat_pana"
                name="modificat_pana"
              />
            </b-col>
          </b-row>

          <div class="d-flex align-items-center mt-2">
            <b-button
              variant="primary"
              size="sm"
              :disabled="cautareInCurs"
              @click="cautaDosare"
            >
              <b-spinner
                v-if="cautareInCurs"
                small
                class="mr-1"
              />
              Caută
            </b-button>
            <b-button
              variant="outline-secondary"
              size="sm"
              class="ml-1"
              @click="goleste"
            >
              Golește
            </b-button>
            <small class="text-muted ml-2">
              Completați cel puțin numărul dosarului, obiectul sau numele părții.
              Filtrele „modificat de la / până la” folosesc data ultimei modificări a dosarului.
            </small>
          </div>
        </b-card>

        <b-card
          class="border"
          body-class="p-2"
        >
          <div class="d-flex align-items-center mb-1">
            <h6 class="mb-0">
              Dosare găsite: {{ dosare.length }}
            </h6>
            <b-badge
              v-if="incomplet"
              variant="light-warning"
              class="ml-1"
            >
              serviciul întoarce cel mult 1000 de dosare — restrângeți criteriile
            </b-badge>
          </div>

          <b-table
            :items="dosare"
            :fields="campuriDosare"
            responsive
            small
            hover
            show-empty
            empty-text="Nicio căutare făcută încă."
            class="mb-0"
          >
            <template #cell(numar)="rand">
              <div>
                <strong>{{ rand.item.numar }}</strong>
                <div
                  v-if="rand.item.numar_vechi"
                  class="text-muted small"
                >
                  vechi: {{ rand.item.numar_vechi }}
                </div>
              </div>
            </template>

            <template #cell(instanta)="rand">
              <div>
                {{ rand.item.institutie_eticheta }}
                <div
                  v-if="rand.item.departament"
                  class="text-muted small"
                >
                  {{ rand.item.departament }}
                </div>
              </div>
            </template>

            <template #cell(stare)="rand">
              <div>
                {{ rand.item.stadiu || '—' }}
                <div
                  v-if="rand.item.categorie"
                  class="text-muted small"
                >
                  {{ rand.item.categorie }}
                </div>
              </div>
            </template>

            <template #cell(termen)="rand">
              <div v-if="ultimulTermen(rand.item)">
                {{ ultimulTermen(rand.item).data }}
                <div
                  v-if="ultimulTermen(rand.item).solutie"
                  class="text-muted small"
                >
                  {{ ultimulTermen(rand.item).solutie }}
                </div>
              </div>
              <span
                v-else
                class="text-muted"
              >—</span>
            </template>

            <template #cell(actiuni)="rand">
              <b-button
                variant="flat-primary"
                size="sm"
                :title="rand.detailsShowing ? 'Ascunde detaliile' : 'Arată părțile, termenele și căile de atac'"
                @click="rand.toggleDetails"
              >
                <feather-icon :icon="rand.detailsShowing ? 'ChevronUpIcon' : 'ChevronDownIcon'" />
              </b-button>
            </template>

            <template #row-details="rand">
              <b-card
                class="border mb-0"
                body-class="p-2"
              >
                <b-row>
                  <b-col md="4">
                    <h6 class="small text-uppercase text-muted">
                      Părți
                    </h6>
                    <div
                      v-for="(parte, i) in rand.item.parti"
                      :key="'p' + i"
                      class="small"
                    >
                      <strong>{{ parte.nume }}</strong>
                      <span class="text-muted"> — {{ parte.calitate }}</span>
                    </div>
                    <div
                      v-if="!rand.item.parti.length"
                      class="small text-muted"
                    >
                      Nu sunt părți înregistrate.
                    </div>
                  </b-col>

                  <b-col md="5">
                    <h6 class="small text-uppercase text-muted">
                      Termene
                    </h6>
                    <div
                      v-for="(termen, i) in rand.item.sedinte"
                      :key="'s' + i"
                      class="small mb-1"
                    >
                      <strong>{{ termen.data }}</strong>
                      <span v-if="termen.ora"> ora {{ termen.ora }}</span>
                      <span
                        v-if="termen.complet"
                        class="text-muted"
                      > · complet {{ termen.complet }}</span>
                      <div
                        v-if="termen.solutie"
                        class="text-muted"
                      >
                        {{ termen.solutie }}
                        <span v-if="termen.numar_document">({{ termen.numar_document }})</span>
                      </div>
                      <div
                        v-if="termen.solutie_sumar"
                        class="text-muted font-small-2"
                      >
                        {{ termen.solutie_sumar }}
                      </div>
                    </div>
                    <div
                      v-if="!rand.item.sedinte.length"
                      class="small text-muted"
                    >
                      Nu sunt termene înregistrate.
                    </div>
                  </b-col>

                  <b-col md="3">
                    <h6 class="small text-uppercase text-muted">
                      Căi de atac
                    </h6>
                    <div
                      v-for="(cale, i) in rand.item.cai_atac"
                      :key="'c' + i"
                      class="small"
                    >
                      <strong>{{ cale.tip }}</strong>
                      <div class="text-muted">
                        {{ cale.data_declarare }} · {{ cale.parte_declaratoare }}
                      </div>
                    </div>
                    <div
                      v-if="!rand.item.cai_atac.length"
                      class="small text-muted"
                    >
                      Nu sunt căi de atac.
                    </div>

                    <h6 class="small text-uppercase text-muted mt-1">
                      Obiect
                    </h6>
                    <div class="small text-muted">
                      {{ rand.item.obiect || '—' }}
                    </div>
                  </b-col>
                </b-row>
              </b-card>
            </template>
          </b-table>
        </b-card>
      </b-tab>

      <!-- Ședințele unei instanțe într-o zi: ambele criterii sunt cerute de serviciu -->
      <b-tab title="Ședințe">
        <b-card
          class="border mb-2"
          body-class="p-2"
        >
          <b-row class="align-items-end">
            <b-col md="3">
              <label class="small mb-0">Data ședinței</label>
              <datacalendaristica
                v-model="sedinteCautare.data"
                name="data_sedinta"
              />
            </b-col>
            <b-col md="5">
              <label class="small mb-0">Instanța</label>
              <b-form-input
                v-model="filtruInstanta"
                size="sm"
                class="mb-1"
                placeholder="filtrează instanțele…"
              />
              <b-form-select
                v-model="sedinteCautare.institutie"
                size="sm"
                :options="optiuniInstante"
              />
            </b-col>
            <b-col md="4">
              <b-button
                variant="primary"
                size="sm"
                :disabled="!sedinteCautare.data || !sedinteCautare.institutie || sedinteInCurs"
                @click="cautaSedinte"
              >
                <b-spinner
                  v-if="sedinteInCurs"
                  small
                  class="mr-1"
                />
                Caută ședințele
              </b-button>
            </b-col>
          </b-row>
        </b-card>

        <b-card
          v-for="(sedinta, i) in sedinte"
          :key="'sed' + i"
          class="border mb-1"
          body-class="p-2"
        >
          <div class="d-flex align-items-center mb-1">
            <h6 class="mb-0">
              {{ sedinta.data }}
              <span v-if="sedinta.ora">· ora {{ sedinta.ora }}</span>
            </h6>
            <span class="text-muted small ml-1">
              {{ sedinta.departament }}
              <span v-if="sedinta.complet">· complet {{ sedinta.complet }}</span>
            </span>
            <b-badge
              variant="light-secondary"
              class="ml-auto"
            >
              {{ sedinta.dosare.length }} dosare
            </b-badge>
          </div>

          <b-table
            :items="sedinta.dosare"
            :fields="campuriDosareSedinta"
            responsive
            small
            class="mb-0"
          />
        </b-card>

        <b-card
          v-if="!sedinte.length"
          class="border"
          body-class="p-2"
        >
          <span class="text-muted small">
            Alegeți data și instanța, apoi apăsați „Caută ședințele”.
          </span>
        </b-card>
      </b-tab>

      <!-- Dosarele urmărite: aplicația le verifică zilnic și trimite modificările pe email -->
      <b-tab title="Monitorizare">
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
                Adaugă un dosar sau o parte
              </h6>

              <b-row no-gutters>
                <b-col
                  cols="4"
                  class="pr-1"
                >
                  <b-form-select
                    v-model="monitorizareNoua.tip"
                    size="sm"
                    :options="tipuriMonitorizare"
                  />
                </b-col>
                <b-col cols="8">
                  <b-form-input
                    v-model="monitorizareNoua.valoare"
                    size="sm"
                    :placeholder="monitorizareNoua.tip === 'dosar' ? 'ex. 1234/3/2024' : 'nume sau denumire parte'"
                    @keyup.enter="adaugaMonitorizare"
                  />
                </b-col>
              </b-row>

              <b-form-input
                v-model="monitorizareNoua.email"
                size="sm"
                class="mt-1"
                placeholder="email pentru înștiințări (implicit, al dumneavoastră)"
              />

              <div class="d-flex align-items-center mt-2">
                <b-button
                  variant="primary"
                  size="sm"
                  :disabled="!monitorizareNoua.valoare || salvareInCurs"
                  @click="adaugaMonitorizare"
                >
                  <b-spinner
                    v-if="salvareInCurs"
                    small
                    class="mr-1"
                  />
                  Adaugă
                </b-button>
                <small class="text-muted ml-2">
                  Prima verificare doar reține starea de acum; pe email ajung modificările apărute după ea.
                </small>
              </div>
            </b-card>
          </b-col>

          <b-col md="6">
            <b-card
              class="h-100 border mb-0"
              body-class="p-2"
            >
              <h6 class="mb-2">
                Încarcă o listă din Excel
              </h6>

              <b-form-file
                v-model="fisierImport"
                size="sm"
                accept=".xls,.xlsx,.csv"
                placeholder="alegeți fișierul…"
                browse-text="Alege"
              />

              <b-form-input
                v-model="emailImport"
                size="sm"
                class="mt-1"
                placeholder="email pentru toate liniile (opțional)"
              />

              <div class="d-flex align-items-center mt-2">
                <b-button
                  variant="primary"
                  size="sm"
                  :disabled="!fisierImport || importInCurs"
                  @click="importa"
                >
                  <b-spinner
                    v-if="importInCurs"
                    small
                    class="mr-1"
                  />
                  Încarcă
                </b-button>
                <small class="text-muted ml-2">
                  Coloane recunoscute: „Numar dosar”, „Nume parte”, „Email”, „Instanta”.
                  Fără cap de tabel, se citește prima coloană, iar tipul se deduce din formă.
                </small>
              </div>
            </b-card>
          </b-col>
        </b-row>

        <b-card
          class="border mb-2"
          body-class="p-2"
        >
          <div class="d-flex align-items-center mb-1">
            <h6 class="mb-0">
              Dosare și părți urmărite: {{ monitorizari.length }}
            </h6>
            <b-button
              variant="outline-primary"
              size="sm"
              class="ml-auto"
              :disabled="!monitorizari.length || verificareInCurs"
              @click="verificaAcum(null)"
            >
              <b-spinner
                v-if="verificareInCurs"
                small
                class="mr-1"
              />
              Verifică acum
            </b-button>
          </div>

          <b-table
            :items="monitorizari"
            :fields="campuriMonitorizari"
            responsive
            small
            hover
            show-empty
            empty-text="Nu urmăriți încă niciun dosar."
            class="mb-0"
          >
            <template #cell(urmarit)="rand">
              <div>
                <strong>{{ rand.item.valoare }}</strong>
                <div class="text-muted small">
                  {{ rand.item.tip_eticheta }}
                  <span v-if="rand.item.institutie">· {{ rand.item.institutie }}</span>
                </div>
              </div>
            </template>

            <template #cell(verificare)="rand">
              <div>
                {{ rand.item.ultima_verificare || 'niciodată' }}
                <div
                  v-if="rand.item.ultima_eroare"
                  class="text-danger small"
                >
                  {{ rand.item.ultima_eroare }}
                </div>
              </div>
            </template>

            <template #cell(activ)="rand">
              <b-form-checkbox
                :checked="rand.item.activ"
                switch
                size="sm"
                @change="comutaMonitorizarea(rand.item, $event)"
              />
            </template>

            <template #cell(actiuni)="rand">
              <b-button
                variant="flat-primary"
                size="sm"
                title="Verifică acum acest dosar"
                @click="verificaAcum(rand.item.id)"
              >
                <feather-icon icon="RefreshCwIcon" />
              </b-button>
              <b-button
                variant="flat-danger"
                size="sm"
                title="Nu mai urmări"
                @click="stergeMonitorizarea(rand.item)"
              >
                <feather-icon icon="Trash2Icon" />
              </b-button>
            </template>
          </b-table>
        </b-card>

        <b-card
          class="border"
          body-class="p-2"
        >
          <h6 class="mb-1">
            Modificări sesizate
          </h6>

          <b-table
            :items="modificari"
            :fields="campuriModificari"
            responsive
            small
            hover
            show-empty
            empty-text="Nicio modificare sesizată până acum."
            class="mb-0"
          >
            <template #cell(dosar)="rand">
              <div>
                <strong>{{ rand.item.dosar_numar }}</strong>
                <div class="text-muted small">
                  {{ rand.item.institutie }}
                </div>
              </div>
            </template>

            <template #cell(tip_eticheta)="rand">
              <b-badge :variant="culoareModificare(rand.item.tip)">
                {{ rand.item.tip_eticheta }}
              </b-badge>
            </template>

            <template #cell(notificat_la)="rand">
              <span
                v-if="rand.item.notificat_la"
                class="text-success small"
              >
                trimis {{ rand.item.notificat_la }}
              </span>
              <span
                v-else
                class="text-muted small"
              >în așteptare</span>
            </template>
          </b-table>
        </b-card>
      </b-tab>
    </b-tabs>
  </div>
</template>

<script>
export default {
  name: 'PortalJust',

  data() {
    return {
      tabActiv: 0,
      institutii: [],
      filtruInstanta: '',
      cautare: {
        numar_dosar: '',
        obiect: '',
        nume_parte: '',
        institutie: null,
        data_start: '',
        data_stop: '',
        modificat_de: '',
        modificat_pana: '',
      },
      sedinteCautare: { data: '', institutie: null },
      dosare: [],
      sedinte: [],
      incomplet: false,
      cautareInCurs: false,
      sedinteInCurs: false,
      eroare: '',
      info: '',
      campuriDosare: [
        { key: 'numar', label: 'Număr dosar' },
        { key: 'instanta', label: 'Instanța' },
        { key: 'data', label: 'Înregistrat' },
        { key: 'stare', label: 'Stadiu' },
        { key: 'termen', label: 'Ultimul termen' },
        { key: 'data_modificare', label: 'Modificat' },
        { key: 'actiuni', label: '' },
      ],
      campuriDosareSedinta: [
        { key: 'numar', label: 'Număr dosar' },
        { key: 'ora', label: 'Ora' },
        { key: 'categorie', label: 'Categorie' },
        { key: 'stadiu', label: 'Stadiu' },
      ],

      monitorizari: [],
      modificari: [],
      monitorizareNoua: {
        tip: 'dosar', valoare: '', institutie: null, email: '',
      },
      fisierImport: null,
      emailImport: '',
      salvareInCurs: false,
      importInCurs: false,
      verificareInCurs: false,
      tipuriMonitorizare: [
        { value: 'dosar', text: 'Număr dosar' },
        { value: 'parte', text: 'Nume parte' },
      ],
      campuriMonitorizari: [
        { key: 'urmarit', label: 'Urmărit' },
        { key: 'email', label: 'Înștiințare la' },
        { key: 'dosare_urmarite', label: 'Dosare' },
        { key: 'verificare', label: 'Ultima verificare' },
        { key: 'ultima_modificare', label: 'Ultima modificare' },
        { key: 'activ', label: 'Activ' },
        { key: 'actiuni', label: '' },
      ],
      campuriModificari: [
        { key: 'sesizat_la', label: 'Sesizat' },
        { key: 'dosar', label: 'Dosar' },
        { key: 'tip_eticheta', label: 'Tip' },
        { key: 'descriere', label: 'Ce s-a schimbat' },
        { key: 'urmarit_pentru', label: 'Urmărit după' },
        { key: 'notificat_la', label: 'Email' },
      ],
    }
  },

  computed: {
    /** Lista se filtrează în pagină: sunt peste 240 de instanțe. */
    optiuniInstante() {
      const filtru = this.filtruInstanta.trim().toLowerCase()

      const potrivite = filtru
        ? this.institutii.filter(i => i.eticheta.toLowerCase().indexOf(filtru) !== -1)
        : this.institutii

      return [{ value: null, text: 'Toate instanțele' }]
        .concat(potrivite.map(i => ({ value: i.valoare, text: i.eticheta })))
    },
  },

  created() {
    document.title = `${window.app_name} -> Grefier alert`

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

    this.incarcaInstitutii()
    this.incarcaMonitorizari()
  },

  methods: {
    incarcaInstitutii() {
      this.$http.get('/portal-just/institutii')
        .then(raspuns => {
          this.institutii = raspuns.data.data
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Lista instanțelor nu a putut fi încărcată')
        })
    },

    cautaDosare() {
      this.eroare = ''
      this.info = ''
      this.cautareInCurs = true

      this.$http.get('/portal-just/dosare', { params: this.criteriiCompletate() })
        .then(raspuns => {
          this.dosare = raspuns.data.data
          this.incomplet = raspuns.data.incomplet

          if (!this.dosare.length) {
            this.info = 'Niciun dosar nu corespunde criteriilor.'
          }
        })
        .catch(err => {
          this.dosare = []
          this.eroare = this.mesajEroare(err, 'Căutarea nu a putut fi făcută')
        })
        .then(() => {
          this.cautareInCurs = false
        })
    },

    cautaSedinte() {
      this.eroare = ''
      this.info = ''
      this.sedinteInCurs = true

      this.$http.get('/portal-just/sedinte', { params: this.sedinteCautare })
        .then(raspuns => {
          this.sedinte = raspuns.data.data

          if (!this.sedinte.length) {
            this.info = 'Instanța nu are ședințe în ziua aleasă.'
          }
        })
        .catch(err => {
          this.sedinte = []
          this.eroare = this.mesajEroare(err, 'Ședințele nu au putut fi citite')
        })
        .then(() => {
          this.sedinteInCurs = false
        })
    },

    /** Criteriile goale nu se trimit, ca serviciul să nu le trateze ca filtre. */
    criteriiCompletate() {
      const params = {}

      Object.keys(this.cautare).forEach(cheie => {
        const valoare = this.cautare[cheie]

        if (valoare !== null && valoare !== '') {
          params[cheie] = valoare
        }
      })

      return params
    },

    goleste() {
      Object.keys(this.cautare).forEach(cheie => {
        this.cautare[cheie] = cheie === 'institutie' ? null : ''
      })

      this.dosare = []
      this.incomplet = false
      this.eroare = ''
      this.info = ''
    },

    /** Termenele vin în ordinea serviciului; ne interesează cel mai recent. */
    ultimulTermen(dosar) {
      return dosar.sedinte && dosar.sedinte.length ? dosar.sedinte[0] : null
    },

    incarcaMonitorizari() {
      this.$http.get('/portal-just/monitorizari')
        .then(raspuns => {
          this.monitorizari = raspuns.data.data
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Lista dosarelor urmărite nu a putut fi încărcată')
        })

      this.incarcaModificari()
    },

    incarcaModificari() {
      this.$http.get('/portal-just/modificari')
        .then(raspuns => {
          this.modificari = raspuns.data.data
        })
        .catch(() => {
          // istoricul lipsă nu blochează restul filei
        })
    },

    adaugaMonitorizare() {
      this.eroare = ''
      this.info = ''
      this.salvareInCurs = true

      this.$http.post('/portal-just/monitorizari', this.monitorizareNoua)
        .then(raspuns => {
          this.info = raspuns.data.message
          this.monitorizareNoua.valoare = ''
          this.incarcaMonitorizari()
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Monitorizarea nu a putut fi adăugată')
        })
        .then(() => {
          this.salvareInCurs = false
        })
    },

    importa() {
      this.eroare = ''
      this.info = ''
      this.importInCurs = true

      const date = new FormData()
      date.append('fisier', this.fisierImport)
      if (this.emailImport) {
        date.append('email', this.emailImport)
      }

      this.$http.post('/portal-just/monitorizari/import', date, {
        headers: { 'Content-Type': 'multipart/form-data' },
      })
        .then(raspuns => {
          this.info = raspuns.data.message
          this.fisierImport = null
          this.incarcaMonitorizari()
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Fișierul nu a putut fi importat')
        })
        .then(() => {
          this.importInCurs = false
        })
    },

    /** Fără id se verifică tot ce este activ. */
    verificaAcum(id) {
      this.eroare = ''
      this.info = ''
      this.verificareInCurs = true

      this.$http.post('/portal-just/monitorizari/verifica', id ? { id } : {})
        .then(raspuns => {
          this.info = raspuns.data.message

          if (raspuns.data.data.esecuri.length) {
            this.eroare = raspuns.data.data.esecuri.join(' · ')
          }

          this.incarcaMonitorizari()
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Verificarea nu a putut fi făcută')
        })
        .then(() => {
          this.verificareInCurs = false
        })
    },

    comutaMonitorizarea(monitorizare, activ) {
      this.$http.put(`/portal-just/monitorizari/${monitorizare.id}`, { activ })
        .then(() => {
          this.incarcaMonitorizari()
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Monitorizarea nu a putut fi actualizată')
        })
    },

    stergeMonitorizarea(monitorizare) {
      this.$bvModal.msgBoxConfirm(
        `Nu mai urmăriți „${monitorizare.valoare}”? Se șterge și istoricul modificărilor.`,
        { okTitle: 'Șterge', cancelTitle: 'Renunță', okVariant: 'danger' },
      )
        .then(confirmat => {
          if (!confirmat) {
            return
          }

          this.$http.delete(`/portal-just/monitorizari/${monitorizare.id}`)
            .then(raspuns => {
              this.info = raspuns.data.message
              this.incarcaMonitorizari()
            })
            .catch(err => {
              this.eroare = this.mesajEroare(err, 'Monitorizarea nu a putut fi ștearsă')
            })
        })
    },

    culoareModificare(tip) {
      const culori = {
        dosar_nou: 'light-primary',
        termen_nou: 'light-info',
        solutie: 'light-success',
        stadiu: 'light-warning',
        cale_atac: 'light-warning',
        parte: 'light-secondary',
        obiect: 'light-secondary',
      }

      return culori[tip] || 'light-secondary'
    },

    mesajEroare(err, implicit) {
      return err.response && err.response.data && err.response.data.message
        ? err.response.data.message
        : implicit
    },
  },
}
</script>

<style lang="scss">
.portal-just > .nav-tabs {
  margin-bottom: 0.5rem;
}
</style>
