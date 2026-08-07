<template>
  <div>
    <!-- Alegerea fișierelor și acțiunile stau împreună, într-un chenar:
         formează un singur pas al lucrului, deosebit de filtrele de dedesubt. -->
    <b-card
      class="border mb-2"
      body-class="p-2"
    >
      <!-- Eticheta și cele două căi de alegere a fișierelor stau una sub alta,
           iar acțiunile alături, pe lățimea textului lor -->
      <b-row class="align-items-end">
        <b-col md="3">
          <label class="mb-0">Selectați declarații (XML sau PDF) de prelucrat</label>
          <b-form-file
            v-model="fisiere"
            multiple
            accept=".xml,.pdf"
            placeholder="Alegeți unul sau mai multe fișiere..."
            browse-text="Răsfoiește"
            :file-name-formatter="numeFisiere"
          />
          <!-- selectarea unui folder întreg: atributul e specific browserului -->
          <b-form-file
            ref="folder"
            v-model="dinFolder"
            multiple
            directory
            webkitdirectory
            class="mt-1"
            placeholder="...sau alegeți un folder întreg"
            browse-text="Folder"
            :file-name-formatter="numeFisiere"
          />
        </b-col>
        <b-col md="auto">
          <div>
            <!-- Cei doi pași ai prelucrării, unul sub altul, fiecare cu semnul
                 lui. Cât lucrează, pasul în desfășurare se învârte, iar cele
                 terminate rămân bifate: se vede unde s-a ajuns, nu doar că
                 butonul e ocupat. -->
            <b-button
              variant="outline-primary"
              class="text-left buton-pasi"
              :disabled="!deIncarcat.length || lucreaza"
              @click="incarca"
            >
              <div :class="pasulActiv === 'incarca' ? 'font-weight-bolder' : ''">
                <b-spinner
                  v-if="pasulActiv === 'incarca'"
                  small
                  class="mr-1"
                />
                <feather-icon
                  v-else
                  :icon="incarcaTerminat ? 'CheckIcon' : 'UploadIcon'"
                  size="15"
                  class="mr-1"
                  :class="incarcaTerminat ? 'text-success' : ''"
                />
                Încarcă
                <!-- Numarul se scoate in evidenta: pe fondul butonului, o eticheta
                   deschisa la culoare aproape ca dispare. -->
                <b-badge
                  v-if="deIncarcat.length"
                  variant="warning"
                  class="ml-1"
                >
                  {{ deIncarcat.length }}
                </b-badge>
              </div>
              <div :class="pasulActiv === 'incarca' ? 'font-weight-bolder' : ''">
                <b-spinner
                  v-if="pasulActiv === 'incarca'"
                  small
                  class="mr-1"
                />
                <feather-icon
                  v-else
                  :icon="incarcaTerminat ? 'CheckIcon' : 'CheckCircleIcon'"
                  size="15"
                  class="mr-1"
                  :class="incarcaTerminat ? 'text-success' : ''"
                />
                Validează
              </div>
            </b-button>

            <!-- Semnarea nu mai e un pas al butonului, ci o alegere: cine are
                 dreptul poate lăsa declarațiile valide să fie semnate pe loc,
                 fără să mai apese butonul de alături. -->
            <div
              v-if="poateSemna"
              class="pentru-tiparire mt-50"
              title="După validare, semnează pe loc declarațiile valide din acest lot"
            >
              <b-form-checkbox
                v-model="semneazaDupaValidare"
                size="sm"
                class="comutator-primar"
              >
                <small
                  class="text-nowrap"
                  :class="semneazaDupaValidare ? 'text-primary' : 'text-muted'"
                >
                  Semnează declarațiile valide
                </small>
              </b-form-checkbox>
            </div>
          </div>
        </b-col>

        <!-- Semnarea celor rămase valide în tabel, cu reglajele ei dedesubt.
             Toată coloana lipsește la cine n-are dreptul de semnare. -->
        <b-col
          v-if="poateSemna"
          md="auto"
        >
          <div>
            <b-button
              variant="outline-primary"
              class="text-left"
              :disabled="!deSemnat.length || lucreaza"
              @click="semneazaDinTabel"
            >
              <b-spinner
                v-if="semnareInCurs"
                small
                class="mr-1"
              />
              <feather-icon
                v-else
                icon="Edit3Icon"
                size="15"
                class="mr-1"
              />
              Semnează declarațiile valide
              <b-badge
                v-if="deSemnat.length"
                variant="warning"
                class="ml-1"
              >
                {{ deSemnat.length }}
              </b-badge>
            </b-button>

            <!-- Pasul care nu se poate lua înapoi: de aceea stă sub buton,
                 nebifat, și se aprinde doar când omul îl cere. -->
            <div
              v-if="poateDepune"
              class="pentru-tiparire mt-50"
              title="După semnare, trimite la ANAF declarațiile semnate în această sesiune, fără să mai fie nevoie de butonul „Depune”"
            >
              <b-form-checkbox
                v-model="depuneDupaSemnare"
                size="sm"
                class="comutator-primar"
              >
                <small
                  class="text-nowrap"
                  :class="depuneDupaSemnare ? 'text-danger' : 'text-muted'"
                >
                  Depune declarațiile semnate
                </small>
              </b-form-checkbox>
            </div>

            <div
              class="pentru-tiparire"
              title="Trimite la imprimanta dumneavoastră toate declarațiile semnate în această sesiune"
            >
              <b-form-checkbox
                v-model="tiparire"
                size="sm"
                class="comutator-primar"
              >
                <small
                  class="text-nowrap"
                  :class="tiparire ? 'text-primary' : 'text-muted'"
                >
                  Imprimă declarațiile semnate
                </small>
              </b-form-checkbox>
            </div>
          </div>
        </b-col>
        <!-- Depunerea la mijlocul spațiului rămas, recipisele lipite de marginea
             din dreapta -->
        <b-col class="d-flex align-items-stretch">
          <div class="d-flex align-items-center justify-content-center flex-grow-1">
            <b-button
              v-if="poateDepune"
              variant="outline-info"
              :disabled="!deDepus.length || depunereInCurs"
              @click="depuneSemnate"
            >
              <b-spinner
                v-if="depunereInCurs"
                small
                class="mr-1"
              />
              <feather-icon
                v-else
                icon="SendIcon"
                size="15"
                class="mr-1"
              />
              Depune declarațiile semnate
              <b-badge
                v-if="deDepus.length"
                variant="warning"
                class="ml-1"
              >
                {{ deDepus.length }}
              </b-badge>
            </b-button>
          </div>

          <!-- Grupul își ia lățimea din conținut, ca bifele cu text lung să nu
               fie strânse peste ce e alături. -->
          <div class="d-flex flex-column justify-content-center flex-shrink-0">
            <!-- Reglajele tipăririi stau deasupra butonului pe care îl privesc,
                 aliniate cu colțul lui din stânga. -->
            <div
              class="pentru-tiparire"
              title="Trimite la imprimanta dumneavoastră toate recipisele aduse în această sesiune"
            >
              <b-form-checkbox
                v-model="tiparireRecipise"
                size="sm"
                class="comutator-verde"
              >
                <small
                  class="text-nowrap"
                  :class="tiparireRecipise ? 'text-success' : 'text-muted'"
                >
                  Imprimare recipise descărcate
                </small>
              </b-form-checkbox>
            </div>

            <!-- Filigranul ajută la sortarea teancului tipărit: fiecare pagină
                 poartă denumirea firmei de care ține. -->
            <div
              class="pentru-tiparire"
              title="Aplică watermark cu denumirea firmei pe recipisele de imprimat"
            >
              <b-form-checkbox
                v-model="filigran"
                size="sm"
                class="comutator-verde"
                :disabled="!tiparireRecipise"
              >
                <small
                  class="text-nowrap"
                  :class="filigran && tiparireRecipise ? 'text-success' : 'text-muted'"
                >
                  Aplică watermark
                </small>
              </b-form-checkbox>
            </div>

            <!-- Apăsarea descarcă acum; din săgeată se alege manual sau automat -->
            <b-dropdown
              split
              variant="outline-success"
              block
              :disabled="recipiseInCurs"
              @click="verificaRecipise()"
            >
              <template #button-content>
                <b-spinner
                  v-if="recipiseInCurs"
                  small
                  class="mr-1"
                />
                <feather-icon
                  v-else
                  icon="DownloadIcon"
                  size="15"
                  class="mr-1"
                />
                <!-- Butonul poartă varianta aleasă, ca să se vadă în ce fel lucrează -->
                Descarcă recipise{{ automat.activ ? ' automat' : '' }}
                <b-badge
                  v-if="deDescarcat.length"
                  variant="warning"
                  class="ml-1"
                >
                  {{ deDescarcat.length }}
                </b-badge>
              </template>

              <b-dropdown-item
                :active="!automat.activ"
                @click="automat.activ = false"
              >
                Descarcă recipise
              </b-dropdown-item>
              <b-dropdown-item
                :active="automat.activ"
                @click="automat.activ = true"
              >
                Descarcă recipise automat
              </b-dropdown-item>
            </b-dropdown>

            <!-- Recipisele apar la ANAF după un timp, nu imediat: intervalul
                 repetării se alege doar când descărcarea e pe automat. -->
            <div class="automat-recipise mt-1">
              <div
                v-if="automat.activ"
                class="d-flex align-items-center justify-content-center"
              >
                <small class="text-success mr-1">la</small>
                <b-form-select
                  v-model.number="automat.minute"
                  size="sm"
                  class="lista-minute"
                  :options="optiuniMinute"
                />
              </div>

              <!-- Când s-a descărcat ultima oară: altfel nu se poate ști dacă
                   repetarea automată chiar lucrează. -->
              <div class="text-center">
                <small class="text-muted">
                  <span v-if="ultimaDescarcare">
                    Ultima descărcare: {{ ultimaDescarcare }} · {{ recipiseAduse }}
                  </span>
                  <span v-else>Nicio descărcare încă</span>
                </small>
              </div>
            </div>
          </div>

        </b-col>
      </b-row>
    </b-card>

    <b-row class="mb-2">
      <b-col md="2">
        <label class="small mb-0">Tip declarație</label>
        <b-form-input
          v-model="filtre.tip"
          size="sm"
          placeholder="ex. D394"
          @change="incarcaLista"
        />
      </b-col>
      <b-col md="2">
        <label class="small mb-0">CUI</label>
        <b-form-input
          v-model="filtre.cui"
          size="sm"
          placeholder="cod fiscal"
          @change="incarcaLista"
        />
      </b-col>
      <b-col md="3">
        <label class="small mb-0">Denumire firmă</label>
        <b-form-input
          v-model="filtre.den_firma"
          size="sm"
          placeholder="parte din denumire"
          @change="incarcaLista"
        />
      </b-col>
      <b-col md="2">
        <label class="small mb-0">Perioada</label>
        <b-row no-gutters>
          <b-col cols="5">
            <b-form-input
              v-model="filtre.luna"
              size="sm"
              type="number"
              min="1"
              max="12"
              placeholder="luna"
              @change="incarcaLista"
            />
          </b-col>
          <b-col
            cols="7"
            class="pl-1"
          >
            <b-form-input
              v-model="filtre.anul"
              size="sm"
              type="number"
              placeholder="anul"
              @change="incarcaLista"
            />
          </b-col>
        </b-row>
      </b-col>
      <b-col md="2">
        <label class="small mb-0">Index încărcare</label>
        <b-form-input
          v-model="filtre.index_recipisa"
          size="sm"
          placeholder="index ANAF"
          @change="incarcaLista"
        />
      </b-col>
      <b-col md="1">
        <label class="small mb-0">Stare</label>
        <b-form-select
          v-model="filtre.pas"
          size="sm"
          :options="optiuniPas"
          @change="incarcaLista"
        />
      </b-col>
    </b-row>

    <div
      v-if="semnareInCurs || depunereMesaj"
      class="mb-2"
    >
      <small class="text-muted">
        <span v-if="semnareInCurs">Se semnează {{ semnareInCurs }}...</span>
        <span v-else>Se depune {{ depunereMesaj }}...</span>
      </small>
    </div>

    <!--
      Cât timp se cercetează se spune la ce declarație s-a ajuns: pentru fiecare
      se întreabă ANAF de starea ei, iar o sesiune cu zeci de declarații ține
      minute. O rotiță singură nu deosebește lucrul de împotmolire.
    -->
    <div
      v-if="recipiseInCurs && mersul"
      class="text-muted mb-2"
    >
      {{ mersul }}
      <b-progress
        v-if="deCercetat"
        :value="cercetate"
        :max="deCercetat"
        variant="success"
        height="6px"
        class="mt-1"
      />
    </div>

    <b-alert
      v-if="eroare"
      show
      variant="danger"
      class="mb-2"
    >
      {{ eroare }}
    </b-alert>

    <b-card
      class="border mb-0"
      body-class="p-1"
    >
      <b-table
        ref="tabel"
        :items="declaratii"
        :fields="campuri"
        :busy="listaInCurs"
        :per-page="pePagina"
        :current-page="pagina"
        responsive
        striped
        small
        show-empty
        empty-text="Nu există declarații pentru filtrul selectat."
        class="tabel-compact mb-0"
      >
        <template #table-busy>
          <div class="text-center my-2">
            <b-spinner class="align-middle mr-1" />
            Se încarcă declarațiile...
          </div>
        </template>

        <!-- Denumirea vine din Entități înrolate; dacă CUI-ul nu e înrolat,
             rămâne cea din declarație și se atrage atenția. -->
        <template #cell(den_firma)="rand">
          <div class="d-flex align-items-center">
            <span>{{ rand.item.den_firma || '-' }}</span>
            <feather-icon
              v-if="!rand.item.inrolata"
              v-b-tooltip.hover.html.right="explicatieNeinrolata(rand.item)"
              icon="AlertTriangleIcon"
              size="15"
              class="text-warning ml-50 flex-shrink-0"
            />
          </div>
        </template>

        <template #cell(perioada)="rand">
          {{ rand.item.luna ? rand.item.luna + '/' + rand.item.anul : rand.item.anul || '-' }}
          <b-badge
            v-if="rand.item.rectificativa"
            variant="warning"
            class="ml-1"
          >
            rectificativă
          </b-badge>
        </template>

        <template #cell(pas)="rand">
          <b-badge :variant="variantaPas(rand.item.pas)">
            {{ etichetaPas(rand.item.pas) }}
          </b-badge>
        </template>

        <!-- Orice eșec, de la validare sau de la semnare. Textul poate fi lung,
             așa că apare întreg la trecerea cu mouse-ul. -->
        <template #cell(eroare)="rand">
          <div
            v-if="rand.item.eroare"
            class="d-flex align-items-center"
          >
            <div
              class="small text-danger"
              :class="desfasurata(rand.item) ? 'coloana-eroare-desfasurata' : 'coloana-eroare'"
              :title="rand.item.eroare"
            >
              {{ peUnRand(rand.item.eroare) }}
            </div>

            <!-- Săgeata arată că textul continuă și îl desfășoară pe loc -->
            <b-button
              v-if="eroareaEsteLunga(rand.item)"
              size="sm"
              variant="flat-secondary"
              class="btn-icon p-0 ml-1 flex-shrink-0"
              :title="desfasurata(rand.item) ? 'Restrânge eroarea' : 'Arată eroarea întreagă'"
              @click="comutaEroarea(rand.item)"
            >
              <feather-icon
                :icon="desfasurata(rand.item) ? 'ChevronUpIcon' : 'ChevronDownIcon'"
                size="16"
              />
            </b-button>

            <!-- Explicația are rost doar la erorile validatorului ANAF; cele de
                 semnare vin de la programul de acces la certificat. -->
            <b-button
              v-if="rand.item.eroare_de_validare"
              size="sm"
              variant="flat-primary"
              class="btn-icon p-0 ml-1 flex-shrink-0"
              title="SPV Wizard — explică eroarea pe înțelesul tuturor"
              @click="explicaEroarea(rand.item)"
            >
              <feather-icon
                icon="ZapIcon"
                size="16"
              />
            </b-button>
          </div>
          <span
            v-else
            class="text-muted"
          >-</span>
        </template>

        <template #cell(index_recipisa)="rand">
          <span
            v-if="rand.item.index_recipisa"
            class="text-nowrap"
          >{{ rand.item.index_recipisa }}</span>
          <span
            v-else
            class="text-muted"
          >-</span>
        </template>

        <template #cell(data_depunere)="rand">
          <span
            v-if="rand.item.data_depunere"
            class="text-nowrap"
          >{{ rand.item.data_depunere }}</span>
          <span
            v-else
            class="text-muted"
          >-</span>
        </template>

        <template #cell(stare_declaratie)="rand">
          <div
            v-if="rand.item.stare_declaratie"
            :class="clasaStare(rand.item.clasificare)"
          >
            {{ rand.item.stare_declaratie }}
          </div>
          <span
            v-else-if="rand.item.index_recipisa"
            class="text-muted"
          >în așteptarea recipisei</span>
          <span v-else>-</span>
        </template>

        <!-- După semnare, certificatul folosit; înainte, cel cu care e înrolată
             firma, adică tokenul spre care va fi trimisă declarația. -->
        <template #cell(certificat_nume)="rand">
          <span v-if="rand.item.certificat_nume">{{ rand.item.certificat_nume }}</span>
          <small
            v-else-if="rand.item.certificat_inrolare"
            class="text-muted"
            title="Certificatul cu care este înrolată firma; cu el se va semna"
          >
            {{ rand.item.certificat_inrolare }}
          </small>
          <span v-else>-</span>
        </template>

        <template #cell(actiuni)="rand">
          <!-- Singura cale de ieșire dintr-o semnare eșuată: altfel declarația
               ar rămâne blocată, fără buton de depunere. -->
          <b-button
            v-if="poateSemna && rand.item.pas === 'eroare_semnare'"
            size="sm"
            variant="outline-primary"
            class="mr-1 mb-1"
            :disabled="ocupat === rand.item.id"
            @click="actiune(rand.item, 'semneaza')"
          >
            Reîncearcă semnarea
          </b-button>
          <b-button
            v-if="poateDepune && rand.item.semnat && !rand.item.index_recipisa"
            size="sm"
            variant="outline-success"
            class="mr-1 mb-1"
            :disabled="ocupat === rand.item.id"
            @click="actiune(rand.item, 'depune')"
          >
            Depune
          </b-button>
          <!-- Documentele pot sta pe server sau doar în arhiva de la client -->
          <b-button
            v-if="rand.item.cale_pdf_semnat || rand.item.arhiva_semnat || rand.item.cale_pdf"
            size="sm"
            variant="outline-secondary"
            class="mr-1 mb-1"
            @click="deschide(rand.item, rand.item.cale_pdf_semnat || rand.item.arhiva_semnat ? 'semnat' : 'pdf')"
          >
            PDF
          </b-button>
          <b-button
            v-if="rand.item.cale_recipisa || rand.item.arhiva_recipisa"
            size="sm"
            variant="outline-info"
            class="mr-1 mb-1"
            @click="deschide(rand.item, 'recipisa')"
          >
            Recipisă
          </b-button>
          <!-- O declarație ajunsă la ANAF nu se mai șterge: rămâne dovada depunerii -->
          <b-button
            v-if="!esteDepusa(rand.item)"
            size="sm"
            variant="outline-danger"
            class="mb-1"
            @click="sterge(rand.item)"
          >
            Șterge
          </b-button>
        </template>
      </b-table>

      <!-- Paginarea apare doar cand nu incape totul pe o pagina. -->
      <div
        v-if="declaratii.length > pePagina"
        class="d-flex align-items-center justify-content-between mt-1"
      >
        <small class="text-muted text-nowrap">
          {{ deLaRand }}–{{ panaLaRand }} din {{ declaratii.length }}
        </small>

        <b-pagination
          v-model="pagina"
          :total-rows="declaratii.length"
          :per-page="pePagina"
          size="sm"
          align="center"
          class="mb-0"
        />

        <b-form-select
          v-model="pePaginaAles"
          :options="marimiPagina"
          size="sm"
          class="selector-pagina"
        />
      </div>
    </b-card>

    <!-- SPV Wizard: erorile validatorului ANAF, traduse pe intelesul oricui -->
    <b-modal
      v-model="explicatieVizibila"
      size="lg"
      ok-only
      ok-title="Am înțeles"
      scrollable
      modal-class="modul-spv"
    >
      <template #modal-title>
        <feather-icon
          icon="ZapIcon"
          size="18"
          class="text-primary mr-50"
        />
        SPV Wizard
        <span class="small text-muted ml-50">ce înseamnă eroarea</span>
      </template>

      <b-alert
        :show="explicatieEroare !== ''"
        variant="danger"
      >
        <div class="alert-body">
          {{ explicatieEroare }}
        </div>
      </b-alert>

      <div v-if="explicatie">
        <p
          v-if="explicatie.rezumat"
          class="mb-2"
        >
          {{ explicatie.rezumat }}
        </p>
        <p
          v-else-if="explicatieTotal"
          class="mb-2 text-muted"
        >
          Validatorul a raportat {{ explicatieTotal }}
          {{ explicatieTotal === 1 ? 'problemă' : 'probleme' }}. Le iau pe rând.
        </p>

        <b-card
          v-for="(problema, i) in explicatie.probleme"
          :key="i"
          class="border mb-1"
          body-class="p-2"
        >
          <div class="d-flex align-items-center mb-1">
            <b-badge :variant="culoareSeveritate(problema.severitate)">
              {{ etichetaSeveritate(problema.severitate) }}
            </b-badge>
            <span
              v-if="problema.camp"
              class="ml-1 font-weight-bold"
            >{{ problema.camp }}</span>
            <b-badge
              v-if="problema.regula"
              variant="light-secondary"
              class="ml-1"
            >
              regula {{ problema.regula }}
            </b-badge>
            <span
              v-if="problema.sectiune"
              class="ml-1 text-muted small"
            >în secțiunea {{ problema.sectiune }}</span>
          </div>

          <!-- Mesajul asa cum l-a scris validatorul: sus, la vedere, ca cine e
               obisnuit cu el sa nu fie nevoit sa-l caute. -->
          <div class="mesaj-original mb-1">
            <span class="small text-muted">Mesajul validatorului ANAF:</span>
            <code class="d-block">{{ problema.mesaj }}</code>
          </div>

          <p
            v-if="problema.explicatie"
            class="mb-1"
          >
            {{ problema.explicatie }}
          </p>
          <p
            v-else
            class="mb-1 text-muted"
          >
            Pentru acest mesaj nu am o explicație pregătită; mai sus este textul original al validatorului.
          </p>

          <div
            v-if="problema.de_corectat || problema.locatie"
            class="mb-1"
          >
            <strong class="small">De corectat:</strong>
            <div
              v-if="problema.de_corectat"
              class="small"
            >
              {{ problema.de_corectat }}
            </div>

            <!-- Locul exact din fisier, ca sa nu fie cautat de mana -->
            <div
              v-if="problema.locatie"
              class="small mt-1"
            >
              <div class="indicatie-fisier">
                Deschide fișierul XML în Notepad++ și apasă <kbd>Ctrl+G</kbd>, apoi mergi la
                <strong>linia {{ problema.locatie.linie }}</strong>,
                coloana {{ problema.locatie.coloana }}.
                <span v-if="problema.locatie.aparitii > 1">
                  Valoarea apare de {{ problema.locatie.aparitii }} ori în fișier; aceasta este cea reclamată.
                </span>
              </div>

              <!-- Randul din fisier: albastru, cu partea gresita in rosu -->
              <div class="rand-xml mt-1">
                <span class="text-muted mr-1">{{ problema.locatie.linie }}</span><code
                  class="xml-rand"
                >{{ problema.locatie.inainte }}<span class="xml-gresit">{{ problema.locatie.potrivire }}</span>{{ problema.locatie.dupa }}</code>
              </div>
              <div
                v-if="problema.locatie.trunchiat"
                class="text-muted"
              >
                Rândul este lung, așa că se vede doar bucata din jurul valorii.
              </div>
            </div>
          </div>

          <div
            v-if="problema.cauta"
            class="mb-1"
          >
            <strong class="small">Caută în fișierul XML:</strong>
            <code class="ml-1">{{ problema.cauta }}</code>
          </div>
        </b-card>

        <!-- Cât timp mai vin rezultate, se spune la ce se lucrează -->
        <div
          v-if="explicatieInCurs"
          class="d-flex align-items-center text-muted my-2"
        >
          <b-spinner
            small
            class="mr-1"
          />
          <span v-if="explicatieTotal">
            Lucrez la problema {{ explicatie.probleme.length + 1 }} din {{ explicatieTotal }}...
          </span>
          <span v-else>Citesc erorile validatorului...</span>
        </div>
      </div>
    </b-modal>
  </div>
