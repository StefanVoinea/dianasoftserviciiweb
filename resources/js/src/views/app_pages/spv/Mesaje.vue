<template>
  <div class="card">
    <div class="card-body">
      <div class="row mb-3">
        <div class="col-md-4">
          <div class="d-flex align-items-center justify-content-between">
            <label class="form-label mb-0">
              Firme
              <span
                v-if="firme.length"
                class="text-muted"
              >· {{ firme.length }} {{ firme.length === 1 ? 'aleasă' : 'alese' }}</span>
            </label>
            <b-button
              size="sm"
              variant="flat-primary"
              class="py-0 px-50"
              :disabled="!societati.length"
              @click="alegeToateFirmele"
            >
              <small>{{ firme.length === societati.length ? 'niciuna' : 'toate' }}</small>
            </b-button>
          </div>
          <v-select
            v-model="firme"
            multiple
            label="eticheta"
            :options="optiuniFirmeInrolate"
            placeholder="Toate firmele înrolate"
            class="select-firme"
          >
            <template #no-options>
              Nicio entitate înrolată. Sincronizați-le mai întâi.
            </template>
          </v-select>
        </div>
        <div class="col-md-2">
          <label class="form-label">Zile de interogat la ANAF</label>
          <input
            v-model.number="zile"
            type="number"
            min="1"
            max="60"
            class="form-control"
          >
        </div>
        <!-- Butoanele stau pe aceeași linie; reglajul descărcării automate trece
             sub ele, ca să nu ridice unul dintre butoane din rând. -->
        <div class="col-md-6 d-flex flex-column justify-content-end align-items-end">
          <div class="d-flex align-items-end">
            <b-button
              variant="outline-primary"
              class="mr-1"
              @click="deschideAlerte"
            >
              <feather-icon
                icon="BellIcon"
                class="mr-50"
              />Configurare alerte
            </b-button>

            <!-- Apăsarea aduce acum; din săgeată se alege manual sau automat -->
            <b-dropdown
              split
              variant="outline-success"
              :disabled="loading"
              @click="loadMessages()"
            >
              <template #button-content>
                <b-spinner
                  v-if="loading"
                  small
                  class="mr-1"
                />
                <feather-icon
                  v-else
                  icon="DownloadIcon"
                  size="15"
                  class="mr-1"
                />
                Descarcă mesaje{{ automat.activ ? ' automat' : '' }}
                <b-badge
                  v-if="fisiereLipsa"
                  variant="warning"
                  class="ml-1"
                >
                  {{ fisiereLipsa }}
                </b-badge>
              </template>

              <b-dropdown-item
                :active="!automat.activ"
                @click="automat.activ = false"
              >
                Descarcă mesaje
              </b-dropdown-item>
              <b-dropdown-item
                :active="automat.activ"
                @click="automat.activ = true"
              >
                Descarcă mesaje automat
              </b-dropdown-item>
            </b-dropdown>
          </div>

          <div class="automat-mesaje mt-1 text-right">
            <div
              v-if="automat.activ"
              class="d-flex align-items-center justify-content-end"
            >
              <small class="text-success mr-1">la</small>
              <b-form-select
                v-model.number="automat.minute"
                size="sm"
                class="lista-minute"
                :options="optiuniMinute"
              />
            </div>

            <small class="text-muted">
              <span v-if="ultimaDescarcare">
                Ultima descărcare: {{ ultimaDescarcare }} · {{ mesajeAduse }}
              </span>
              <span v-else>Nicio descărcare încă</span>
            </small>
          </div>
        </div>
      </div>

      <!-- Înștiințări pe email când intră un anumit fel de document -->
      <b-modal
        v-model="alerteVizibile"
        title="Alerte pe email pentru documente noi în SPV"
        size="lg"
        ok-only
        ok-title="Închide"
        modal-class="modul-spv"
      >
        <b-alert
          v-if="eroareAlerte"
          show
          variant="danger"
          class="py-1 px-2 small"
        >
          {{ eroareAlerte }}
        </b-alert>

        <p class="text-muted small">
          Când ANAF pune în SPV un document de tipul ales, adresa primește o
          înștiințare. Lăsate goale, certificatul și firma înseamnă „oricare”.
        </p>

        <b-table
          v-if="alerte.length"
          :items="alerte"
          :fields="campuriAlerte"
          small
          responsive
          class="mb-2"
        >
          <template #cell(unde)="rand">
            <div>{{ rand.item.certificat_nume || 'orice certificat' }}</div>
            <div class="small text-muted">
              {{ numeFirma(rand.item.cif) }}
            </div>
          </template>

          <template #cell(tip_document)="rand">
            {{ rand.item.tip_document || 'orice document' }}
          </template>

          <template #cell(trimise)="rand">
            <span v-if="rand.item.trimise">
              {{ rand.item.trimise }}
              <div class="small text-muted">{{ rand.item.ultima_alerta_la }}</div>
            </span>
            <span
              v-else
              class="text-muted"
            >—</span>
          </template>

          <template #cell(actiuni)="rand">
            <b-button
              size="sm"
              variant="flat-secondary"
              class="btn-icon"
              title="Modifică"
              @click="editeazaAlerta(rand.item)"
            >
              <feather-icon icon="Edit2Icon" />
            </b-button>
            <b-button
              size="sm"
              variant="flat-danger"
              class="btn-icon"
              title="Șterge"
              @click="stergeAlerta(rand.item)"
            >
              <feather-icon icon="Trash2Icon" />
            </b-button>
          </template>
        </b-table>

        <hr>
        <h6>{{ alerta.id ? 'Modifică alerta' : 'Alertă nouă' }}</h6>

        <b-row>
          <b-col md="6">
            <label>Adresa de email</label>
            <b-form-input
              v-model="alerta.email"
              type="email"
              placeholder="nume@firma.ro"
              class="mb-2"
            />
          </b-col>
          <b-col md="6">
            <label>Tipul documentului</label>
            <b-form-input
              v-model="alerta.tip_document"
              list="tipuri-documente"
              placeholder="orice document"
              class="mb-1"
            />
            <b-form-datalist
              id="tipuri-documente"
              :options="optiuniTipuri"
            />
            <small class="text-muted d-block mb-2">
              <span v-if="tipuriVazute">
                Primele {{ tipuriVazute }} din listă au apărut deja în mesajele
                dumneavoastră; restul sunt feluri obișnuite.
              </span>
              Puteți scrie și altceva: se potrivește pe bucată de text, fără să
              conteze literele mari sau mici.
            </small>
          </b-col>
        </b-row>

        <b-row>
          <b-col md="6">
            <label>Certificatul digital</label>
            <b-form-select
              v-model="alerta.certificat_id"
              :options="optiuniCertificate"
              class="mb-2"
            />
          </b-col>
          <b-col md="6">
            <label>Firma</label>
            <b-form-select
              v-model="alerta.cif"
              :options="optiuniFirme"
              class="mb-2"
            />
            <small class="text-muted">
              Nealeasă, alerta prinde toate firmele înrolate certificatului de alături.
            </small>
          </b-col>
        </b-row>

        <div class="d-flex align-items-center mt-1">
          <b-form-checkbox v-model="alerta.activ">
            Activă
          </b-form-checkbox>
          <b-button
            variant="primary"
            size="sm"
            class="ml-auto"
            :disabled="!alerta.email"
            @click="salveazaAlerta"
          >
            {{ alerta.id ? 'Salvează' : 'Adaugă' }}
          </b-button>
          <b-button
            v-if="alerta.id"
            variant="flat-secondary"
            size="sm"
            class="ml-1"
            @click="alertaNoua"
          >
            Renunță
          </b-button>
        </div>
      </b-modal>

      <div
        v-if="loading"
        class="text-muted"
      >
        Se încarcă mesajele SPV și se descarcă fișierele...
      </div>
      <div
        v-else-if="error"
        class="alert alert-danger"
      >
        {{ error }}
      </div>
      <div
        v-else-if="!messages.length"
        class="text-muted"
      >
        Nu există mesaje pentru filtrul selectat.
      </div>
      <div v-else>
        <div
          v-if="infoDescarcare"
          class="alert alert-info py-2"
        >
          {{ infoDescarcare }}
        </div>

        <p class="text-muted small mb-2">
          <span v-if="mesajeFiltrate.length !== messages.length">
            {{ mesajeFiltrate.length }} din {{ messages.length }} mesaje
          </span>
          <span v-else>{{ messages.length }} mesaje în istoric</span>.
          Tabelul arată ce este deja stocat; „Descarcă mesaje” aduce noutățile de
          la ANAF, pe fereastra de zile de mai sus.
        </p>

        <table class="table table-sm table-bordered">
          <thead>
            <tr>
              <th>Tip</th>
              <th>CIF</th>
              <th>Denumire</th>
              <th>Data creare</th>
              <th>ID solicitare</th>
              <th>Detalii</th>
              <th>Certificat</th>
              <th>Fișier</th>
            </tr>
            <!-- Filtrele lucrează pe ce e deja adus, deci răspund pe loc -->
            <tr class="rand-filtre">
              <th
                v-for="camp in campuriFiltrabile"
                :key="camp.cheie"
              >
                <b-form-input
                  v-model="filtre[camp.cheie]"
                  size="sm"
                  :placeholder="camp.eticheta"
                />
              </th>
              <th>
                <b-form-select
                  v-model="filtre.descarcat"
                  size="sm"
                  :options="[
                    { value: '', text: 'toate' },
                    { value: 'da', text: 'descărcate' },
                    { value: 'nu', text: 'nedescărcate' },
                  ]"
                />
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(item, index) in mesajePagina"
              :key="index"
            >
              <td>{{ item.tip || '-' }}</td>
              <td>{{ item.cif || '-' }}</td>
              <td>{{ item.den_firma || '-' }}</td>
              <td>{{ item.data_creare || '-' }}</td>
              <td>{{ item.id_solicitare || '-' }}</td>
              <td>{{ item.detalii || '-' }}</td>
              <td>{{ item.certificat || '-' }}</td>
              <td>
                <button
                  v-if="item.descarcat"
                  class="btn btn-sm btn-outline-secondary"
                  @click="openFile(item.id)"
                >
                  Deschide
                </button>
                <span
                  v-else
                  :class="item.ultima_eroare ? 'text-danger' : 'text-muted'"
                >
                  {{ statusFisier(item) }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>

        <paginare
          v-model="pagina"
          :per-page.sync="pePagina"
          :total="mesajeFiltrate.length"
        />
      </div>
    </div>
  </div>
</template>

<script>
import vSelect from 'vue-select'

export default {
  name: 'SpvMesaje',
  components: { vSelect },
  data() {
    return {
      // Firmele pentru care se cer mesaje, din Entități înrolate
      firme: [],
      zile: 30,
      loading: false,
      error: '',
      messages: [],
      pagina: 1,
      pePagina: 25,
      infoDescarcare: '',

      // Filtre pe coloanele tabelului; lucrează pe ce e deja adus
      filtre: {
        tip: '', cif: '', den_firma: '', data_creare: '', id_solicitare: '', detalii: '', certificat: '', descarcat: '',
      },
      campuriFiltrabile: [
        { cheie: 'tip', eticheta: 'Tip' },
        { cheie: 'cif', eticheta: 'CIF' },
        { cheie: 'den_firma', eticheta: 'Denumire' },
        { cheie: 'data_creare', eticheta: 'Data' },
        { cheie: 'id_solicitare', eticheta: 'ID solicitare' },
        { cheie: 'detalii', eticheta: 'Detalii' },
        { cheie: 'certificat', eticheta: 'Certificat' },
      ],

      // Descărcarea repetată, ca la recipise
      automat: { activ: false, minute: 10 },
      optiuniMinute: [
        { value: 5, text: '5 minute' },
        { value: 10, text: '10 minute' },
        { value: 15, text: '15 minute' },
        { value: 30, text: '30 minute' },
        { value: 60, text: '60 minute' },
      ],
      cronometru: null,
      ultimaDescarcare: '',
      ultimaDescarcareNumar: 0,

      alerteVizibile: false,
      alerte: [],
      certificate: [],
      societati: [],
      tipuri: [],
      alerta: {},
      eroareAlerte: '',
      campuriAlerte: [
        { key: 'email', label: 'Email' },
        { key: 'tip_document', label: 'Document' },
        { key: 'unde', label: 'Pentru' },
        { key: 'trimise', label: 'Trimise' },
        { key: 'actiuni', label: '' },
      ],
    }
  },
  computed: {
    /** Mesajele care trec de filtrele scrise pe coloane. */
    mesajeFiltrate() {
      const contine = (valoare, cautat) => String(valoare || '')
        .toLowerCase()
        .indexOf(cautat.toLowerCase()) !== -1

      return this.messages.filter(mesaj => {
        if (this.filtre.descarcat === 'da' && !mesaj.descarcat) return false
        if (this.filtre.descarcat === 'nu' && mesaj.descarcat) return false

        return this.campuriFiltrabile.every(camp => {
          const cautat = (this.filtre[camp.cheie] || '').trim()

          return cautat === '' || contine(mesaj[camp.cheie], cautat)
        })
      })
    },
    /** Doar randurile paginii curente: tabelul e scris de mana, nu e b-table. */
    mesajePagina() {
      const inceput = (this.pagina - 1) * this.pePagina

      return this.mesajeFiltrate.slice(inceput, inceput + this.pePagina)
    },
    /**
     * Câte documente are de adus chiar acest buton.
     *
     * Recipisele și răspunsurile la solicitări nu se numără: ele se aduc din
     * filele lor, iar aici numărul ar rămâne aprins fără ca butonul să-l poată
     * stinge vreodată.
     */
    fisiereLipsa() {
      return this.messages.filter(mesaj => !mesaj.descarcat && !mesaj.fila_care_aduce).length
    },
    mesajeAduse() {
      if (!this.ultimaDescarcareNumar) return 'fără mesaje noi'

      return this.ultimaDescarcareNumar === 1 ? 'un mesaj nou' : `${this.ultimaDescarcareNumar} mesaje noi`
    },
    /** Firmele alese, ca listă de CIF-uri pentru interogarea ANAF. */
    listaCif() {
      return this.firme.map(firma => firma.cif)
    },
    optiuniFirmeInrolate() {
      return this.societati.map(societate => ({
        cif: societate.cif,
        eticheta: societate.denumire ? `${societate.denumire} (${societate.cif})` : societate.cif,
      }))
    },
    /**
     * Felurile de document din listă. Cele deja întâlnite se scot în față cu un
     * semn, ca să se vadă ce chiar a venit față de ce e doar posibil.
     */
    optiuniTipuri() {
      // Doar valoarea, fără vreun semn în plus: din datalist se scrie în câmp
      // exact ce e aici, iar un caracter străin ar strica potrivirea.
      return this.tipuri.map(tip => tip.valoare)
    },
    /** Câte dintre felurile din listă au apărut deja în mesaje. */
    tipuriVazute() {
      return this.tipuri.filter(tip => tip.vazut).length
    },
    optiuniCertificate() {
      return [{ value: null, text: 'orice certificat' }]
        .concat(this.certificate.map(certificat => ({ value: certificat.id, text: certificat.cn })))
    },
    /** Firmele se restrâng la certificatul ales, dacă e vreunul. */
    optiuniFirme() {
      const potrivite = this.alerta.certificat_id
        ? this.societati.filter(societate => societate.certificat_id === this.alerta.certificat_id)
        : this.societati

      return [{ value: null, text: 'toate firmele înrolate' }]
        .concat(potrivite.map(societate => ({
          value: societate.cif,
          text: `${societate.denumire || 'fără denumire'} (${societate.cif})`,
        })))
    },
  },
  watch: {
    // Orice schimbare a setarii o tine minte si reporneste cronometrul.
    'automat.activ': function reporneste() {
      this.salveazaSetarea()
      this.reglaCronometrul()
    },
    'automat.minute': function reporneste() {
      this.salveazaSetarea()
      this.reglaCronometrul()
    },
    // Dupa filtrare, pagina veche poate sa nu mai existe.
    mesajeFiltrate() {
      const ultima = Math.max(1, Math.ceil(this.mesajeFiltrate.length / this.pePagina))

      if (this.pagina > ultima) this.pagina = ultima
    },
  },
  created() {
    this.incarcaSetarea()
    this.incarcaStocate()
    this.incarcaSocietatiInrolate()
    this.reglaCronometrul()
  },
  beforeDestroy() {
    this.opresteCronometrul()
  },
  methods: {
    deschideAlerte() {
      this.eroareAlerte = ''
      this.alerteVizibile = true
      this.alertaNoua()

      this.$http.get('/spv-alerte')
        .then(({ data }) => {
          this.alerte = data.data
          this.certificate = data.certificate
          this.societati = data.societati
          this.tipuri = data.tipuri
        })
        .catch(err => {
          this.eroareAlerte = this.mesajEroareAlerte(err, 'Alertele nu au putut fi încărcate')
        })
    },
    alertaNoua() {
      this.alerta = {
        email: '', tip_document: '', certificat_id: null, cif: null, activ: true,
      }
    },
    editeazaAlerta(alerta) {
      this.alerta = { ...alerta }
    },
    salveazaAlerta() {
      this.eroareAlerte = ''

      const cerere = this.alerta.id
        ? this.$http.put(`/spv-alerte/${this.alerta.id}`, this.alerta)
        : this.$http.post('/spv-alerte', this.alerta)

      cerere
        .then(() => {
          this.alertaNoua()
          this.reincarcaAlerte()
        })
        .catch(err => {
          this.eroareAlerte = this.mesajEroareAlerte(err, 'Alerta nu a putut fi salvată')
        })
    },
    stergeAlerta(alerta) {
      this.$bvModal.msgBoxConfirm(`Ștergeți alerta către ${alerta.email}?`, {
        title: 'Ștergere alertă',
        okTitle: 'Șterge',
        cancelTitle: 'Renunță',
        okVariant: 'danger',
      }).then(confirmat => {
        if (!confirmat) return

        this.$http.delete(`/spv-alerte/${alerta.id}`)
          .then(() => {
            this.reincarcaAlerte()
          })
          .catch(err => {
            this.eroareAlerte = this.mesajEroareAlerte(err, 'Alerta nu a putut fi ștearsă')
          })
      })
    },
    reincarcaAlerte() {
      this.$http.get('/spv-alerte').then(({ data }) => {
        this.alerte = data.data
      })
    },
    numeFirma(cif) {
      if (!cif) return 'toate firmele înrolate'

      const societate = this.societati.find(alta => alta.cif === cif)

      return societate ? `${societate.denumire || ''} (${cif})` : cif
    },
    /** Firmele înrolate, pentru lista de la CIF și pentru fereastra de alerte. */
    incarcaSocietatiInrolate() {
      return this.$http.get('/anaf-societati', { params: { doar_active: 1 } })
        .then(({ data }) => {
          this.societati = data.data.map(societate => ({
            cif: societate.cif,
            denumire: societate.denumire,
            certificat_id: societate.certificat_id,
          }))
        })
        .catch(() => {
          this.societati = []
        })
    },
    mesajEroareAlerte(err, implicit) {
      return err.response && err.response.data && err.response.data.message
        ? err.response.data.message
        : implicit
    },
    // Fisierele se descarca automat la citirea listei; cand asta nu a reusit,
    // in locul butonului de deschidere se afiseaza motivul.
    statusFisier(item) {
      // Recipisele și răspunsurile se aduc din filele lor, nu de aici: altfel
      // ar rămâne veșnic „nedescărcate” și ar părea o defecțiune.
      if (item.fila_care_aduce) return `Se aduce din ${item.fila_care_aduce}`
      if (item.ultima_eroare) return `Descărcare eșuată: ${item.ultima_eroare}`

      return 'Nedescărcat — se preia la următoarea descărcare'
    },
    /** Ce a adus apăsarea, spus în cuvinte, chiar și când n-a adus nimic. */
    rezumatCitire(payload, descarcare) {
      const parti = []

      if (payload.noi) {
        parti.push(payload.noi === 1 ? 'un mesaj nou' : `${payload.noi} mesaje noi`)
      }

      const desprefisiere = this.rezumatDescarcare(descarcare)

      if (desprefisiere) parti.push(desprefisiere)

      if (parti.length) return `ANAF a răspuns: ${parti.join(', ')}.`

      const intoarse = payload.intoarse || 0

      return `ANAF a răspuns, dar nu e nimic nou: cele ${intoarse} mesaje din `
        + `ultimele ${this.zile} zile erau deja aduse.`
    },
    rezumatDescarcare(rezultat) {
      if (!rezultat) return ''

      const parti = []
      if (rezultat.descarcate) parti.push(`${rezultat.descarcate} fișiere descărcate`)
      if (rezultat.ramase) parti.push(`${rezultat.ramase} rămase pentru următoarea încărcare`)
      if (rezultat.erori && rezultat.erori.length) parti.push(`${rezultat.erori.length} eșuate`)

      return parti.join(', ')
    },
    // Mesajele SPV stau pe discul privat, deci se cer prin API si se deschid
    // dintr-un blob local, nu printr-un link direct catre /storage.
    openFile(id) {
      this.error = ''

      this.$http.get('/spv/fisier', { params: { id }, responseType: 'blob' })
        .then(response => {
          const url = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }))
          window.open(url, '_blank')
          setTimeout(() => window.URL.revokeObjectURL(url), 60000)
        })
        .catch(() => {
          this.error = 'Fișierul nu a putut fi deschis.'
        })
    },
    /**
     * Mesajele deja stocate. Nu întreabă ANAF: deschiderea filei și reîncărcarea
     * paginii n-au de ce să consume din limita de apeluri.
     */
    incarcaStocate() {
      this.error = ''

      return this.$http.get('/spv/stocate')
        .then(({ data }) => {
          this.messages = Array.isArray(data.data.mesaje) ? data.data.mesaje : []
        })
        .catch(err => {
          this.error = this.mesajEroareAlerte(err, 'Mesajele stocate nu au putut fi încărcate')
        })
    },
    /**
     * Aduce de la ANAF mesajele noi și documentele lipsă.
     *
     * @param {boolean} tacut pornit de cronometru: nu deranjează când n-a venit nimic
     */
    loadMessages(tacut = false) {
      this.loading = true
      this.error = ''

      const params = { zile: this.zile }

      // O singură firmă se poate cere direct de la ANAF; pentru mai multe,
      // interogarea merge pe toate și se filtrează la afișare.
      if (this.listaCif.length === 1) {
        [params.cif] = this.listaCif
      }

      return this.$http.get('/spv', { params })
        .then(response => {
          if (response.data && response.data.success) {
            const payload = response.data.data || {}
            this.messages = Array.isArray(payload.mesaje) ? payload.mesaje : []

            this.ultimaDescarcare = this.acum()
            this.ultimaDescarcareNumar = payload.noi || 0
            window.localStorage.setItem('mesaje_ultima_descarcare', JSON.stringify({
              la: this.ultimaDescarcare,
              noi: this.ultimaDescarcareNumar,
            }))

            /*
             * O apăsare trebuie să spună întotdeauna ce a găsit — și când n-a
             * găsit nimic. Altfel butonul pare că nu face nimic, iar omul crede
             * pe bună dreptate că e stricat.
             */
            this.infoDescarcare = tacut && !payload.noi
              ? ''
              : this.rezumatCitire(payload, response.data.descarcare)
          } else {
            this.error = response.data.message || 'Nu s-au putut încărca datele SPV'
          }
        })
        .catch(err => {
          this.error = err.response && err.response.data && err.response.data.message
            ? err.response.data.message
            : 'Eroare la conectarea la SPV'
        })
        .finally(() => {
          this.loading = false
        })
    },
    alegeToateFirmele() {
      this.firme = this.firme.length === this.societati.length ? [] : [...this.optiuniFirmeInrolate]
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
        const salvat = JSON.parse(window.localStorage.getItem('mesaje_descarcare_automat'))

        if (salvat && typeof salvat.minute === 'number' && salvat.minute > 0) {
          const permise = this.optiuniMinute.map(o => o.value)
          const minute = permise.indexOf(salvat.minute) !== -1
            ? salvat.minute
            : permise.reduce((a, b) => (Math.abs(b - salvat.minute) < Math.abs(a - salvat.minute) ? b : a))

          this.automat = { activ: !!salvat.activ, minute }
        }

        const ultima = JSON.parse(window.localStorage.getItem('mesaje_ultima_descarcare'))

        if (ultima) {
          this.ultimaDescarcare = ultima.la || ''
          this.ultimaDescarcareNumar = ultima.noi || 0
        }
      } catch (e) {
        // setare veche sau stricată — se rămâne pe valorile implicite
      }
    },
    salveazaSetarea() {
      window.localStorage.setItem('mesaje_descarcare_automat', JSON.stringify(this.automat))
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

      if (!this.automat.activ || !minute || minute < 1) return

      this.cronometru = window.setInterval(() => {
        if (!this.loading) this.loadMessages(true)
      }, minute * 60 * 1000)
    },
  },
}
</script>

<style lang="scss">
@import "@core/scss/vue/libs/vue-select.scss";

/* Lista de intervale: doar cat sa incapa "60 minute". */
.lista-minute {
  width: 6.5rem;
  height: 1.6rem;
  padding: 0 1.2rem 0 0.4rem;
  line-height: 1.2;
  background-position: right 0.35rem center;
}

.automat-mesaje small {
  font-size: 0.72rem;
}

/* Randul de filtre: casute scunde, ca sa nu ingroase antetul. */
.rand-filtre th {
  padding: 0.15rem 0.25rem;
}

.rand-filtre input,
.rand-filtre select {
  height: 1.7rem;
  padding: 0 0.35rem;
  font-size: 0.78rem;
}

/* Multe firme alese ar impinge randul in jos; lista se deruleaza in loc. */
.select-firme .vs__selected-options {
  max-height: 6rem;
  overflow-y: auto;
}
</style>
