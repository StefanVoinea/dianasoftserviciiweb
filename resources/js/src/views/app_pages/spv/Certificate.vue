<template>
  <div>
    <b-row class="mb-2">
      <b-col
        md="6"
        class="mb-0"
      >
        <b-card
          class="h-100 border mb-0"
          body-class="p-2"
        >
          <h6 class="mb-2">
            Citește token-urile conectate
          </h6>

          <b-row no-gutters>
            <b-col
              cols="7"
              class="pr-1"
            >
              <b-form-input
                v-model="bridgeNou.bridge_url"
                size="sm"
                placeholder="Adresa calculatorului cu token-ul"
              />
            </b-col>
            <b-col cols="5">
              <b-form-input
                v-model="bridgeNou.bridge_token"
                size="sm"
                placeholder="Cod de acces"
              />
            </b-col>
          </b-row>

          <div class="d-flex align-items-center mt-2">
            <b-button
              variant="primary"
              size="sm"
              :disabled="sincronizareInCurs"
              @click="descopera"
            >
              <b-spinner
                v-if="sincronizareInCurs"
                small
                class="mr-1"
              />
              Citește
            </b-button>
            <small class="text-muted ml-2">
              Gol = calculatorul din configurație. Reapăsați după schimbarea token-ului.
            </small>
          </div>
        </b-card>
      </b-col>

      <b-col
        md="6"
        class="mb-0"
      >
        <b-card
          class="h-100 border mb-0"
          body-class="p-2"
        >
          <h6 class="mb-2">
            Calculator nou cu token?
          </h6>

          <small class="text-muted d-block mb-2">
            Rulați <code>instaleaza.ps1</code> din kit pe acel calculator: programul pornește
            apoi automat la fiecare autentificare. Fiecare kit are cod de acces propriu.
          </small>

          <b-button
            variant="outline-primary"
            size="sm"
            :disabled="kitInCurs"
            @click="descarcaKit"
          >
            <b-spinner
              v-if="kitInCurs"
              small
              class="mr-1"
            />
            Descarcă kitul de instalare a programului de acces la certificatul digital
          </b-button>
        </b-card>
      </b-col>
    </b-row>

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

    <b-card
      class="border mb-2"
      body-class="p-2"
    >
      <b-table
        :items="certificate"
        :fields="campuri"
        :busy="listaInCurs"
        responsive
        striped
        small
        class="mb-0"
        show-empty
        empty-text="Niciun certificat înregistrat. Apăsați „Citește token-urile conectate”."
      >
        <template #table-busy>
          <div class="text-center my-2">
            <b-spinner class="align-middle mr-1" />
            Se încarcă...
          </div>
        </template>

        <template #cell(titular)="rand">
          <div>{{ rand.item.cn || '-' }}</div>
          <div class="small text-muted">
            {{ rand.item.email || '' }}
          </div>
        </template>

        <template #cell(emitent)="rand">
          <div class="small">
            {{ scurt(rand.item.emitent) }}
          </div>
          <div class="small text-muted">
            serie {{ rand.item.serie || '-' }}
          </div>
        </template>

        <template #cell(bridge)="rand">
          <!-- câte o acțiune de fiecare parte a informațiilor despre calculator -->
          <div class="d-flex align-items-center">
            <!-- .window: tabelul are scroll orizontal, altfel tooltipul e
                 constrâns în el și ajunge peste buton -->
            <b-button
              v-b-tooltip.hover.top.window.v-light
              size="sm"
              variant="outline-secondary"
              class="btn-icon mr-2"
              title="Configurează calculatorul și codul de acces"
              @click="deschideBridge(rand.item)"
            >
              <feather-icon icon="SettingsIcon" />
            </b-button>

            <div>
              <div class="small">
                {{ rand.item.bridge_url }}
              </div>
              <!-- Unde ține acel calculator arhiva de documente -->
              <div
                v-if="rand.item.arhiva_cale"
                class="small text-muted"
              >
                <feather-icon
                  icon="FolderIcon"
                  size="12"
                  class="mr-25"
                />{{ rand.item.arhiva_cale }}
              </div>
              <!-- Dosarul din care își ia singur declarațiile de semnat -->
              <div
                v-if="rand.item.monitorizare_activa && rand.item.monitorizare_cale"
                class="small text-muted"
              >
                <feather-icon
                  icon="EyeIcon"
                  size="12"
                  class="mr-25"
                />{{ rand.item.monitorizare_cale }}
              </div>
              <!-- Licența programului local: se reînnoiește singură, dar se
                   vede, ca să nu surprindă pe nimeni când se apropie de capăt. -->
              <div
                v-if="rand.item.licenta_pana_la"
                class="small text-muted"
              >
                <feather-icon
                  icon="KeyIcon"
                  size="12"
                  class="mr-25"
                />licență până la {{ rand.item.licenta_pana_la }}
              </div>
              <b-badge
                v-if="rand.item.implicit"
                variant="primary"
              >
                implicit
              </b-badge>
              <b-badge
                v-if="certificatActiv === rand.item.id"
                variant="success"
              >
                activ acum
              </b-badge>
            </div>

            <b-button
              v-b-tooltip.hover.top.window.v-light
              size="sm"
              variant="outline-success"
              class="btn-icon ml-2"
              :disabled="certificatActiv === rand.item.id"
              title="Folosește acest certificat pentru operațiile mele"
              @click="alegeActiv(rand.item)"
            >
              <feather-icon icon="CheckCircleIcon" />
            </b-button>
          </div>
        </template>

        <template #cell(valabilitate)="rand">
          <div>{{ rand.item.valabil_pana_la || '-' }}</div>
          <b-badge :variant="variantaExpirare(rand.item)">
            {{ textExpirare(rand.item) }}
          </b-badge>
        </template>

        <template #cell(entitati)="rand">
          {{ rand.item.entitati }}
        </template>

        <template #cell(utilizatori)="rand">
          <!-- lista completă e în fereastra de gestionare; aici doar un rezumat -->
          <div
            v-if="rand.item.utilizatori.length"
            class="small"
          >
            {{ rand.item.utilizatori[0].email }}
            <span v-if="rand.item.utilizatori.length > 1">
              +{{ rand.item.utilizatori.length - 1 }}
            </span>
          </div>
          <span
            v-else
            class="small text-muted"
          >niciunul</span>
          <b-button
            size="sm"
            variant="outline-primary"
            class="mt-1"
            @click="deschideUtilizatori(rand.item)"
          >
            Gestionează
          </b-button>
        </template>

        <template #cell(avertizare)="rand">
          <span
            v-if="rand.item.avertizat_la"
            class="small"
          >
            trimisă la {{ rand.item.avertizat_la }}
          </span>
          <span
            v-else
            class="small text-muted"
          >—</span>
        </template>
      </b-table>
    </b-card>

    <b-card
      class="border mb-0"
      body-class="p-2"
    >
      <h6 class="mb-2">
        Avertizare expirare pe email
      </h6>

      <b-row
        no-gutters
        class="align-items-center mb-2"
      >
        <b-col
          md="4"
          class="pr-1"
        >
          <b-form-input
            v-model="emailNou"
            type="email"
            size="sm"
            placeholder="Adresă de email"
          />
        </b-col>
        <b-col
          md="4"
          class="pr-1"
        >
          <b-form-select
            v-model="certificatAles"
            :options="optiuniCertificat"
            size="sm"
          />
        </b-col>
        <b-col md="4">
          <b-button
            variant="primary"
            size="sm"
            :disabled="!emailNou"
            @click="aboneaza"
          >
            Înscrie
          </b-button>
          <small class="text-muted ml-2">
            Avertizare cu {{ zileAvertizare }} de zile înainte, repetată până la înlocuire.
          </small>
        </b-col>
      </b-row>

      <b-table
        :items="abonati"
        :fields="campuriAbonati"
        responsive
        striped
        small
        class="mb-0"
        show-empty
        empty-text="Nicio adresă înscrisă — avertizările de expirare nu vor fi trimise."
      >
        <template #cell(certificat_id)="rand">
          {{ numeCertificat(rand.item.certificat_id) }}
        </template>

        <template #cell(actiuni)="rand">
          <b-button
            size="sm"
            variant="outline-danger"
            @click="dezaboneaza(rand.item)"
          >
            Șterge
          </b-button>
        </template>
      </b-table>
    </b-card>

    <b-modal
      v-model="bridgeVizibil"
      :title="'Calculatorul certificatului ' + (bridgeFormular.cn || '')"
      ok-title="Salvează"
      cancel-title="Renunță"
      @ok="salveazaBridge"
    >
      <p class="text-muted small">
        Calculatorul din rețea pe care este conectat acest token. Lăsat gol, se folosește
        cel din configurația aplicației.
      </p>

      <label>Adresa calculatorului</label>
      <b-form-input
        v-model="bridgeFormular.bridge_url"
        placeholder="http://192.168.1.20:8099"
        class="mb-3"
      />

      <label>Cod de acces (gol = cel din configurație)</label>
      <b-form-input
        v-model="bridgeFormular.bridge_token"
        class="mb-3"
      />

      <label>Dosarul arhivei pe acel calculator</label>
      <b-input-group class="mb-1">
        <b-form-input
          v-model="bridgeFormular.arhiva_cale"
          placeholder="D:\Documente fiscale"
        />
        <b-input-group-append>
          <b-button
            variant="outline-secondary"
            @click="deschideFoldere('arhiva_cale')"
          >
            <feather-icon
              icon="FolderIcon"
              class="mr-50"
            />Alege...
          </b-button>
        </b-input-group-append>
      </b-input-group>
      <small class="text-muted d-block mb-3">
        Aici se strâng declarațiile semnate, recipisele și documentele aduse din SPV,
        pe firme și pe tipuri. Merge și un folder din rețea
        (<code>\\server\arhiva</code>). Lăsat gol, se folosește dosarul scris în
        <code>bridge.env</code> pe acel calculator.
      </small>

      <hr>
      <b-form-checkbox
        v-model="bridgeFormular.monitorizare_activa"
        class="mb-1"
      >
        Urmărește un dosar și prelucrează singur declarațiile puse acolo
      </b-form-checkbox>

      <b-input-group
        v-if="bridgeFormular.monitorizare_activa"
        class="mb-1"
      >
        <b-form-input
          v-model="bridgeFormular.monitorizare_cale"
          placeholder="D:\Declarații de semnat"
        />
        <b-input-group-append>
          <b-button
            variant="outline-secondary"
            @click="deschideFoldere('monitorizare_cale')"
          >
            <feather-icon
              icon="FolderIcon"
              class="mr-50"
            />Alege...
          </b-button>
        </b-input-group-append>
      </b-input-group>

      <small
        v-if="bridgeFormular.monitorizare_activa"
        class="text-muted d-block mb-3"
      >
        Din cinci în cinci minute, declarațiile puse acolo — XML sau PDF — se
        încarcă, se validează și se semnează singure, apoi trec în subdosarul
        <code>prelucrate</code>. Un PDF venit deja semnat nu se mai semnează încă
        o dată. Ce nu trece de validare ajunge în
        <code>erori</code>, iar utilizatorii atașați certificatului firmei
        primesc email cu motivul.
        <span
          v-if="bridgeFormular.monitorizare_la"
          class="d-block"
        >Ultima verificare: {{ bridgeFormular.monitorizare_la }}</span>
      </small>

      <b-form-checkbox v-model="bridgeFormular.implicit">
        Certificat implicit (folosit când utilizatorul nu are unul atribuit)
      </b-form-checkbox>
    </b-modal>

    <!-- Dosarele se citesc de pe calculatorul cu tokenul, nu de pe cel din fața
         omului: acolo se scrie arhiva. -->
    <b-modal
      v-model="foldereVizibil"
      title="Alegeți dosarul arhivei"
      ok-title="Alege acest dosar"
      cancel-title="Renunță"
      :ok-disabled="!folderCurent"
      @ok="alegeFolder"
    >
      <b-alert
        v-if="eroareFoldere"
        show
        variant="danger"
        class="py-1 px-2 small"
      >
        {{ eroareFoldere }}
      </b-alert>

      <div class="d-flex align-items-center mb-1">
        <b-button
          size="sm"
          variant="outline-secondary"
          class="btn-icon mr-1"
          :disabled="folderParinte === null"
          title="Un nivel mai sus"
          @click="rasfoieste(folderParinte)"
        >
          <feather-icon icon="CornerLeftUpIcon" />
        </b-button>
        <code class="small">{{ folderCurent || 'Acest calculator' }}</code>
        <b-spinner
          v-if="foldereInCurs"
          small
          class="ml-1"
        />
      </div>

      <b-list-group class="lista-foldere">
        <b-list-group-item
          v-for="folder in foldere"
          :key="folder.cale"
          button
          class="py-50 px-1 small"
          @click="rasfoieste(folder.cale)"
        >
          <feather-icon
            :icon="folderCurent ? 'FolderIcon' : 'HardDriveIcon'"
            size="14"
            class="mr-50 text-muted"
          />{{ folder.nume }}
        </b-list-group-item>
        <b-list-group-item
          v-if="!foldere.length && !foldereInCurs"
          class="py-50 px-1 small text-muted"
        >
          Niciun subdosar aici.
        </b-list-group-item>
      </b-list-group>
    </b-modal>

    <b-modal
      v-model="utilizatoriVizibil"
      :title="'Utilizatorii certificatului ' + (certificatCurent.cn || '')"
      size="lg"
      ok-only
      ok-title="Închide"
    >
      <p class="text-muted small">
        Persoanele din rețea care folosesc acest certificat. Adresa poate fi înscrisă chiar
        dacă nu are încă un cont în aplicație.
      </p>

      <b-row class="mb-3">
        <b-col md="5">
          <label>Adresă de email</label>
          <b-form-input
            v-model="utilizatorNou.email"
            type="email"
            placeholder="coleg@firma.ro"
          />
        </b-col>
        <b-col md="4">
          <label>Nume (opțional)</label>
          <b-form-input v-model="utilizatorNou.nume" />
        </b-col>
        <b-col
          md="3"
          class="d-flex align-items-end"
        >
          <b-button
            variant="primary"
            :disabled="!utilizatorNou.email"
            @click="ataseaza"
          >
            Atașează
          </b-button>
        </b-col>
      </b-row>

      <b-form-checkbox
        v-model="utilizatorNou.avertizare"
        class="mb-3"
      >
        Înscrie adresa și la avertizările de expirare
      </b-form-checkbox>

      <b-alert
        v-if="eroareModal"
        show
        variant="danger"
      >
        {{ eroareModal }}
      </b-alert>

      <b-table
        :items="certificatCurent.utilizatori || []"
        :fields="campuriUtilizatori"
        responsive
        striped
        small
        show-empty
        empty-text="Niciun utilizator atașat acestui certificat."
      >
        <template #cell(are_cont)="rand">
          <b-badge :variant="rand.item.are_cont ? 'success' : 'secondary'">
            {{ rand.item.are_cont ? 'cont în aplicație' : 'doar email' }}
          </b-badge>
        </template>

        <template #cell(actiuni)="rand">
          <b-button
            size="sm"
            variant="outline-danger"
            @click="detaseaza(rand.item)"
          >
            Elimină
          </b-button>
        </template>
      </b-table>
    </b-modal>
  </div>