</template>

<script>
import citesteFluxul, { areFlux } from '@/libs/flux'

export default {
  name: 'SpvDeclaratii',
  data() {
    return {
      fisiere: [],
      dinFolder: [],
      declaratii: [],
      // Câte fișiere se trimit deodată la validare (vezi trimiteInGrupuri)
      FISIERE_DEODATA: 3,
      explicatie: null,
      explicatieVizibila: false,
      explicatieInCurs: false,
      // Câte probleme a anunțat serverul că are de prelucrat
      explicatieTotal: 0,
      explicatieEroare: '',
      // Ce declarație se semnează chiar acum, după încărcare
      semnareInCurs: '',
      /*
       * Drepturile omului în firma aleasă, cerute de la server. Ascunderea din
       * interfață e doar înlesnire; refuzul adevărat vine tot de la server.
       */
      poateSemna: false,
      poateDepune: false,
      // Semnarea de la sine a ce tocmai s-a validat
      semneazaDupaValidare: false,
      // La sfârșitul prelucrării, un singur fișier cu tot ce s-a semnat
      tiparire: true,
      /*
       * Depunerea de la sine a ce s-a semnat acum. Nebifată din start: la ANAF
       * nu se depune din greșeală, iar depunerea nu se poate retrage.
       */
      depuneDupaSemnare: false,
      // La fel, pentru recipisele aduse într-o rulare
      tiparireRecipise: true,
      // Denumirea firmei, scrisă în filigran pe fiecare pagină de recipisă
      filigran: true,
      depunereInCurs: false,
      depunereMesaj: '',
      // Descărcarea repetată a recipiselor, cu intervalul ales de utilizator
      automat: { activ: false, minute: 10 },
      optiuniMinute: [
        { value: 5, text: '5 minute' },
        { value: 10, text: '10 minute' },
        { value: 15, text: '15 minute' },
        { value: 30, text: '30 minute' },
        { value: 60, text: '60 minute' },
      ],
      cronometru: null,
      ultimaDescarcare: '',
      ultimaDescarcareNumar: 0,
      // Id-urile declarațiilor la care eroarea e arătată întreagă
      eroriDesfasurate: [],
      eroare: '',
      listaInCurs: false,
      incarcaInCurs: false,
      recipiseInCurs: false,
      // Mersul lucrului, cat timp se cerceteaza: „7 din 42 — D112 15208744"
      mersul: '',
      cercetate: 0,
      deCercetat: 0,
      ocupat: null,
      filtre: {
        tip: '', cui: '', den_firma: '', luna: '', anul: '', index_recipisa: '', pas: null,
      },
      optiuniPas: [
        { value: null, text: 'Toate' },
        // „Încărcate" și „Validate" sunt pași de trecere: după încărcare,
        // declarațiile ajung singure la semnat sau la eroare de validare.
        // Fisierele din dosarul urmarit care n-au putut fi citite deloc
        { value: 'eroare_preluare', text: 'Nu au putut fi citite' },
        { value: 'eroare_validare', text: 'Erori de validare' },
        { value: 'eroare_semnare', text: 'Erori la semnare' },
        { value: 'semnat', text: 'Semnate' },
        { value: 'depus', text: 'Depuse' },
        { value: 'finalizat', text: 'Finalizate' },
        { value: 'eroare_depunere', text: 'Erori la depunere' },
      ],
      campuri: [
        { key: 'tip', label: 'Tip' },
        { key: 'cui', label: 'CUI' },
        { key: 'den_firma', label: 'Denumire' },
        { key: 'perioada', label: 'Perioada' },
        { key: 'pas', label: 'Stare flux' },
        { key: 'eroare', label: 'Eroare' },
        { key: 'index_recipisa', label: 'Index încărcare' },
        { key: 'data_depunere', label: 'Depusă la' },
        { key: 'stare_declaratie', label: 'Rezultat ANAF' },
        { key: 'certificat_nume', label: 'Semnat cu' },
        { key: 'actiuni', label: 'Acțiuni' },
      ],
      pagina: 1,
      // „auto" înseamnă câte rânduri încap pe ecran fără derulare.
      pePaginaAles: 'auto',
      pePaginaAuto: 15,
      marimiPagina: [
        { value: 'auto', text: 'cât încape' },
        { value: 10, text: '10 / pagină' },
        { value: 25, text: '25 / pagină' },
        { value: 50, text: '50 / pagină' },
        { value: 100, text: '100 / pagină' },
      ],
    }
  },
  computed: {
    /** Fișierele selectate direct și cele din folder, doar XML și PDF. */
    deIncarcat() {
      return [...(this.fisiere || []), ...(this.dinFolder || [])]
        .filter(fisier => /\.(xml|pdf)$/i.test(fisier.name))
    },
    /**
     * Declarațiile semnate care încă n-au ajuns la ANAF.
     *
     * Se ia din lista afișată, deci filtrele active se aplică și aici: se
     * depune ce se vede, nu ce nu se vede.
     */
    deDepus() {
      return this.declaratii.filter(d => d.semnat && !d.index_recipisa)
    },
    /**
     * Declarațiile trecute de validare care așteaptă semnătura.
     *
     * Ca și la depunere, se ia din lista afișată: se semnează ce se vede.
     */
    deSemnat() {
      return this.declaratii.filter(d => d.pas === 'validat')
    },
    /** Declarațiile depuse a căror recipisă n-a fost încă descărcată. */
    deDescarcat() {
      return this.declaratii.filter(d => d.index_recipisa && !d.cale_recipisa && !d.arhiva_recipisa)
    },
    /** Ce a adus ultima descărcare, scris pe scurt. */
    recipiseAduse() {
      if (!this.ultimaDescarcareNumar) return 'fără recipise noi'

      return this.ultimaDescarcareNumar === 1 ? 'o recipisă' : `${this.ultimaDescarcareNumar} recipise`
    },
    /**
     * Butonul lucrează cât ține oricare dintre pași — inclusiv depunerea
     * pornită de bifa de sub el, ca să nu se înceapă un lot nou peste ea.
     */
    lucreaza() {
      return this.incarcaInCurs || !!this.semnareInCurs || this.depunereInCurs
    },
    /**
     * Ce pas se face acum.
     *
     * Încărcarea și validarea se fac în aceeași cerere — serverul validează
     * fiecare fișier pe măsură ce îl primește — deci ele se aprind împreună.
     */
    pasulActiv() {
      if (this.semnareInCurs) return 'semneaza'

      return this.incarcaInCurs ? 'incarca' : ''
    },
    /** După încărcare, primii doi pași rămân bifați cât ține semnarea. */
    incarcaTerminat() {
      return !!this.semnareInCurs
    },
    /** Câte rânduri se arată: fie alegerea omului, fie cât încape pe ecran. */
    pePagina() {
      return this.pePaginaAles === 'auto' ? this.pePaginaAuto : this.pePaginaAles
    },
    /** Numărul primului rând de pe pagina curentă. */
    deLaRand() {
      return this.declaratii.length ? (this.pagina - 1) * this.pePagina + 1 : 0
    },
    /** Numărul ultimului rând de pe pagina curentă. */
    panaLaRand() {
      return Math.min(this.pagina * this.pePagina, this.declaratii.length)
    },
  },
  watch: {
    // După filtrare sau reîncărcare, pagina veche poate să nu mai existe.
    declaratii() {
      const ultima = Math.max(1, Math.ceil(this.declaratii.length / this.pePagina))

      if (this.pagina > ultima) this.pagina = ultima

      // Abia acum există rânduri de măsurat, ca să se știe câte încap.
      this.$nextTick(this.masoaraPagina)
    },
    pePagina() {
      this.pagina = 1
    },
    // Orice schimbare a setării repornește cronometrul și o ține minte.
    'automat.activ': function reporneste() {
      this.salveazaSetarea()
      this.reglaCronometrul()
    },
    'automat.minute': function reporneste() {
      this.salveazaSetarea()
      this.reglaCronometrul()
    },
  },
  created() {
    this.incarcaDrepturile()
    this.incarcaSetarea()
    this.incarcaLista()
    this.reglaCronometrul()
  },
  mounted() {
    this.masoaraPagina()
    window.addEventListener('resize', this.masoaraPagina)
  },
  beforeDestroy() {
    // Fără asta, cronometrul ar continua să ceară recipise după plecarea din filă.
    this.opresteCronometrul()
    window.removeEventListener('resize', this.masoaraPagina)
  },
  methods: {
    /**
     * Câte rânduri încap pe ecran sub tabel, fără derulare.
     *
     * Se măsoară un rând adevărat, nu o valoare presupusă: înălțimea depinde de
     * temă, de zoom-ul din browser și de ce scrie în coloana de eroare.
     */
    masoaraPagina() {
      const tabel = this.$refs.tabel && this.$refs.tabel.$el

      if (!tabel) return

      const rand = tabel.querySelector('tbody tr')
      const antet = tabel.querySelector('thead')

      const inaltimeRand = rand ? rand.getBoundingClientRect().height : 0
      const inaltimeAntet = antet ? antet.getBoundingClientRect().height : 0

      // Sub tabel mai stau bara de paginare și marginea de jos a paginii.
      const REZERVA = 70
      const disponibil = window.innerHeight
        - tabel.getBoundingClientRect().top
        - inaltimeAntet
        - REZERVA

      const incap = Math.floor(disponibil / (inaltimeRand || 31))

      // Sub cinci rânduri tabelul nu mai spune nimic; mai bine se derulează.
      this.pePaginaAuto = Math.max(5, incap)
    },
    /**
     * Ce înseamnă lipsa firmei din Entități înrolate și ce are de făcut omul.
     *
     * Textul e lung pentru un tooltip, dar aici stă tot ce ar trebui altfel
     * căutat prin altă filă: de ce apare, ce merge totuși și cum se rezolvă.
     */
    explicatieNeinrolata(declaratie) {
      const cui = declaratie.cui || 'acest CUI'

      return [
        '<div class="text-left">',
        '<b>Firma nu este înrolată</b><br>',
        `CUI-ul <b>${cui}</b> nu apare în <i>Entități înrolate</i>, adică niciun certificat `,
        'din aplicație nu are drept de reprezentare la ANAF pentru el.',
        '<hr class="my-50">',
        '<b>Denumirea</b> afișată este cea scrisă în declarație, nu cea de la ANAF, ',
        'deci poate fi prescurtată sau greșită. Tot ea ajunge și pe watermarkul recipisei.',
        '<hr class="my-50">',
        '<b>Validarea și semnarea</b> se pot face. <b>Depunerea</b> va fi respinsă de SPV.',
        '<hr class="my-50">',
        '<b>De făcut:</b> depuneți la ANAF formularul 150 pentru acest CUI, ',
        'iar după aprobare apăsați <i>Inițializează / actualizează lista</i> în fila ',
        '<i>Entități înrolate</i>. ',
        'Dacă firma e deja înrolată cu alt certificat, adăugați acel certificat în ',
        'fila <i>Certificate digitale</i>.',
        '</div>',
      ].join('')
    },
    incarcaSetarea() {
      try {
        const salvat = JSON.parse(window.localStorage.getItem('declaratii_recipise_automat'))

        if (salvat && typeof salvat.minute === 'number' && salvat.minute > 0) {
          // O valoare salvată înainte poate să nu fie printre cele din listă;
          // atunci lista ar rămâne goală, așa că se ia cea mai apropiată.
          const permise = this.optiuniMinute.map(o => o.value)
          const minute = permise.indexOf(salvat.minute) !== -1
            ? salvat.minute
            : permise.reduce((a, b) => (Math.abs(b - salvat.minute) < Math.abs(a - salvat.minute) ? b : a))

          this.automat = { activ: !!salvat.activ, minute }
        }
      } catch (e) {
        // setare veche sau stricată — se rămâne pe valorile implicite
      }

      const brut = window.localStorage.getItem('declaratii_recipise_ultima') || ''

      try {
        const ultima = JSON.parse(brut)
        this.ultimaDescarcare = ultima.la || ''
        this.ultimaDescarcareNumar = ultima.descarcate || 0
      } catch (e) {
        // Forma veche păstra doar momentul, ca text simplu.
        this.ultimaDescarcare = brut
        this.ultimaDescarcareNumar = 0
      }
    },
    /** Momentul de acum, scris ca peste tot în modul: zz.ll.aaaa hh:mm:ss. */
    acum() {
      const d = new Date()
      const doua = n => String(n).padStart(2, '0')

      return `${doua(d.getDate())}.${doua(d.getMonth() + 1)}.${d.getFullYear()} `
        + `${doua(d.getHours())}:${doua(d.getMinutes())}:${doua(d.getSeconds())}`
    },
    salveazaSetarea() {
      window.localStorage.setItem('declaratii_recipise_automat', JSON.stringify(this.automat))
    },
    opresteCronometrul() {
      if (this.cronometru) {
        window.clearInterval(this.cronometru)
        this.cronometru = null
      }
    },
    reglaCronometrul() {
      this.opresteCronometrul()

      const minute = Number(this.automat.minute)

      if (!this.automat.activ || !minute || minute < 1) {
        return
      }

      this.cronometru = window.setInterval(() => {
        // Se cere ANAF-ului doar când chiar e ceva de adus: fără recipise în
        // așteptare, o interogare la fiecare interval ar fi în gol. Și nu se
        // suprapune peste o descărcare pornită de mână sau peste ea însăși.
        if (!this.recipiseInCurs && this.deDescarcat.length) {
          this.verificaRecipise(true)
        }
      }, minute * 60 * 1000)
    },
    // Fisierele stau pe discul privat (storage/app), deci se cer prin API (cu
    // token) si se deschid dintr-un blob local, nu printr-un link catre /storage.
    deschide(declaratie, tip) {
      this.eroare = ''

      this.$http.get(`/declaratii/${declaratie.id}/fisier/${tip}`, { responseType: 'blob' })
        .then(raspuns => {
          const url = window.URL.createObjectURL(new Blob([raspuns.data], { type: 'application/pdf' }))
          window.open(url, '_blank')
          setTimeout(() => window.URL.revokeObjectURL(url), 60000)
        })
        .catch(() => {
          this.eroare = 'Fișierul nu a putut fi deschis.'
        })
    },
    etichetaPas(pas) {
      const etichete = {
        incarcat: 'Încărcată',
        validat: 'Validată',
        eroare_preluare: 'Nu a putut fi citită',
        eroare_validare: 'Eroare validare',
        eroare_semnare: 'Eroare semnare',
        semnat: 'Semnată',
        depus: 'Depusă',
        finalizat: 'Finalizată',
        eroare_depunere: 'Eroare depunere',
      }
      return etichete[pas] || pas
    },
    variantaPas(pas) {
      if (pas === 'finalizat') return 'success'
      if (pas === 'depus' || pas === 'semnat') return 'info'
      if (pas === 'validat') return 'primary'
      if (pas && pas.indexOf('eroare') === 0) return 'danger'
      return 'secondary'
    },
    clasaStare(clasificare) {
      if (clasificare === 'valid') return 'text-success'
      if (clasificare === 'invalid') return 'text-danger'
      if (clasificare === 'valid_cu_atentionari') return 'text-warning'
      return 'text-muted'
    },
    incarcaLista() {
      this.listaInCurs = true

      // Se trimit doar filtrele completate, ca serverul să nu le trateze pe
      // cele goale ca pe o căutare după șir vid.
      const params = {}
      Object.keys(this.filtre).forEach(cheie => {
        const valoare = this.filtre[cheie]

        if (valoare !== null && valoare !== '') {
          params[cheie] = valoare
        }
      })

      this.$http.get('/declaratii', { params })
        .then(raspuns => {
          this.declaratii = raspuns.data.data || []
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Nu s-au putut încărca declarațiile')
        })
        .finally(() => {
          this.listaInCurs = false
        })
    },
    // Din folder se rețin doar declarațiile, restul fișierelor se ignoră.
    numeFisiere(fisiere) {
      const declaratii = fisiere.filter(f => /\.(xml|pdf)$/i.test(f.name))

      if (declaratii.length === 1) return declaratii[0].name

      return `${declaratii.length} declarații din ${fisiere.length} fișiere`
    },
    /**
     * Ce are voie omul să facă aici.
     *
     * Se cere de la server, nu din localStorage: drepturile se pot schimba, iar
     * bifele care le privesc n-au ce căuta pe ecranul cuiva care nu le are.
     */
    incarcaDrepturile() {
      this.$http.get('/context')
        .then(({ data }) => {
          this.poateSemna = !!data.data.poate_semna
          this.poateDepune = !!data.data.poate_depune
        })
        .catch(() => {
          this.poateSemna = false
          this.poateDepune = false
        })
        .then(() => {
          // Bifele rămase fără drept nu trebuie să tragă după ele nicio acțiune.
          if (!this.poateSemna) this.semneazaDupaValidare = false
          if (!this.poateDepune) this.depuneDupaSemnare = false
        })
    },
    /**
     * Trimite fișierele în grupuri care merg deodată.
     *
     * Într-o singură cerere, serverul le lua unul câte unul, iar fiecare trece
     * prin validatorul ANAF — un program Java care pornește din nou pentru
     * fiecare fișier și ține câteva secunde. Zece declarații însemnau astfel
     * minute de așteptare, cu un singur fir de lucru ocupat. Trimise câte trei
     * deodată, serverul le validează în paralel.
     *
     * Mai mult de atât n-are rost: validatorul mănâncă procesor, iar cererile
     * peste puterea serverului s-ar aștepta oricum una pe alta.
     */
    trimiteInGrupuri(fisiere, cateOdata) {
      const rezultate = []
      const erori = []

      const unul = fisier => {
        const formular = new FormData()
        formular.append('fisiere[]', fisier)

        return this.$http.post('/declaratii', formular, { headers: { 'Content-Type': 'multipart/form-data' } })
          .then(raspuns => {
            rezultate.push(...(raspuns.data.data || []))
            erori.push(...(raspuns.data.erori || []))
          })
          .catch(err => {
            const date = err.response && err.response.data

            if (date && date.erori && date.erori.length) {
              erori.push(...date.erori)
            } else {
              erori.push(`${fisier.name}: ${this.mesajEroare(err, 'nu a putut fi încărcat')}`)
            }
          })
      }

      // Grupurile pleacă unul după altul, dar fișierele dintr-un grup, deodată.
      let lant = Promise.resolve()

      for (let i = 0; i < fisiere.length; i += cateOdata) {
        const grup = fisiere.slice(i, i + cateOdata)

        lant = lant.then(() => Promise.all(grup.map(unul)))
      }

      return lant.then(() => ({ rezultate, erori }))
    },
    incarca() {
      this.eroare = ''
      this.incarcaInCurs = true

      this.trimiteInGrupuri(this.deIncarcat.slice(), this.FISIERE_DEODATA)
        .then(({ rezultate: incarcate, erori }) => {
          this.fisiere = []
          this.dinFolder = []

          const respinse = incarcate.filter(d => d.pas === 'eroare_validare').length
          const dejaSemnate = incarcate.filter(d => d.pas === 'semnat').length

          const parti = [`${incarcate.length} încărcate`]
          if (dejaSemnate) parti.push(`${dejaSemnate} veneau deja semnate`)
          if (respinse) parti.push(`${respinse} respinse la validare`)

          this.notifica(parti.join(', '), respinse || erori.length ? 'warning' : 'success')

          if (erori.length) {
            this.eroare = erori.join(' | ')
          }

          // Cele validate se semnează acum doar dacă s-a cerut prin bifă; cele
          // care veneau deja semnate (PDF-uri semnate în altă parte) rămân cum
          // sunt, dar intră la depunere: sunt și ele semnate în această sesiune.
          const deSemnatAcum = this.semneazaDupaValidare && this.poateSemna
            ? incarcate.filter(d => d.pas === 'validat')
            : []

          return this.semneazaValidate(
            deSemnatAcum,
            incarcate.filter(d => d.pas === 'semnat'),
          )
        })
        .catch(err => {
          const date = err.response && err.response.data
          this.eroare = date && date.erori && date.erori.length
            ? date.erori.join(' | ')
            : this.mesajEroare(err, 'Nu s-au putut încărca declarațiile')
        })
        .finally(() => {
          this.incarcaInCurs = false
          this.incarcaLista()
        })
    },

    /**
     * Semnează, una câte una, declarațiile tocmai validate.
     *
     * Semnarea se face pe rând, nu deodată: fiecare trece prin tokenul de pe
     * calculatorul cu certificatul, iar cererile paralele s-ar încurca la
     * dialogul de PIN. O semnare eșuată nu le oprește pe următoarele —
     * declarația rămâne validată și poate fi semnată din tabel.
     *
     * @param {Array} declaratii     cele validate, de semnat acum
     * @param {Array} venitesemnate  cele încărcate deja semnate, doar de depus
     */
    /**
     * Semnează declarațiile valide rămase în tabel.
     *
     * E aceeași lucrare ca după încărcare, doar că pornită de om, peste tot ce
     * s-a strâns nesemnat — nu doar peste lotul din sesiunea de acum.
     */
    semneazaDinTabel() {
      const declaratii = this.deSemnat

      if (!declaratii.length) {
        return Promise.resolve()
      }

      this.eroare = ''

      return this.semneazaValidate(declaratii).then(() => this.incarcaLista())
    },

    semneazaValidate(declaratii, venitesemnate = []) {
      if (!declaratii.length) {
        return this.depuneDinSesiune(venitesemnate)
      }

      const esuate = []
      const reusite = []

      const urmatoarea = i => {
        if (i >= declaratii.length) {
          if (esuate.length) {
            this.eroare = `Nu s-au putut semna: ${esuate.join(' | ')}`
          }

          this.notifica(
            `${declaratii.length - esuate.length} din ${declaratii.length} semnate`,
            esuate.length ? 'warning' : 'success',
          )

          return null
        }

        const declaratie = declaratii[i]
        this.semnareInCurs = `${declaratie.tip} (${i + 1} din ${declaratii.length})`

        return this.$http.post(`/declaratii/${declaratie.id}/semneaza`)
          .then(() => {
            reusite.push(declaratie)
          })
          .catch(err => {
            esuate.push(`${declaratie.tip} ${declaratie.cui}: ${this.mesajEroare(err, 'semnare eșuată')}`)
          })
          .then(() => urmatoarea(i + 1))
      }

      return urmatoarea(0).then(() => {
        this.semnareInCurs = ''

        // Se adună doar cele semnate acum, nu tot ce e semnat în tabel.
        if (this.tiparire && reusite.length) {
          this.semnareInCurs = 'fișierul pentru tipărire'

          return this.descarcaPentruTiparire(reusite.map(d => d.id)).then(() => {
            this.semnareInCurs = ''
          })
        }

        return null
      }).then(() => this.depuneDinSesiune([...venitesemnate, ...reusite]))
    },

    /**
     * Depunerea de la sine, când bifa de sub buton e pusă.
     *
     * Nu se mai cere confirmare: bifa e chiar consimțământul, dată de fiecare
     * dată de la zero. Pleacă doar declarațiile din această sesiune; restul
     * celor semnate în tabel rămân pe butonul „Depune”.
     */
    depuneDinSesiune(declaratii) {
      if (!this.depuneDupaSemnare || !this.poateDepune || !declaratii.length) {
        return Promise.resolve()
      }

      return this.trimiteLaAnaf(declaratii)
    },

    /**
     * Trimite documentele la imprimanta utilizatorului.
     *
     * Hârtia iese pe calculatorul lui, nu se mai descarcă niciun fișier. Dacă
     * n-are imprimantă aleasă sau calculatorul e închis, se cade pe descărcare:
     * mai bine un fișier de tipărit de mână decât nimic după o semnare reușită.
     *
     * @param {string} tip 'semnat' pentru declarații, 'recipisa' pentru recipise
     */
    descarcaPentruTiparire(ids, tip = 'semnat') {
      // Filigranul cu denumirea firmei se pune deocamdată doar pe recipise.
      const date = { id: ids, tip, filigran: tip === 'recipisa' && this.filigran }
      const ceAnume = tip === 'recipisa' ? 'Recipisele' : 'Declarațiile'

      return this.$http.post('/declaratii/concateneaza', { ...date, tipareste: true })
        .then(({ data }) => {
          this.$bvToast.toast(
            `${data.data.documente} documente trimise la „${data.data.imprimanta}”.`,
            { title: 'Trimis la imprimantă', variant: 'success' },
          )
        })
        .catch(err => {
          const motiv = err.response && err.response.data && err.response.data.message
            ? err.response.data.message
            : 'Programul local nu a răspuns.'

          this.eroare = `${ceAnume} sunt gata, dar tipărirea nu a reușit: ${motiv} Se descarcă fișierul.`

          return this.descarcaFisierUnit(date, ids.length)
        })
    },

    /** Varianta de rezervă: un singur PDF, salvat local. */
    descarcaFisierUnit(date, cate) {
      const numeFisier = date.tip === 'recipisa' ? `recipise_${cate}.pdf` : `declaratii_semnate_${cate}.pdf`

      return this.$http.post('/declaratii/concateneaza', date, { responseType: 'blob' })
        .then(raspuns => {
          const url = window.URL.createObjectURL(new Blob([raspuns.data], { type: 'application/pdf' }))
          const legatura = document.createElement('a')

          legatura.href = url
          legatura.download = numeFisier
          document.body.appendChild(legatura)
          legatura.click()
          document.body.removeChild(legatura)

          setTimeout(() => window.URL.revokeObjectURL(url), 60000)
        })
        .catch(() => {
          this.eroare = 'Nici fișierul pentru tipărire nu a putut fi creat.'
        })
    },

    /**
     * Depune, una câte una, toate declarațiile semnate din listă.
     *
     * Se cere confirmarea o dată, dar cu firmele numite: depunerea la ANAF nu
     * se poate lua înapoi, iar aici pleacă mai multe declarații dintr-o
     * singură apăsare.
     */
    depuneSemnate() {
      const declaratii = this.deDepus

      if (!declaratii.length) {
        return
      }

      const cate = declaratii.length
      const numite = cate === 1 ? 'o declarație semnată' : `${cate} declarații semnate`

      const firme = [...new Set(declaratii.map(d => d.den_firma || d.cui))]
      const listate = firme.slice(0, 4).join(', ') + (firme.length > 4 ? ' și altele' : '')

      this.$bvModal.msgBoxConfirm(
        `Se trimit acum la ANAF ${numite} (${listate}). După trimitere nu mai pot fi retrase.`,
        { okTitle: 'Depune', cancelTitle: 'Renunță', okVariant: 'danger' },
      ).then(confirmat => {
        if (!confirmat) {
          return null
        }

        return this.trimiteLaAnaf(declaratii)
      })
    },

    /** Trimiterea propriu-zisă: una câte una, un eșec nu le oprește pe următoarele. */
    trimiteLaAnaf(declaratii) {
      this.eroare = ''
      this.depunereInCurs = true

      const esuate = []

      const urmatoarea = i => {
        if (i >= declaratii.length) {
          if (esuate.length) {
            this.eroare = `Nu s-au putut depune: ${esuate.join(' | ')}`
          }

          this.notifica(
            `${declaratii.length - esuate.length} din ${declaratii.length} depuse`,
            esuate.length ? 'warning' : 'success',
          )

          return null
        }

        const declaratie = declaratii[i]
        this.depunereMesaj = `${declaratie.tip} (${i + 1} din ${declaratii.length})`

        return this.$http.post(`/declaratii/${declaratie.id}/depune`)
          .catch(err => {
            esuate.push(`${declaratie.tip} ${declaratie.cui}: ${this.mesajEroare(err, 'depunere eșuată')}`)
          })
          .then(() => urmatoarea(i + 1))
      }

      return urmatoarea(0).then(() => {
        this.depunereInCurs = false
        this.depunereMesaj = ''
        this.incarcaLista()
      })
    },

    actiune(declaratie, tip) {
      this.eroare = ''
      this.ocupat = declaratie.id

      const mesaje = {
        semneaza: 'Declarația a fost semnată',
        depune: 'Declarația a fost depusă la ANAF',
      }

      this.$http.post(`/declaratii/${declaratie.id}/${tip}`)
        .then(() => {
          this.notifica(mesaje[tip], 'success')
          this.incarcaLista()
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Operația a eșuat')
          this.incarcaLista()
        })
        .finally(() => {
          this.ocupat = null
        })
    },
    /**
     * @param {boolean} tacut pornit de cronometru: anunță doar când chiar a
     *                        descărcat ceva, ca să nu bombardeze utilizatorul
     *                        cu mesaje „0 recipise" la fiecare interval.
     */
    verificaRecipise(tacut = false) {
      this.eroare = ''
      this.recipiseInCurs = true
      this.mersul = ''
      this.cercetate = 0
      this.deCercetat = 0

      this.cereRecipisele()
        .then(rezultat => {
          this.ultimaDescarcare = this.acum()
          this.ultimaDescarcareNumar = rezultat.descarcate || 0

          window.localStorage.setItem('declaratii_recipise_ultima', JSON.stringify({
            la: this.ultimaDescarcare,
            descarcate: this.ultimaDescarcareNumar,
          }))

          if (!tacut || rezultat.descarcate > 0) {
            this.notifica(
              `Verificate: ${rezultat.verificate}, recipise descărcate: ${rezultat.descarcate}`,
              rezultat.descarcate > 0 ? 'success' : 'info',
            )
          }
          if (rezultat.erori && rezultat.erori.length) {
            this.eroare = rezultat.erori.join(' | ')
          }

          this.incarcaLista()

          // Recipisele venite acum se pot aduna într-un singur fișier de tipărit.
          const aduse = rezultat.descarcate_id || []

          if (this.tiparireRecipise && aduse.length) {
            return this.descarcaPentruTiparire(aduse, 'recipisa')
          }

          return null
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Descărcarea recipiselor a eșuat')
        })
        .finally(() => {
          this.recipiseInCurs = false
          this.mersul = ''
          this.cercetate = 0
          this.deCercetat = 0
        })
    },
    /**
     * Cere recipisele, spunând la fiecare a câta declarație e din câte.
     *
     * Răspunsul curge — câte un rând pe măsură ce se lucrează —, așa că omul
     * vede mersul, nu doar o rotiță: pentru fiecare declarație se întreabă ANAF
     * de starea ei, iar recipisa găsită se aduce prin tokenul clientului. O
     * sesiune cu zeci de declarații ține minute, iar din afară lucrul și
     * împotmolirea arată la fel.
     *
     * @returns {Promise<object>} rezultatul, în forma răspunsului obișnuit
     */
    cereRecipisele() {
      // Browserele fără fetch cu flux rămân pe calea dinainte, fără numărătoare.
      if (!areFlux()) {
        return this.$http.post('/declaratii/recipise').then(raspuns => raspuns.data.data)
      }

      let rezultat = {
        verificate: 0,
        descarcate: 0,
        descarcate_id: [],
        erori: [],
      }

      return citesteFluxul('declaratii/recipise/flux', pas => {
        if (pas.tip === 'inceput') {
          this.deCercetat = pas.total
          this.cercetate = 0

          if (pas.total) this.mersul = `0 din ${pas.total} declarații cercetate...`

          return
        }

        if (pas.tip === 'pas') {
          this.cercetate = pas.facute

          const care = pas.ce ? ` — ${pas.ce}` : ''
          const adusa = pas.adus ? ' (recipisă adusă)' : ''

          this.mersul = `${pas.facute} din ${pas.total} declarații cercetate${care}${adusa}`

          return
        }

        if (pas.tip === 'gata') rezultat = pas
      }).then(() => rezultat)
    },
    sterge(declaratie) {
      this.$bvModal.msgBoxConfirm(`Ștergeți declarația ${declaratie.tip} pentru CUI ${declaratie.cui}?`, {
        okTitle: 'Șterge',
        cancelTitle: 'Renunță',
        okVariant: 'danger',
      }).then(confirmat => {
        if (!confirmat) return

        this.$http.delete(`/declaratii/${declaratie.id}`)
          .then(() => {
            this.notifica('Declarația a fost ștearsă', 'success')
            this.incarcaLista()
          })
          .catch(err => {
            this.eroare = this.mesajEroare(err, 'Ștergerea a eșuat')
          })
      })
    },
    mesajEroare(err, implicit) {
      return err.response && err.response.data && err.response.data.message
        ? err.response.data.message
        : implicit
    },
    /**
     * Erorile validatorului vin pe mai multe rânduri; în tabel se arată ca un
     * text continuu, ca să nu rămână doar antetul de secțiune vizibil.
     */
    peUnRand(text) {
      return (text || '').replace(/\s*\r?\n\s*/g, ' · ').trim()
    },
    /**
     * Săgeata apare doar când textul chiar nu încape pe un rând. Pragul e
     * estimat după lățimea coloanei, nu măsurat: o săgeată în plus la o eroare
     * scurtă ar fi doar zgomot.
     */
    eroareaEsteLunga(declaratie) {
      return this.peUnRand(declaratie.erori_validare).length > 45
    },
    desfasurata(declaratie) {
      return this.eroriDesfasurate.indexOf(declaratie.id) !== -1
    },
    comutaEroarea(declaratie) {
      const pozitie = this.eroriDesfasurate.indexOf(declaratie.id)

      if (pozitie === -1) {
        this.eroriDesfasurate.push(declaratie.id)
      } else {
        this.eroriDesfasurate.splice(pozitie, 1)
      }
    },
    /** A ajuns la ANAF: fie pasul o spune, fie există deja un index de încărcare. */
    esteDepusa(declaratie) {
      return ['depus', 'finalizat'].indexOf(declaratie.pas) !== -1 || !!declaratie.index_recipisa
    },
    culoareSeveritate(severitate) {
      if (severitate === 'blocant') return 'danger'

      return severitate === 'atentionare' ? 'info' : 'warning'
    },
    etichetaSeveritate(severitate) {
      if (severitate === 'blocant') return 'Blochează validarea'

      return severitate === 'atentionare' ? 'Atenționare' : 'Eroare'
    },
    /**
     * Cere serverului traducerea erorilor pentru declarația aleasă.
     *
     * Răspunsul curge pe bucăți (câte un obiect JSON pe rând), așa că fiecare
     * eroare se arată de îndată ce sosește, fără să se aștepte terminarea
     * tuturor. Se folosește fetch, nu axios: numai el dă acces la conținut pe
     * măsură ce vine.
     */
    explicaEroarea(declaratie) {
      this.explicatie = { rezumat: '', probleme: [] }
      this.explicatieTotal = 0
      this.explicatieInCurs = true
      this.explicatieVizibila = true

      this.explicatieEroare = ''

      const antete = this.$http.defaults.headers.common
      // Adresa API vine din window.api_url (definit în pagină); instanța axios
      // nu are baseURL propriu, așa că nu se poate citi de acolo.
      const baza = (window.api_url || this.$http.defaults.baseURL || '').replace(/\/+$/, '')
      const adresa = `${baza}/declaratii/${declaratie.id}/erori`

      fetch(adresa, {
        headers: {
          // „application/json" trebuie să apară: fără el, la o sesiune expirată
          // Laravel redirecționează spre login în loc să răspundă 401, iar
          // fluxul ar părea gol în loc de eșuat.
          Accept: 'application/json, application/x-ndjson',
          Authorization: antete.Authorization,
          AuthorizationHeader: antete.AuthorizationHeader,
        },
      })
        .then(raspuns => {
          if (!raspuns.ok) throw new Error(`HTTP ${raspuns.status}`)

          return this.citesteFluxul(raspuns.body.getReader())
        })
        .catch(err => {
          // Eșecul se arată în fereastră, nu în spatele ei: altfel utilizatorul
          // vede doar o fereastră goală și nu știe ce s-a întâmplat.
          this.explicatieInCurs = false
          this.explicatieEroare = `Explicația nu a putut fi obținută (${err.message}).`
        })
    },

    /** Citește răspunsul rând cu rând și adaugă fiecare problemă cum sosește. */
    citesteFluxul(cititor) {
      const decodor = new TextDecoder('utf-8')
      let ramas = ''

      const urmatorul = () => cititor.read().then(({ done, value }) => {
        if (done) {
          this.explicatieInCurs = false

          return null
        }

        ramas += decodor.decode(value, { stream: true })

        const randuri = ramas.split('\n')
        // Ultimul poate fi incomplet: rămâne pentru bucata următoare.
        ramas = randuri.pop()

        randuri.forEach(rand => this.adaugaPas(rand))

        return urmatorul()
      })

      return urmatorul()
    },

    adaugaPas(rand) {
      if (!rand.trim()) return

      let pas

      try {
        pas = JSON.parse(rand)
      } catch (e) {
        return
      }

      if (pas.tip === 'inceput') {
        this.explicatieTotal = pas.total
      } else if (pas.tip === 'problema') {
        this.explicatie.probleme.push(pas.data)
      } else if (pas.tip === 'gata') {
        this.explicatie.rezumat = pas.rezumat
        this.explicatie.netradus = pas.netradus
        this.explicatieInCurs = false
      }
    },
    notifica(mesaj, variant) {
      this.$bvToast.toast(mesaj, { title: 'Declarații ANAF', variant, solid: true })
    },
  },
}
</script>

