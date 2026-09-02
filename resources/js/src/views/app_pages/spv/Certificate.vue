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
            Dezarhivați kitul în folderul <code>C:\DianaSoft_SPV_Curier</code> și rulați cu Run As Administrator
            <code>instaleaza.bat</code> din kit pe acel calculator: programul pornește
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

          <!-- Legătura care „a mers și s-a oprit" are aproape întotdeauna
               aceeași pricină, iar ea se dezleagă pe calculatorul clientului:
               ajutorul stă aici, lângă kit, unde se uită omul când instalează. -->
          <b-button
            variant="flat-warning"
            size="sm"
            class="mt-1"
            @click="ajutorVizibil = true"
          >
            <feather-icon
              icon="HelpCircleIcon"
              size="14"
              class="mr-25"
            />
            Nu comunică? Firewall și antivirus
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
        :tbody-tr-class="clasaRand"
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
              <!-- Legătura prin tunel: se vede dacă programul de acolo e pornit,
                   fără să fie nevoie să încerci o operație ca să afli. -->
              <div
                v-if="rand.item.mod_legatura === 'tunel'"
                class="small"
                :class="rand.item.agent_treaz ? 'text-success' : 'text-danger'"
              >
                <feather-icon
                  :icon="rand.item.agent_treaz ? 'WifiIcon' : 'WifiOffIcon'"
                  size="12"
                  class="mr-25"
                />{{ rand.item.agent_treaz
                  ? 'prin tunel, program pornit'
                  : (rand.item.agent_vazut_la
                    ? 'prin tunel, oprit din ' + rand.item.agent_vazut_la
                    : 'prin tunel, încă nepornit') }}
              </div>
              <!-- Versiunea programului de la client: se înnoiește singură, dar
                   se vede, ca să știm cine a rămas în urmă. -->
              <div
                v-if="rand.item.versiune_bridge"
                class="small"
                :class="rand.item.versiune_bridge === versiuneProgram ? 'text-muted' : 'text-warning'"
              >
                <feather-icon
                  :icon="rand.item.versiune_bridge === versiuneProgram ? 'CheckCircleIcon' : 'DownloadIcon'"
                  size="12"
                  class="mr-25"
                />{{ rand.item.versiune_bridge === versiuneProgram
                  ? 'program la zi'
                  : 'program vechi — se înnoiește singur' }}
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
              <!--
                PIN-ul de pe token. Citirea certificatului nu-l cere niciodată —
                el se cere abia când cheia e chiar folosită, la semnare sau la
                intrarea în SPV. Până acum asta se afla pe pielea omului: prima
                lucrare se împotmolea într-o fereastră deschisă pe alt ecran.
                Acum se probează la intrarea în aplicație și se vede aici.
              -->
              <div
                v-if="rand.item.pin_stare"
                class="small"
                :class="rand.item.pin_stare === 'gata' ? 'text-success' : 'text-danger'"
                :title="rand.item.pin_motiv
                  || 'Verificat la ' + (rand.item.pin_verificat_la || '—')"
              >
                <feather-icon
                  :icon="rand.item.pin_stare === 'gata' ? 'UnlockIcon' : 'LockIcon'"
                  size="12"
                  class="mr-25"
                />{{ textulPinului(rand.item) }}
              </div>

              <!-- Scos din uz: se spune primul, fiindcă schimbă înțelesul a tot
                   ce scrie alături — calculator, dosar urmărit, licență. -->
              <b-badge
                v-if="!rand.item.activ"
                variant="secondary"
              >
                scos din uz
              </b-badge>
              <b-badge
                v-if="rand.item.implicit"
                variant="primary"
              >
                implicit
              </b-badge>
              <b-badge
                v-if="certificatActiv === rand.item.id && rand.item.activ"
                variant="success"
              >
                activ acum
              </b-badge>
            </div>

            <!-- Licența se reînnoiește singură în fiecare dimineață; butonul e
                 pentru cazurile care nu pot aștepta: un calculator nou, unul
                 care a stat închis, un abonament tocmai plătit. -->
            <b-button
              v-b-tooltip.hover.top.window.v-light
              size="sm"
              variant="outline-secondary"
              class="btn-icon ml-2"
              :disabled="licentaInCurs === rand.item.id || !rand.item.activ"
              title="Reînnoiește acum licența programului local"
              @click="reinnoiesteLicenta(rand.item)"
            >
              <b-spinner
                v-if="licentaInCurs === rand.item.id"
                small
              />
              <feather-icon
                v-else
                icon="KeyIcon"
              />
            </b-button>

            <b-button
              v-b-tooltip.hover.top.window.v-light
              size="sm"
              variant="outline-success"
              class="btn-icon ml-2"
              :disabled="certificatActiv === rand.item.id || !rand.item.activ"
              title="Folosește acest certificat pentru operațiile mele"
              @click="alegeActiv(rand.item)"
            >
              <feather-icon icon="CheckCircleIcon" />
            </b-button>

            <!-- Certificatul cu care clientul nu lucrează în relația cu SPV:
                 rămâne în listă, dar aplicația nu-l mai ia în seamă. -->
            <b-button
              v-b-tooltip.hover.top.window.v-light
              size="sm"
              :variant="rand.item.activ ? 'outline-danger' : 'outline-primary'"
              class="btn-icon ml-2"
              :disabled="activareInCurs === rand.item.id"
              :title="rand.item.activ
                ? 'Scoate certificatul din uz — aplicația îl va ignora'
                : 'Repune certificatul în uz'"
              @click="comutaActiv(rand.item)"
            >
              <b-spinner
                v-if="activareInCurs === rand.item.id"
                small
              />
              <feather-icon
                v-else
                :icon="rand.item.activ ? 'PowerIcon' : 'RefreshCwIcon'"
              />
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
      modal-class="modul-spv"
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

      <!-- Cum ajunge serverul la calculatorul cu tokenul. Prin tunel nu trebuie
           deschis niciun port pe routerul clientului: programul de acolo
           întreabă singur serverul ce are de făcut. -->
      <b-form-group label="Legătura cu acest calculator">
        <b-form-radio
          v-model="bridgeFormular.mod_legatura"
          value="direct"
          class="mb-50"
        >
          Directă — serverul îl caută la adresa de mai sus
          <small class="d-block text-muted">
            Merge când aplicația și calculatorul sunt în aceeași rețea, sau când
            acesta are adresă publică.
          </small>
        </b-form-radio>
        <b-form-radio
          v-model="bridgeFormular.mod_legatura"
          value="tunel"
        >
          Prin tunel — programul întreabă singur serverul
          <small class="d-block text-muted">
            Pentru calculatoare din spatele unui router. Nu se deschide niciun
            port: legătura pleacă dinspre client, pe 443. Cere agentul din kitul
            nou, instalat pe acel calculator.
          </small>
        </b-form-radio>
      </b-form-group>

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

      <b-form-group
        v-if="bridgeFormular.monitorizare_activa"
        label="Cât de des se verifică dosarul"
        class="mb-1"
      >
        <b-form-select
          v-model="bridgeFormular.monitorizare_cadenta"
          :options="cadenteMonitorizare"
        />
      </b-form-group>

      <!-- Cât de departe merge singur: doar validare, și semnare, sau tot
           drumul până la ANAF. Depunerea nu se mai poate lua înapoi, de aceea
           vine debifată. -->
      <b-form-checkbox
        v-if="bridgeFormular.monitorizare_activa"
        v-model="bridgeFormular.monitorizare_semneaza"
        class="mb-50"
      >
        Semnează declarațiile valide
        <small class="d-block text-muted">
          Nebifat, declarațiile doar se validează; semnarea rămâne de făcut
          din fila Declarații fiscale.
        </small>
      </b-form-checkbox>

      <b-form-checkbox
        v-if="bridgeFormular.monitorizare_activa"
        v-model="bridgeFormular.monitorizare_depune"
        class="mb-1"
      >
        Depune declarațiile semnate
        <small class="d-block text-muted">
          Pleacă la ANAF doar ce a ajuns semnat — semnat aici sau venit gata
          semnat. Depunerea nu se mai poate lua înapoi.
        </small>
      </b-form-checkbox>

      <!-- PIN-ul trimis de la distanță: alegerea celui care ține tokenul.
           Nebifată, aplicația spune doar că fereastra e deschisă, și atât. -->
      <hr>

      <b-form-checkbox
        v-model="bridgeFormular.pin_de_la_distanta"
        class="mb-1"
      >
        Pot trimite PIN-ul acestui token din aplicație
        <small class="d-block text-muted">
          Când o lucrare se oprește fiindcă tokenul își cere PIN-ul, aplicația
          îl cere aici și îl scrie în fereastra deschisă pe calculatorul
          clientului. Codul trece o singură dată și nu se păstrează nicăieri —
          nici în aplicație, nici pe server.
        </small>
        <small class="d-block text-warning mt-25">
          Nebifat, aplicația doar vă spune care token așteaptă, iar codul se
          scrie de mână, acolo. Bifați numai dacă tokenul e al dumneavoastră
          sau aveți învoirea celui care răspunde de el: PIN-ul e dovada că
          semnătura vă aparține.
        </small>
      </b-form-checkbox>

      <small
        v-if="bridgeFormular.monitorizare_activa"
        class="text-muted d-block mb-3"
      >
        La cadența aleasă, declarațiile puse acolo — XML sau PDF — se încarcă
        și se validează singure, apoi, după bifele de mai sus, se semnează și
        se depun; la final trec în subdosarul
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
      modal-class="modul-spv"
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
      modal-class="modul-spv"
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

    <!-- Ce trebuie deblocat pe calculatorul clientului ca legătura să meargă -->
    <b-modal
      v-model="ajutorVizibil"
      size="lg"
      ok-only
      ok-title="Am înțeles"
      scrollable
      modal-class="modul-spv"
    >
      <template #modal-title>
        <feather-icon
          icon="ShieldIcon"
          size="18"
          class="text-primary mr-50"
        />
        Firewall și antivirus — ce trebuie deblocat
      </template>

      <p class="mb-2">
        Legătura care a mers și apoi s-a oprit nu e o pană a aplicației: e, aproape
        întotdeauna, filtrarea traficului criptat din antivirus, aprinsă singură la o
        actualizare sau la reînnoirea abonamentului. Toate setările de mai jos se fac pe
        calculatorul unde stă tokenul.
      </p>

      <b-alert
        show
        variant="warning"
      >
        <div class="alert-body">
          <strong>Regula de aur.</strong> Programul local nu trimite parole la ANAF: se
          legitimează cu certificatul de pe token, într-o legătură în care și clientul își
          arată certificatul. Antivirusul care scanează HTTPS desface legătura și o reface
          cu certificatul lui — dar cheia de pe token n-o are, deci legitimarea se rupe.
          Adresele de mai jos trebuie <strong>scoase de sub scanarea HTTPS</strong>
          („filtrare SSL/TLS”, „scanare conexiuni criptate”, „Scan SSL”), nu doar permise.
        </div>
      </b-alert>

      <h6 class="mt-2">
        1. Ce trebuie să poată ieși, pe portul 443
      </h6>
      <ul class="mb-2 pl-3">
        <li><code>app.dianasoft.ro</code> — agentul întreabă serverul ce are de lucru</li>
        <li><code>webserviced.anaf.ro</code> — mesajele și documentele din SPV</li>
        <li><code>decl.anaf.mfinante.gov.ro</code> — depunerea declarațiilor</li>
        <li><code>webserviceapl.anaf.ro</code> — e-Transport, dacă se folosește</li>
      </ul>
      <p class="small text-muted mb-2">
        Pe legătura „prin tunel” nu se deschide niciun port de intrare și nu se umblă la
        router. Portul <code>8099</code> se deschide numai dacă certificatul e configurat
        „direct”, și numai către adresa serverului aplicației.
      </p>

      <h6>2. Ce se scoate de sub scanare</h6>
      <ul class="mb-2 pl-3">
        <li>dosarul de instalare, cu tot ce e sub el — de obicei <code>C:\DianaSoft_SPV_Curier</code></li>
        <li>procesele <code>php.exe</code> (din dosarul kitului) și <code>C:\Windows\System32\curl.exe</code></li>
        <li><code>powershell.exe</code>, <code>PDFtoPrinter.exe</code> și <code>itextsharp.dll</code> — citirea certificatelor, semnarea, tipărirea</li>
      </ul>
      <p class="small text-muted mb-2">
        La ESET, în plus: <em>Configurare avansată (F5) → Protecții → SSL/TLS</em>, cele patru
        adrese la „excluse din filtrare”; iar dacă HIPS e pe mod interactiv, o regulă prin care
        <code>php.exe</code> are voie să pornească alte programe.
      </p>

      <h6>3. Imprimantele</h6>
      <p class="small mb-2">
        Tipărirea se face chiar pe calculatorul cu tokenul, deci nu cere nicio regulă de
        firewall — decât dacă imprimanta e în rețea (ieșire pe <code>9100</code> RAW,
        <code>445</code> partajată de pe un server, <code>631</code> IPP, <code>515</code> LPR).
        Două pricini obișnuite: <code>PDFtoPrinter.exe</code> pus în carantină ca „aplicație
        nedorită”, sau imprimanta instalată în alt cont Windows decât cel sub care rulează
        programul — atunci nici nu apare în listă.
      </p>

      <h6>4. Verificarea, în ordinea aceasta</h6>
      <p class="small mb-1">
        Pe calculatorul cu tokenul, în PowerShell:
      </p>
      <pre class="verificari-ajutor mb-2">Get-ScheduledTask "Acces token ANAF*"
