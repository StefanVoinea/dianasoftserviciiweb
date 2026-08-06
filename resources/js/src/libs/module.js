import axios from '@axios'

/*
 * Modulele pe care le vede contul conectat: SPV Curier, Dispecer e-Transport,
 * Grefier alert.
 *
 * Lista vine de la server (/context), fiindcă ține de două lucruri pe care
 * browserul n-are de unde să le știe: abonamentul firmei și darea către om.
 * Se cere o singură dată pe încărcare de pagină, iar răspunsul se ține și în
 * localStorage — la un refresh, paznicul rutelor trebuie să hotărască pe loc,
 * înainte de a apuca să răspundă serverul.
 *
 * Ce se ține aici este doar înlesnire, nu pază: cererile către un modul nedat
 * sunt oprite oricum de server. De aceea nici nu e nevoie ca localStorage să
 * fie de încredere.
 */

const CHEIE = 'dianasoft_module'

let cerere = null

/** Ultimele module știute, dintr-o încărcare anterioară a paginii. */
export const moduleStiute = () => {
  try {
    const scris = window.localStorage.getItem(CHEIE)

    return scris ? JSON.parse(scris) : null
  } catch (e) {
    return null
  }
}

/**
 * Contextul contului, cerut o singură dată pe încărcare de pagină, oricâți ar
 * întreba între timp: și paznicul rutelor, și antetul cu siglele au nevoie de
 * el, iar două cereri pentru același răspuns n-ar folosi nimănui.
 */
export const contextul = () => {
  if (cerere) return cerere

  cerere = axios.get('/context')
    .then(({ data }) => {
      const context = (data && data.data) || {}

      try {
        window.localStorage.setItem(CHEIE, JSON.stringify(context.module || []))
      } catch (e) {
        // Un localStorage plin sau oprit nu are voie să oprească navigarea.
      }

      return context
    })
    .catch(() => ({ module: moduleStiute() || [] }))

  return cerere
}

export const moduleleMele = () => contextul().then(context => context.module || [])

/** La deconectare, ca următorul om să nu moștenească modulele celui dinainte. */
export const uitaModulele = () => {
  cerere = null

  try {
    window.localStorage.removeItem(CHEIE)
  } catch (e) {
    // nimic de făcut
  }
}

export default moduleleMele