<style scoped>
/*
 * Eroarea ocupa un singur rand in tabel; sageata de alaturi arata ca textul
 * continua si il desfasoara pe loc. Textul se taie fara puncte de suspensie,
 * fiindca sageata spune deja ca urmeaza.
 */
.coloana-eroare {
  max-width: 280px;
  min-width: 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: clip;
  /* Se stinge spre capat, ca taietura sa nu para o litera lipsa */
  mask-image: linear-gradient(to right, black 85%, transparent 100%);
  -webkit-mask-image: linear-gradient(to right, black 85%, transparent 100%);
}

.coloana-eroare-desfasurata {
  max-width: 280px;
  min-width: 0;
  white-space: normal;
}

/*
 * Randuri stranse: incap mai multe declaratii pe ecran, fara derulare.
 * Celulele sunt desenate de componenta de tabel, nu de sablonul acesta, deci
 * regulile trebuie sa treaca dincolo de limita ei (::v-deep).
 */
.tabel-compact ::v-deep th,
.tabel-compact ::v-deep td {
  padding: 0.3rem 0.4rem !important;
  vertical-align: middle;
  font-size: 0.85rem;
}

.tabel-compact ::v-deep .badge {
  padding: 0.25rem 0.4rem;
}

.tabel-compact ::v-deep .btn {
  padding: 0.15rem 0.4rem;
  font-size: 0.75rem;
}

