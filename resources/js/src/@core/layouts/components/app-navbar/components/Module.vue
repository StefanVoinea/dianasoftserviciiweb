<template>
  <ul class="nav navbar-nav module-antet">
    <!-- Sigla-rădăcină: DianaSoft e casa, modulele de alături sunt produsele. -->
    <li class="nav-item">
      <b-link
        class="nav-link d-flex align-items-center"
        title="DianaSoft — Automatizări software și software la comandă"
        @click="mergiAcasa"
      >
        <b-img
          :src="radacina.lockup"
          alt="DianaSoft — Automatizări software și software la comandă"
          class="sigla-lockup d-none d-xl-block"
        />
        <b-img
          :src="radacina.semn"
          alt="DianaSoft"
          class="sigla-radacina-semn d-xl-none"
        />
      </b-link>
    </li>

    <!-- Bara verticală desparte casa de produsele ei -->
    <li
      class="separator-radacina"
      aria-hidden="true"
    />

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
        <!-- Sigla completă, unde modulul are una: ține loc și de semn, și de
             nume. Pe ecrane înguste n-ar încăpea, așa că rămâne doar semnul. -->
        <b-img
          v-if="modul.lockup"
          :src="modul.lockup"
          :alt="modul.nume"
          class="sigla-lockup d-none d-xl-block"
        />
        <b-img
          v-if="modul.sigla"
          :src="modul.sigla"
          :alt="modul.nume"
          class="sigla-modul"
          :class="{ 'd-xl-none': modul.lockup }"
        />
        <!-- Administrarea n-are siglă: nu e un modul vândut, e unealta noastră.
             Pictograma stă în același chenar, ca rândul să rămână drept. -->
        <span
          v-if="!modul.sigla"
          class="sigla-modul d-inline-flex align-items-center justify-content-center"
        >
          <feather-icon
            :icon="modul.pictograma"
            size="16"
          />
        </span>
        <span
          v-if="!modul.lockup"
          class="d-none d-xl-inline ml-50"
        >{{ modul.nume }}</span>
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
import { contextul, contextStiut } from '@/libs/module'

export default {
  components: { BLink, BImg },
  data() {
    return {
      radacina: {
        // eslint-disable-next-line global-require, import/no-unresolved
        lockup: require('@/assets/images/sigle/dianasoft-orizontal.svg'),
        // Sub 64px se foloseste semnul simplificat (regula setului de brand)
        // eslint-disable-next-line global-require, import/no-unresolved
        semn: require('@/assets/images/sigle/dianasoft-simbol.svg'),
      },
      module: [
        {
          cheie: 'spv',
          nume: 'SPV Curier',
          ruta: 'spv',
          descriere: 'Declarații, mesaje și solicitări în Spațiul Privat Virtual',
          // eslint-disable-next-line global-require, import/no-unresolved
          sigla: require('@/assets/images/sigle/spv-curier-simbol.svg'),
          // Sigla completă, arătată în locul semnului + numelui unde încape
          // eslint-disable-next-line global-require, import/no-unresolved
          lockup: require('@/assets/images/sigle/spv-curier-orizontal.svg'),
        },
        {
          cheie: 'etransport',
          nume: 'Dispecer e-Transport',
          ruta: 'etransport',
          descriere: 'Declararea transporturilor și urmărirea UIT-urilor',
          // eslint-disable-next-line global-require, import/no-unresolved
          sigla: require('@/assets/images/sigle/dispecer-simbol.svg'),
        },
        {
          cheie: 'portal_just',
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
      /*
       * Si dreptul de administrare se ia de la ce se stia ultima data: altfel,
       * o singura cerere picata ii stergea omului sigla de administrare pentru
       * toata sederea in pagina.
       */
      esteAdministrator: Boolean(contextStiut().super_admin),
      /*
       * Modulele pe care le vede contul acesta — cumpărate de firmă și date lui
       * de administratorul ei. Se pornește de la ce se știa de data trecută, ca
       * antetul să nu clipească la fiecare încărcare; „null" înseamnă că încă nu
       * se știe nimic.
       */
      alese: moduleStiute(),
    }
  },
  computed: {
    /**
     * Modulele date contului, plus administrarea la cine are dreptul.
     *
     * Cât timp nu se știe ce module are omul — prima intrare în aplicație, sau
     * un server care n-a răspuns — se arată toate. Ascunderea de aici e doar
     * înlesnire: cererile către un modul nedat sunt oprite oricum de server,
     * oricine le-ar trimite. Un antet golit dintr-o pană de rețea ar fi însă o
     * pagubă adevărată — omul ar crede că i-a dispărut aplicația.
     */
    vizibile() {
      const alelui = this.alese === null
        ? this.module
        : this.module.filter(modul => this.alese.indexOf(modul.cheie) !== -1)

      return this.esteAdministrator ? [...alelui, this.administrare] : alelui
    },
  },
  created() {
    /*
     * Serverul spune cine e; drepturile nu se țin în browser. Se folosește
     * același răspuns pe care îl cere și paznicul rutelor — o singură cerere
     * pe încărcare de pagină, nu una pentru fiecare care întreabă.
     */
    contextul()
      .then(context => {
        this.esteAdministrator = Boolean(context.super_admin)

        /*
         * Un server mai vechi nu trimite deloc câmpul „module". Atunci nu se
         * ascunde nimic: lipsa lui nu e o interdicție, e o necunoscută.
         */
        this.alese = Array.isArray(context.module) ? context.module : null
      })
      .catch(() => {
        /*
         * Nu se sterge nimic la pana: raman valorile de la ultima incarcare, iar
         * daca nici acelea nu sunt, modulele raman „necunoscute" — adica se
         * arata toate. Serverul opreste oricum ce nu e voie.
         */
        this.alese = null
      })
  },
  methods: {
    esteActiv(modul) {
      return this.$route.name === modul.ruta
    },
    mergiAcasa() {
      if (this.$route.name === 'home') return

      this.$router.push({ name: 'home' }).catch(() => {})
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

/*
 * Sigla completă nu stă în chenar: poartă singură și semnul, și numele.
 * 30px respectă minimul de brand (28px) și încape în bara de 3rem.
 */
.sigla-lockup {
  height: 30px;
  width: auto;
  flex-shrink: 0;
}

/* Semnul-rădăcină pe ecrane înguste: simplificat, fără chenar */
.sigla-radacina-semn {
  height: 26px;
  width: auto;
  flex-shrink: 0;
}

/* Bara verticală dintre sigla-rădăcină și module */
.separator-radacina {
  width: 1px;
  align-self: center;
  height: 26px;
  margin: 0 0.6rem;
  background: #dcd9d0;
  flex-shrink: 0;
}

/* Pictograma administrării, în chenarul siglelor */
.module-antet .sigla-modul.d-inline-flex {
  color: #7367f0;
}
</style>
