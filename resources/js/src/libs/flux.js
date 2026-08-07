/**
 * Citirea unui răspuns care curge, rând cu rând.
 *
 * O descărcare de zeci de documente ține minute: fiecare are pauza cerută de
 * ANAF și drumul până la tokenul clientului. Într-un răspuns obișnuit, omul
 * vede o rotiță și atât — nu știe dacă merge, unde s-a ajuns, sau dacă s-a
 * împotmolit. Serverul trimite de aceea câte un obiect JSON pe rând (NDJSON),
 * iar de aici fiecare rând ajunge la filă de îndată ce sosește.
 *
 * Se folosește fetch, nu axios: numai el dă acces la conținut pe măsură ce vine.
 */

import axios from '@axios'

/** Adresa întreagă a unei rute din API. */
function adresa(cale) {
  // Adresa API vine din window.api_url (definit în pagină); instanța axios nu
  // are baseURL propriu, așa că nu se poate citi doar de acolo.
  const baza = (window.api_url || axios.defaults.baseURL || '').replace(/\/+$/, '')

  return `${baza}/${String(cale).replace(/^\/+/, '')}`
}

/**
 * Ce înseamnă, pe șleau, un răspuns care n-a mers.
 *
 * „HTTP 404" nu spune nimănui nimic — și cel mai adesea nu înseamnă că lipsește
 * ceva din datele omului, ci că serverul nu cunoaște ruta: ori are cod mai
 * vechi, ori i-au rămas rutele în cache de dinainte ca ea să fie adăugată.
 * Scris așa, se știe de la prima citire unde să se caute.
 */
function eroareaLegaturii(status) {
  const talcuri = {
    401: 'sesiunea a expirat — intrați din nou în aplicație',
    403: 'contul acesta nu are drept la operația cerută',
    404: 'serverul nu cunoaște această adresă — are cod mai vechi sau rutele rămase în cache'
      + ' (pe server: php artisan route:clear)',
    419: 'sesiunea a expirat — reîncărcați pagina',
    500: 'serverul a întâmpinat o eroare — vedeți jurnalul aplicației',
    502: 'serverul din față n-a primit răspuns',
    504: 'răspunsul a întârziat prea mult',
  }

  const eroare = new Error(talcuri[status] || `răspuns neașteptat de la server (HTTP ${status})`)
  eroare.status = status

  return eroare
}

/**
 * Cere ruta și dă fiecare rând primit, pe măsură ce sosește.
 *
 * @param {string} cale ruta din API, fără adresa de bază
 * @param {function(object): void} laFiecarePas primește fiecare obiect citit
 * @returns {Promise<void>} se împlinește când fluxul s-a încheiat
 */
export default function citesteFluxul(cale, laFiecarePas) {
  const antete = axios.defaults.headers.common

  return fetch(adresa(cale), {
    headers: {
      // „application/json" trebuie să apară: fără el, la o sesiune expirată
      // Laravel redirecționează spre login în loc să răspundă 401, iar fluxul
      // ar părea gol în loc de eșuat.
      Accept: 'application/json, application/x-ndjson',
      Authorization: antete.Authorization,
      AuthorizationHeader: antete.AuthorizationHeader,
    },
  })
    .then(raspuns => {
      if (!raspuns.ok) throw eroareaLegaturii(raspuns.status)

      const cititor = raspuns.body.getReader()
      const decodor = new TextDecoder('utf-8')
      let ramas = ''

      const urmatorul = () => cititor.read().then(({ done, value }) => {
        if (done) return null

        ramas += decodor.decode(value, { stream: true })

        const randuri = ramas.split('\n')
        // Ultimul poate fi incomplet: rămâne pentru bucata următoare.
        ramas = randuri.pop()

        randuri.forEach(rand => {
          if (!rand.trim()) return

          try {
            laFiecarePas(JSON.parse(rand))
          } catch (e) {
            // Un rând stricat nu are de ce să oprească restul lucrului.
          }
        })

        return urmatorul()
      })

      return urmatorul()
    })
}

/** Browserele fără fetch cu flux (foarte vechi) trebuie să rămână pe calea obișnuită. */
export function areFlux() {
  return typeof window.fetch === 'function' && typeof window.TextDecoder === 'function'
}