/* Cate randuri pe pagina: cat sa incapa „100 / pagina", nu mai mult. */
.selector-pagina {
  width: 8rem;
  height: 1.7rem;
  padding: 0 1.2rem 0 0.4rem;
  line-height: 1.2;
  background-position: right 0.35rem center;
}

/* Campul de minute: doar cat sa incapa un numar de trei cifre, scrise la mijloc. */
/* Lista de intervale: doar cat sa incapa „60 minute", scunda ca sa nu ingroase randul. */
.lista-minute {
  width: 6.5rem;
  height: 1.6rem;
  padding: 0 1.2rem 0 0.4rem;
  line-height: 1.2;
  background-position: right 0.35rem center;
}

/*
 * Comutatorul se face verde doar cand e pornit, ca si textul de langa el:
 * stins, ramane cenusiu. Bulina si sina lui sunt desenate de componenta, deci
 * regulile trebuie sa treaca dincolo de ea.
 */
.comutator-verde ::v-deep .custom-control-input:checked ~ .custom-control-label::before {
  background-color: #28c76f;
  border-color: #28c76f;
}

.comutator-verde ::v-deep .custom-control-input:focus ~ .custom-control-label::before {
  box-shadow: 0 0 0 0.15rem rgba(40, 199, 111, 0.35);
}

