<template>
  <div>
    <b-alert
      v-if="eroare"
      show
      variant="danger"
      class="py-2"
    >
      {{ eroare }}
    </b-alert>

    <b-tabs
      content-class="pt-1"
      @input="tabSchimbat"
    >
      <b-tab
        title="Clienții aplicației"
        active
      >
        <b-card
          class="border mb-2"
          body-class="p-2"
        >
          <div class="d-flex align-items-center justify-content-end">
            <div>
              <b-button
                variant="flat-secondary"
                size="sm"
                class="mr-1"
                @click="deschideIstoric"
              >
                <feather-icon
                  icon="ClockIcon"
                  class="mr-50"
                />Istoric notificări
              </b-button>
              <b-button
                variant="outline-primary"
                size="sm"
                class="mr-1"
                @click="deschideNotificare(null)"
              >
                <feather-icon
                  icon="SendIcon"
                  class="mr-50"
                />Trimite notificare
              </b-button>
              <b-button
                variant="primary"
                size="sm"
                @click="deschideClientNou"
              >
                <feather-icon
                  icon="PlusIcon"
                  class="mr-50"
                />Client nou
              </b-button>
            </div>
          </div>
        </b-card>

        <b-card
          class="border mb-0"
          body-class="p-1"
        >
          <b-table
            :items="clienti"
            :fields="campuri"
            :busy="listaInCurs"
            responsive
            striped
            small
            show-empty
            empty-text="Niciun client înregistrat."
            class="tabel-compact mb-0"
          >
            <template #table-busy>
              <div class="text-center my-2">
                <b-spinner class="align-middle mr-1" />
                Se încarcă...
              </div>
            </template>

            <template #cell(client)="rand">
              <div>{{ rand.item.denumire }}</div>
              <div class="small text-muted">
                {{ rand.item.cui || 'fără CUI' }}
              </div>
            </template>

            <template #cell(stare)="rand">
              <b-badge :variant="variantaStare(rand.item)">
                {{ textStare(rand.item) }}
              </b-badge>
              <div
                v-if="rand.item.abonament && rand.item.abonament.zile_ramase !== null"
                class="small text-muted"
              >
                {{ rand.item.abonament.zile_ramase >= 0
                  ? rand.item.abonament.zile_ramase + ' zile rămase'
                  : 'expirat de ' + (-rand.item.abonament.zile_ramase) + ' zile' }}
              </div>
            </template>

            <template #cell(tarif)="rand">
              <span v-if="rand.item.abonament">{{ rand.item.abonament.tarif_lunar }} lei/lună</span>
              <span
                v-else
                class="text-muted"
              >—</span>
            </template>

            <template #cell(module)="rand">
              <div v-if="rand.item.abonament">
                <b-badge
                  v-for="modul in moduleActive(rand.item)"
                  :key="modul"
                  variant="light-primary"
                  class="mr-25"
                >
                  {{ modul }}
                </b-badge>
                <span
                  v-if="!moduleActive(rand.item).length"
                  class="small text-muted"
                >niciun modul</span>
              </div>
              <span
                v-else
                class="small text-muted"
              >nelimitat</span>
            </template>

            <template #cell(utilizatori)="rand">
              <div
                v-for="user in rand.item.utilizatori"
                :key="user.id"
                class="d-flex align-items-center small py-25"
              >
                <feather-icon
                  v-if="user.administrator"
                  v-b-tooltip.hover
                  icon="ShieldIcon"
                  size="13"
                  class="text-primary mr-50"
                  title="Administrator al firmei"
                />
                <span :class="user.blocat ? 'text-danger' : ''">{{ user.email }}</span>
                <b-badge
                  v-if="user.blocat"
                  variant="light-danger"
                  class="ml-50"
                >
                  blocat
                </b-badge>

                <!-- Modulele la care ajunge omul acesta. Se văd aici, în tabel,
                 fiindcă altfel ar trebui deschisă fereastra fiecărui cont ca să
                 se afle cine cu ce lucrează. -->
                <b-badge
                  v-for="nume in numeleModulelor(user.module)"
                  :key="nume"
                  variant="light-info"
                  class="ml-50"
                >
                  {{ nume }}
                </b-badge>
                <span
                  v-if="!numeleModulelor(user.module).length"
                  class="ml-50 text-muted"
                >fără module</span>

                <b-button
                  size="sm"
                  variant="flat-secondary"
                  class="btn-icon ml-auto"
                  title="Modifică"
                  @click="deschideUtilizator(rand.item, user)"
                >
                  <feather-icon
                    icon="Edit2Icon"
                    size="13"
                  />
                </b-button>
                <b-button
                  size="sm"
                  variant="flat-warning"
                  class="btn-icon"
                  title="Deconectează acum"
                  @click="deconecteaza(user)"
                >
                  <feather-icon
                    icon="LogOutIcon"
                    size="13"
                  />
                </b-button>
              </div>

              <b-button
                size="sm"
                variant="outline-primary"
                class="mt-50"
                @click="deschideUtilizator(rand.item, null)"
              >
                Cont nou
              </b-button>
            </template>

            <template #cell(actiuni)="rand">
              <!-- Unul sub altul, la aceeași lățime: coloana rămâne îngustă. -->
              <b-button
                size="sm"
                variant="outline-primary"
                class="d-block butoane-actiuni mb-50"
                @click="deschideAbonament(rand.item)"
              >
                Abonament
              </b-button>
              <!-- Periodicitățile declarațiilor, aduse din programul vechi al clientului -->
              <b-button
                size="sm"
                variant="outline-primary"
                class="d-block butoane-actiuni mb-50"
                title="Importă periodicitățile declarațiilor din vector.mde (programul vechi al clientului)"
                @click="deschideImportVector(rand.item)"
              >
                Import vector
              </b-button>
              <!-- Istoricul depunerilor din programul vechi, cu declarațiile și recipisele lor -->
              <b-button
                size="sm"
                variant="outline-primary"
                class="d-block butoane-actiuni mb-50"
                title="Importă istoricul depunerilor din declmf.mde (programul vechi al clientului)"
                @click="deschideImportDeclaratii(rand.item)"
              >
                Import declarații
              </b-button>
              <b-button
                size="sm"
                variant="flat-secondary"
                class="btn-icon"
                title="Trimite o notificare utilizatorilor acestui client"
                @click="deschideNotificare(rand.item)"
              >
                <feather-icon icon="SendIcon" />
              </b-button>
            </template>
          </b-table>
        </b-card>
      </b-tab>

      <!-- Cat depune fiecare client cu aplicatia si cand a folosit-o ultima oara -->
      <b-tab
        title="Statistici"
        lazy
      >
        <b-card
          class="border mb-0"
          body-class="p-1"
        >
          <b-table
            :items="statistici"
            :fields="campuriStatistici"
            :busy="statisticiInCurs"
            responsive
            striped
            small
            show-empty
            empty-text="Nicio depunere înregistrată."
            class="tabel-compact mb-0"
          >
            <template #table-busy>
              <div class="text-center my-2">
                <b-spinner class="align-middle mr-1" />
                Se încarcă...
              </div>
            </template>

            <template #cell(client)="rand">
              <div>{{ rand.item.denumire }}</div>
              <div class="small text-muted">
                {{ rand.item.cui || 'fără CUI' }}
              </div>
            </template>

            <template #cell(total)="rand">
              <div>{{ rand.item.declaratii }} declarații</div>
              <div class="small text-muted">
                {{ rand.item.cuiuri }} CUI-uri unice
              </div>
            </template>

            <template #cell(luna_curenta)="rand">
              <div>{{ rand.item.declaratii_luna_curenta }} declarații</div>
              <div class="small text-muted">
                {{ rand.item.cuiuri_luna_curenta }} CUI-uri
              </div>
            </template>

            <template #cell(luna_anterioara)="rand">
              <div>{{ rand.item.declaratii_luna_anterioara }} declarații</div>
              <div class="small text-muted">
                {{ rand.item.cuiuri_luna_anterioara }} CUI-uri
              </div>
            </template>

            <template #cell(ultima_depunere)="rand">
              {{ rand.item.ultima_depunere || '—' }}
            </template>

            <template #cell(ultima_accesare)="rand">
              {{ rand.item.ultima_accesare || '—' }}
            </template>
          </b-table>
        </b-card>
      </b-tab>
    </b-tabs>

    <!--
      Importul vectorului din programul vechi: fisierul vector.mde al
      clientului, cu tabelul vectormf — cate un rand pe firma, cate o coloana
      pe declaratie, periodicitatea in ea. Randurile intra ca „manuale" si au
      intaietate in raportul de depunere.
    -->
    <b-modal
      v-model="importVectorVizibil"
      :title="`Import vector — ${importVectorClient ? importVectorClient.denumire : ''}`"
      :ok-title="importVectorInCurs ? 'Se importă...' : 'Importă'"
      cancel-title="Renunță"
      :ok-disabled="!importVectorFisier || importVectorInCurs"
      @ok.prevent="importaVector"
    >
      <p class="text-muted small">
        Alegeți fișierul <strong>vector.mde</strong> din programul vechi al clientului
        (sau tabelul <code>vectormf</code> exportat ca CSV). Periodicitățile intră ca
        rânduri „manuale" pe fiecare CUI și au întâietate în raportul de depunere.
        Reimportul nu dublează nimic.
      </p>

      <b-form-file
        v-model="importVectorFisier"
        accept=".mde,.mdb,.accdb,.csv"
        placeholder="Alegeți fișierul..."
        browse-text="Răsfoiește"
      />

      <b-alert
        v-if="importVectorEroare"
        show
        variant="danger"
        class="py-1 px-2 mt-1 mb-0"
      >
        {{ importVectorEroare }}
      </b-alert>

      <b-alert
        v-if="importVectorRezultat"
        show
        variant="success"
        class="py-1 px-2 mt-1 mb-0"
      >
        {{ importVectorRezultat.firme }} firme citite,
        {{ importVectorRezultat.scrise }} periodicități scrise.
        <div
          v-if="importVectorRezultat.sarite.length"
          class="small mt-50"
        >
          Coloane fără nume de declarație, sărite (de adăugat manual dacă contează):
          <ul class="mb-0 pl-1">
            <li
              v-for="sarita in importVectorRezultat.sarite"
              :key="sarita"
            >{{ sarita }}</li>
          </ul>
        </div>
      </b-alert>
    </b-modal>

    <!--
      Istoricul depunerilor din programul vechi (declmf.mde), cu tabelul
      depuneri — câte un rând pe depunere, cu căile declarației și recipisei pe
      calculatorul clientului. Rândurile intră în Declarații fiscale ca istoric
      încheiat, iar fișierele se copiază în arhivă prin programul local.
    -->
    <b-modal
      v-model="importDeclVizibil"
      :title="`Import declarații — ${importDeclClient ? importDeclClient.denumire : ''}`"
      :ok-title="importDeclInCurs ? 'Se importă...' : 'Importă'"
      cancel-title="Renunță"
      :ok-disabled="!importDeclFisier || importDeclInCurs"
      @ok.prevent="importaDeclaratii"
    >
      <p class="text-muted small">
        Alegeți fișierul <strong>declmf.mde</strong> din programul vechi al clientului
        (sau tabelul <code>depuneri</code> exportat ca CSV). Depunerile intră în
        „Declarații fiscale" ca istoric încheiat, firmele înrolate fără denumire o
        primesc din tabel, iar declarațiile și recipisele — aflate pe calculatorul
        clientului — se copiază în arhivă în fundal, prin programul local.
        Reimportul nu dublează nimic.
      </p>

      <b-form-file
        v-model="importDeclFisier"
        accept=".mde,.mdb,.accdb,.csv"
        placeholder="Alegeți fișierul..."
        browse-text="Răsfoiește"
      />

      <b-alert
        v-if="importDeclEroare"
        show
        variant="danger"
        class="py-1 px-2 mt-1 mb-0"
      >
        {{ importDeclEroare }}
      </b-alert>

      <b-alert
        v-if="importDeclRezultat"
        show
        variant="success"
        class="py-1 px-2 mt-1 mb-0"
      >
        {{ importDeclRezultat.randuri }} rânduri citite:
        {{ importDeclRezultat.scrise }} declarații noi,
        {{ importDeclRezultat.existente }} deja existente,
        {{ importDeclRezultat.respinse }} respinse de ANAF (nu se importă),
        {{ importDeclRezultat.denumiri }} denumiri de firmă completate.
        <span v-if="importDeclRezultat.sterse">
          {{ importDeclRezultat.sterse }} rânduri respinse, aduse de un import mai vechi, au fost șterse.
        </span>
        <div
          v-if="importDeclRezultat.de_arhivat"
          class="small mt-50"
        >
          Copierea în arhivă a {{ importDeclRezultat.de_arhivat }} depuneri a pornit
          în fundal, prin programul local al clientului — mersul se vede în Jurnal.
        </div>
        <div
          v-if="importDeclRezultat.sarite.length"
          class="small mt-50"
        >
          Rânduri sărite:
          <ul class="mb-0 pl-1">
            <li
              v-for="sarita in importDeclRezultat.sarite"
              :key="sarita"
            >{{ sarita }}</li>
          </ul>
        </div>
      </b-alert>
    </b-modal>

    <!-- Client nou, cu primul lui cont de administrator -->
    <b-modal
      v-model="clientVizibil"
      title="Client nou"
      ok-title="Creează"
      cancel-title="Renunță"
      @ok.prevent="salveazaClient"
    >
      <b-alert
        v-if="eroareFormular"
        show
        variant="danger"
        class="py-1 px-2 small"
      >
        {{ eroareFormular }}
      </b-alert>

      <label>Denumirea firmei</label>
      <b-form-input
        v-model="clientNou.denumire"
        class="mb-2"
      />

      <label>CUI</label>
      <b-form-input
        v-model="clientNou.cui"
        class="mb-2"
      />

      <hr>
      <p class="text-muted small">
        Primul cont al clientului este administratorul firmei: el va putea crea
        mai departe conturi pentru colegi.
      </p>

      <label>Numele persoanei</label>
      <b-form-input
        v-model="clientNou.nume"
        class="mb-2"
      />

      <label>Email (cu el se autentifică)</label>
      <b-form-input
        v-model="clientNou.email"
        type="email"
        class="mb-2"
      />

      <label>Parolă (minimum 8 caractere)</label>
      <b-form-input
        v-model="clientNou.parola"
        type="text"
        class="mb-2"
      />

      <label>Perioadă de probă (zile)</label>
      <b-form-input
        v-model.number="clientNou.proba_zile"
        type="number"
        min="0"
        max="365"
      />
    </b-modal>

    <!-- Contul unui client: creare sau modificare -->
    <b-modal
      v-model="utilizatorVizibil"
      :title="utilizator.id ? 'Contul ' + utilizator.email : 'Cont nou'"
      ok-title="Salvează"
      cancel-title="Renunță"
      @ok.prevent="salveazaUtilizator"
    >
      <b-alert
        v-if="eroareFormular"
        show
        variant="danger"
        class="py-1 px-2 small"
      >
        {{ eroareFormular }}
      </b-alert>

      <label>Nume</label>
      <b-form-input
        v-model="utilizator.nume"
        class="mb-2"
      />

      <label>Email</label>
      <b-form-input
        v-model="utilizator.email"
        type="email"
        class="mb-2"
      />

      <label>Telefon</label>
      <b-form-input
        v-model="utilizator.telefon"
        class="mb-2"
      />

      <label>{{ utilizator.id ? 'Parolă nouă (gol = neschimbată)' : 'Parolă' }}</label>
      <b-form-input
        v-model="utilizator.parola"
        type="text"
        class="mb-2"
      />

      <label>Adrese IP de la care are voie să se conecteze</label>
      <b-form-textarea
        v-model="utilizator.ip_permise"
        rows="2"
        placeholder="Lăsat gol: de oriunde. Ex: 86.120.4.15, 192.168.1.0/24, 79.112.*"
        class="mb-1"
      />
      <div
        v-if="ipCurent"
        class="mb-1"
      >
        <small class="text-muted">
          Adresa de la care lucrați acum: <code>{{ ipCurent }}</code>
        </small>
      </div>
      <small class="text-muted d-block mb-2">
        Gol înseamnă de oriunde. La o încercare de la altă adresă, contul e
        respins, iar dumneavoastră primiți un email cu cine și de unde a încercat.
      </small>

      <b-form-checkbox
        v-model="utilizator.administrator"
        class="mb-1"
      >
        Administrator în firma clientului
      </b-form-checkbox>

      <b-form-checkbox
        v-if="utilizator.id"
        v-model="utilizator.blocat"
        class="mb-2"
      >
        Blocat (nu se mai poate autentifica; sesiunile deschise se închid)
      </b-form-checkbox>

      <hr>

      <!-- Modulele hotărăsc ce vede omul în meniu. Fără niciunul, ar intra
           într-o aplicație goală, așa că bifele pornesc de la abonament. -->
      <label>Module la care are acces</label>
      <b-form-checkbox-group
        v-model="utilizator.module"
        stacked
        class="mb-1"
      >
        <b-form-checkbox
          v-for="modul in moduleAplicatie"
          :key="modul.cheie"
          :value="modul.cheie"
          class="mb-25"
        >
          {{ modul.nume }}
          <span
            v-if="!modulDinAbonament(modul)"
            class="small text-danger"
          >— în afara abonamentului firmei</span>
          <small class="text-muted d-block">{{ modul.descriere }}</small>
        </b-form-checkbox>
      </b-form-checkbox-group>
      <small class="text-muted d-block">
        Modulele nebifate nu se văd: nici în antet, nici la o cerere trimisă
        de-a dreptul. Ce ține de un modul — pagini, meniu — merge după el.
      </small>
    </b-modal>

    <!-- Înștiințare către utilizatori: în aplicație și/sau pe email -->
    <b-modal
      v-model="notificareVizibila"
      title="Trimite o notificare"
      size="lg"
      ok-title="Trimite"
      cancel-title="Renunță"
      :ok-disabled="trimitereInCurs"
      @ok.prevent="trimiteNotificare"
    >
      <b-alert
        v-if="eroareFormular"
        show
        variant="danger"
        class="py-1 px-2 small"
      >
        {{ eroareFormular }}
      </b-alert>

      <b-alert
        v-if="rezultatTrimitere"
        show
        :variant="rezultatTrimitere.esecuri.length ? 'warning' : 'success'"
        class="py-1 px-2 small"
      >
        Trimisă către {{ rezultatTrimitere.destinatari }} utilizatori
        ({{ rezultatTrimitere.emailuri }} pe email).
        <span v-if="rezultatTrimitere.esecuri.length">
          Emailul nu a plecat către: {{ rezultatTrimitere.esecuri.join(', ') }}.
          Notificarea îi așteaptă totuși în aplicație.
        </span>
      </b-alert>

      <label>Cui</label>
      <b-form-radio-group
        v-model="notificare.destinatari"
        class="mb-2"
      >
        <b-form-radio value="client">
          Utilizatorii unui client
        </b-form-radio>
        <b-form-radio value="utilizatori">
          Anumiți utilizatori
        </b-form-radio>
        <b-form-radio value="toti">
          Toată lumea
        </b-form-radio>
      </b-form-radio-group>

      <b-form-select
        v-if="notificare.destinatari === 'client'"
        v-model="notificare.company_id"
        :options="optiuniClienti"
        class="mb-2"
      />

      <div
        v-if="notificare.destinatari === 'utilizatori'"
        class="mb-2 lista-destinatari"
      >
        <b-form-checkbox-group v-model="notificare.utilizatori">
          <div
            v-for="client in clienti"
            :key="client.id"
            class="mb-1"
          >
            <div class="small text-muted">
              {{ client.denumire }}
            </div>
            <b-form-checkbox
              v-for="user in client.utilizatori"
              :key="user.id"
              :value="user.id"
              class="d-block"
            >
              {{ user.email }}
            </b-form-checkbox>
          </div>
        </b-form-checkbox-group>
      </div>

      <label>Titlu</label>
      <b-form-input
        v-model="notificare.titlu"
        class="mb-2"
      />

      <label>Mesaj</label>
      <b-form-textarea
        v-model="notificare.mesaj"
        rows="5"
        class="mb-2"
      />

      <b-row>
        <b-col cols="6">
          <label>Importanță</label>
          <b-form-select
            v-model="notificare.importanta"
            :options="[
              { value: 'informare', text: 'Informare' },
              { value: 'avertizare', text: 'Avertizare' },
              { value: 'urgenta', text: 'Urgentă' },
            ]"
          />
        </b-col>
        <b-col cols="6">
          <label>Pe unde</label>
          <b-form-checkbox
            v-model="notificare.in_aplicatie"
            class="mt-50"
          >
            În aplicație
          </b-form-checkbox>
          <b-form-checkbox v-model="notificare.pe_email">
            Pe email
          </b-form-checkbox>
        </b-col>
      </b-row>

      <hr>
      <b-form-checkbox v-model="notificare.confirma_citirea">
        Anunță-mă când o citește cineva
      </b-form-checkbox>
      <small class="text-muted d-block">
        Primiți câte o înștiințare pentru fiecare destinatar care confirmă citirea.
        La trimiterile către mulți oameni, urmăriți mai bine numărul din
        <i>Istoric notificări</i>.
      </small>
    </b-modal>

    <!-- Ce s-a trimis si cine a citit -->
    <b-modal
      v-model="istoricVizibil"
      title="Istoric notificări"
      size="lg"
      ok-only
      ok-title="Închide"
    >
      <b-alert
        v-if="!istoric.length && !istoricInCurs"
        show
        variant="light"
        class="py-1 px-2 small"
      >
        Nu s-a trimis încă nicio notificare.
      </b-alert>

      <div
        v-if="istoricInCurs"
        class="text-center my-2"
      >
        <b-spinner />
      </div>

      <b-card
        v-for="lot in istoric"
        :key="lot.lot || lot.titlu + lot.trimisa_la"
        class="border mb-1"
        body-class="p-1"
      >
        <div class="d-flex align-items-start">
          <div class="flex-grow-1">
            <div class="font-weight-bolder">
              {{ lot.titlu }}
            </div>
            <small class="text-muted">
              {{ lot.trimisa_la }}
              <span v-if="lot.trimis_de_nume"> · {{ lot.trimis_de_nume }}</span>
              <span v-if="lot.pe_email"> · și pe email</span>
            </small>
          </div>
          <b-badge
            :variant="lot.citite === lot.destinatari ? 'light-success' : 'light-warning'"
            class="flex-shrink-0"
          >
            citită de {{ lot.citite }} din {{ lot.destinatari }}
          </b-badge>
          <b-button
            size="sm"
            variant="flat-secondary"
            class="btn-icon flex-shrink-0 ml-50"
            @click="$set(desfasurate, lot.lot || lot.titlu, !desfasurate[lot.lot || lot.titlu])"
          >
            <feather-icon
              :icon="desfasurate[lot.lot || lot.titlu] ? 'ChevronUpIcon' : 'ChevronDownIcon'"
            />
          </b-button>
        </div>

        <div
          v-if="desfasurate[lot.lot || lot.titlu]"
          class="mt-1"
        >
          <div
            v-for="rand in lot.lista"
            :key="rand.id"
            class="d-flex align-items-center small py-25 border-top"
          >
            <feather-icon
              :icon="rand.citita_la ? 'CheckCircleIcon' : 'ClockIcon'"
              size="14"
              :class="rand.citita_la ? 'text-success mr-50' : 'text-muted mr-50'"
            />
            <span>{{ rand.email }}</span>
            <span class="ml-auto text-muted">
              {{ rand.citita_la ? 'citită la ' + rand.citita_la : 'necitită' }}
            </span>
            <feather-icon
              v-if="rand.eroare_email"
              v-b-tooltip.hover
              icon="MailIcon"
              size="14"
              class="text-danger ml-50"
              :title="'Emailul nu a plecat: ' + rand.eroare_email"
            />
          </div>
        </div>
      </b-card>
    </b-modal>

    <!-- Tariful, proba, plata si modulele -->
    <b-modal
      v-model="abonamentVizibil"
      :title="'Abonamentul clientului ' + (clientCurent.denumire || '')"
      ok-title="Salvează"
      cancel-title="Renunță"
      @ok.prevent="salveazaAbonament"
    >
      <b-alert
        v-if="eroareFormular"
        show
        variant="danger"
        class="py-1 px-2 small"
      >
        {{ eroareFormular }}
      </b-alert>

      <label>Tarif lunar (lei)</label>
      <b-form-input
        v-model.number="abonament.tarif_lunar"
        type="number"
        min="0"
        step="0.01"
        class="mb-2"
      />

      <b-row>
        <b-col cols="6">
          <label>Perioadă de probă (zile)</label>
          <b-form-input
            v-model.number="abonament.proba_zile"
            type="number"
            min="0"
            max="365"
          />
          <small class="text-muted">Schimbarea numărului pornește proba de azi.</small>
        </b-col>
        <b-col cols="6">
          <label>Proba ține până la</label>
          <b-form-input
            v-model="abonament.proba_pana_la"
            type="date"
          />
        </b-col>
      </b-row>

      <label class="mt-2">Abonament plătit până la</label>
      <b-form-input
        v-model="abonament.platit_pana_la"
        type="date"
        class="mb-2"
      />
      <small class="text-muted d-block mb-2">
        După ce trec și proba, și data plății, modulele se închid. Datele rămân.
      </small>

      <hr>
      <label>Module</label>
      <b-form-checkbox
        v-model="abonament.modul_spv"
        class="mb-50"
      >
        ANAF Spațiu Privat Virtual
      </b-form-checkbox>
      <b-form-checkbox
        v-model="abonament.modul_etransport"
        class="mb-50"
      >
        e-Transport
      </b-form-checkbox>
      <b-form-checkbox
        v-model="abonament.modul_portal_just"
        class="mb-2"
      >
        Portal Just
      </b-form-checkbox>

      <hr>
      <b-form-checkbox
        v-model="abonament.blocat"
        class="mb-2"
      >
        Oprit (indiferent de plată sau probă)
      </b-form-checkbox>

      <b-form-input
        v-if="abonament.blocat"
        v-model="abonament.motiv_blocare"
        placeholder="Motivul, așa cum îl va vedea clientul"
        class="mb-2"
      />

      <label>Observații</label>
      <b-form-textarea
        v-model="abonament.observatii"
        rows="2"
      />
    </b-modal>
  </div>
