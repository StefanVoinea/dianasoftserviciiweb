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
        <b-col md="6">
          <!-- Situația unei luni: ce declarații reies din vectorul fiscal al
               fiecărei entități și ce s-a depus pentru ele. -->
          <b-button
            variant="outline-primary"
            class="mr-1"
            @click="deschideVector"
          >
            <feather-icon
              icon="FileTextIcon"
              size="14"
              class="mr-50"
            />
            Raport depunere declarații
          </b-button>
        </b-col>
      </b-row>

      <b-row class="mb-2">
        <b-col class="d-flex align-items-center">
          <!-- Implicit se arata doar cele in lucru: ele sunt firmele clientului.
               Cele scoase din uz si cele fara drepturi se cer anume. -->
          <b-form-checkbox
            v-model="arataToate"
            @change="incarcaLista"
          >
            Arată și cele scoase din uz sau fără drepturi
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

        <!--
          Starea e buton, nu doar insignă: un client are adesea în certificat
          firme pe care nu le mai ține, iar ele încărcau degeaba fiecare
          interogare și fiecare listă de ales.

          „Fără drepturi" nu se apasă: acolo vorbește ANAF, nu noi — entitatea
          se întoarce singură când drepturile revin.
        -->
        <template #cell(activ)="rand">
          <b-button
            v-if="rand.item.activ"
            size="sm"
            :variant="rand.item.scos_din_uz ? 'outline-secondary' : 'success'"
            :disabled="schimbaStarea === rand.item.id"
            :title="rand.item.scos_din_uz
              ? 'Este ignorată peste tot. Apăsați ca să fie iar luată în seamă.'
              : 'Se lucrează cu ea. Apăsați ca să fie ignorată peste tot.'"
            @click="schimbaUzul(rand.item)"
          >
            <b-spinner
              v-if="schimbaStarea === rand.item.id"
              small
              class="mr-50"
            />
            {{ rand.item.scos_din_uz ? 'Scoasă din uz' : 'În lucru' }}
          </b-button>

          <b-badge
            v-else
            variant="secondary"
            title="ANAF nu mai dă drepturi pe această entitate acestui certificat"
          >
            Fără drepturi
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
          <!-- Doar pictograme: numele și explicația stau în tooltip -->
          <b-button
            size="sm"
            variant="outline-primary"
            class="mr-50"
            title="Redenumește — schimbă denumirea entității. Denumirea scrisă de mână are prioritate față de cea preluată din documentele SPV."
            @click="editeaza(rand.item)"
          >
            <feather-icon
              icon="Edit2Icon"
              size="14"
            />
          </b-button>
          <!-- Obligațiile în vigoare, așa cum le-a scris ANAF în vectorul fiscal -->
          <b-button
            size="sm"
            variant="outline-primary"
            class="mr-50"
            title="Vector fiscal — taxele în vigoare ale entității, așa cum le-a scris ANAF: cod, periodicitate și data intrării în vigoare. Doar de citit."
            @click="deschideVectorFiscal(rand.item)"
          >
            <feather-icon
              icon="FileTextIcon"
              size="14"
            />
          </b-button>
          <!-- Declarațiile așteptate ale acestei entități: deduse + scrise de om -->
          <b-button
            size="sm"
            variant="outline-primary"
            class="mr-50"
            title="Actualizare frecvență declarații — declarațiile așteptate de la entitate: cele deduse din vectorul fiscal și din istoricul depunerilor, plus cele adăugate de dvs. Se pot adăuga, modifica și șterge — ele intră în raportul de depunere."
            @click="deschideActualizare(rand.item)"
          >
            <feather-icon
              icon="CalendarIcon"
              size="14"
            />
          </b-button>
          <!-- Ce nu se află în fișierele ANAF și trebuie scris o dată: adresa,
               banca, contul, cine semnează. De aici se ia antetul declarațiilor. -->
          <b-button
            size="sm"
            variant="outline-primary"
            title="Date pentru declarații — adresa, banca, contul, codul CAEN și cine semnează. Nu se află în fișierele de la ANAF, se scriu o dată aici și se iau de aici la fiecare declarație întocmită."
            @click="deschideDateleDeclaratiilor(rand.item)"
          >
            <feather-icon
              icon="ClipboardIcon"
              size="14"
            />
          </b-button>
        </template>
      </b-table>

      <paginare
        v-model="pagina"
        :per-page.sync="pePagina"
        :total="societatiFiltrate.length"
      />
    </b-card>

    <!--
      Vectorul fiscal al unei luni: se alege luna raportată și forma dorită,
      iar fișierul vine gata întocmit — PDF pe hârtia cunoscută sau Excel cu
      aceleași rânduri și coloane.
    -->
    <b-modal
      v-model="vectorVizibil"
      title="Raport depunere declarații"
      modal-class="modul-spv"
      :ok-title="vectorInCurs ? 'Se întocmește...' : 'Descarcă'"
      cancel-title="Renunță"
      :ok-disabled="vectorInCurs"
      @ok.prevent="descarcaVector"
    >
      <p class="text-muted small">
        Din vectorul fiscal al fiecărei entități se deduc declarațiile lunii alese.
        Pentru cele depuse se arată indexul recipisei cu data și ora depunerii;
        pentru celelalte, periodicitatea obligației și atenționare dacă depunerea
        era datorată chiar în luna aleasă.
      </p>

      <b-form-group label="Luna raportată">
        <b-row>
          <b-col cols="7">
            <b-form-select
              v-model="vector.luna"
              :options="lunileAnului"
            />
          </b-col>
          <b-col cols="5">
            <b-form-select
              v-model="vector.anul"
              :options="aniiDeAles"
            />
          </b-col>
        </b-row>
      </b-form-group>

      <b-form-group label="Formatul dorit">
        <b-form-radio-group
          v-model="vector.format"
          :options="[
            { value: 'pdf', text: 'PDF' },
            { value: 'excel', text: 'Excel' },
          ]"
        />
      </b-form-group>

      <b-alert
        v-if="vectorEroare"
        show
        variant="danger"
        class="py-1 px-2 mb-0"
      >
        {{ vectorEroare }}
      </b-alert>
    </b-modal>

    <!--
      Vectorul fiscal, așa cum l-a scris ANAF: obligațiile (taxele) în vigoare
      ale entității, cu periodicitatea și data intrării în vigoare. Aici nu se
      modifică nimic — e ce spune ANAF; ce așteaptă aplicația se îndreaptă din
      „Actualizare frecvență declarații".
    -->
    <b-modal
      v-model="vfVizibil"
      :title="`Vector fiscal — ${etichetaEntitate(vfCui)}`"
      modal-class="modul-spv"
      size="lg"
      ok-only
      ok-title="Închide"
    >
      <b-alert
        v-if="vfEroare"
        show
        variant="danger"
        class="py-1 px-2"
      >
        {{ vfEroare }}
      </b-alert>

      <b-form-checkbox
        v-model="vfArataIstoricul"
        class="mb-1"
      >
        Arată și obligațiile încheiate
      </b-form-checkbox>

      <b-table
        :items="vfRanduriFiltrate"
        :fields="[
          { key: 'cod_imp', label: 'Cod' },
          { key: 'semnificatie', label: 'Taxa' },
          { key: 'perfisc', label: 'Periodicitate' },
          { key: 'data_inceput', label: 'În vigoare de la' },
          { key: 'data_sfarsit', label: 'Până la' },
        ]"
        :busy="vfInCurs"
        responsive
        striped
        small
        show-empty
        empty-text="Vectorul fiscal nu a fost încă preluat din SPV — apăsați „Solicită datele lipsă din SPV”."
      >
        <template #table-busy>
          <div class="text-center my-2">
            <b-spinner class="align-middle mr-1" />
            Se încarcă...
          </div>
        </template>

        <template #cell(data_sfarsit)="rand">
          <span v-if="rand.item.data_sfarsit">{{ rand.item.data_sfarsit }}</span>
          <b-badge
            v-else
            variant="light-primary"
          >
            în vigoare
          </b-badge>
        </template>
      </b-table>

      <p
        v-if="vfDataVector"
        class="small text-muted mb-0"
      >
        Vector fiscal extras din SPV la {{ vfDataVector }}.
      </p>
    </b-modal>

    <!--
      Actualizarea frecvenței declarațiilor: cele așteptate pe fiecare CUI.

      Rândurile „dedusă" le scrie aplicația din vectorul SPV și din istoricul
      depunerilor, la fiecare întocmire a raportului lunar. Omul adaugă aici ce
      nu se poate deduce — Bilanț semestrial, de pildă — cu periodicitatea și
      valabilitatea lui; pe același tip, rândul manual bate deducția.
    -->
    <b-modal
      v-model="actualizareVizibila"
      :title="`Actualizare frecvență declarații — ${actualizareDenumire}`"
      modal-class="modul-spv"
      size="xl"
      ok-only
      ok-title="Închide"
    >
      <!-- Adăugarea: tipul, periodicitatea și valabilitatea. Entitatea e cea
           de pe rândul al cărei buton a deschis fereastra. -->
      <b-row class="mb-1 align-items-end">
        <b-col md="3">
          <label class="small mb-0">Tip declarație</label>
          <b-form-input
            v-model="noua.tip"
            size="sm"
            list="tipuri-declaratii"
            placeholder="D300, BILANT..."
          />
          <datalist id="tipuri-declaratii">
            <option
              v-for="tip in tipuriCunoscute"
              :key="tip"
            >{{ tip }}</option>
          </datalist>
        </b-col>
        <b-col md="3">
          <label class="small mb-0">Periodicitate</label>
          <b-form-select
            v-model="noua.perfisc"
            :options="periodicitati"
            size="sm"
          />
        </b-col>
        <b-col md="2">
          <label class="small mb-0">Valabilă de la</label>
          <b-form-input
            v-model="noua.data_inceput"
            type="date"
            size="sm"
          />
        </b-col>
        <b-col md="2">
          <label class="small mb-0">până la</label>
          <b-form-input
            v-model="noua.data_sfarsit"
            type="date"
            size="sm"
          />
        </b-col>
        <b-col md="2">
          <b-button
            variant="primary"
            size="sm"
            block
            :disabled="!noua.tip || actualizareInCurs"
            @click="adaugaDeclaratie"
          >
            Adaugă
          </b-button>
        </b-col>
      </b-row>

      <b-alert
        v-if="actualizareEroare"
        show
        variant="danger"
        class="py-1 px-2"
      >
        {{ actualizareEroare }}
      </b-alert>

      <b-table
        :items="declaratiiFiltrate"
        :fields="campuriDeclaratii"
        :busy="actualizareInCurs"
        responsive
        striped
        small
        show-empty
        empty-text="Nicio declarație — se completează la prima întocmire a raportului lunar sau manual, de mai sus."
      >
        <template #table-busy>
          <div class="text-center my-2">
            <b-spinner class="align-middle mr-1" />
            Se încarcă...
          </div>
        </template>

        <!-- Rândul în lucru își arată câmpurile de scris chiar în tabel:
             și cele deduse se pot îndrepta — îndreptate, devin manuale,
             ca următoarea deducție să nu le rescrie. -->
        <template #cell(perfisc)="rand">
          <b-form-select
            v-if="editataId === rand.item.id"
            v-model="editata.perfisc"
            :options="periodicitati"
            size="sm"
          />
          <span v-else>{{ rand.item.perfisc }}</span>
        </template>

        <template #cell(valabilitate)="rand">
          <div
            v-if="editataId === rand.item.id"
            class="d-flex"
          >
            <b-form-input
              v-model="editata.data_inceput"
              type="date"
              size="sm"
              class="mr-50"
            />
            <b-form-input
              v-model="editata.data_sfarsit"
              type="date"
              size="sm"
            />
          </div>
          <span v-else>
            {{ rand.item.data_inceput || '...' }} → {{ rand.item.data_sfarsit || 'în vigoare' }}
          </span>
        </template>

        <template #cell(sursa)="rand">
          <b-badge :variant="rand.item.sursa === 'manuala' ? 'primary' : 'light-primary'">
            {{ rand.item.sursa === 'manuala' ? 'manuală' : 'dedusă' }}
          </b-badge>
          <div
            v-if="rand.item.obligatii"
            class="small text-muted"
          >
            {{ rand.item.obligatii }}
          </div>
        </template>

        <template #cell(actiuni)="rand">
          <template v-if="editataId === rand.item.id">
            <b-button
              size="sm"
              variant="primary"
              class="mr-50"
              :disabled="actualizareInCurs"
              @click="salveazaModificarea"
            >
              Salvează
            </b-button>
            <b-button
              size="sm"
              variant="outline-secondary"
              @click="editataId = null"
            >
              Renunță
            </b-button>
          </template>
          <template v-else>
            <b-button
              size="sm"
              variant="outline-primary"
              class="mr-50"
              :disabled="actualizareInCurs"
              @click="incepeModificarea(rand.item)"
            >
              Modifică
            </b-button>
            <b-button
              size="sm"
              variant="outline-danger"
              :disabled="actualizareInCurs"
              @click="stergeDeclaratie(rand.item)"
            >
              Șterge
            </b-button>
          </template>
        </template>
      </b-table>
    </b-modal>

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

    <!--
      Datele care nu se află în fișierele de la ANAF și fără de care declarația
      nu se poate întocmi: adresa, banca, contul, codul CAEN, cine semnează.
      Se scriu o dată și rămân — nu se cer la fiecare lună.
    -->
    <b-modal
      v-model="dateleVizibile"
      size="lg"
      title="Date pentru declarații"
      :ok-title="dateleInCurs ? 'Se salvează...' : 'Salvează'"
      cancel-title="Renunță"
      :ok-disabled="dateleInCurs"
      modal-class="modul-spv"
      scrollable
      @ok.prevent="salveazaDatele"
    >
      <p
        v-if="dateleFirmei"
        class="text-muted small mb-1"
      >
        {{ dateleFirmei.denumire || 'Entitate fără denumire' }} ({{ dateleFirmei.cif }}).
        Din ele se scrie antetul declarațiilor întocmite de aplicație — decontul de TVA
        scos din SAF-T, de pildă. Cifrele declarației vin din fișierul ANAF; astea nu se
        află nicăieri în el.
      </p>

      <b-alert
        v-if="dateleFirmei && dateleFirmei.lipsesc.length"
        show
        variant="warning"
        class="py-1 px-2"
      >
        <strong>Mai lipsesc:</strong> {{ dateleFirmei.lipsesc.join(', ') }}.
      </b-alert>
      <b-alert
        v-else-if="dateleFirmei"
        show
        variant="success"
        class="py-1 px-2"
      >
        Datele sunt complete — declarațiile se pot întocmi.
      </b-alert>

      <div v-if="dateleFirmei">
        <h6 class="mt-2">
          Firma
        </h6>
        <b-row>
          <b-col cols="12">
            <b-form-group label="Adresa">
              <b-form-input v-model="dateleScrise.adresa" />
            </b-form-group>
          </b-col>
          <b-col md="4">
            <b-form-group label="Telefon">
              <b-form-input v-model="dateleScrise.telefon" />
            </b-form-group>
          </b-col>
          <b-col md="4">
            <b-form-group label="Fax">
              <b-form-input v-model="dateleScrise.fax" />
            </b-form-group>
          </b-col>
          <b-col md="4">
            <b-form-group label="E-mail">
              <b-form-input
                v-model="dateleScrise.email"
                type="email"
              />
            </b-form-group>
          </b-col>
          <b-col md="5">
            <b-form-group label="Banca">
              <b-form-input v-model="dateleScrise.banca" />
            </b-form-group>
          </b-col>
          <b-col md="5">
            <b-form-group label="Contul (IBAN)">
              <b-form-input v-model="dateleScrise.cont" />
            </b-form-group>
          </b-col>
          <b-col md="2">
            <b-form-group label="Cod CAEN">
              <b-form-input v-model="dateleScrise.caen" />
            </b-form-group>
          </b-col>
        </b-row>

        <h6 class="mt-1">
          Cine semnează
        </h6>
        <b-row>
          <b-col md="4">
            <b-form-group label="Nume">
              <b-form-input v-model="dateleScrise.nume_declarant" />
            </b-form-group>
          </b-col>
          <b-col md="4">
            <b-form-group label="Prenume">
              <b-form-input v-model="dateleScrise.prenume_declarant" />
            </b-form-group>
          </b-col>
          <b-col md="4">
            <b-form-group label="Funcția">
              <b-form-input v-model="dateleScrise.functie_declarant" />
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <!-- Bifa aceasta schimbă și temeiul declarației: „prin împuternicit". -->
            <b-form-checkbox v-model="dateleScrise.prin_reprezentant">
              Declarația se depune prin împuternicit
            </b-form-checkbox>
          </b-col>
        </b-row>

        <h6 class="mt-2">
          Decontul de TVA (D300)
        </h6>
        <b-row>
          <b-col md="4">
            <b-form-group label="Felul decontului">
              <b-form-select
                v-model="dateleScrise.d300_tip_decont"
                :options="felurileDecontului"
              />
            </b-form-group>
          </b-col>
          <b-col md="4">
            <b-form-group label="Pro-rata (%)">
              <b-form-input
                v-model="dateleScrise.d300_pro_rata"
                type="number"
                min="0"
                max="100"
                step="0.01"
                placeholder="100"
              />
            </b-form-group>
          </b-col>
        </b-row>
        <b-row>
          <b-col md="6">
            <b-form-checkbox
              v-model="dateleScrise.d300_bifa_interne"
              class="mb-50"
            >
              Operațiuni interne
            </b-form-checkbox>
            <b-form-checkbox
              v-model="dateleScrise.d300_bifa_cereale"
              class="mb-50"
            >
              Cereale și plante tehnice
            </b-form-checkbox>
            <b-form-checkbox v-model="dateleScrise.d300_bifa_mob">
              Telefoane mobile
            </b-form-checkbox>
          </b-col>
          <b-col md="6">
            <b-form-checkbox
              v-model="dateleScrise.d300_bifa_disp"
              class="mb-50"
            >
              Dispozitive cu circuite integrate
            </b-form-checkbox>
            <b-form-checkbox
              v-model="dateleScrise.d300_bifa_cons"
              class="mb-50"
            >
              Console de jocuri, tablete, laptopuri
            </b-form-checkbox>
            <b-form-checkbox v-model="dateleScrise.d300_solicit_ramb">
              Se solicită rambursarea soldului sumei negative
            </b-form-checkbox>
          </b-col>
        </b-row>
      </div>

      <b-alert
        v-if="dateleEroare"
        show
        variant="danger"
        class="py-1 px-2 mb-0 mt-1"
      >
        {{ dateleEroare }}
      </b-alert>
    </b-modal>
  </div>