/* Comutatorul de langa butonul de prelucrare poarta culoarea lui. */
.comutator-primar ::v-deep .custom-control-input:checked ~ .custom-control-label::before {
  background-color: #7367f0;
  border-color: #7367f0;
}

.comutator-primar ::v-deep .custom-control-input:focus ~ .custom-control-label::before {
  box-shadow: 0 0 0 0.15rem rgba(115, 103, 240, 0.35);
}

/*
 * Comutatoarele stau langa butoane, fara chenar: sunt reglaje, nu actiuni, si
 * nu trebuie sa concureze cu butoanele. Textul e mic si sters cand e stins.
 */
/*
 * Bifa incepe fix in dreptul coltului din stanga al butonului si sta lipita de
 * el: se citesc ca un singur lucru, reglajul si actiunea pe care o priveste.
 */
.pentru-tiparire {
  padding: 0;
  margin-bottom: 0.15rem;
  line-height: 1.1;
}

/* Textul incepe imediat dupa bifa sau comutator, fara golul lasat de tema. */
.pentru-tiparire ::v-deep .custom-switch {
  padding-left: 3rem;
}

.pentru-tiparire ::v-deep .custom-checkbox {
  padding-left: 1.35rem;
}

.pentru-tiparire small,
.automat-recipise small {
  font-size: 0.72rem;
}