curl.exe -sS -o NUL -w "%{http_code}`n" http://127.0.0.1:8099/certificate
Get-Content C:\DianaSoft_SPV_Curier\agent.log -Tail 30
Test-NetConnection app.dianasoft.ro -Port 443</pre>
      <ul class="small mb-2 pl-3">
        <li>a doua comandă trebuie să răspundă <code>401</code> — programul cere codul de acces, deci trăiește;</li>
        <li>rândurile „<em>Serverul nu răspunde: …</em>” poartă cu ele și pricina — port închis, TLS desfăcut de antivirus, sau un răspuns venit de la altcineva de pe drum;</li>
        <li>la kiturile mai vechi, „<em>Serverul nu răspunde; reîncerc peste 5s</em>” apare și când totul merge: dacă printre acele rânduri sunt și rânduri „<em>Comanda …</em>”, legătura e bună;</li>
        <li>„<em>Serverul nu-mi recunoaște codul de acces</em>” nu e firewall: apăsați „Citește token-urile conectate”.</li>
      </ul>

      <p class="small text-muted mb-0">
        Pașii întregi, pe fiecare antivirus în parte (ESET, Defender, Bitdefender, Kaspersky,
        Avast/AVG, Norton, McAfee), sunt în ghidul <em>Firewall și antivirus pentru programul
          de acces la token</em>, de cerut de la DianaSoft.
      </p>
    </b-modal>
  </div>