</template>

<script>
export default {
  name: 'SpvCertificate',
  data() {
    return {
      certificate: [],
      abonati: [],
      zileAvertizare: 30,
      emailNou: '',
      certificatAles: null,
      eroare: '',
      listaInCurs: false,
      sincronizareInCurs: false,
      campuri: [
        { key: 'titular', label: 'Titular' },
        { key: 'emitent', label: 'Emitent' },
        { key: 'valabilitate', label: 'Valabil până la' },
        { key: 'bridge', label: 'Calculator / utilizare' },
        { key: 'entitati', label: 'Entități' },
        { key: 'utilizatori', label: 'Utilizatori' },
        { key: 'avertizare', label: 'Ultima avertizare' },
      ],
      campuriAbonati: [
        { key: 'email', label: 'Email' },
        { key: 'certificat_id', label: 'Certificat' },
        { key: 'actiuni', label: 'Acțiuni' },
      ],
      info: '',
      kitInCurs: false,
      bridgeNou: { bridge_url: '', bridge_token: '' },
      bridgeVizibil: false,
      bridgeFormular: {},
      foldereVizibil: false,
      campFoldere: 'arhiva_cale',
      foldereInCurs: false,
      foldere: [],
      folderCurent: '',
      folderParinte: null,
      eroareFoldere: '',
      certificatActiv: null,
      utilizatoriVizibil: false,
      certificatCurent: {},
      utilizatorNou: { email: '', nume: '', avertizare: false },
      eroareModal: '',
      campuriUtilizatori: [
        { key: 'email', label: 'Email' },
        { key: 'nume', label: 'Nume' },
        { key: 'are_cont', label: 'Stare' },
        { key: 'actiuni', label: 'Acțiuni' },
      ],
    }
  },
  computed: {
    optiuniCertificat() {
      return [{ value: null, text: 'Toate certificatele' }].concat(
        this.certificate.map(c => ({ value: c.id, text: `${c.cn} (expiră ${c.expira_la})` })),
      )
    },
  },
  created() {
    const salvat = window.localStorage.getItem('anaf_certificat_activ')
    if (salvat) {
      this.certificatActiv = Number(salvat)
    }
    this.incarcaLista()
  },
  methods: {
    scurt(text) {
      if (!text) return '-'
      const cn = text.match(/CN=([^,]+)/)
      return cn ? cn[1] : text
    },
    numeCertificat(id) {
      if (!id) return 'Toate'
      const certificat = this.certificate.find(c => c.id === id)
      return certificat ? certificat.cn : `#${id}`
    },
    textExpirare(certificat) {
      if (certificat.expirat) return 'expirat'
      if (certificat.zile_ramase === null) return 'necunoscut'
      return `${certificat.zile_ramase} zile rămase`
    },
    variantaExpirare(certificat) {
      if (certificat.expirat) return 'danger'
      if (certificat.zile_ramase !== null && certificat.zile_ramase <= this.zileAvertizare) return 'warning'
      return 'success'
    },
    incarcaLista() {
      this.listaInCurs = true

      this.$http.get('/anaf-certificate')
        .then(raspuns => {
          this.certificate = raspuns.data.data || []
          this.abonati = raspuns.data.abonati || []
          this.zileAvertizare = raspuns.data.zile_avertizare || 30
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Nu s-au putut încărca certificatele')
        })
        .finally(() => {
          this.listaInCurs = false
        })
    },
    /**
     * Kitul se cere prin API (cu token) și se salvează local; tokenul bridge-ului
     * generat pentru acel calculator se afișează pentru configurare.
     */
    descarcaKit() {
      this.eroare = ''
      this.info = ''
      this.kitInCurs = true

      this.$http.get('/anaf-certificate/kit', { responseType: 'blob' })
        .then(raspuns => {
          const url = window.URL.createObjectURL(new Blob([raspuns.data], { type: 'application/zip' }))
          const link = document.createElement('a')
          link.href = url
          link.download = 'kit-acces-token-anaf.zip'
          document.body.appendChild(link)
          link.click()
          document.body.removeChild(link)
          setTimeout(() => window.URL.revokeObjectURL(url), 60000)

          const token = raspuns.headers['x-bridge-token']
          this.info = token
            ? `Kit descărcat. Codul de acces pentru acel calculator: ${token}`
            : 'Kit descărcat.'
        })
        .catch(() => {
          this.eroare = 'Kitul nu a putut fi generat.'
        })
        .finally(() => {
          this.kitInCurs = false
        })
    },
    descopera() {
      this.eroare = ''
      this.info = ''
      this.sincronizareInCurs = true

      this.$http.post('/anaf-certificate/descopera', this.bridgeNou)
        .then(raspuns => {
          const gasite = raspuns.data.data || []
          const entitati = raspuns.data.entitati || {}

          const parti = [`${gasite.length} certificat(e) înregistrate`]
          if (entitati.total) parti.push(`${entitati.total} entități înrolate preluate`)
          this.info = `${parti.join(', ')}.`

          if (entitati.erori && entitati.erori.length) {
            this.eroare = entitati.erori.join(' | ')
          }

          this.incarcaLista()
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Certificatele nu au putut fi citite')
        })
        .finally(() => {
          this.sincronizareInCurs = false
        })
    },
    deschideBridge(certificat) {
      this.bridgeFormular = {
        id: certificat.id,
        cn: certificat.cn,
        bridge_url: certificat.bridge_implicit ? '' : certificat.bridge_url,
        bridge_token: '',
        arhiva_cale: certificat.arhiva_cale || '',
        monitorizare_cale: certificat.monitorizare_cale || '',
        monitorizare_activa: Boolean(certificat.monitorizare_activa),
        monitorizare_la: certificat.monitorizare_la,
        implicit: certificat.implicit,
      }
      this.bridgeVizibil = true
    },
    /**
     * Răsfoirea pornește din dosarul deja scris, dacă există.
     *
     * @param {string} camp câmpul în care se scrie dosarul ales
     */
    deschideFoldere(camp) {
      this.eroareFoldere = ''
      this.campFoldere = camp
      this.foldereVizibil = true
      this.rasfoieste(this.bridgeFormular[camp] || '')
    },
    /**
     * Cere dosarele calculatorului cu tokenul. Cale goală = discurile lui.
     */
    rasfoieste(cale) {
      if (cale === null) return

      this.foldereInCurs = true
      this.eroareFoldere = ''

      this.$http.get(`/anaf-certificate/${this.bridgeFormular.id}/foldere`, { params: { cale } })
        .then(({ data }) => {
          this.foldere = data.data.foldere
          this.folderCurent = data.data.cale
          this.folderParinte = data.data.parinte
        })
        .catch(err => {
          this.eroareFoldere = this.mesajEroare(err, 'Dosarele nu au putut fi citite')

          // La o cale greșită se rămâne totuși cu ceva de ales: discurile.
          if (cale !== '') this.rasfoieste('')
        })
        .finally(() => {
          this.foldereInCurs = false
        })
    },
    alegeFolder() {
      this.$set(this.bridgeFormular, this.campFoldere, this.folderCurent)
    },
    salveazaBridge() {
      this.eroare = ''

      this.$http.put(`/anaf-certificate/${this.bridgeFormular.id}`, {
        bridge_url: this.bridgeFormular.bridge_url || null,
        bridge_token: this.bridgeFormular.bridge_token || null,
        arhiva_cale: this.bridgeFormular.arhiva_cale || null,
        monitorizare_cale: this.bridgeFormular.monitorizare_cale || null,
        monitorizare_activa: this.bridgeFormular.monitorizare_activa,
        implicit: this.bridgeFormular.implicit,
      })
        .then(() => {
          this.incarcaLista()
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Configurarea nu a putut fi salvată')
        })
    },
    /**
     * Certificatul ales este trimis ca antet la toate cererile modulului, deci
     * operațiile merg pe bridge-ul calculatorului unde e tokenul respectiv.
     */
    alegeActiv(certificat) {
      this.certificatActiv = certificat.id
      this.$http.defaults.headers.common['X-Certificat-Id'] = certificat.id
      window.localStorage.setItem('anaf_certificat_activ', certificat.id)
      this.info = `Operațiile vor folosi certificatul „${certificat.cn}”.`
    },
    deschideUtilizatori(certificat) {
      this.certificatCurent = certificat
      this.utilizatorNou = { email: '', nume: '', avertizare: false }
      this.eroareModal = ''
      this.utilizatoriVizibil = true
    },
    ataseaza() {
      this.eroareModal = ''

      this.$http.post(`/anaf-certificate/${this.certificatCurent.id}/utilizatori`, this.utilizatorNou)
        .then(() => {
          this.utilizatorNou = { email: '', nume: '', avertizare: false }
          this.reincarcaSiActualizeazaModalul()
        })
        .catch(err => {
          this.eroareModal = this.mesajEroare(err, 'Utilizatorul nu a putut fi atașat')
        })
    },
    detaseaza(utilizator) {
      this.eroareModal = ''

      this.$http.delete(`/anaf-certificate/utilizatori/${utilizator.id}`)
        .then(() => {
          this.reincarcaSiActualizeazaModalul()
        })
        .catch(err => {
          this.eroareModal = this.mesajEroare(err, 'Utilizatorul nu a putut fi eliminat')
        })
    },
    // Modalul ramane deschis, dar trebuie sa arate lista actualizata.
    reincarcaSiActualizeazaModalul() {
      const { id } = this.certificatCurent

      this.$http.get('/anaf-certificate')
        .then(raspuns => {
          this.certificate = raspuns.data.data || []
          this.abonati = raspuns.data.abonati || []
          this.certificatCurent = this.certificate.find(c => c.id === id) || this.certificatCurent
        })
        .catch(err => {
          this.eroareModal = this.mesajEroare(err, 'Lista nu a putut fi reîncărcată')
        })
    },
    aboneaza() {
      this.eroare = ''

      this.$http.post('/anaf-certificate/abonare', {
        email: this.emailNou,
        certificat_id: this.certificatAles,
      })
        .then(() => {
          this.emailNou = ''
          this.incarcaLista()
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Adresa nu a putut fi înscrisă')
        })
    },
    dezaboneaza(abonat) {
      this.$http.delete(`/anaf-certificate/abonare/${abonat.id}`)
        .then(() => {
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
  },
}
</script>

<style scoped>
/* Lista de dosare: cat sa se vada cateva randuri, restul se deruleaza. */
.lista-foldere {
  max-height: 15rem;
  overflow-y: auto;
}
</style>
