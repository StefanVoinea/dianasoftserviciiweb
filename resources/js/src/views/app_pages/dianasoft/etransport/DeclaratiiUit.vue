<template>
  <div>
    <!-- In formular mesajele stau langa butoane, jos: acolo se uita omul
         dupa apasare, iar cu multe linii capul paginii nici nu se vede. -->
    <b-alert
      v-if="info && !declaratia"
      show
      variant="info"
      class="py-2"
    >
      {{ info }}
    </b-alert>

    <b-alert
      v-if="eroare && !declaratia"
      show
      variant="danger"
      class="py-2"
    >
      {{ eroare }}
    </b-alert>

    <!-- Lista declaratiilor -->
    <b-card
      v-if="!declaratia"
      class="border"
      body-class="p-2"
    >
      <div class="d-flex align-items-center mb-2">
        <b-button
          variant="primary"
          size="sm"
          @click="deschideNoua"
        >
          <feather-icon
            icon="PlusIcon"
            class="mr-25"
          />
          Declarație nouă
        </b-button>

        <b-form-select
          v-model="filtruStare"
          :options="optiuniFiltruStare"
          size="sm"
          class="ml-2"
          style="width: auto"
          @change="incarcaLista"
        />

        <!-- Arhiva zilnica a furnizorului: cate o ciorna pe fiecare factura -->
        <b-button
          v-if="importPermis"
          variant="outline-primary"
          size="sm"
          class="ml-2"
          @click="arhivaVizibila = true"
        >
          <feather-icon
            icon="ArchiveIcon"
            class="mr-25"
          />
          Import arhivă
        </b-button>

        <!-- Formularul cu codurile UIT pentru transportator -->
        <b-button
          variant="outline-primary"
          size="sm"
          class="ml-auto"
          @click="deschideFormular"
        >
          <feather-icon
            icon="TruckIcon"
            class="mr-25"
          />
          Formular transportator
        </b-button>

        <!-- Intrastat-ul lunar iese din aceleasi date, gata declarate la UIT -->
        <b-button
          variant="outline-primary"
          size="sm"
          class="ml-1"
          @click="deschideIntrastat"
        >
          <feather-icon
            icon="FileTextIcon"
            class="mr-25"
          />
          Declarația Intrastat
        </b-button>
      </div>

      <b-table
        :items="declaratii"
        :fields="campuriLista"
        :busy="listaInCurs"
        responsive
        striped
        small
        show-empty
        class="mb-0"
        empty-text="Nicio declarație. Începeți cu „Declarație nouă”."
      >
        <template #table-busy>
          <div class="text-center my-2">
            <b-spinner class="align-middle mr-1" />
            Se încarcă...
          </div>
        </template>

        <template #cell(stare)="rand">
          <b-badge :variant="culoareStare(rand.item.stare)">
            {{ rand.item.stare_eticheta }}
          </b-badge>
          <div
            v-if="rand.item.uit"
            class="small font-weight-bold mt-25"
          >
            {{ rand.item.uit }}
          </div>
        </template>

        <template #cell(operatiune)="rand">
          <div class="small">
            {{ rand.item.operatiune || '-' }}
          </div>
          <div class="small text-muted">
            {{ rand.item.partener || '' }}
          </div>
        </template>

        <template #cell(magazin)="rand">
          <div class="small">
            {{ rand.item.magazin || '-' }}
          </div>
          <div
            v-if="rand.item.magazin_cod"
            class="small text-muted"
          >
            {{ rand.item.magazin_cod }}
          </div>
        </template>

        <template #cell(actiuni)="rand">
          <b-button
            v-b-tooltip.hover.top.window.v-light
            size="sm"
            variant="outline-secondary"
            class="btn-icon"
            :title="rand.item.poate_fi_modificata ? 'Deschide' : 'Vezi detaliile'"
            @click="deschide(rand.item.id)"
          >
            <feather-icon :icon="rand.item.poate_fi_modificata ? 'Edit2Icon' : 'EyeIcon'" />
          </b-button>
          <b-button
            v-if="rand.item.index_incarcare && !rand.item.uit"
            v-b-tooltip.hover.top.window.v-light
            size="sm"
            variant="outline-secondary"
            class="btn-icon ml-25"
            title="Verifică starea la ANAF"
            @click="verificaDinLista(rand.item.id)"
          >
            <feather-icon icon="RefreshCwIcon" />
          </b-button>
          <b-button
            v-if="rand.item.uit"
            v-b-tooltip.hover.top.window.v-light
            size="sm"
            variant="outline-secondary"
            class="btn-icon ml-25"
            title="Trimite codul UIT pe email"
            @click="deschideEmail(rand.item)"
          >
            <feather-icon icon="MailIcon" />
          </b-button>
          <b-button
            v-if="rand.item.poate_fi_modificata"
            v-b-tooltip.hover.top.window.v-light
            size="sm"
            variant="outline-danger"
            class="btn-icon ml-25"
            title="Șterge ciorna"
            @click="sterge(rand.item.id)"
          >
            <feather-icon icon="Trash2Icon" />
          </b-button>
        </template>
      </b-table>
    </b-card>

    <!-- Formularul declaratiei -->
    <div v-else>
      <div class="d-flex align-items-center mb-2">
        <b-button
          variant="outline-secondary"
          size="sm"
          @click="inchideFormularul"
        >
          <feather-icon
            icon="ArrowLeftIcon"
            class="mr-25"
          />
          Înapoi la listă
        </b-button>

        <b-badge
          :variant="culoareStare(declaratia.stare)"
          class="ml-2"
        >
          {{ declaratia.stare_eticheta || 'Ciornă nouă' }}
        </b-badge>

        <h5
          v-if="declaratia.uit"
          class="mb-0 ml-2"
        >
          UIT: <span class="text-success font-weight-bolder">{{ declaratia.uit }}</span>
        </h5>
        <b-button
          v-if="declaratia.uit"
          variant="outline-primary"
          size="sm"
          class="ml-2"
          @click="deschideEmail(declaratia)"
        >
          <feather-icon
            icon="MailIcon"
            class="mr-25"
          />
          Trimite pe email
        </b-button>
      </div>

      <!-- Import fisiere: doar pentru utilizatorii cu formate de fisiere cunoscute -->
      <b-card
        v-if="editabila && importPermis"
        class="border mb-2"
        body-class="p-2"
      >
        <h6 class="mb-1">
          Import linii din fișierele furnizorului
        </h6>
        <b-row
          no-gutters
          class="align-items-center"
        >
          <b-col md="6">
            <b-form-file
              v-model="fisiereDeImportat"
              multiple
              size="sm"
              accept=".xlsx,.xls,.ods,.txt"
              placeholder="Excel cu detaliile facturii sau raport text..."
              browse-text="Alege"
            />
          </b-col>
          <b-col
            md="6"
            class="pl-md-1 d-flex align-items-center mt-1 mt-md-0"
          >
            <b-form-checkbox v-model="grupate">
              grupează pe cod vamal
            </b-form-checkbox>
            <b-button
              variant="primary"
              size="sm"
              class="ml-2"
              :disabled="!fisiereDeImportat.length || importInCurs"
              @click="importaFisierele"
            >
              <b-spinner
                v-if="importInCurs"
                small
                class="mr-1"
              />
              Importă
            </b-button>
          </b-col>
        </b-row>
        <small
          v-if="declaratia.fisiere_importate && declaratia.fisiere_importate.length"
          class="text-muted d-block mt-1"
        >
          Importate: {{ declaratia.fisiere_importate.join(', ') }}
        </small>
      </b-card>

      <!-- Operatiune si partener -->
      <b-card
        class="border mb-2"
        body-class="p-2"
      >
        <b-row>
          <b-col md="3">
            <label class="small mb-0">CIF declarant*</label>
            <b-form-input
              v-model="declaratia.cif_declarant"
              size="sm"
              :disabled="!editabila"
            />
          </b-col>
          <b-col md="5">
            <label class="small mb-0">Tip operațiune*</label>
            <b-form-select
              v-model="declaratia.tip_operatiune"
              :options="optiuniTipOperatiune"
              size="sm"
              :disabled="!editabila"
              @change="tipOperatiuneSchimbat"
            />
          </b-col>
          <b-col md="4">
            <label class="small mb-0">Referință internă</label>
            <b-form-input
              v-model="declaratia.referinta_interna"
              size="sm"
              :disabled="!editabila"
            />
          </b-col>
        </b-row>

        <b-row class="mt-1">
          <b-col md="3">
            <label class="small mb-0">Țara partenerului*</label>
            <b-form-select
              v-model="declaratia.partener_tara"
              :options="optiuniTari"
              size="sm"
              :disabled="!editabila"
            />
          </b-col>
          <b-col md="3">
            <label class="small mb-0">Cod partener (TVA)</label>
            <b-form-input
              v-model="declaratia.partener_cod"
              size="sm"
              :disabled="!editabila"
            />
          </b-col>
          <b-col md="6">
            <label class="small mb-0">Denumire partener*</label>
            <b-form-input
              v-model="declaratia.partener_denumire"
              size="sm"
              :disabled="!editabila"
            />
          </b-col>
        </b-row>
      </b-card>

      <!-- Bunuri transportate -->
      <b-card
        class="border mb-2"
        body-class="p-2"
      >
        <div class="d-flex align-items-center mb-1">
          <h6 class="mb-0">
            Bunuri transportate
          </h6>
          <b-button
            v-if="editabila"
            variant="outline-primary"
            size="sm"
            class="ml-2"
            @click="adaugaLinie"
          >
            <feather-icon icon="PlusIcon" />
            Linie nouă
          </b-button>
        </div>

        <datalist id="coduri-vamale-lista">
          <option
            v-for="cod in coduriVamaleGasite"
            :key="cod.cod"
            :value="cod.cod"
          >
            {{ cod.denumire_scurta || cod.denumire }}
          </option>
        </datalist>

        <div class="table-responsive mb-0">
          <table class="table table-sm table-striped">
            <thead>
              <tr class="small">
                <th style="min-width: 110px">
                  Scop
                </th>
                <th style="min-width: 110px">
                  Cod vamal
                </th>
                <th style="min-width: 220px">
                  Denumire*
                </th>
                <th style="min-width: 80px">
                  Cantitate*
                </th>
                <th style="min-width: 90px">
                  UM
                </th>
                <th style="min-width: 90px">
                  Kg net
                </th>
                <th style="min-width: 90px">
                  Kg brut*
                </th>
                <th style="min-width: 100px">
                  Valoare {{ declaratia.valuta }}
                </th>
                <th style="min-width: 100px">
                  Valoare lei
                </th>
                <th v-if="editabila" />
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(linie, index) in declaratia.linii"
                :key="index"
              >
                <td>
                  <b-form-select
                    v-model="linie.scop_operatiune"
                    :options="optiuniScop"
                    size="sm"
                    :disabled="!editabila"
                  />
                </td>
                <td>
                  <b-form-input
                    v-model="linie.cod_tarifar"
                    size="sm"
                    list="coduri-vamale-lista"
                    :disabled="!editabila"
                    @input="cautaCodVamal($event)"
                    @change="codVamalAles(linie)"
                  />
                </td>
                <td>
                  <b-form-input
                    v-model="linie.denumire"
                    size="sm"
                    :disabled="!editabila"
                  />
                </td>
                <td>
                  <b-form-input
                    v-model.number="linie.cantitate"
                    type="number"
                    step="0.01"
                    size="sm"
                    :disabled="!editabila"
                  />
                </td>
                <td>
                  <b-form-select
                    v-model="linie.um"
                    :options="optiuniUm"
                    size="sm"
                    :disabled="!editabila"
                  />
                </td>
                <td>
                  <b-form-input
                    v-model.number="linie.greutate_neta"
                    type="number"
                    step="0.001"
                    size="sm"
                    :disabled="!editabila"
                  />
                </td>
                <td>
                  <b-form-input
                    v-model.number="linie.greutate_bruta"
                    type="number"
                    step="0.001"
                    size="sm"
                    :disabled="!editabila"
                  />
                </td>
                <td>
                  <b-form-input
                    v-model.number="linie.valoare"
                    type="number"
                    step="0.01"
                    size="sm"
                    :disabled="!editabila"
                    @change="recalculeazaLeii"
                  />
                </td>
                <td>
                  <b-form-input
                    v-model.number="linie.valoare_lei"
                    type="number"
                    step="0.01"
                    size="sm"
                    :disabled="!editabila"
                  />
                </td>
                <td v-if="editabila">
                  <b-button
                    variant="outline-danger"
                    size="sm"
                    class="btn-icon"
                    @click="declaratia.linii.splice(index, 1)"
                  >
                    <feather-icon icon="Trash2Icon" />
                  </b-button>
                </td>
              </tr>
            </tbody>
            <tfoot>
              <tr class="small font-weight-bold">
                <td colspan="3">
                  Total
                </td>
                <td>{{ total('cantitate') }}</td>
                <td />
                <td>{{ total('greutate_neta') }}</td>
                <td>{{ total('greutate_bruta') }}</td>
                <td>{{ total('valoare') }}</td>
                <td>{{ total('valoare_lei') }}</td>
                <td v-if="editabila" />
              </tr>
            </tfoot>
          </table>
        </div>

        <b-row
          v-if="editabila"
          class="align-items-end mt-1"
        >
          <b-col md="2">
            <label class="small mb-0">Valuta fișierelor</label>
            <b-form-select
              v-model="declaratia.valuta"
              :options="['EUR', 'RON', 'USD', 'HUF', 'GBP', 'CHF']"
              size="sm"
            />
          </b-col>
          <b-col md="3">
            <label class="small mb-0">Curs lei</label>
            <b-input-group size="sm">
              <b-form-input
                v-model.number="declaratia.curs"
                type="number"
                step="0.0001"
              />
              <b-input-group-append>
                <b-button
                  variant="outline-primary"
                  @click="iaCursulBnr"
                >
                  Curs BNR
                </b-button>
              </b-input-group-append>
            </b-input-group>
          </b-col>
          <b-col
            md="4"
            class="mt-1 mt-md-0"
          >
            <label class="small mb-0">Kg brut total (de pe DDT/CMR)</label>
            <b-input-group size="sm">
              <b-form-input
                v-model.number="greutateBrutaTotala"
                type="number"
                step="0.001"
              />
              <b-input-group-append>
                <b-button
                  variant="outline-primary"
                  @click="imparteGreutateaBruta"
                >
                  Împarte pe linii
                </b-button>
              </b-input-group-append>
            </b-input-group>
            <small class="text-muted">
              proporțional cu greutatea netă, când fișierul nu are kg brut pe linie
            </small>
          </b-col>
          <b-col
            md="3"
            class="mt-1 mt-md-0"
          >
            <b-button
              variant="outline-primary"
              size="sm"
              block
              @click="recalculeazaLeii(true)"
            >
              Recalculează valorile în lei
            </b-button>
          </b-col>
        </b-row>
      </b-card>

      <!-- Date transport -->
      <b-card
        class="border mb-2"
        body-class="p-2"
      >
        <h6 class="mb-1">
          Date transport
        </h6>
        <b-row>
          <b-col md="2">
            <label class="small mb-0">Nr. vehicul*</label>
            <b-form-input
              v-model="declaratia.nr_vehicul"
              size="sm"
              placeholder="B111ABC"
              :disabled="!editabila"
            />
          </b-col>
          <b-col md="2">
            <label class="small mb-0">Nr. remorcă 1</label>
            <b-form-input
              v-model="declaratia.nr_remorca1"
              size="sm"
              :disabled="!editabila"
            />
          </b-col>
          <b-col md="2">
            <label class="small mb-0">Nr. remorcă 2</label>
            <b-form-input
              v-model="declaratia.nr_remorca2"
              size="sm"
              :disabled="!editabila"
            />
          </b-col>
          <b-col md="3">
            <label class="small mb-0">Data transportului*</label>
            <b-form-input
              v-model="declaratia.data_transport"
              type="date"
              size="sm"
              :disabled="!editabila"
              @change="iaCursulBnr(true)"
            />
          </b-col>
        </b-row>
        <b-row class="mt-1">
          <b-col md="2">
            <label class="small mb-0">Țara transportator*</label>
            <b-form-select
              v-model="declaratia.transportator_tara"
              :options="optiuniTari"
              size="sm"
              :disabled="!editabila"
            />
          </b-col>
          <b-col md="3">
            <label class="small mb-0">Cod transportator (CIF)</label>
            <b-form-input
              v-model="declaratia.transportator_cod"
              size="sm"
              :disabled="!editabila"
            />
          </b-col>
          <b-col md="7">
            <label class="small mb-0">Denumire transportator*</label>
            <b-form-input
              v-model="declaratia.transportator_denumire"
              size="sm"
              :disabled="!editabila"
            />
          </b-col>
        </b-row>
      </b-card>

      <!-- Magazinul (destinatia finala), din arhiva importata; ramane editabil -->
      <b-card
        v-if="importPermis"
        class="border mb-2"
        body-class="p-2"
      >
        <h6 class="mb-1">
          Magazin (destinația finală)
        </h6>
        <b-row>
          <b-col md="5">
            <label class="small mb-0">Gestiunea</label>
            <b-form-select
              :value="(declaratia.loc_final && declaratia.loc_final.magazin_cod) || ''"
              :options="optiuniGestiuni"
              size="sm"
              :disabled="!editabila"
              @change="alegeGestiunea"
            />
          </b-col>
          <b-col md="3">
            <label class="small mb-0">Cod magazin</label>
            <b-form-input
              :value="declaratia.loc_final && declaratia.loc_final.magazin_cod"
              size="sm"
              :disabled="!editabila"
              @input="$set(declaratia.loc_final, 'magazin_cod', $event)"
            />
          </b-col>
          <b-col md="4">
            <label class="small mb-0">Denumire magazin</label>
            <b-form-input
              :value="declaratia.loc_final && declaratia.loc_final.magazin_denumire"
              size="sm"
              :disabled="!editabila"
              @input="$set(declaratia.loc_final, 'magazin_denumire', $event)"
            />
          </b-col>
        </b-row>
      </b-card>

      <!-- Traseul -->
      <b-row>
        <b-col md="6">
          <loc-traseu
            v-model="declaratia.loc_start"
            titlu="Începutul traseului"
            :fel="felTraseu.start"
            :judete="nomenclatoare.judete"
            :ptf="nomenclatoare.ptf"
            :birouri="nomenclatoare.birouri_vamale"
            :editabila="editabila"
          />
        </b-col>
        <b-col md="6">
          <loc-traseu
            v-model="declaratia.loc_final"
            titlu="Sfârșitul traseului"
            :fel="felTraseu.final"
            :judete="nomenclatoare.judete"
            :ptf="nomenclatoare.ptf"
            :birouri="nomenclatoare.birouri_vamale"
            :editabila="editabila"
          />
        </b-col>
      </b-row>

      <!-- Documente transport -->
      <b-card
        class="border mb-2"
        body-class="p-2"
      >
        <div class="d-flex align-items-center mb-1">
          <h6 class="mb-0">
            Documente transport
          </h6>
          <b-button
            v-if="editabila"
            variant="outline-primary"
            size="sm"
            class="ml-2"
            @click="declaratia.documente.push({ tip: 20, numar: '', data: '', observatii: '' })"
          >
            <feather-icon icon="PlusIcon" />
            Adaugă
          </b-button>
        </div>

        <b-row
          v-for="(document, index) in declaratia.documente"
          :key="index"
          class="align-items-end mb-50"
        >
          <b-col md="3">
            <label class="small mb-0">Tip*</label>
            <b-form-select
              v-model="document.tip"
              :options="optiuniTipDocument"
              size="sm"
              :disabled="!editabila"
            />
          </b-col>
          <b-col md="3">
            <label class="small mb-0">Număr</label>
            <b-form-input
              v-model="document.numar"
              size="sm"
              :disabled="!editabila"
            />
          </b-col>
          <b-col md="3">
            <label class="small mb-0">Dată*</label>
            <b-form-input
              v-model="document.data"
              type="date"
              size="sm"
              :disabled="!editabila"
            />
          </b-col>
          <b-col md="2">
            <!-- La „Altele" ANAF cere scris ce fel de document e (regula BR-026) -->
            <label class="small mb-0">Observații{{ document.tip === 9999 ? '*' : '' }}</label>
            <b-form-input
              v-model="document.observatii"
              size="sm"
              :disabled="!editabila"
              :state="document.tip === 9999 && !document.observatii ? false : null"
              :placeholder="document.tip === 9999 ? 'ce fel de document este' : ''"
            />
          </b-col>
          <b-col
            v-if="editabila"
            md="1"
          >
            <b-button
              variant="outline-danger"
              size="sm"
              class="btn-icon"
              @click="declaratia.documente.splice(index, 1)"
            >
              <feather-icon icon="Trash2Icon" />
            </b-button>
          </b-col>
        </b-row>
      </b-card>

      <div
        v-if="editabila"
        class="d-flex mb-2"
      >
        <b-button
          variant="outline-primary"
          :disabled="salvareInCurs"
          @click="salveaza()"
        >
          <b-spinner
            v-if="salvareInCurs"
            small
            class="mr-1"
          />
          Salvează ciorna
        </b-button>
        <b-button
          variant="primary"
          class="ml-2"
          :disabled="depunereInCurs"
          @click="depune"
        >
          <b-spinner
            v-if="depunereInCurs"
            small
            class="mr-1"
          />
          Depune la ANAF pentru UIT
        </b-button>
      </div>

      <div
        v-else-if="declaratia.index_incarcare && !declaratia.uit"
        class="mb-2"
      >
        <b-button
          variant="outline-primary"
          @click="verifica"
        >
          <feather-icon
            icon="RefreshCwIcon"
            class="mr-25"
          />
          Verifică starea la ANAF
        </b-button>
      </div>

      <!-- Raspunsul depunerii, chiar sub butoane: aici se uita omul dupa apasare -->
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
    </div>

    <!--
      Arhiva zilnica a furnizorului (ZIP): T02 cu liniile pe coduri vamale si
      D01 cu destinatia finala. Din ea se fac cate o ciorna pe fiecare factura,
      cu magazinul si adresa de descarcare gata puse.
    -->
    <b-modal
      v-model="arhivaVizibila"
      title="Import arhivă zilnică"
      :ok-title="arhivaInCurs ? 'Se importă...' : 'Importă arhiva'"
      cancel-title="Renunță"
      :ok-disabled="!arhivaFisier || arhivaInCurs"
      @ok.prevent="importaArhiva"
    >
      <p class="text-muted small">
        Alegeți arhiva ZIP primită de la furnizor pentru o zi de livrare.
        Pentru fiecare factură se face câte o ciornă de declarație, cu liniile,
        partenerul, valoarea în lei la cursul zilei facturii și locul de
        descărcare (magazinul) din distinta D01. Rămân de completat vehiculul,
        transportatorul și data transportului.
      </p>

      <b-form-file
        v-model="arhivaFisier"
        accept=".zip"
        placeholder="Alegeți arhiva..."
        browse-text="Răsfoiește"
      />

      <b-alert
        v-if="arhivaEroare"
        show
        variant="danger"
        class="py-1 px-2 mt-1 mb-0"
      >
        {{ arhivaEroare }}
      </b-alert>

      <b-alert
        v-if="arhivaRezultat"
        show
        variant="success"
        class="py-1 px-2 mt-1 mb-0"
      >
        {{ arhivaRezultat.ciorne.length }} ciorne create:
        <ul class="mb-0 pl-1 small">
          <li
            v-for="ciorna in arhivaRezultat.ciorne"
            :key="ciorna.id"
          >
            Factura {{ ciorna.factura }}<span v-if="ciorna.magazin"> — {{ ciorna.magazin }}</span>
          </li>
        </ul>
        <div
          v-if="arhivaRezultat.avertismente.length"
          class="small mt-50 text-danger"
        >
          {{ arhivaRezultat.avertismente.join(' | ') }}
        </div>
      </b-alert>
    </b-modal>

    <!--
      Un cod de magazin nou, intalnit la import: utilizatorul spune cum se
      numeste gestiunea la el, iar de atunci codul se recunoaste singur.
    -->
    <b-modal
      v-model="gestiuneVizibila"
      title="Gestiune nouă"
      :ok-title="gestiuneInCurs ? 'Se salvează...' : 'Salvează gestiunea'"
      cancel-title="Mai târziu"
      no-close-on-backdrop
      :ok-disabled="!gestiuneNoua || !gestiuneNoua.denumire || gestiuneInCurs"
      @ok.prevent="salveazaGestiunea"
      @cancel="urmatoareaGestiune"
    >
      <template v-if="gestiuneNoua">
        <p class="text-muted small mb-1">
          În arhivă a apărut codul de magazin
          <strong>{{ gestiuneNoua.cod_furnizor }}</strong>
          <span v-if="gestiuneNoua.denumire_furnizor"> ({{ gestiuneNoua.denumire_furnizor }})</span>,
          care nu e încă în lista gestiunilor. Spuneți cum se numește la dumneavoastră
          și se va recunoaște singur de acum înainte.
        </p>

        <label class="small mb-0">Denumirea gestiunii*</label>
        <b-form-input
          v-model="gestiuneNoua.denumire"
          size="sm"
          class="mb-1"
          placeholder="ex.: 2276 Pitesti"
        />

        <b-row>
          <b-col md="4">
            <label class="small mb-0">Cod intern</label>
            <b-form-input
              v-model="gestiuneNoua.cod"
              size="sm"
              placeholder="ex.: 2276"
            />
          </b-col>
          <b-col md="8">
            <label class="small mb-0">Prescurtare (foaia din formular)</label>
            <b-form-input
              v-model="gestiuneNoua.prescurtare"
              size="sm"
              placeholder="ex.: Pitesti"
            />
          </b-col>
        </b-row>

        <b-alert
          v-if="gestiuneEroare"
          show
          variant="danger"
          class="py-1 px-2 mt-1 mb-0"
        >
          {{ gestiuneEroare }}
        </b-alert>

        <p
          v-if="gestiuniNoi.length"
          class="text-muted small mt-1 mb-0"
        >
          Mai sunt {{ gestiuniNoi.length }} coduri noi de lămurit după acesta.
        </p>
      </template>
    </b-modal>

    <!--
      Formularul cu codurile UIT pentru transportator: cate o foaie pe magazin,
      cu punctul de trecere, vehiculul si locul de descarcare. Se descarca si,
      cu adrese scrise, pleaca si pe email.
    -->
    <b-modal
      v-model="formularVizibil"
      title="Formular transportator (coduri UIT)"
      :ok-title="formularInCurs ? 'Se întocmește...' : 'Generează'"
      cancel-title="Renunță"
      :ok-disabled="!formularIdAlese.length || formularInCurs"
      @ok.prevent="genereazaFormular"
    >
      <p class="text-muted small">
        Alegeți declarațiile cu UIT care merg în același transport: fișierul
        are câte o foaie pe magazin, cu codul UIT al fiecărei facturi. Cu
        adrese scrise, fișierul pleacă și pe email.
      </p>

      <div class="d-flex align-items-center justify-content-between">
        <label class="mb-0">Declarațiile cu UIT</label>
        <b-button
          size="sm"
          variant="flat-primary"
          class="py-0 px-50"
          @click="alegeToateFormular"
        >
          <small>{{ formularIdAlese.length === declaratiiCuUit.length ? 'niciuna' : 'toate' }}</small>
        </b-button>
      </div>
      <b-form-checkbox-group
        v-model="formularIdAlese"
        stacked
        class="lista-formular"
      >
        <b-form-checkbox
          v-for="d in declaratiiCuUit"
          :key="d.id"
          :value="d.id"
        >
          {{ d.uit }}
          <span class="text-muted small">
            — {{ d.partener || '' }}{{ d.data_transport ? ', transport ' + d.data_transport : '' }}
          </span>
        </b-form-checkbox>
      </b-form-checkbox-group>

      <label class="small mb-0 mt-1">Trimite și pe email (opțional)</label>
      <b-form-input
        v-model="formularAdrese"
        placeholder="transportator@firma.ro, dispecer@firma.ro"
      />

      <b-alert
        v-if="formularEroare"
        show
        variant="danger"
        class="py-1 px-2 mt-1 mb-0"
      >
        {{ formularEroare }}
      </b-alert>
    </b-modal>

    <!--
      Declaratia Intrastat, intocmita din declaratiile e-Transport cu UIT ale
      lunii alese. Fisierul XML se incarca apoi in aplicatia Intrastat a INS,
      care il valideaza si il depune.
    -->
    <b-modal
      v-model="intrastatVizibil"
      title="Declarația Intrastat"
      :ok-title="intrastatInCurs ? 'Se întocmește...' : 'Generează XML'"
      cancel-title="Renunță"
      :ok-disabled="intrastatInCurs || !intrastat.nume || !intrastat.prenume || !intrastat.telefon"
      @ok.prevent="genereazaIntrastat"
    >
      <p class="text-muted small">
        Declarația se întocmește din declarațiile e-Transport cu UIT ale lunii
        alese: sosirile din achizițiile intracomunitare, expedierile din
        livrările intracomunitare, cu liniile adunate pe cod NC8 și țară.
        Fișierul XML se încarcă în aplicația Intrastat (INS), care îl validează
        și îl depune.
      </p>

      <b-row>
        <b-col cols="4">
          <label class="small mb-0">Luna</label>
          <b-form-select
            v-model="intrastat.luna"
            :options="[1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]"
          />
        </b-col>
        <b-col cols="4">
          <label class="small mb-0">Anul</label>
          <b-form-input
            v-model.number="intrastat.anul"
            type="number"
            min="2000"
            max="2100"
          />
        </b-col>
        <b-col cols="4">
          <label class="small mb-0">Fluxul</label>
          <b-form-select
            v-model="intrastat.flux"
            :options="[
              { value: 'sosiri', text: 'Sosiri (AIC)' },
              { value: 'expedieri', text: 'Expedieri (LIC)' },
            ]"
          />
        </b-col>
      </b-row>

      <b-row class="mt-1">
        <b-col cols="6">
          <label class="small mb-0">Condiția de livrare (Incoterm)</label>
          <b-form-select
            v-model="intrastat.incoterm"
            :options="['EXW', 'FCA', 'FAS', 'FOB', 'CFR', 'CIF', 'CPT', 'CIP', 'DAP', 'DPU', 'DDP']"
          />
        </b-col>
      </b-row>

      <hr>
      <p class="text-muted small mb-1">
        Persoana de contact, cerută de INS în declarație:
      </p>
      <b-row>
        <b-col cols="6">
          <label class="small mb-0">Nume*</label>
          <b-form-input v-model="intrastat.nume" />
        </b-col>
        <b-col cols="6">
          <label class="small mb-0">Prenume*</label>
          <b-form-input v-model="intrastat.prenume" />
        </b-col>
      </b-row>
      <b-row class="mt-1">
        <b-col cols="6">
          <label class="small mb-0">Telefon*</label>
          <b-form-input v-model="intrastat.telefon" />
        </b-col>
        <b-col cols="6">
          <label class="small mb-0">Email</label>
          <b-form-input
            v-model="intrastat.email"
            type="email"
          />
        </b-col>
      </b-row>

      <b-alert
        v-if="intrastatEroare"
        show
        variant="danger"
        class="py-1 px-2 mt-1 mb-0"
      >
        {{ intrastatEroare }}
      </b-alert>
    </b-modal>

    <!-- Trimiterea codului UIT pe email -->
    <b-modal
      v-model="emailVizibil"
      :title="`Trimite codul UIT ${emailUit || ''}`"
      :ok-disabled="!emailAdrese.trim() || emailInCurs"
      ok-title="Trimite"
      cancel-title="Renunță"
      @ok.prevent="trimiteEmailUit"
    >
      <b-form-input
        v-model="emailAdrese"
        autofocus
        placeholder="sofer@firma.ro, dispecer@firma.ro"
        @keyup.enter="trimiteEmailUit"
      />
      <small class="text-muted">
        Mai multe adrese se despart prin virgulă. Emailul cuprinde codul UIT,
        vehiculul și data transportului.
      </small>
    </b-modal>
  </div>
