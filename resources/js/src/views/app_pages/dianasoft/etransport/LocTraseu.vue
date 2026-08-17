<template>
  <b-card
    class="border mb-2"
    body-class="p-2"
  >
    <h6 class="mb-1">
      {{ titlu }}
      <small class="text-muted">{{ subtitlu }}</small>
    </h6>

    <!-- Punct de trecere a frontierei -->
    <b-form-select
      v-if="fel === 'ptf'"
      :value="loc.cod_ptf"
      :options="optiuni(ptf)"
      size="sm"
      :disabled="!editabila"
      @change="schimba('cod_ptf', $event)"
    />

    <!-- Birou vamal -->
    <b-form-select
      v-else-if="fel === 'birou_vamal'"
      :value="loc.cod_birou_vamal"
      :options="optiuni(birouri)"
      size="sm"
      :disabled="!editabila"
      @change="schimba('cod_birou_vamal', $event)"
    />

    <!-- Adresa -->
    <div v-else>
      <b-row>
        <b-col md="5">
          <label class="small mb-0">Județ*</label>
          <b-form-select
            :value="loc.cod_judet"
            :options="optiuni(judete)"
            size="sm"
            :disabled="!editabila"
            @change="schimba('cod_judet', $event)"
          />
        </b-col>
        <b-col md="7">
          <label class="small mb-0">Localitate*</label>
          <b-form-input
            :value="loc.localitate"
            size="sm"
            :disabled="!editabila"
            @input="schimba('localitate', $event)"
          />
        </b-col>
      </b-row>
      <b-row class="mt-1">
        <b-col md="7">
          <label class="small mb-0">Stradă*</label>
          <b-form-input
            :value="loc.strada"
            size="sm"
            :disabled="!editabila"
            @input="schimba('strada', $event)"
          />
        </b-col>
        <b-col md="2">
          <label class="small mb-0">Număr</label>
          <b-form-input
            :value="loc.numar"
            size="sm"
            :disabled="!editabila"
            @input="schimba('numar', $event)"
          />
        </b-col>
        <b-col md="3">
          <label class="small mb-0">Cod poștal</label>
          <b-form-input
            :value="loc.cod_postal"
            size="sm"
            :disabled="!editabila"
            @input="schimba('cod_postal', $event)"
          />
        </b-col>
      </b-row>
      <b-row class="mt-1">
        <b-col md="3">
          <label class="small mb-0">Bloc</label>
          <b-form-input
            :value="loc.bloc"
            size="sm"
            :disabled="!editabila"
            @input="schimba('bloc', $event)"
          />
        </b-col>
        <b-col md="3">
          <label class="small mb-0">Scară</label>
          <b-form-input
            :value="loc.scara"
            size="sm"
            :disabled="!editabila"
            @input="schimba('scara', $event)"
          />
        </b-col>
        <b-col md="6">
          <label class="small mb-0">Alte informații</label>
          <b-form-input
            :value="loc.alte_info"
            size="sm"
            :disabled="!editabila"
            @input="schimba('alte_info', $event)"
          />
        </b-col>
      </b-row>
    </div>
  </b-card>
</template>

<script>
/**
 * Un capat al traseului rutier: dupa operatiune, e un punct de trecere a
 * frontierei, un birou vamal sau o adresa cu judet si strada.
 */
export default {
  name: 'LocTraseu',
  props: {
    value: { type: Object, default: () => ({}) },
    titlu: { type: String, required: true },
    fel: { type: String, required: true },
    judete: { type: [Object, Array], default: () => ({}) },
    ptf: { type: [Object, Array], default: () => ({}) },
    birouri: { type: [Object, Array], default: () => ({}) },
    editabila: { type: Boolean, default: true },
  },
  computed: {
    loc() {
      return this.value || {}
    },
    subtitlu() {
      return {
        ptf: 'punct de trecere a frontierei',
        birou_vamal: 'birou vamal',
        adresa: 'adresă',
      }[this.fel] || ''
    },
  },
  methods: {
    optiuni(obiect) {
      return Object.keys(obiect || {}).map(cod => ({ value: Number(cod), text: obiect[cod] }))
    },
    schimba(camp, valoare) {
      this.$emit('input', { ...this.loc, tip: this.fel, [camp]: valoare })
    },
  },
}
</script>