</template>

<script>
export default {
  name: 'SpvCertificate',
  data() {
    return {
      certificate: [],
      // Certificatul caruia i se probeaza PIN-ul chiar acum
      pinInCurs: null,
      abonati: [],
      zileAvertizare: 30,
      // Versiunea programului local pe care o are serverul acum
      versiuneProgram: '',
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
      // Certificatul pentru care se trimite acum licența
      licentaInCurs: null,
      // Certificatul care se scoate din uz sau se repune chiar acum
      activareInCurs: null,
      // Ajutorul pentru firewall și antivirus, de pe calculatorul clientului
      ajutorVizibil: false,
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
      // Din cât în cât se verifică dosarul urmărit al certificatului
      cadenteMonitorizare: [
        { value: 1, text: 'La 1 minut' },
        { value: 3, text: 'La 3 minute' },
        { value: 5, text: 'La 5 minute' },
        { value: 10, text: 'La 10 minute' },
        { value: 15, text: 'La 15 minute' },
        { value: 30, text: 'La 30 de minute' },
        { value: 60, text: 'La 60 de minute' },
      ],
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
    /**
     * Ce scrie lângă certificat despre PIN-ul de pe token.
     *
     * „gata" înseamnă că driverul îl are în minte și cheia se poate folosi acum
     * — nu că l-am ști noi: PIN-ul nu trece prin aplicație și nu se păstrează
     * nicăieri, rămâne între om și driver.
     */
    textulPinului(certificat) {
      if (certificat.pin_stare === 'gata') return 'PIN introdus — tokenul e gata de lucru'
      if (certificat.pin_stare === 'refuzat') return 'PIN neintrodus — semnarea și SPV vor eșua'

      return 'tokenul nu a putut fi întrebat de PIN'
    },
    /**
     * Cere probarea PIN-ului și reîncarcă lista.
     *
     * Se cheamă și singură, la intrarea în aplicație. Aici e pentru cazul în
     * care omul a conectat tokenul între timp, sau a închis fereastra fără să
     * scrie PIN-ul și vrea să încerce din nou.
     */
    verificaPinul(certificat) {
      this.pinInCurs = certificat ? certificat.id : 'toate'

      return this.$http.post('/anaf-certificate/verifica-pin', certificat ? { certificat: certificat.id } : {})
        .then(() => this.incarcaLista())
        .catch(() => {
          // Un token care nu răspunde nu e o eroare de arătat aici: starea lui
          // se vede oricum lângă el, scrisă de server.
        })
        .finally(() => {
          this.pinInCurs = null
        })
    },
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
    /** Rândul certificatului scos din uz se stinge, fără să dispară din listă. */
    clasaRand(certificat) {
      return certificat && !certificat.activ ? 'rand-scos-din-uz' : ''
    },
    incarcaLista() {
      this.listaInCurs = true

      this.$http.get('/anaf-certificate')
        .then(raspuns => {
          this.certificate = raspuns.data.data || []
          this.abonati = raspuns.data.abonati || []
          this.zileAvertizare = raspuns.data.zile_avertizare || 30
          this.versiuneProgram = raspuns.data.versiune_program || ''
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
          link.download = 'kit_spv_curier.zip'
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
        monitorizare_cadenta: certificat.monitorizare_cadenta || 5,
        monitorizare_semneaza: certificat.monitorizare_semneaza !== false,
        monitorizare_depune: Boolean(certificat.monitorizare_depune),
        pin_de_la_distanta: Boolean(certificat.pin_de_la_distanta),
        monitorizare_la: certificat.monitorizare_la,
        mod_legatura: certificat.mod_legatura || 'direct',
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
        monitorizare_cadenta: this.bridgeFormular.monitorizare_cadenta,
        monitorizare_semneaza: this.bridgeFormular.monitorizare_semneaza,
        monitorizare_depune: this.bridgeFormular.monitorizare_depune,
        pin_de_la_distanta: this.bridgeFormular.pin_de_la_distanta,
        mod_legatura: this.bridgeFormular.mod_legatura,
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
    /** Trimite acum licența programului local, fără să aștepte reînnoirea de noapte. */
    reinnoiesteLicenta(certificat) {
      this.eroare = ''
      this.info = ''
      this.licentaInCurs = certificat.id

      this.$http.post(`/anaf-certificate/${certificat.id}/licenta`)
        .then(({ data }) => {
          this.info = data.message
          this.incarcaLista()
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Licența nu a putut fi trimisă')
        })
        .finally(() => {
          this.licentaInCurs = null
        })
    },
    /**
     * Scoate certificatul din uz, sau îl repune.
     *
     * Dacă era chiar cel ales pentru operațiile mele, alegerea se șterge pe loc:
     * altfel cererile ar pleca mai departe cu el în antet, iar serverul, care nu
     * mai lucrează cu el, le-ar rezolva pe altul, fără să se vadă de ce.
     */
    comutaActiv(certificat) {
      this.eroare = ''
      this.info = ''
      this.activareInCurs = certificat.id

      this.$http.post(`/anaf-certificate/${certificat.id}/activare`)
        .then(({ data }) => {
          this.info = data.message

          if (!data.data.activ && this.certificatActiv === certificat.id) {
            this.certificatActiv = null
            delete this.$http.defaults.headers.common['X-Certificat-Id']
            window.localStorage.removeItem('anaf_certificat_activ')
          }

          this.incarcaLista()
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Starea certificatului nu a putut fi schimbată')
        })
        .finally(() => {
          this.activareInCurs = null
        })
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

/*
 * Certificatul scos din uz: randul se stinge, ca sa se vada dintr-o privire ca
 * aplicatia nu lucreaza cu el, dar ramane citibil — datele lui se cauta si dupa
 * scoatere. Randurile le scrie b-table, adica alt component, de aceea ::v-deep.
 */
::v-deep .rand-scos-din-uz {
  opacity: 0.55;
}

/* Comenzile de verificare: de citit si de copiat asa cum sunt scrise. */
.verificari-ajutor {
  padding: 0.5rem 0.75rem;
  border-left: 3px solid rgba(115, 103, 240, 0.5);
  background: rgba(115, 103, 240, 0.06);
  border-radius: 0.2rem;
  font-size: 0.8rem;
  white-space: pre-wrap;
  word-break: break-word;
}
</style>
