<template>
  <div>
    <b-alert
      v-if="eroare"
      show
      variant="danger"
      class="py-2"
    >
      {{ eroare }}
    </b-alert>

    <b-card
      class="border mb-2"
      body-class="p-2"
    >
      <div class="d-flex align-items-center justify-content-between">
        <div>
          <h6 class="mb-25">
            Utilizatorii firmei
          </h6>
          <small class="text-muted">
            Un utilizator obișnuit vede doar declarațiile și solicitările depuse de el,
            plus mesajele din SPV ale certificatelor la care i-ați dat acces.
          </small>
        </div>
        <b-button
          variant="primary"
          size="sm"
          class="flex-shrink-0"
          @click="deschide(null)"
        >
          <feather-icon
            icon="UserPlusIcon"
            class="mr-50"
          />Utilizator nou
        </b-button>
      </div>
    </b-card>

    <b-card
      class="border mb-0"
      body-class="p-1"
    >
      <b-row class="mb-2">
        <b-col md="4">
          <b-form-input
            v-model="cautare"
            size="sm"
            placeholder="Caută după nume, email sau telefon"
          />
        </b-col>
        <b-col
          md="3"
          class="d-flex align-items-center"
        >
          <small
            v-if="cautare"
            class="text-muted"
          >
            {{ utilizatoriFiltrati.length }} din {{ utilizatori.length }}
          </small>
        </b-col>
      </b-row>

      <b-table
        :items="utilizatoriFiltrati"
        :fields="campuri"
        :busy="listaInCurs"
        responsive
        striped
        small
        show-empty
        empty-text="Niciun utilizator."
        class="tabel-compact mb-0"
      >
        <template #table-busy>
          <div class="text-center my-2">
            <b-spinner class="align-middle mr-1" />
            Se încarcă...
          </div>
        </template>

        <template #cell(persoana)="rand">
          <div class="d-flex align-items-center">
            <feather-icon
              v-if="rand.item.administrator"
              v-b-tooltip.hover
              icon="ShieldIcon"
              size="14"
              class="text-primary mr-50"
              title="Administrator: vede tot ce s-a lucrat pentru firmă"
            />
            <feather-icon
              v-if="!rand.item.administrator && rand.item.poate_semna"
              v-b-tooltip.hover
              icon="EditIcon"
              size="14"
              class="text-success mr-50"
              title="Poate semna declarațiile validate"
            />
            <feather-icon
              v-if="!rand.item.administrator && rand.item.poate_depune"
              v-b-tooltip.hover
              icon="SendIcon"
              size="14"
              class="text-warning mr-50"
              title="Poate depune declarațiile semnate"
            />
            <div>
              <div>{{ rand.item.nume }}</div>
              <div class="small text-muted">
                {{ rand.item.email }}
              </div>
            </div>
          </div>
        </template>

        <template #cell(certificate)="rand">
          <b-badge
            v-for="certificat in rand.item.certificate"
            :key="certificat.id"
            variant="light-primary"
            class="mr-25 mb-25"
          >
            {{ certificat.cn }}
          </b-badge>
          <span
            v-if="!rand.item.certificate.length"
            class="small text-muted"
          >
            niciun certificat — nu vede mesaje SPV
          </span>
        </template>

        <template #cell(imprimanta)="rand">
          <span v-if="rand.item.imprimanta">
            <feather-icon
              icon="PrinterIcon"
              size="13"
              class="text-muted mr-25"
            />{{ rand.item.imprimanta }}
          </span>
          <span
            v-else
            class="small text-muted"
          >nealeasă</span>
        </template>

        <template #cell(ip_permise)="rand">
          <span
            v-if="rand.item.ip_permise"
            class="small"
          >{{ rand.item.ip_permise }}</span>
          <span
            v-else
            class="small text-muted"
          >de oriunde</span>
        </template>

        <template #cell(stare)="rand">
          <b-badge :variant="rand.item.blocat ? 'light-danger' : 'light-success'">
            {{ rand.item.blocat ? 'blocat' : 'activ' }}
          </b-badge>
        </template>

        <template #cell(actiuni)="rand">
          <b-button
            size="sm"
            variant="outline-secondary"
            class="mr-1"
            @click="deschide(rand.item)"
          >
            Modifică
          </b-button>
          <b-button
            size="sm"
            variant="outline-warning"
            :disabled="rand.item.eu"
            :title="rand.item.eu ? 'Nu vă puteți deconecta pe dumneavoastră de aici' : 'Închide sesiunile deschise'"
            @click="deconecteaza(rand.item)"
          >
            Deconectează
          </b-button>
        </template>
      </b-table>
    </b-card>

    <b-modal
      v-model="formularVizibil"
      :title="formular.id ? 'Utilizatorul ' + formular.email : 'Utilizator nou'"
      ok-title="Salvează"
      cancel-title="Renunță"
      @ok.prevent="salveaza"
    >
      <b-alert
        v-if="eroareFormular"
        show
        variant="danger"
        class="py-1 px-2 small"
      >
        {{ eroareFormular }}
      </b-alert>

      <label>Nume</label>
      <b-form-input
        v-model="formular.nume"
        class="mb-2"
      />

      <label>Email (cu el se autentifică)</label>
      <b-form-input
        v-model="formular.email"
        type="email"
        class="mb-2"
      />

      <label>Telefon</label>
      <b-form-input
        v-model="formular.telefon"
        class="mb-2"
      />

      <label>{{ formular.id ? 'Parolă nouă (gol = neschimbată)' : 'Parolă (minimum 8 caractere)' }}</label>
      <!-- „new-password" ține browserul să nu completeze aici, din obișnuință,
           parola cu care e logat administratorul: ea ar deveni, la salvare,
           parola omului editat. -->
      <b-form-input
        v-model="formular.parola"
        type="text"
        autocomplete="new-password"
        class="mb-2"
      />

      <label>Certificate digitale la care are acces</label>
      <b-form-checkbox-group
        v-model="formular.certificate"
        stacked
        class="mb-1"
      >
        <b-form-checkbox
          v-for="certificat in certificate"
          :key="certificat.id"
          :value="certificat.id"
        >
          {{ certificat.cn }}
        </b-form-checkbox>
      </b-form-checkbox-group>
      <small class="text-muted d-block mb-2">
        Vede mesajele din SPV ale certificatelor bifate. Declarațiile și solicitările
        le vede doar pe ale lui, indiferent de certificat.
      </small>

      <hr>
      <label>Imprimanta pe care iese hârtia</label>
      <b-row no-gutters>
        <b-col cols="5">
          <b-form-select
            v-model="formular.imprimanta_certificat_id"
            :options="optiuniCalculatoare"
            class="mb-1"
            @change="incarcaImprimante"
          />
        </b-col>
        <b-col cols="7">
          <b-form-select
            v-model="formular.imprimanta"
            :options="optiuniImprimante"
            :disabled="!formular.imprimanta_certificat_id || imprimanteInCurs"
            class="mb-1"
          />
        </b-col>
      </b-row>
      <small
        v-if="eroareImprimante"
        class="text-danger d-block mb-2"
      >{{ eroareImprimante }}</small>
      <small
        v-else
        class="text-muted d-block mb-2"
      >
        Declarațiile și recipisele bifate pentru imprimare se trimit direct aici,
        pe calculatorul ales — nu se mai descarcă niciun fișier. Lista vine de la
        calculatorul acela, deci trebuie să fie pornit.
      </small>

      <hr>
      <label>Adrese IP de la care are voie să se conecteze</label>
      <b-form-textarea
        v-model="formular.ip_permise"
        rows="2"
        placeholder="Lăsat gol: de oriunde. Ex: 86.120.4.15, 192.168.1.0/24, 79.112.*"
        class="mb-1"
      />
      <div
        v-if="ipCurent"
        class="mb-1"
      >
        <small class="text-muted">
          Adresa de la care lucrați acum: <code>{{ ipCurent }}</code>
        </small>
        <b-button
          size="sm"
          variant="flat-primary"
          class="py-0 px-50 ml-50"
          @click="adaugaIpCurent"
        >
          <small>adaugă</small>
        </b-button>
      </div>
      <small class="text-muted d-block mb-2">
        Se scriu despărțite prin virgulă sau pe rânduri. Se acceptă o adresă
        întreagă, un interval (<code>/24</code>) sau un început de adresă
        (<code>*</code>). <strong>Gol înseamnă de oriunde.</strong>
        La o încercare de la altă adresă, contul e respins, iar administratorul
        aplicației primește un email cu cine și de unde a încercat.
      </small>

      <hr>
      <b-form-checkbox
        v-model="formular.administrator"
        class="mb-1"
      >
        Administrator: gestionează utilizatorii și vede tot ce s-a lucrat pentru firmă
      </b-form-checkbox>

      <b-form-checkbox
        v-model="formular.poate_semna"
        :disabled="formular.administrator"
        class="mb-1"
      >
        Poate semna declarațiile validate
      </b-form-checkbox>

      <b-form-checkbox
        v-model="formular.poate_depune"
        :disabled="formular.administrator"
        class="mb-1"
      >
        Poate depune declarațiile semnate la ANAF
      </b-form-checkbox>

      <small class="text-muted d-block mb-2">
        Administratorul firmei are oricum ambele drepturi.
        <strong>Depunerea nu se mai poate lua înapoi</strong>, așa că se dă anume.
      </small>

      <b-form-checkbox
        v-if="formular.id && !formular.eu"
        v-model="formular.blocat"
      >
        Blocat (nu se mai poate autentifica; sesiunile deschise se închid)
      </b-form-checkbox>
    </b-modal>
  </div>