</template>

<script>
/*
 * Administrarea clienților aplicației. Pagina e vizibilă doar contului din
 * config('app.super_admin'); serverul refuză oricum rutele pentru altcineva,
 * dar și meniul se ascunde, ca să nu apară butoane fără rost.
 */
export default {
  name: 'Administrare',
  data() {
    return {
      clienti: [],
      listaInCurs: false,
      ipCurent: '',
      eroare: '',
      eroareFormular: '',

      clientVizibil: false,
      clientNou: {},

      // Importul vectorului din programul vechi (vector.mde)
      importVectorVizibil: false,
      importVectorInCurs: false,
      importVectorClient: null,
      importVectorFisier: null,
      importVectorEroare: '',
      importVectorRezultat: null,

      // Statistici despre folosirea aplicatiei, pe client
      statistici: [],
      statisticiInCurs: false,
      statisticiIncarcate: false,
      campuriStatistici: [
        { key: 'client', label: 'Client' },
        { key: 'total', label: 'Depuse cu aplicația' },
        { key: 'luna_curenta', label: 'Luna curentă' },
        { key: 'luna_anterioara', label: 'Luna anterioară' },
        { key: 'ultima_depunere', label: 'Ultima depunere' },
        { key: 'ultima_accesare', label: 'Ultima accesare' },
      ],

      // Importul depunerilor din programul vechi (declmf.mde)
      importDeclVizibil: false,
      importDeclInCurs: false,
      importDeclClient: null,
      importDeclFisier: null,
      importDeclEroare: '',
      importDeclRezultat: null,

      utilizatorVizibil: false,
      utilizator: {},
      clientCurent: {},
      // Modulele aplicației, de bifat pentru fiecare cont
      moduleAplicatie: [],

      abonamentVizibil: false,
      abonament: {},

      notificareVizibila: false,
      notificare: {},
      trimitereInCurs: false,
      rezultatTrimitere: null,

      istoricVizibil: false,
      istoricInCurs: false,
      istoric: [],
      desfasurate: {},

      campuri: [
        { key: 'client', label: 'Client' },
        { key: 'stare', label: 'Stare' },
        { key: 'tarif', label: 'Tarif' },
        // Se spune limpede al cui e fiecare rand: firma cumpara, omul primeste
        { key: 'module', label: 'Module cumpărate' },
        { key: 'utilizatori', label: 'Conturi' },
        { key: 'actiuni', label: '' },
      ],
    }
  },
  computed: {
    optiuniClienti() {
      return this.clienti.map(client => ({ value: client.id, text: client.denumire }))
    },
  },
  created() {
    document.title = `${window.app_name} -> Administrare clienți`
    this.incarca()

    // Adresa vazuta de server: arata din prima daca in fata sta un proxy care
    // ascunde adresa adevarata, caz in care filtrarea pe IP n-ar lucra corect.
    this.$http.get('/context')
      .then(({ data }) => {
        this.ipCurent = data.data.ip_curent || ''
      })
      .catch(() => {
        this.ipCurent = ''
      })
  },
  methods: {
    incarca() {
      this.listaInCurs = true
      this.eroare = ''

      this.$http.get('/administrare/clienti')
        .then(({ data }) => {
          this.clienti = data.data
          this.moduleAplicatie = data.module || []
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Lista clienților nu a putut fi încărcată')
        })
        .finally(() => {
          this.listaInCurs = false
        })
    },
    /**
     * Modulele cumpărate de firmă, cu numele lor de produs.
     *
     * Numele vin de la server, din catalogul modulelor: altfel ar fi scrise în
     * două locuri, iar tabelul ar spune „SPV" acolo unde fereastra de cont
     * spune „SPV Curier".
     */
    moduleActive(client) {
      if (!client.abonament) return []

      return this.moduleAplicatie
        .filter(modul => client.abonament[`modul_${modul.cheie}`])
        .map(modul => modul.nume)
    },
    /** Numele modulelor date unui cont, din cheile lor. */
    numeleModulelor(chei) {
      if (!chei || !chei.length) return []

      return this.moduleAplicatie
        .filter(modul => chei.indexOf(modul.cheie) !== -1)
        .map(modul => modul.nume)
    },
    variantaStare(client) {
      if (!client.abonament) return 'light-secondary'
      if (!client.abonament.activ) return 'light-danger'

      return client.abonament.in_proba ? 'light-warning' : 'light-success'
    },
    textStare(client) {
      if (!client.abonament) return 'fără abonament'
      if (!client.abonament.activ) return client.abonament.blocat ? 'oprit' : 'expirat'

      return client.abonament.in_proba ? 'în probă' : 'activ'
    },
    deschideClientNou() {
      this.eroareFormular = ''
      this.clientNou = {
        denumire: '', cui: '', nume: '', email: '', parola: '', proba_zile: 30,
      }
      this.clientVizibil = true
    },
    salveazaClient() {
      this.eroareFormular = ''

      this.$http.post('/administrare/clienti', this.clientNou)
        .then(() => {
          this.clientVizibil = false
          this.incarca()
        })
        .catch(err => {
          this.eroareFormular = this.mesajEroare(err, 'Clientul nu a putut fi creat')
        })
    },
    /**
     * Modulele cu care pornește un cont nou: cele plătite de client.
     *
     * Un cont fără niciun modul intră într-o aplicație fără meniu și n-are ce
     * face acolo, așa că se bifează din start ce ține de abonament. Alegerea
     * rămâne a omului — bifele se pot schimba înainte de salvare.
     */
    moduleImplicite(client) {
      if (!client || !client.abonament) return []

      return this.moduleAplicatie
        .filter(modul => this.modulDinAbonament(modul, client))
        .map(modul => modul.cheie)
    },
    /** Ține modulul de abonamentul clientului, sau se dă pe deasupra lui? */
    modulDinAbonament(modul, client) {
      const { abonament } = client || this.clientCurent || {}

      return Boolean(abonament && abonament[`modul_${modul.cheie}`])
    },
    deschideUtilizator(client, user) {
      this.eroareFormular = ''
      this.clientCurent = client
      this.utilizator = user
        ? { ...user, parola: '', module: (user.module || []).slice() }
        : {
          nume: '',
          email: '',
          telefon: '',
          parola: '',
          administrator: false,
          blocat: false,
          ip_permise: '',
          module: this.moduleImplicite(client),
        }
      this.utilizatorVizibil = true
    },
    salveazaUtilizator() {
      this.eroareFormular = ''

      const cerere = this.utilizator.id
        ? this.$http.put(`/administrare/utilizatori/${this.utilizator.id}`, {
          ...this.utilizator,
          company_id: this.clientCurent.id,
        })
        : this.$http.post(`/administrare/clienti/${this.clientCurent.id}/utilizatori`, this.utilizator)

      cerere
        .then(() => {
          this.utilizatorVizibil = false
          this.incarca()
        })
        .catch(err => {
          this.eroareFormular = this.mesajEroare(err, 'Contul nu a putut fi salvat')
        })
    },
    deconecteaza(user) {
      this.$bvModal.msgBoxConfirm(`Închideți acum toate sesiunile contului ${user.email}?`, {
        title: 'Deconectare',
        okTitle: 'Deconectează',
        cancelTitle: 'Renunță',
        okVariant: 'warning',
      }).then(confirmat => {
        if (!confirmat) return

        this.$http.post(`/administrare/utilizatori/${user.id}/deconectare`)
          .then(({ data }) => {
            this.eroare = ''
            this.$bvToast.toast(`${data.data.sesiuni} sesiuni închise.`, { title: 'Deconectat', variant: 'success' })
          })
          .catch(err => {
            this.eroare = this.mesajEroare(err, 'Deconectarea a eșuat')
          })
      })
    },
    deschideImportVector(client) {
      this.importVectorClient = client
      this.importVectorFisier = null
      this.importVectorEroare = ''
      this.importVectorRezultat = null
      this.importVectorVizibil = true
    },
    /** Statisticile se aduc la prima deschidere a filei; apoi rămân. */
    tabSchimbat(index) {
      if (index === 1 && !this.statisticiIncarcate) {
        this.incarcaStatistici()
      }
    },
    incarcaStatistici() {
      this.statisticiInCurs = true

      this.$http.get('/administrare/statistici')
        .then(raspuns => {
          this.statistici = raspuns.data.data || []
          this.statisticiIncarcate = true
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Statisticile nu au putut fi încărcate')
        })
        .finally(() => {
          this.statisticiInCurs = false
        })
    },
    deschideImportDeclaratii(client) {
      this.importDeclClient = client
      this.importDeclFisier = null
      this.importDeclEroare = ''
      this.importDeclRezultat = null
      this.importDeclVizibil = true
    },
    /**
     * Trimite fișierul declmf.mde și scrie istoricul depunerilor pe clientul
     * ales. Fereastra rămâne deschisă, ca rezultatul să poată fi citit.
     */
    importaDeclaratii() {
      this.importDeclEroare = ''
      this.importDeclRezultat = null
      this.importDeclInCurs = true

      const date = new FormData()
      date.append('fisier', this.importDeclFisier)

      this.$http.post(`/administrare/clienti/${this.importDeclClient.id}/import-declaratii`, date)
        .then(raspuns => {
          this.importDeclRezultat = raspuns.data.data
          this.importDeclFisier = null
        })
        .catch(err => {
          this.importDeclEroare = err.response && err.response.data && err.response.data.message
            ? err.response.data.message
            : 'Importul a eșuat.'
        })
        .finally(() => {
          this.importDeclInCurs = false
        })
    },
    /**
     * Trimite fișierul vector.mde și scrie periodicitățile pe clientul ales.
     *
     * Fereastra rămâne deschisă după import, ca rezultatul — și mai ales
     * coloanele sărite — să poată fi citite pe îndelete.
     */
    importaVector() {
      this.importVectorEroare = ''
      this.importVectorRezultat = null
      this.importVectorInCurs = true

      const date = new FormData()
      date.append('fisier', this.importVectorFisier)

      this.$http.post(`/administrare/clienti/${this.importVectorClient.id}/import-vector`, date)
        .then(raspuns => {
          this.importVectorRezultat = raspuns.data.data
          this.importVectorFisier = null
        })
        .catch(err => {
          this.importVectorEroare = err.response && err.response.data && err.response.data.message
            ? err.response.data.message
            : 'Importul a eșuat.'
        })
        .finally(() => {
          this.importVectorInCurs = false
        })
    },
    deschideAbonament(client) {
      this.eroareFormular = ''
      this.clientCurent = client
      this.abonament = client.abonament
        ? { ...client.abonament }
        : {
          tarif_lunar: 0,
          proba_zile: 30,
          proba_pana_la: null,
          platit_pana_la: null,
          blocat: false,
          motiv_blocare: '',
          modul_spv: true,
          modul_etransport: false,
          modul_portal_just: false,
          observatii: '',
        }
      this.abonamentVizibil = true
    },
    salveazaAbonament() {
      this.eroareFormular = ''

      this.$http.put(`/administrare/clienti/${this.clientCurent.id}/abonament`, this.abonament)
        .then(() => {
          this.abonamentVizibil = false
          this.incarca()
        })
        .catch(err => {
          this.eroareFormular = this.mesajEroare(err, 'Abonamentul nu a putut fi salvat')
        })
    },
    /** Deschisă de la un client anume, notificarea pornește cu el ales. */
    deschideNotificare(client) {
      this.eroareFormular = ''
      this.rezultatTrimitere = null
      this.notificare = {
        destinatari: client ? 'client' : 'toti',
        company_id: client ? client.id : null,
        utilizatori: [],
        titlu: '',
        mesaj: '',
        importanta: 'informare',
        in_aplicatie: true,
        pe_email: false,
        confirma_citirea: true,
      }
      this.notificareVizibila = true
    },
    deschideIstoric() {
      this.istoricVizibil = true
      this.istoricInCurs = true
      this.desfasurate = {}

      this.$http.get('/administrare/notificari')
        .then(({ data }) => {
          this.istoric = data.data
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Istoricul nu a putut fi încărcat')
          this.istoricVizibil = false
        })
        .finally(() => {
          this.istoricInCurs = false
        })
    },
    trimiteNotificare() {
      this.eroareFormular = ''
      this.rezultatTrimitere = null
      this.trimitereInCurs = true

      this.$http.post('/administrare/notificari', this.notificare)
        .then(({ data }) => {
          this.rezultatTrimitere = data.data

          // Fereastra rămâne deschisă doar dacă un email n-a plecat: altfel
          // rezultatul n-ar mai fi văzut de nimeni.
          if (!data.data.esecuri.length) {
            this.notificareVizibila = false
            this.$bvToast.toast(
              `Notificare trimisă către ${data.data.destinatari} utilizatori.`,
              { title: 'Trimis', variant: 'success' },
            )
          }
        })
        .catch(err => {
          this.eroareFormular = this.mesajEroare(err, 'Notificarea nu a putut fi trimisă')
        })
        .finally(() => {
          this.trimitereInCurs = false
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
/* Butoanele de actiuni stau unul sub altul, la aceeasi latime. */
.butoane-actiuni {
  width: 100%;
  min-width: 9.5rem;
  white-space: nowrap;
}

.tabel-compact ::v-deep th,
.tabel-compact ::v-deep td {
  padding: 0.4rem 0.5rem !important;
  vertical-align: top;
  font-size: 0.85rem;
}

/* Lista de destinatari poate fi lungă; se derulează în locul ei. */
.lista-destinatari {
  max-height: 14rem;
  overflow-y: auto;
  border: 1px solid rgba(0, 0, 0, 0.1);
  border-radius: 0.35rem;
  padding: 0.5rem;
}
</style>
