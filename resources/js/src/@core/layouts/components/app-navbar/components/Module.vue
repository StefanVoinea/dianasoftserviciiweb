<template>
  <ul class="nav navbar-nav module-antet">
    <li
      v-for="modul in vizibile"
      :key="modul.ruta"
      class="nav-item"
    >
      <b-link
        class="nav-link d-flex align-items-center"
        :class="{ activ: esteActiv(modul) }"
        :title="modul.descriere"
        @click="mergiLa(modul)"
      >
        <b-img
          v-if="modul.sigla"
          :src="modul.sigla"
          :alt="modul.nume"
          class="sigla-modul"
        />
        <!-- Administrarea n-are siglă: nu e un modul vândut, e unealta noastră.
             Pictograma stă în același chenar, ca rândul să rămână drept. -->
        <span
          v-else
          class="sigla-modul d-inline-flex align-items-center justify-content-center"
        >
          <feather-icon
            :icon="modul.pictograma"
            size="16"
          />
        </span>
        <span class="d-none d-xl-inline ml-50">{{ modul.nume }}</span>
      </b-link>
    </li>
  </ul>
</template>

<script>
/*
 * Modulele aplicației, în antet, cu sigla fiecăruia.
 *
 * Au luat locul denumirii firmei și al numelui aplicației cu versiunea: acolo
 * stătea o informație pe care omul o știe oricum, iar acum stă drumul spre
 * lucrul pe care vrea să-l facă.
 *
 * Se folosește varianta „simbol": la înălțimea unui antet, sigla orizontală ar
 * scrie numele modulului cu litere de câțiva pixeli, ilizibile. Numele stă
 * alături, ca text, și dispare pe ecrane înguste — sigla rămâne.
 */
import { BLink, BImg } from 'bootstrap-vue'

export default {
  components: { BLink, BImg },
  data() {
    return {
      module: [
        {
          nume: 'SPV Curier',
          ruta: 'spv',
          descriere: 'Declarații, mesaje și solicitări în Spațiul Privat Virtual',
          // eslint-disable-next-line global-require, import/no-unresolved
          sigla: require('@/assets/images/sigle/spv-curier-simbol.svg'),
        },
        {
          nume: 'Dispecer e-Transport',
          ruta: 'etransport',
          descriere: 'Declararea transporturilor și urmărirea UIT-urilor',
          // eslint-disable-next-line global-require, import/no-unresolved
          sigla: require('@/assets/images/sigle/dispecer-simbol.svg'),
        },
        {
          nume: 'Grefier alert',
          ruta: 'portal-just',
          descriere: 'Dosare, termene și monitorizare în Portal Just',
          // eslint-disable-next-line global-require, import/no-unresolved
          sigla: require('@/assets/images/sigle/grefier-simbol.svg'),
        },
      ],
      administrare: {
        nume: 'Administrare clienți',
        ruta: 'administrare',
        descriere: 'Firme, conturi, module, tarife și perioade de probă',
        pictograma: 'UsersIcon',
      },
      esteAdministrator: false,
    }
  },
  computed: {
    /** Administrarea apare doar la contul care are dreptul. */
    vizibile() {
      return this.esteAdministrator ? [...this.module, this.administrare] : this.module
    },
  },
  created() {
    // Serverul spune cine e; dreptul de administrare nu se ține în browser.
    this.$http.get('/context')
      .then(({ data }) => {
        this.esteAdministrator = Boolean(data.data.super_admin)
      })
      .catch(() => {
        this.esteAdministrator = false
      })
  },
  methods: {
    esteActiv(modul) {
      return this.$route.name === modul.ruta
    },
    /** Apăsat pe modulul în care ești deja, nu are rost să reîncarce pagina. */
    mergiLa(modul) {
      if (this.esteActiv(modul)) return

      this.$router.push({ name: modul.ruta }).catch(() => {})
    },
  },
}
</script>

<style lang="scss" scoped>
.module-antet {
  list-style: none;
  padding: 0;
  margin: 0;
}

.module-antet .nav-link {
  padding: 0.25rem 0.6rem;
  border-radius: 0.4rem;
  font-weight: 500;
  white-space: nowrap;
}

.module-antet .nav-link:hover {
  background: rgba(115, 103, 240, 0.08);
}

/* Modulul deschis se deosebește, ca să se vadă unde ești */
.module-antet .nav-link.activ {
  background: rgba(115, 103, 240, 0.12);
}

/*
 * Fiecare siglă în chenarul ei: siglele au fonduri diferite, iar fără chenar
 * se scurg una într-alta și par un singur desen lung.
 */
.sigla-modul {
  width: 26px;
  height: 26px;
  flex-shrink: 0;
  padding: 2px;
  background: #fff;
  border: 1px solid rgba(115, 103, 240, 0.35);
  border-radius: 0.35rem;
}

.module-antet .nav-link.activ .sigla-modul {
  border-color: #7367f0;
}

/* Pictograma administrării, în chenarul siglelor */
.module-antet .sigla-modul.d-inline-flex {
  color: #7367f0;
}
</style>