</template>

<script>
export default {
  name: 'SpvSocietati',
  data() {
    return {
      societati: [],
      // Implicit se arata doar entitatile in lucru — ele sunt firmele clientului
      arataToate: false,
      // Entitatea careia i se schimba starea chiar acum
      schimbaStarea: null,
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
      // Datele firmei care intra in antetul declaratiilor
      dateleVizibile: false,
      dateleInCurs: false,
      dateleEroare: '',
      dateleFirmei: null,
      dateleScrise: {},
      felurileDecontului: [],
      vectorVizibil: false,
      vectorInCurs: false,
      vectorEroare: '',
      /*
       * Luna raportată implicit e cea trecută: pe cea în curs abia se depune,
       * deci raportul ei ar fi mai mereu plin de „nedepusă" fără vină.
       */
      vector: {
        luna: 0,
        anul: 0,
        format: 'pdf',
      },
      // Tabelul declaratiilor asteptate pe CUI: deduse + scrise de om
      actualizareVizibila: false,
      actualizareInCurs: false,
      actualizareEroare: '',
      declaratii: [],
      // Randul din tabel aflat in lucru si valorile lui de scris
      editataId: null,
      editata: {
        perfisc: 'Lunar', data_inceput: '', data_sfarsit: '',
      },
      // Entitatea al carei buton a deschis fereastra de actualizare
      actualizareCui: '',
      // Vectorul fiscal ANAF al unei entitati: obligatiile ei, doar de citit
      vfVizibil: false,
      vfInCurs: false,
      vfEroare: '',
      vfCui: '',
      vfRanduri: [],
      vfArataIstoricul: false,
      noua: {
        tip: '', perfisc: 'Lunar', data_inceput: '', data_sfarsit: '',
      },
      periodicitati: ['Lunar', 'Trimestrial', 'Semestrial', 'Anual'],
      // Sugestii pentru campul de tip; se poate scrie si altceva
      tipuriCunoscute: [
        'D100', 'D101', 'D112', 'D205', 'D212', 'D300', 'D301', 'D307', 'D311',
        'D390', 'D394', 'D406', 'BILANT',
      ],
      campuriDeclaratii: [
        { key: 'tip', label: 'Tip' },
        { key: 'perfisc', label: 'Periodicitate' },
        { key: 'valabilitate', label: 'Valabilitate' },
        { key: 'sursa', label: 'Sursa' },
        { key: 'actiuni', label: '' },
      ],
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
    /** Fereastra e legată de entitatea de pe rândul al cărei buton a deschis-o. */
    declaratiiFiltrate() {
      return this.declaratii.filter(d => d.cui === this.actualizareCui)
    },
    actualizareDenumire() {
      return this.etichetaEntitate(this.actualizareCui)
    },
    /** Implicit doar obligațiile în vigoare; istoricul se cere anume. */
    vfRanduriFiltrate() {
      if (this.vfArataIstoricul) return this.vfRanduri

      return this.vfRanduri.filter(r => !r.data_sfarsit)
    },
    vfDataVector() {
      const cuData = this.vfRanduri.find(r => r.data_vector)

      return cuData ? cuData.data_vector : ''
    },
    lunileAnului() {
      return [
        'ianuarie', 'februarie', 'martie', 'aprilie', 'mai', 'iunie',
        'iulie', 'august', 'septembrie', 'octombrie', 'noiembrie', 'decembrie',
      ].map((nume, index) => ({ value: index + 1, text: nume }))
    },
    /** Anii de ales: de la anul viitor înapoi, cât ține istoricul rezonabil. */
    aniiDeAles() {
      const anulAcesta = new Date().getFullYear()

      return Array.from({ length: 7 }, (v, i) => anulAcesta + 1 - i)
    },
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
    /**
     * Scoate din uz o entitate, sau o pune iar în lucru.
     *
     * Un client are adesea în certificat firme pe care nu le mai ține: ele
     * încărcau degeaba fiecare interogare la ANAF și fiecare listă de ales.
     * Scoasă din uz, entitatea e ignorată peste tot — la mesaje, la solicitări,
     * la alerte — dar rămâne în evidență, cu documentele ei.
     *
     * Alegerea aceasta stă deoparte de „activ", care e cuvântul ANAF-ului:
     * altfel prima sincronizare ar șterge-o.
     */
    schimbaUzul(societate) {
      this.eroare = ''
      this.schimbaStarea = societate.id

      const scoasa = !societate.scos_din_uz

      this.$http.put(`/anaf-societati/${societate.id}`, { scos_din_uz: scoasa })
        .then(() => {
          this.info = scoasa
            ? `${societate.denumire || societate.cif} a fost scoasă din uz și va fi ignorată peste tot.`
            : `${societate.denumire || societate.cif} este iar în lucru.`

          return this.incarcaLista()
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Starea entității nu a putut fi schimbată')
        })
        .finally(() => {
          this.schimbaStarea = null
        })
    },
    incarcaLista() {
      this.listaInCurs = true
      // Filtrarea pe CIF, denumire si tip se face pe coloanele tabelului.
      const params = {}
      if (!this.arataToate) params.doar_active = 1

      return this.$http.get('/anaf-societati', { params })
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

      // Pe entitatile scoase din uz nu se cheltuie apeluri la ANAF.
      const cifuri = this.societati.filter(s => s.in_lucru).map(s => s.cif)

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
    etichetaEntitate(cui) {
      const gasita = this.societati.find(s => s.cif === cui)

      return gasita ? `${gasita.denumire || 'fără denumire'} (${gasita.cif})` : cui
    },
    /** Vectorul fiscal ANAF al entității: obligațiile ei, doar de citit. */
    deschideVectorFiscal(societate) {
      this.vfCui = societate.cif
      this.vfEroare = ''
      this.vfRanduri = []
      this.vfArataIstoricul = false
      this.vfVizibil = true
      this.vfInCurs = true

      this.$http.get('/vector-fiscal/spv', { params: { cui: societate.cif } })
        .then(raspuns => {
          this.vfRanduri = raspuns.data.data || []
        })
        .catch(err => {
          this.vfEroare = this.mesajEroare(err, 'Vectorul fiscal nu a putut fi încărcat')
        })
        .finally(() => {
          this.vfInCurs = false
        })
    },
    deschideActualizare(societate) {
      this.actualizareCui = societate.cif
      this.actualizareEroare = ''
      this.noua = {
        tip: '', perfisc: 'Lunar', data_inceput: '', data_sfarsit: '',
      }
      this.actualizareVizibila = true
      // La deschidere, deducția se face pe loc: tabelul are ce arăta și
      // înainte de primul raport descărcat.
      this.incarcaDeclaratii(true)
    },
    incarcaDeclaratii(deduce) {
      this.actualizareInCurs = true

      // Filtrarea pe entitate se face pe lista deja adusă; se cere tot.
      return this.$http.get('/vector-fiscal/declaratii', { params: deduce ? { deduce: 1 } : {} })
        .then(raspuns => {
          this.declaratii = raspuns.data.data || []
        })
        .catch(err => {
          this.actualizareEroare = this.mesajEroare(err, 'Declarațiile nu au putut fi încărcate')
        })
        .finally(() => {
          this.actualizareInCurs = false
        })
    },
    adaugaDeclaratie() {
      this.actualizareEroare = ''
      this.actualizareInCurs = true

      const date = {
        cui: this.actualizareCui,
        tip: this.noua.tip,
        perfisc: this.noua.perfisc,
        data_inceput: this.noua.data_inceput || null,
        data_sfarsit: this.noua.data_sfarsit || null,
      }

      this.$http.post('/vector-fiscal/declaratii', date)
        .then(() => {
          // Firma rămâne aleasă: de obicei se adaugă mai multe tipuri la rând.
          this.noua.tip = ''
          this.noua.data_inceput = ''
          this.noua.data_sfarsit = ''

          return this.incarcaDeclaratii()
        })
        .catch(err => {
          this.actualizareEroare = this.mesajEroare(err, 'Declarația nu a putut fi adăugată')
          this.actualizareInCurs = false
        })
    },
    incepeModificarea(declaratie) {
      this.actualizareEroare = ''
      this.editataId = declaratie.id
      this.editata = {
        perfisc: declaratie.perfisc,
        data_inceput: declaratie.data_inceput || '',
        data_sfarsit: declaratie.data_sfarsit || '',
      }
    },
    /**
     * Salvează îndreptarea — și pe rândurile deduse.
     *
     * Serverul le trece atunci la „manuală": altfel următoarea întocmire a
     * raportului ar scrie deducția la loc peste ce a îndreptat omul.
     */
    salveazaModificarea() {
      this.actualizareEroare = ''
      this.actualizareInCurs = true

      const date = {
        perfisc: this.editata.perfisc,
        data_inceput: this.editata.data_inceput || null,
        data_sfarsit: this.editata.data_sfarsit || null,
      }

      this.$http.put(`/vector-fiscal/declaratii/${this.editataId}`, date)
        .then(() => {
          this.editataId = null

          return this.incarcaDeclaratii()
        })
        .catch(err => {
          this.actualizareEroare = this.mesajEroare(err, 'Modificarea nu a putut fi salvată')
          this.actualizareInCurs = false
        })
    },
    stergeDeclaratie(declaratie) {
      this.actualizareEroare = ''
      this.actualizareInCurs = true

      this.$http.delete(`/vector-fiscal/declaratii/${declaratie.id}`)
        .then(() => this.incarcaDeclaratii())
        .catch(err => {
          this.actualizareEroare = this.mesajEroare(err, 'Declarația nu a putut fi ștearsă')
          this.actualizareInCurs = false
        })
    },
    deschideVector() {
      // Luna trecută, propusă de fiecare dată: cea aleasă rândul trecut poate
      // fi departe în urmă, iar omul vine de obicei pentru luna abia încheiată.
      const lunaTrecuta = new Date()
      lunaTrecuta.setDate(1)
      lunaTrecuta.setMonth(lunaTrecuta.getMonth() - 1)

      this.vector.luna = lunaTrecuta.getMonth() + 1
      this.vector.anul = lunaTrecuta.getFullYear()
      this.vectorEroare = ''
      this.vectorVizibil = true
    },
    /**
     * Descarcă vectorul fiscal al lunii alese, în forma aleasă.
     *
     * Fișierul vine ca blob, nu printr-un link direct: ruta cere tokenul, iar
     * un `window.open` nu-l poartă cu el.
     */
    descarcaVector() {
      this.vectorEroare = ''
      this.vectorInCurs = true

      const params = { luna: this.vector.luna, anul: this.vector.anul, format: this.vector.format }

      this.$http.get('/vector-fiscal/lunar', { params, responseType: 'blob' })
        .then(raspuns => {
          const url = window.URL.createObjectURL(new Blob([raspuns.data]))
          const legatura = document.createElement('a')
          const extensie = this.vector.format === 'excel' ? 'xlsx' : 'pdf'
          const luna = String(this.vector.luna).padStart(2, '0')

          legatura.href = url
          legatura.download = `vector_fiscal_${luna}_${this.vector.anul}.${extensie}`
          document.body.appendChild(legatura)
          legatura.click()
          document.body.removeChild(legatura)

          setTimeout(() => window.URL.revokeObjectURL(url), 60000)

          this.vectorVizibil = false
        })
        .catch(err => this.aratEroareVector(err))
        .finally(() => {
          this.vectorInCurs = false
        })
    },
    /**
     * Eroarea vine tot ca blob (răspunsul a fost cerut așa) — se citește din
     * el mesajul serverului, ca omul să afle de ce n-a primit fișierul.
     */
    aratEroareVector(err) {
      const implicit = 'Raportul nu a putut fi întocmit.'

      if (!(err.response && err.response.data instanceof Blob)) {
        this.vectorEroare = this.mesajEroare(err, implicit)

        return
      }

      err.response.data.text()
        .then(text => {
          const date = JSON.parse(text)
          this.vectorEroare = date.message || implicit
        })
        .catch(() => {
          this.vectorEroare = implicit
        })
    },
    editeaza(societate) {
      this.formular = { ...societate }
      this.formularVizibil = true
    },

    /**
     * Datele de declarație ale entității, aduse de la server.
     *
     * Se cer de fiecare dată: ele se schimbă rar, dar când se schimbă contează
     * să nu lucrăm cu ce s-a încărcat la deschiderea filei.
     */
    deschideDateleDeclaratiilor(societate) {
      this.dateleFirmei = null
      this.dateleScrise = {}
      this.dateleEroare = ''
      this.dateleVizibile = true
      this.dateleInCurs = true

      this.$http.get(`/anaf-societati/${societate.id}/date-declaratii`)
        .then(raspuns => {
          this.arataDatele(raspuns.data.data, societate.id)
        })
        .catch(err => {
          this.dateleEroare = this.mesajEroare(err, 'Datele nu au putut fi citite')
        })
        .finally(() => {
          this.dateleInCurs = false
        })
    },

    salveazaDatele() {
      this.dateleEroare = ''
      this.dateleInCurs = true

      this.$http.put(`/anaf-societati/${this.dateleFirmei.id}/date-declaratii`, this.dateleScrise)
        .then(raspuns => {
          this.arataDatele(raspuns.data.data, this.dateleFirmei.id)

          // Fereastra rămâne deschisă cât timp mai lipsește ceva: omul vede pe
          // loc ce anume, fără s-o deschidă din nou.
          if (this.dateleFirmei.gata) {
            this.dateleVizibile = false
          }
        })
        .catch(err => {
          this.dateleEroare = this.mesajEroare(err, 'Salvarea a eșuat')
        })
        .finally(() => {
          this.dateleInCurs = false
        })
    },

    /** Răspunsul serverului, pus în formular. */
    arataDatele(date, id) {
      this.dateleFirmei = { ...date, id }
      this.dateleScrise = { ...date.date }
      this.felurileDecontului = Object.keys(date.feluri_decont).map(cheie => ({
        value: cheie,
        text: `${cheie} — ${date.feluri_decont[cheie]}`,
      }))
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
