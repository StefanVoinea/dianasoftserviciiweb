<template>
  <!-- Apare doar când nu încape totul pe o pagină -->
  <div
    v-if="total > pePagina"
    class="d-flex align-items-center justify-content-between mt-1"
  >
    <small class="text-muted text-nowrap">
      {{ deLaRand }}–{{ panaLaRand }} din {{ total }}
    </small>

    <b-pagination
      :value="value"
      :total-rows="total"
      :per-page="pePagina"
      size="sm"
      align="center"
      class="mb-0"
      @input="$emit('input', $event)"
    />

    <b-form-select
      v-model="alegerea"
      :options="marimi"
      size="sm"
      class="selector-pagina"
    />
  </div>
</template>

<script>
/**
 * Paginarea tabelelor, cu „cât încape pe ecran” ca alegere implicită.
 *
 * Se așază imediat sub tabelul pe care îl paginează și îl găsește singur, ca
 * să măsoare un rând adevărat: înălțimea depinde de temă, de zoom-ul din
 * browser și de ce scrie în rânduri, deci o valoare presupusă ar fi greșită
 * exact acolo unde contează.
 *
 * Folosire:
 *   <b-table :per-page="pePagina" :current-page="pagina" ... />
 *   <paginare v-model="pagina" :per-page.sync="pePagina" :total="lista.length" />
 */
export default {
  name: 'Paginare',
  model: { prop: 'value', event: 'input' },
  props: {
    /** Pagina curentă */
    value: { type: Number, default: 1 },
    /** Câte rânduri are lista întreagă */
    total: { type: Number, required: true },
    /** Câte rânduri se arată acum (se schimbă prin .sync) */
    perPage: { type: Number, default: 25 },
    /** Sub atâtea rânduri tabelul nu mai spune nimic; mai bine se derulează */
    minim: { type: Number, default: 5 },
  },
  data() {
    return {
      alegerea: 'auto',
      pePaginaAuto: 15,
      marimi: [
        { value: 'auto', text: 'cât încape' },
        { value: 10, text: '10 / pagină' },
        { value: 25, text: '25 / pagină' },
        { value: 50, text: '50 / pagină' },
        { value: 100, text: '100 / pagină' },
      ],
    }
  },
  computed: {
    pePagina() {
      return this.alegerea === 'auto' ? this.pePaginaAuto : this.alegerea
    },
    deLaRand() {
      return this.total ? (this.value - 1) * this.pePagina + 1 : 0
    },
    panaLaRand() {
      return Math.min(this.value * this.pePagina, this.total)
    },
  },
  watch: {
    pePagina: {
      immediate: true,
      handler(cate) {
        this.$emit('update:perPage', cate)
      },
    },
    alegerea() {
      this.$emit('input', 1)
    },
    // După filtrare sau reîncărcare, pagina veche poate să nu mai existe.
    total() {
      const ultima = Math.max(1, Math.ceil(this.total / this.pePagina))

      if (this.value > ultima) this.$emit('input', ultima)

      this.$nextTick(this.masoara)
    },
  },
  mounted() {
    this.masoara()
    window.addEventListener('resize', this.masoara)
  },
  beforeDestroy() {
    window.removeEventListener('resize', this.masoara)
  },
  methods: {
    /** Câte rânduri încap pe ecran, deasupra acestei bare. */
    masoara() {
      const tabel = this.$el && this.$el.parentElement
        ? this.$el.parentElement.querySelector('table')
        : null

      if (!tabel) return

      const rand = tabel.querySelector('tbody tr')
      const antet = tabel.querySelector('thead')

      const inaltimeRand = rand ? rand.getBoundingClientRect().height : 0
      const inaltimeAntet = antet ? antet.getBoundingClientRect().height : 0

      // Sub tabel mai stau bara aceasta și marginea de jos a paginii.
      const REZERVA = 70
      const disponibil = window.innerHeight
        - tabel.getBoundingClientRect().top
        - inaltimeAntet
        - REZERVA

      this.pePaginaAuto = Math.max(this.minim, Math.floor(disponibil / (inaltimeRand || 31)))
    },
  },
}
</script>

<style scoped>
/* Câte rânduri pe pagină: cât să încapă „100 / pagină”, nu mai mult. */
.selector-pagina {
  width: 8rem;
  height: 1.7rem;
  padding: 0 1.2rem 0 0.4rem;
  line-height: 1.2;
  background-position: right 0.35rem center;
}
</style>