/* Textul incepe imediat dupa comutator: tema lasa 0,5rem in plus fata de el. */
.pentru-tiparire ::v-deep .custom-switch {
  padding-left: 3rem;
}

/*
 * Bara care desparte cele trei parti ale lucrului: incarcare, depunere si
 * recipise. Se intinde pe toata inaltimea randului, nu doar cat elementul,
 * iar continutul fiecarei parti sta la mijlocul spatiului ei.
 */

/* Explicatia se rupe pe randuri, ca sa nu iasa din chenar. */
.pentru-tiparire small {
  white-space: normal;
  overflow-wrap: anywhere;
  word-break: break-word;
}

/* Reglajul descarcarii automate: fara chenar, doar strans sub buton. */
.automat-recipise {
  padding: 0.1rem 0.2rem;
}

/* Butonul cu optiuni pastreaza latimea intreaga, ca cel simplu de dinainte. */
.flex-shrink-0 ::v-deep .dropdown-toggle-split {
  padding-left: 0.5rem;
  padding-right: 0.5rem;
}

/* Acelasi lucru la comutatorul verde: fara gol intre el si text. */
.automat-recipise ::v-deep .custom-switch {
  padding-left: 3rem;
}

/* Cei trei pasi din buton stau stransi, ca butonul sa nu creasca peste masura. */
.buton-pasi div {
  line-height: 1.5;
}

