<template>
  <b-modal
    v-model="vizibil"
    :title="'Tokenul „' + (tokenul.cn || '') + '” își așteaptă PIN-ul'"
    ok-title="Trimite codul"
    cancel-title="Îl scriu acolo"
    ok-variant="primary"
    modal-class="modul-spv"
    :ok-disabled="!pin || inCurs"
    @ok.prevent="trimite"
    @hidden="uita"
  >
    <p
      v-if="tokenul.din_fundal"
      class="mb-2"
    >
      <!-- Nu el a cerut lucrarea; e drept să afle de unde vine. -->
      O lucrare pornită de la sine s-a oprit: pe calculatorul clientului stă
      deschisă o fereastră care cere PIN-ul tokenului.
    </p>
    <p
      v-else
      class="mb-2"
    >
      Pe calculatorul clientului stă deschisă o fereastră care cere PIN-ul
      tokenului. Până nu e scris acolo, lucrarea nu poate merge mai departe.
    </p>

    <p
      v-if="tokenul.fereastra"
      class="text-muted small mb-2"
    >
      Fereastra: <code>{{ tokenul.fereastra }}</code>
      <span v-if="tokenul.de_cand"> · {{ tokenul.de_cand }}</span>
    </p>

    <label>PIN-ul tokenului</label>
    <b-form-input
      ref="camp"
      v-model="pin"
      type="password"
      autocomplete="off"
      :disabled="inCurs"
      placeholder="codul se scrie o singură dată"
      @keyup.enter="trimite"
    />

    <!-- Se spune limpede ce se întâmplă cu codul: cine îl scrie aici are
         dreptul să știe pe unde trece și cât rămâne. -->
    <small class="d-block text-muted mt-1">
      Codul pleacă o singură dată, prin cererea aceasta, până la programul local
      de pe calculatorul clientului, care îl scrie în fereastra deschisă. Nu se
      păstrează nicăieri — nici aici, nici pe server — și nu intră în jurnal.
    </small>

    <b-alert
      :show="Boolean(eroare)"
      variant="danger"
      class="mt-2 mb-0 p-1 small"
    >
      {{ eroare }}
    </b-alert>
  </b-modal>
</template>

<script>
/*
 * Cererea PIN-ului pentru tokenul care îl așteaptă.
 *
 * Când o lucrare se oprește fiindcă tokenul cere PIN-ul, fereastra aceea stă
 * deschisă pe calculatorul clientului — adesea pe alt ecran decât al omului
 * care a apăsat butonul, uneori în alt oraș. Până acum lucrarea pica, iar din
 * aplicație pana semăna cu una de rețea.
 *
 * Se cere numai pentru tokenele la care omul a pornit anume facilitatea, din
 * fila Certificate. Pentru celelalte, aplicația spune doar care token așteaptă,
 * iar codul se scrie de mână, acolo.
 *
 * Și numai pentru lucrările pornite de aici: cine a apăsat butonul pe telefon e
 * întrebat pe telefon. Fac excepție lucrările pornite de la sine — dosarul
 * urmărit, sarcina de noapte —, care n-au pe nimeni în spate și se arată
 * oriunde, fiindcă oricine e prin preajmă le poate dezlega.
 */
import {
  BModal, BFormInput, BAlert,
} from 'bootstrap-vue'

/** Din cât în cât se întreabă dacă vreun token își așteaptă codul. */
const LA_CATE_SECUNDE = 20

export default {
  components: { BModal, BFormInput, BAlert },
  data() {
    return {
      tokenul: {},
      pin: '',
      vizibil: false,
      inCurs: false,
      eroare: '',
      ceasul: null,
    }
  },
  created() {
    this.intreaba()
    this.ceasul = setInterval(this.intreaba, LA_CATE_SECUNDE * 1000)
  },
  beforeDestroy() {
    if (this.ceasul) clearInterval(this.ceasul)
  },
  methods: {
    /** Tokenurile care își așteaptă codul acum și pentru care e voie să-l trimitem. */
    intreaba() {
      // Fila ascunsă nu are cui să arate fereastra; se întreabă când revine.
      if (document.hidden || this.vizibil || this.inCurs) return null

      return this.$http.get('/anaf-certificate/pin/asteptare')
        .then(({ data }) => {
          const asteapta = (data.data || [])[0]

          if (!asteapta) return

          this.tokenul = asteapta
          this.pin = ''
          this.eroare = ''
          this.vizibil = true
        })
        .catch(() => {
          // O întrebare picată nu are de ce să tulbure fila.
        })
    },
    trimite() {
      if (!this.pin || this.inCurs) return null

      this.inCurs = true
      this.eroare = ''

      return this.$http.post(`/anaf-certificate/${this.tokenul.id}/pin`, { pin: this.pin })
        .then(() => {
          this.vizibil = false
          this.uita()
        })
        .catch(err => {
          this.eroare = (err.response && err.response.data && err.response.data.message)
            || 'Codul nu a putut fi trimis.'
        })
        .finally(() => {
          this.inCurs = false
        })
    },
    /** Codul nu rămâne în filă nici după ce fereastra s-a închis. */
    uita() {
      this.pin = ''
    },
  },
}
</script>