</template>

<script>
import LocTraseu from './LocTraseu.vue'

/** Ciorna goala, cum arata inainte de import sau completare. */
const declaratieGoala = () => ({
  id: null,
  stare: 'ciorna',
  stare_eticheta: 'Ciornă',
  poate_fi_modificata: true,
  cif_declarant: '',
  referinta_interna: '',
  tip_operatiune: 10,
  partener_tara: null,
  partener_cod: '',
  partener_denumire: '',
  nr_vehicul: '',
  nr_remorca1: '',
  nr_remorca2: '',
  transportator_tara: 'RO',
  transportator_cod: '',
  transportator_denumire: '',
  data_transport: '',
  loc_start: { tip: 'ptf' },
  loc_final: { tip: 'adresa' },
  documente: [],
  linii: [],
  valuta: 'EUR',
  curs: null,
  fisiere_importate: [],
  index_incarcare: null,
  uit: null,
})

export default {
  name: 'DeclaratiiUit',
  components: { LocTraseu },
  data() {
    return {
      declaratii: [],
      declaratia: null,
      nomenclatoare: {
        tipuri_operatiune: {},
        scopuri: {},
        scopuri_pe_operatiune: {},
        traseu_pe_operatiune: {},
        judete: {},
        ptf: {},
        birouri_vamale: {},
        tipuri_document: {},
        unitati_masura: {},
        tari: {},
      },
      coduriVamaleGasite: [],
      importPermis: false,
      cifImplicit: '',
      fisiereDeImportat: [],
      grupate: true,
      greutateBrutaTotala: null,
      filtruStare: '',
      arhivaVizibila: false,
      arhivaInCurs: false,
      arhivaFisier: null,
      arhivaEroare: '',
      arhivaRezultat: null,
      gestiuni: [],
      gestiuniNoi: [],
      gestiuneNoua: null,
      gestiuneVizibila: false,
      gestiuneInCurs: false,
      gestiuneEroare: '',
      formularVizibil: false,
      formularInCurs: false,
      formularIdAlese: [],
      formularAdrese: '',
      formularEroare: '',
      intrastatVizibil: false,
      intrastatInCurs: false,
      intrastatEroare: '',
      intrastat: {
        luna: 1, anul: 2026, flux: 'sosiri', incoterm: 'EXW', nume: '', prenume: '', telefon: '', email: '',
      },
      emailVizibil: false,
      emailAdrese: '',
      emailUit: '',
      emailDeclaratieId: null,
      emailInCurs: false,
      info: '',
      eroare: '',
      listaInCurs: false,
      importInCurs: false,
      salvareInCurs: false,
      depunereInCurs: false,
      // Coloanele listei stau la computed: magazinul se vede doar la clientii
      // cu retea de magazine (cei cu drept de import).
    }
  },
  computed: {
    editabila() {
      return !this.declaratia || this.declaratia.poate_fi_modificata
    },
    /** Declaratiile cu UIT din lista, de pus in formularul transportatorului. */
    declaratiiCuUit() {
      return this.declaratii.filter(d => d.uit)
    },
    campuriLista() {
      return [
        { key: 'stare', label: 'Stare / UIT' },
        { key: 'cif_declarant', label: 'Declarant' },
        { key: 'operatiune', label: 'Operațiune / partener' },
        // Magazinul vine din arhiva importata: are rost doar la cine importa.
        ...(this.importPermis ? [{ key: 'magazin', label: 'Magazin' }] : []),
        { key: 'vehicul', label: 'Vehicul' },
        { key: 'data_transport', label: 'Transport' },
        { key: 'nr_linii', label: 'Linii' },
        { key: 'valoare_lei', label: 'Valoare lei' },
        { key: 'creata_la', label: 'Creată la' },
        { key: 'actiuni', label: '' },
      ]
    },
    optiuniTipOperatiune() {
      return this.perechi(this.nomenclatoare.tipuri_operatiune)
    },
    optiuniTari() {
      return this.perechi(this.nomenclatoare.tari)
    },
    optiuniUm() {
      return this.perechi(this.nomenclatoare.unitati_masura, cod => cod)
    },
    optiuniTipDocument() {
      return this.perechi(this.nomenclatoare.tipuri_document)
    },
    optiuniFiltruStare() {
      return [
        { value: '', text: 'Toate stările' },
        { value: 'ciorna', text: 'Ciorne' },
        { value: 'depusa', text: 'Depuse' },
        { value: 'validata', text: 'Validate (cu UIT)' },
        { value: 'respinsa', text: 'Respinse' },
      ]
    },
    /** Scopurile permise la tipul de operatiune ales. */
    optiuniScop() {
      const tip = this.declaratia ? this.declaratia.tip_operatiune : null
      const permise = (this.nomenclatoare.scopuri_pe_operatiune || {})[tip] || []

      return permise.map(cod => ({ value: cod, text: this.nomenclatoare.scopuri[cod] || cod }))
    },
    /** Gestiunile pentru lista de selectie a magazinului, cu codul curent daca e strain de lista. */
    optiuniGestiuni() {
      const optiuni = [
        { value: '', text: '— alegeți gestiunea —' },
        ...this.gestiuni.map(g => ({ value: g.cod_furnizor, text: `${g.denumire} (${g.cod_furnizor})` })),
      ]

      const cod = this.declaratia && this.declaratia.loc_final && this.declaratia.loc_final.magazin_cod

      if (cod && !this.gestiuni.some(g => g.cod_furnizor === cod)) {
        optiuni.push({ value: cod, text: `${cod} (gestiune nouă)` })
      }

      return optiuni
    },
    /** Ce fel de loc cere traseul la operatiunea aleasa (ptf / birou_vamal / adresa). */
    felTraseu() {
      const tip = this.declaratia ? this.declaratia.tip_operatiune : null

      return (this.nomenclatoare.traseu_pe_operatiune || {})[tip] || { start: 'adresa', final: 'adresa' }
    },
  },
  created() {
    this.incarcaNomenclatoarele()
    this.incarcaLista()
  },
  methods: {
    perechi(obiect, text) {
      return Object.keys(obiect || {}).map(cod => ({
        value: Number.isNaN(Number(cod)) ? cod : Number(cod),
        text: text ? `${cod} — ${obiect[cod]}` : obiect[cod],
      }))
    },
    culoareStare(stare) {
      return {
        ciorna: 'light-secondary',
        depusa: 'light-warning',
        validata: 'light-success',
        respinsa: 'light-danger',
      }[stare] || 'light-secondary'
    },
    mesajEroare(err, implicit) {
      return err.response && err.response.data && err.response.data.message
        ? err.response.data.message
        : implicit
    },
    incarcaNomenclatoarele() {
      this.$http.get('/anaf-etransport/declaratii/nomenclatoare')
        .then(raspuns => {
          this.nomenclatoare = raspuns.data
          this.importPermis = !!raspuns.data.import_permis
          this.cifImplicit = raspuns.data.cif_implicit || ''

          if (this.importPermis) {
            this.incarcaGestiunile()
          }

          // Nomenclatoarele pot sosi dupa ce omul a apucat sa deschida ciorna.
          if (this.declaratia && !this.declaratia.id && !this.declaratia.cif_declarant) {
            this.declaratia.cif_declarant = this.cifImplicit
          }
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Nomenclatoarele nu s-au putut încărca')
        })
    },
    incarcaLista() {
      this.listaInCurs = true
      const params = this.filtruStare ? { stare: this.filtruStare } : {}

      this.$http.get('/anaf-etransport/declaratii', { params })
        .then(raspuns => {
          this.declaratii = raspuns.data.data || []
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Declarațiile nu s-au putut încărca')
        })
        .finally(() => {
          this.listaInCurs = false
        })
    },
    deschideNoua() {
      this.info = ''
      this.eroare = ''
      this.declaratia = declaratieGoala()
      // Declarantul e de obicei chiar clientul; ramane editabil.
      this.declaratia.cif_declarant = this.cifImplicit
      this.tipOperatiuneSchimbat()
    },
    deschide(id) {
      this.info = ''
      this.eroare = ''

      this.$http.get(`/anaf-etransport/declaratii/${id}`)
        .then(raspuns => {
          this.declaratia = raspuns.data.data
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Declarația nu s-a putut deschide')
        })
    },
    inchideFormularul() {
      this.declaratia = null
      this.incarcaLista()
    },
    sterge(id) {
      this.$bvModal.msgBoxConfirm('Ștergeți ciorna?', { okTitle: 'Șterge', cancelTitle: 'Renunță' })
        .then(confirmat => {
          if (!confirmat) return

          this.$http.delete(`/anaf-etransport/declaratii/${id}`)
            .then(() => this.incarcaLista())
            .catch(err => {
              this.eroare = this.mesajEroare(err, 'Ștergerea a eșuat')
            })
        })
    },
    tipOperatiuneSchimbat() {
      if (!this.declaratia) return

      const fel = this.felTraseu

      // Traseul isi schimba felul dupa operatiune; ce era completat pentru alt fel nu se pastreaza.
      if ((this.declaratia.loc_start || {}).tip !== fel.start) {
        this.declaratia.loc_start = { tip: fel.start }
      }
      if ((this.declaratia.loc_final || {}).tip !== fel.final) {
        this.declaratia.loc_final = { tip: fel.final }
      }

      // Scopul implicit al liniilor: primul permis la operatiunea aleasa.
      const permise = (this.nomenclatoare.scopuri_pe_operatiune || {})[this.declaratia.tip_operatiune] || []
      this.declaratia.linii.forEach(linie => {
        if (!permise.includes(linie.scop_operatiune)) {
          // eslint-disable-next-line no-param-reassign
          linie.scop_operatiune = permise[0] || 9999
        }
      })
    },
    adaugaLinie() {
      const permise = (this.nomenclatoare.scopuri_pe_operatiune || {})[this.declaratia.tip_operatiune] || []

      this.declaratia.linii.push({
        cod_tarifar: '',
        denumire: '',
        scop_operatiune: permise[0] || 9999,
        cantitate: null,
        um: 'H87',
        greutate_neta: null,
        greutate_bruta: null,
        valoare: null,
        valoare_lei: null,
      })
    },
    importaFisierele() {
      this.eroare = ''
      this.importInCurs = true

      const formular = new FormData()
      this.fisiereDeImportat.forEach(fisier => formular.append('fisiere[]', fisier))
      formular.append('grupate', this.grupate ? 1 : 0)

      this.$http.post('/anaf-etransport/declaratii/importa', formular, { headers: { 'Content-Type': 'multipart/form-data' } })
        .then(raspuns => {
          this.aplicaImportul(raspuns.data)
          this.fisiereDeImportat = []
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Importul a eșuat')
        })
        .finally(() => {
          this.importInCurs = false
        })
    },
    aplicaImportul(rezultat) {
      const permise = (this.nomenclatoare.scopuri_pe_operatiune || {})[this.declaratia.tip_operatiune] || []
      const scop = permise[0] || 9999

      const linii = (rezultat.linii || []).map(linie => ({
        ...linie,
        scop_operatiune: scop,
        valoare_lei: null,
      }))

      this.declaratia.linii = this.declaratia.linii.concat(linii)
      this.declaratia.fisiere_importate = (this.declaratia.fisiere_importate || []).concat(rezultat.fisiere || [])

      const antet = rezultat.antet || {}
      if (antet.valuta) this.declaratia.valuta = antet.valuta
      if (antet.partener_denumire && !this.declaratia.partener_denumire) {
        this.declaratia.partener_denumire = antet.partener_denumire
      }
      if (antet.partener_cod && !this.declaratia.partener_cod) {
        this.declaratia.partener_cod = antet.partener_cod
      }
      if (antet.partener_tara && !this.declaratia.partener_tara) {
        this.declaratia.partener_tara = antet.partener_tara
      }
      if (antet.document_numar) {
        this.declaratia.documente.push({
          tip: 20,
          numar: antet.document_numar,
          data: antet.document_data || '',
          observatii: '',
        })
      }

      this.recalculeazaLeii()

      const avertismente = rezultat.avertismente || []
      this.info = `${linii.length} linii importate.${avertismente.length ? ` ${avertismente.join(' ')}` : ''}`
    },
    cautaCodVamal(termen) {
      if (!termen || termen.length < 2) return

      this.$http.get('/anaf-etransport/declaratii/coduri-vamale', { params: { q: termen } })
        .then(raspuns => {
          this.coduriVamaleGasite = raspuns.data.data || []
        })
        .catch(() => {
          // autocomplete-ul nu blocheaza formularul
        })
    },
    codVamalAles(linie) {
      const gasit = this.coduriVamaleGasite.find(cod => cod.cod === linie.cod_tarifar)

      if (gasit) {
        // eslint-disable-next-line no-param-reassign
        linie.denumire = gasit.denumire
      }
    },
    iaCursulBnr(tacut) {
      if (!this.declaratia || !this.editabila) return

      if (this.declaratia.valuta === 'RON') {
        this.declaratia.curs = 1
        this.recalculeazaLeii()

        return
      }

      const data = this.declaratia.data_transport || new Date().toISOString().slice(0, 10)

      this.$http.get('/anaf-etransport/declaratii/curs', { params: { valuta: this.declaratia.valuta, data } })
        .then(raspuns => {
          if (raspuns.data.curs) {
            this.declaratia.curs = raspuns.data.curs
            this.recalculeazaLeii()
          } else if (tacut !== true) {
            this.eroare = raspuns.data.message || 'Nu s-a găsit curs BNR'
          }
        })
        .catch(err => {
          if (tacut !== true) this.eroare = this.mesajEroare(err, 'Cursul BNR nu s-a putut lua')
        })
    },
    /** valoare (in valuta) x curs = valoare lei; scrie doar unde e de scris. */
    recalculeazaLeii(peste) {
      const curs = this.declaratia.valuta === 'RON' ? 1 : this.declaratia.curs

      if (!curs) return

      this.declaratia.linii.forEach(linie => {
        if (linie.valoare !== null && linie.valoare !== '' && (peste === true || !linie.valoare_lei)) {
          // eslint-disable-next-line no-param-reassign
          linie.valoare_lei = Math.round(linie.valoare * curs * 100) / 100
        }
      })
    },
    /** Imparte kg brut total pe linii, proportional cu kg net. */
    imparteGreutateaBruta() {
      const totalNet = this.declaratia.linii.reduce((suma, linie) => suma + (linie.greutate_neta || 0), 0)

      if (!this.greutateBrutaTotala || !totalNet) {
        this.eroare = 'Completați kg brut total și greutățile nete pe linii.'

        return
      }

      this.declaratia.linii.forEach(linie => {
        // eslint-disable-next-line no-param-reassign
        linie.greutate_bruta = Math.round(((linie.greutate_neta || 0) / totalNet) * this.greutateBrutaTotala * 1000) / 1000
      })
    },
    total(camp) {
      const suma = (this.declaratia.linii || []).reduce((acumulat, linie) => acumulat + (Number(linie[camp]) || 0), 0)

      return Math.round(suma * 1000) / 1000
    },
    salveaza(dupa) {
      this.eroare = ''
      this.salvareInCurs = true

      const cale = this.declaratia.id
        ? `/anaf-etransport/declaratii/${this.declaratia.id}`
        : '/anaf-etransport/declaratii'

      return this.$http.post(cale, this.declaratia)
        .then(raspuns => {
          this.declaratia = raspuns.data.data

          if (dupa) return dupa()

          this.info = 'Ciorna a fost salvată.'

          return null
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Salvarea a eșuat')
        })
        .finally(() => {
          this.salvareInCurs = false
        })
    },
    depune() {
      this.info = ''
      this.eroare = ''

      this.salveaza(() => {
        this.depunereInCurs = true

        return this.$http.post(`/anaf-etransport/declaratii/${this.declaratia.id}/depune`)
          .then(raspuns => {
            this.declaratia = raspuns.data.data
            this.info = this.declaratia.uit
              ? `Declarația a fost validată. UIT: ${this.declaratia.uit}`
              : `Declarația a fost depusă, index de încărcare ${this.declaratia.index_incarcare}. Verificați starea pentru UIT.`
          })
          .catch(err => {
            const date = err.response && err.response.data
            const erori = date && date.erori ? date.erori : []

            this.eroare = erori.length
              ? erori.map(e => e.errorMessage || e.mesaj || e).join(' | ')
              : this.mesajEroare(err, 'Depunerea a eșuat')

            if (date && date.data) this.declaratia = date.data
          })
          .finally(() => {
            this.depunereInCurs = false
          })
      })
    },
    verifica() {
      this.eroare = ''

      this.$http.post(`/anaf-etransport/declaratii/${this.declaratia.id}/verifica`)
        .then(raspuns => {
          this.declaratia = raspuns.data.data
          this.info = this.declaratia.uit
            ? `Declarația are UIT: ${this.declaratia.uit}`
            : 'Declarația e încă în prelucrare la ANAF.'
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Starea nu s-a putut verifica')
        })
    },
    importaArhiva() {
      this.arhivaEroare = ''
      this.arhivaRezultat = null
      this.arhivaInCurs = true

      const formular = new FormData()
      formular.append('fisier', this.arhivaFisier)

      this.$http.post('/anaf-etransport/declaratii/importa-arhiva', formular, { headers: { 'Content-Type': 'multipart/form-data' } })
        .then(raspuns => {
          this.arhivaRezultat = { ciorne: raspuns.data.data || [], avertismente: raspuns.data.avertismente || [] }
          this.arhivaFisier = null
          this.incarcaLista()

          // Codurile de magazin nestiute: omul spune pe rand cum se numesc gestiunile.
          this.gestiuniNoi = raspuns.data.gestiuni_noi || []
          this.urmatoareaGestiune()
        })
        .catch(err => {
          this.arhivaEroare = this.mesajEroare(err, 'Importul arhivei a eșuat')
        })
        .finally(() => {
          this.arhivaInCurs = false
        })
    },
    incarcaGestiunile() {
      this.$http.get('/anaf-etransport/declaratii/gestiuni')
        .then(raspuns => {
          this.gestiuni = raspuns.data.data || []
        })
        .catch(() => {})
    },
    alegeGestiunea(codFurnizor) {
      const gestiune = this.gestiuni.find(g => g.cod_furnizor === codFurnizor)

      this.$set(this.declaratia.loc_final, 'magazin_cod', codFurnizor || null)

      if (gestiune) {
        this.$set(this.declaratia.loc_final, 'magazin_denumire', gestiune.denumire)
      }
    },
    /** Scoate din coada urmatorul cod nou de gestiune si deschide fereastra lui. */
    urmatoareaGestiune() {
      const noua = this.gestiuniNoi.shift()

      this.gestiuneEroare = ''
      this.gestiuneNoua = noua
        ? {
          cod_furnizor: noua.cod_furnizor,
          denumire_furnizor: noua.denumire_furnizor,
          denumire: noua.denumire_furnizor || '',
          cod: '',
          prescurtare: '',
        }
        : null
      this.gestiuneVizibila = !!noua
    },
    salveazaGestiunea() {
      this.gestiuneEroare = ''
      this.gestiuneInCurs = true

      this.$http.post('/anaf-etransport/declaratii/gestiuni', {
        cod_furnizor: this.gestiuneNoua.cod_furnizor,
        denumire: this.gestiuneNoua.denumire,
        cod: this.gestiuneNoua.cod || null,
        prescurtare: this.gestiuneNoua.prescurtare || null,
      })
        .then(() => {
          this.incarcaGestiunile()
          this.incarcaLista()
          this.urmatoareaGestiune()
        })
        .catch(err => {
          this.gestiuneEroare = this.mesajEroare(err, 'Gestiunea nu s-a putut salva')
        })
        .finally(() => {
          this.gestiuneInCurs = false
        })
    },
    deschideFormular() {
      this.formularEroare = ''
      // Toate cele cu UIT vin bifate; omul scoate ce nu pleaca in transportul asta.
      this.formularIdAlese = this.declaratiiCuUit.map(d => d.id)
      this.formularVizibil = true
    },
    alegeToateFormular() {
      this.formularIdAlese = this.formularIdAlese.length === this.declaratiiCuUit.length
        ? []
        : this.declaratiiCuUit.map(d => d.id)
    },
    genereazaFormular() {
      this.formularEroare = ''
      this.formularInCurs = true

      this.$http.post('/anaf-etransport/declaratii/formular-transportator', {
        ids: this.formularIdAlese,
        adrese: this.formularAdrese || null,
      })
        .then(raspuns => {
          const rezultat = raspuns.data.data

          // Fisierul se descarca pe loc, din raspuns.
          const octeti = Uint8Array.from(atob(rezultat.continut), litera => litera.charCodeAt(0))
          const url = window.URL.createObjectURL(new Blob([octeti], {
            type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
          }))
          const legatura = document.createElement('a')
          legatura.href = url
          legatura.download = rezultat.nume
          legatura.click()
          setTimeout(() => window.URL.revokeObjectURL(url), 60000)

          this.formularVizibil = false
          this.info = `Formularul cu ${rezultat.foi} foi s-a descărcat${
            rezultat.trimis_catre.length ? ` și a plecat către ${rezultat.trimis_catre.join(', ')}.` : '.'}`
        })
        .catch(err => {
          this.formularEroare = this.mesajEroare(err, 'Formularul nu a putut fi întocmit')
        })
        .finally(() => {
          this.formularInCurs = false
        })
    },
    deschideIntrastat() {
      this.intrastatEroare = ''

      // Luna incheiata e cea care se declara de obicei.
      const lunaTrecuta = new Date()
      lunaTrecuta.setDate(1)
      lunaTrecuta.setMonth(lunaTrecuta.getMonth() - 1)

      this.intrastat.luna = lunaTrecuta.getMonth() + 1
      this.intrastat.anul = lunaTrecuta.getFullYear()

      // Persoana de contact ramane de la o luna la alta.
      try {
        const salvat = JSON.parse(window.localStorage.getItem('intrastat_contact'))
        if (salvat) {
          this.intrastat = { ...this.intrastat, ...salvat }
        }
      } catch (e) {
        // setare veche sau stricata — campurile raman goale
      }

      this.intrastatVizibil = true
    },
    genereazaIntrastat() {
      this.intrastatEroare = ''
      this.intrastatInCurs = true

      window.localStorage.setItem('intrastat_contact', JSON.stringify({
        flux: this.intrastat.flux,
        incoterm: this.intrastat.incoterm,
        nume: this.intrastat.nume,
        prenume: this.intrastat.prenume,
        telefon: this.intrastat.telefon,
        email: this.intrastat.email,
      }))

      this.$http.post('/anaf-etransport/declaratii/intrastat', this.intrastat)
        .then(raspuns => {
          const rezultat = raspuns.data.data

          // Fisierul se descarca pe loc, gata de incarcat in aplicatia Intrastat.
          const url = window.URL.createObjectURL(new Blob([rezultat.xml], { type: 'application/xml' }))
          const legatura = document.createElement('a')
          legatura.href = url
          legatura.download = rezultat.nume
          legatura.click()
          setTimeout(() => window.URL.revokeObjectURL(url), 60000)

          this.intrastatVizibil = false
          this.info = `Declarația Intrastat: ${rezultat.linii} linii din ${rezultat.declaratii} declarații, `
            + `${rezultat.valoare.toLocaleString('ro-RO')} lei. Fișierul ${rezultat.nume} s-a descărcat — `
            + 'se încarcă în aplicația Intrastat (INS) pentru validare și depunere.'
        })
        .catch(err => {
          this.intrastatEroare = this.mesajEroare(err, 'Declarația Intrastat nu a putut fi întocmită')
        })
        .finally(() => {
          this.intrastatInCurs = false
        })
    },
    deschideEmail(declaratie) {
      this.emailDeclaratieId = declaratie.id
      this.emailUit = declaratie.uit
      this.emailVizibil = true
    },
    trimiteEmailUit() {
      if (!this.emailAdrese.trim() || this.emailInCurs) return

      this.emailInCurs = true

      this.$http.post(`/anaf-etransport/declaratii/${this.emailDeclaratieId}/email`, { adrese: this.emailAdrese })
        .then(raspuns => {
          this.emailVizibil = false
          this.info = raspuns.data.message
        })
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Emailul nu a putut fi trimis')
        })
        .finally(() => {
          this.emailInCurs = false
        })
    },
    verificaDinLista(id) {
      this.$http.post(`/anaf-etransport/declaratii/${id}/verifica`)
        .then(() => this.incarcaLista())
        .catch(err => {
          this.eroare = this.mesajEroare(err, 'Starea nu s-a putut verifica')
        })
    },
  },
}
</script>

<style scoped>
/* Lista declaratiilor din formularul transportatorului: multe nu lungesc fereastra. */
.lista-formular {
  max-height: 14rem;
  overflow-y: auto;
  display: block;
}
</style>