/* Mesajul brut al validatorului: se deosebeste de explicatie, fara sa o acopere. */
.mesaj-original {
  padding: 0.25rem 0.5rem;
  border-left: 3px solid rgba(130, 134, 139, 0.4);
  background: rgba(130, 134, 139, 0.08);
  border-radius: 0.2rem;
  overflow-x: auto;
}

.mesaj-original code {
  white-space: pre-wrap;
  word-break: break-word;
}

/* Randul din XML: se deruleaza pe orizontala, nu rupe fereastra. */
.rand-xml {
  overflow-x: auto;
  white-space: pre;
  padding: 0.25rem 0.5rem;
  border-left: 3px solid rgba(115, 103, 240, 0.5);
  background: rgba(115, 103, 240, 0.06);
  border-radius: 0.2rem;
}

/* Randul se scrie cu albastru, iar bucata gresita cu rosu, ca sa sara in ochi. */
.xml-rand {
  color: #1565c0;
}

.xml-gresit {
  color: #d32f2f;
  font-weight: 600;
  background: rgba(211, 47, 47, 0.1);
  border-radius: 0.15rem;
}

/*
 * Indicatia de deschidere a fisierului: verde si mai mare decat textul din jur,
 * fiind lucrul pe care utilizatorul il cauta cu ochii cand vrea sa corecteze.
 */
.indicatie-fisier {
  color: #2e7d32;
  font-size: 1rem;
  font-weight: 500;
  line-height: 1.4;
}

/* Combinatia de taste, pe fundal albastru: se vede ca e de apasat, nu de citit. */
.indicatie-fisier kbd {
  background: #1565c0;
  color: #fff;
  font-weight: 600;
  padding: 0.1rem 0.35rem;
  border-radius: 0.2rem;
}
</style>
