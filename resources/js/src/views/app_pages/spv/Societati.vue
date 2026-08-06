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

      <!-- Cât ține preluarea datelor, se vede la a câta firmă s-a ajuns: cu
           zeci de firme, lucrarea ține minute, iar un buton care se învârte
           mut nu spune nimănui dacă mai are rost să aștepte. -->
      <div
        v-if="progres"
        class="mb-3"
      >
        <div class="d-flex justify-content-between align-items-center mb-50">
          <small class="text-muted">{{ progres.text }}</small>
          <small
            v-if="progres.pas === 'solicitari'"
            class="text-muted"
          >
            {{ Math.round((progres.facut / Math.max(progres.total, 1)) * 100) }}%
          </small>
        </div>
        <b-progress
          :max="Math.max(progres.total, 1)"
          height="6px"
          :animated="progres.pas !== 'solicitari'"
        >
          <b-progress-bar
            :value="progres.pas === 'solicitari' ? progres.facut : Math.max(progres.total, 1)"
            :variant="progres.pas === 'date' ? 'success' : 'primary'"
          />
        </b-progress>
      </div>

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
            variant="primary"
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
      modal-class="modul-spv"
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
      /*
       * Unde s-a ajuns cu preluarea datelor: pasul, cât s-a făcut și textul de
       * sub bară. Gol înseamnă că nu se lucrează nimic acum.
       */
      progres: null,
      // Necazurile strânse pe drum; se arată toate la sfârșit, nu una câte una.
      erori: [],
      // Câte firme intră într-o cerere: fiecare apel la ANAF are pauza lui.
      FIRME_PE_LOT: 5,
      // Câte răspunsuri se citesc într-o cerere
      RASPUNSURI_PE_LOT: 10,
      // Câte reluări se fac cel mult, ca o listă lungă să nu învârtă la nesfârșit
      RUNDE_MAXIME: 40,
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
    /**
     * Aduce datele lipsă ale firmelor, în trei pași.
     *
     * Întâi pleacă solicitările către ANAF, firmă cu firmă; apoi se descarcă
     * mesajele nou intrate, în loturi, până nu mai rămâne niciunul; abia la
     * urmă se citesc din documentele descărcate denumirile și datele firmelor.
     *
     * Se lucrează în tranșe pentru că fiecare apel la ANAF are pauza lui
     * impusă: cu zeci de firme, totul într-o singură cerere web ar depăși orice
     * răbdare a serverului. Așa se vede și la a câta firmă s-a ajuns.
     */
    solicita() {
      this.eroare = ''
      this.info = ''
      this.solicitareInCurs = true
      this.erori = []

      const cifuri = this.societati.filter(s => s.activ).map(s => s.cif)

      this.progres = {
        pas: 'mesaje', facut: 0, total: 1, text: 'Se caută documentele venite deja...',
      }

      // Întâi se vede ce a intrat de curând: poate datele sunt deja acolo și
      // n-are rost să se mai ceară o dată de la ANAF.
      this.ceEDeja()
        .then(() => this.trimiteSolicitari(cifuri))
        .then(rezumat => this.descarcaMesajeNoi(rezumat))
        .then(rezumat => this.preiaRaspunsurile(rezumat))
        .then(rezumat => {
          const parti = []
          if (rezumat.trimise) parti.push(`${rezumat.trimise} solicitări trimise`)
          if (rezumat.descarcate) parti.push(`${rezumat.descarcate} mesaje descărcate`)
          if (rezumat.preluate) parti.push(`${rezumat.preluate} răspunsuri citite`)
          if (rezumat.reinterpretate) parti.push(`${rezumat.reinterpretate} documente reinterpretate`)
          if (rezumat.sarite) parti.push(`${rezumat.sarite} sărite (deja cerute azi sau persoane fizice)`)

          this.info = parti.length ? parti.join(', ') : 'Nu era nimic de solicitat.'

          if (this.erori.length) this.eroare = this.erori.join(' | ')

          this.incarcaLista()
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Solicitarea a eșuat')
        })
        .finally(() => {
          this.solicitareInCurs = false
          this.progres = null
        })
    },

    /**
     * Pasul dinaintea tuturor: ce a intrat deja în SPV.
     *
     * Se aduc mesajele din ultima zi și se citesc răspunsurile venite la
     * solicitările vechi. Documentul cerut ieri poate fi deja acolo, iar firma
     * ale cărei date se află astfel nu mai intră în lista de solicitat — nici
     * ANAF nu are de ce să răspundă a doua oară la aceeași întrebare.
     */
    ceEDeja() {
      return this.$http.get('/spv', { params: { zile: 1, descarca: 1 } })
        .then(raspuns => {
          const d = raspuns.data.descarcare || {}
          if (d.erori && d.erori.length) this.erori.push(...d.erori)

          this.progres = {
            pas: 'mesaje', facut: 1, total: 1, text: 'Se citesc documentele venite deja...',
          }

          return this.$http.post(`/spv/solicitari/preia?zile=1&limita=${this.RASPUNSURI_PE_LOT}`)
        })
        .then(() => this.incarcaLista())
        .catch(err => {
          // Un pas de aflare nu are voie să oprească lucrarea: dacă n-a mers,
          // se merge mai departe și se cer datele de la ANAF, ca înainte.
          this.erori.push(this.mesajEroare(err, 'Documentele venite deja nu au putut fi citite'))
        })
    },

    /** Pasul întâi: solicitările, în tranșe de câteva firme. */
    trimiteSolicitari(cifuri) {
      const rezumat = {
        trimise: 0, sarite: 0, reinterpretate: 0, descarcate: 0, preluate: 0,
      }

      const lot = i => {
        if (i >= cifuri.length) return Promise.resolve(rezumat)

        const acum = cifuri.slice(i, i + this.FIRME_PE_LOT)

        this.progres = {
          pas: 'solicitari',
          facut: i,
          total: cifuri.length,
          text: `Se trimit solicitările: ${i} din ${cifuri.length} firme`,
        }

        // Documentele vechi se recitesc o singură dată, la primul lot.
        return this.$http.post('/anaf-societati/solicita', { cif: acum, reinterpreteaza: i === 0 })
          .then(raspuns => {
            const r = raspuns.data.data
            rezumat.trimise += r.trimise || 0
            rezumat.sarite += r.sarite || 0
            rezumat.reinterpretate += r.reinterpretate || 0
            if (r.erori && r.erori.length) this.erori.push(...r.erori)

            return lot(i + this.FIRME_PE_LOT)
          })
      }

      return lot(0)
    },

    /**
     * Pasul al doilea: mesajele nou intrate în SPV.
     *
     * Serverul aduce fișierele în loturi și spune câte au mai rămas; se cere
     * din nou până nu mai rămâne niciunul.
     */
    descarcaMesajeNoi(rezumatul) {
      const rezumat = { ...rezumatul }

      const runda = trecute => {
        this.progres = {
          pas: 'mesaje',
          facut: rezumat.descarcate,
          total: rezumat.descarcate + 1,
          text: `Se descarcă mesajele noi: ${rezumat.descarcate} aduse`,
        }

        return this.$http.get('/spv', { params: { zile: 60, descarca: 1 } })
          .then(raspuns => {
            const d = raspuns.data.descarcare || {}
            rezumat.descarcate += d.descarcate || 0

            if (d.erori && d.erori.length) this.erori.push(...d.erori)

            // Se oprește când nu mai rămâne nimic sau când o rundă n-a mai adus nimic.
            if (!d.ramase || !d.descarcate || trecute >= this.RUNDE_MAXIME) {
              return rezumat
            }

            return runda(trecute + 1)
          })
      }

      return runda(0)
    },

    /** Pasul al treilea: din documentele descărcate se iau denumirile și datele. */
    preiaRaspunsurile(rezumatul) {
      const rezumat = { ...rezumatul }

      const runda = trecute => {
        this.progres = {
          pas: 'date',
          facut: rezumat.preluate,
          total: rezumat.preluate + 1,
          text: `Se citesc datele firmelor: ${rezumat.preluate} răspunsuri prelucrate`,
        }

        return this.$http.post(`/spv/solicitari/preia?limita=${this.RASPUNSURI_PE_LOT}`)
          .then(raspuns => {
            const r = raspuns.data.data || {}
            rezumat.preluate += r.preluate || 0

            if (r.erori && r.erori.length) this.erori.push(...r.erori)

            // Cele fără răspuns încă nu se mai așteaptă: vin la o rulare viitoare.
            if (!r.preluate || trecute >= this.RUNDE_MAXIME) {
              return rezumat
            }

            return runda(trecute + 1)
          })
      }

      return runda(0)
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