</template>

<script>
/*
 * Utilizatorii din firma clientului, gestionați de administratorul firmei.
 * Serverul refuză rutele pentru ceilalți, dar fila nici nu li se arată.
 */
export default {
  name: 'Utilizatori',
  data() {
    return {
      utilizatori: [],
      certificate: [],
      cautare: '',
      ipCurent: '',
      listaInCurs: false,
      eroare: '',
      eroareFormular: '',
      formularVizibil: false,
      formular: {},
      imprimante: [],
      imprimanteInCurs: false,
      eroareImprimante: '',
      campuri: [
        { key: 'persoana', label: 'Utilizator' },
        { key: 'certificate', label: 'Certificate digitale' },
        { key: 'imprimanta', label: 'Imprimantă' },
        { key: 'ip_permise', label: 'IP permise' },
        { key: 'stare', label: 'Stare' },
        { key: 'actiuni', label: 'Acțiuni' },
      ],
    }
  },
  computed: {
    /** Utilizatorii care se potrivesc cu ce s-a scris in cautare. */
    utilizatoriFiltrati() {
      const cautat = this.cautare.trim().toLowerCase()

      if (!cautat) return this.utilizatori

      return this.utilizatori.filter(utilizator => [utilizator.nume, utilizator.email, utilizator.telefon]
        .some(valoare => String(valoare || '').toLowerCase().indexOf(cautat) !== -1))
    },
    /** Calculatoarele sunt cele ale certificatelor bifate pentru acest om. */
    optiuniCalculatoare() {
      const alese = this.formular.certificate || []

      return [{ value: null, text: 'fără imprimantă' }].concat(
        this.certificate
          .filter(certificat => alese.includes(certificat.id))
          .map(certificat => ({ value: certificat.id, text: certificat.cn })),
      )
    },
    optiuniImprimante() {
      if (this.imprimanteInCurs) return [{ value: null, text: 'se citesc...' }]

      return [{ value: null, text: 'alegeți imprimanta' }].concat(
        this.imprimante.map(imprimanta => ({
          value: imprimanta.nume,
          text: imprimanta.nume + (imprimanta.implicita ? ' (implicită pe acel calculator)' : ''),
          disabled: imprimanta.stare === 'Offline',
        })),
      )
    },
  },
  created() {
    this.incarca()

    // Adresa vazuta de server: ajuta la scrierea listei si arata din prima
    // daca in fata aplicatiei sta un proxy care ascunde adresa adevarata.
    this.$http.get('/context')
      .then(({ data }) => {
        this.ipCurent = data.data.ip_curent || ''
      })
      .catch(() => {
        this.ipCurent = ''
      })
  },
  methods: {
    incarca() {
      this.listaInCurs = true
      this.eroare = ''

      this.$http.get('/client/utilizatori')
        .then(({ data }) => {
          this.utilizatori = data.data
          this.certificate = data.certificate
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Utilizatorii nu au putut fi încărcați')
        })
        .finally(() => {
          this.listaInCurs = false
        })
    },
    /** Pune adresa de acum in lista, fara sa stearga ce era scris. */
    adaugaIpCurent() {
      const scris = (this.formular.ip_permise || '').trim()

      if (scris.indexOf(this.ipCurent) !== -1) return

      this.$set(this.formular, 'ip_permise', scris ? `${scris}, ${this.ipCurent}` : this.ipCurent)
    },
    deschide(utilizator) {
      this.eroareFormular = ''
      this.eroareImprimante = ''
      this.imprimante = []
      this.formular = utilizator
        ? { ...utilizator, parola: '', certificate: utilizator.certificate.map(c => c.id) }
        : {
          nume: '',
          email: '',
          telefon: '',
          parola: '',
          administrator: false,
          poate_semna: false,
          poate_depune: false,
          blocat: false,
          certificate: [],
          imprimanta: null,
          imprimanta_certificat_id: null,
          ip_permise: '',
        }
      this.formularVizibil = true

      if (this.formular.imprimanta_certificat_id) this.incarcaImprimante()
    },
    /**
     * Imprimantele vin de la calculatorul certificatului ales — trebuie să fie
     * pornit, altfel n-avem de unde ști ce imprimante are.
     */
    incarcaImprimante() {
      const certificat = this.formular.imprimanta_certificat_id

      this.imprimante = []
      this.eroareImprimante = ''

      if (!certificat) {
        this.formular.imprimanta = null

        return
      }

      this.imprimanteInCurs = true

      this.$http.get(`/anaf-certificate/${certificat}/imprimante`)
        .then(({ data }) => {
          this.imprimante = data.data
        })
        .catch(err => {
          this.eroareImprimante = this.mesajEroare(err, 'Imprimantele nu au putut fi citite')
        })
        .finally(() => {
          this.imprimanteInCurs = false
        })
    },
    salveaza() {
      this.eroareFormular = ''

      // Câmpul de parolă lăsat gol nu pleacă deloc: la modificare înseamnă
      // „parola rămâne cum era", nu „pune asta în loc".
      const trimise = { ...this.formular }

      if (!trimise.parola || !trimise.parola.trim()) delete trimise.parola

      const cerere = this.formular.id
        ? this.$http.put(`/client/utilizatori/${this.formular.id}`, trimise)
        : this.$http.post('/client/utilizatori', trimise)

      cerere
        .then(() => {
          this.formularVizibil = false
          this.incarca()
        })
        .catch(err => {
          this.eroareFormular = this.mesajEroare(err, 'Utilizatorul nu a putut fi salvat')
        })
    },
    deconecteaza(utilizator) {
      this.$bvModal.msgBoxConfirm(`Închideți acum sesiunile lui ${utilizator.email}?`, {
        title: 'Deconectare',
        okTitle: 'Deconectează',
        cancelTitle: 'Renunță',
        okVariant: 'warning',
      }).then(confirmat => {
        if (!confirmat) return

        this.$http.post(`/client/utilizatori/${utilizator.id}/deconectare`)
          .then(({ data }) => {
            this.$bvToast.toast(`${data.data.sesiuni} sesiuni închise.`, { title: 'Deconectat', variant: 'success' })
          })
          .catch(err => {
            this.eroare = this.mesajEroare(err, 'Deconectarea a eșuat')
          })
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
.tabel-compact ::v-deep th,
.tabel-compact ::v-deep td {
  padding: 0.4rem 0.5rem !important;
  vertical-align: middle;
  font-size: 0.85rem;
}
</style>
