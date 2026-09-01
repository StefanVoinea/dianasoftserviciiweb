import axios from '@axios'

/*
 * Ce are voie să vadă contul conectat: modulele lui — SPV Curier, Dispecer
 * e-Transport, Grefier alert — și dacă e administratorul aplicației.
 *
 * Răspunsul vine de la server (/context), fiindcă ține de lucruri pe care
 * browserul n-are de unde să le știe: abonamentul firmei și darea către om. Se
 * cere o singură dată pe încărcare de pagină, iar ce s-a aflat se ține și în
 * localStorage: la un refresh, paznicul rutelor și antetul trebuie să hotărască
 * pe loc, înainte de a apuca serverul să răspundă.
 *
 * Ce se ține aici e doar înlesnire, nu pază: cererile către un modul nedat, ca
 * și zona de administrare, sunt oprite oricum de server. De aceea nici nu e
 * nevoie ca localStorage să fie de încredere — cel mult, cineva își arată
 * singur o siglă pe care apăsând-o primește „nu aveți acces".
 */

const CHEIE = 'dianasoft_context'

let cerere = null

/** Ce se știa la ultima încărcare: {module, super_admin}. */
export const contextStiut = () => {
  try {
    const scris = window.localStorage.getItem(CHEIE)
    const citit = scris ? JSON.parse(scris) : null

    return citit && typeof citit === 'object' ? citit : {}
  } catch (e) {
    return {}
  }
}

/** @return {string[]|null} null = încă nu se știe */
export const moduleStiute = () => {
  const stiut = contextStiut()

  return Array.isArray(stiut.module) ? stiut.module : null
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
        window.localStorage.setItem(CHEIE, JSON.stringify({
          module: Array.isArray(context.module) ? context.module : null,
          meniu_oprit: Array.isArray(context.meniu_oprit) ? context.meniu_oprit : null,
          super_admin: Boolean(context.super_admin),
        }))
      } catch (e) {
        // Un localStorage plin sau oprit nu are voie să oprească navigarea.
      }

      return context
    })
    .catch(() => {
      /*
       * Pana nu se ține minte: cererea poate fi plecat prea devreme, înainte ca
       * tokenul să ajungă pe antet, si atunci al doilea care întreabă trebuie să
       * primească răspunsul adevărat, nu eșecul primului. Fără randul acesta, o
       * singură cerere picata golea antetul pentru toată încărcarea paginii.
       */
      cerere = null

      // Se întoarce ce se știa de data trecută. Lipsa lui înseamnă „nu se știe",
      // nu „nu are voie": pe necunoscut se lasă omul să treacă.
      return contextStiut()
    })

  return cerere
}

/** @return {Promise<string[]|null>} null = încă nu se știe */
export const moduleleMele = () => contextul()
  .then(context => (Array.isArray(context.module) ? context.module : null))

/**
 * Intrarile de meniu ale modulelor pe care contul nu le are.
 *
 * Meniul omului se tine langa contul lui si poate fi mai vechi decat modulele:
 * cui i s-a luat un modul ii raman intrarile de meniu, si le-ar vedea in antet
 * ca drumuri care nu duc nicaieri. Serverul spune care sunt ele.
 *
 * Gol inseamna „nu se opreste nimic" - si cand chiar nu e nimic de oprit, si
 * cand inca nu se stie. Ca peste tot aici, ascunderea e inlesnire, nu paza.
 *
 * @return {string[]}
 */
export const meniulOprit = () => {
  const stiut = contextStiut()

  return Array.isArray(stiut.meniu_oprit) ? stiut.meniu_oprit : []
}

/** Aceeasi lista, dar dupa ce raspunde serverul. */
export const meniulOpritProaspat = () => contextul()
  .then(context => (Array.isArray(context.meniu_oprit) ? context.meniu_oprit : []))

/** Capetele de meniu ramase fara nimic dedesubt: se deschid goale, deci pleaca. */
const faraCapeteGoale = lista => lista.filter(optiune => (
  Number(optiune.dropdown) !== 1 || lista.some(alta => alta.parent === optiune.name)
))

/**
 * Meniul, fara intrarile modulelor nedate.
 *
 * @param {object[]} optiuni intrarile asa cum vin de la server
 * @param {string[]} oprite slug-urile de ascuns
 * @return {object[]}
 */
export const faraModuleleNedate = (optiuni, oprite) => {
  if (!Array.isArray(optiuni) || !oprite || !oprite.length) return optiuni

  let ramase = optiuni.filter(optiune => oprite.indexOf(optiune.slug) === -1)

  // Un capat golit poate goli la randul lui capatul de deasupra, deci se trece
  // din nou pana cand nu mai pleaca nimic.
  let cate = ramase.length + 1

  while (ramase.length < cate) {
    cate = ramase.length
    ramase = faraCapeteGoale(ramase)
  }

  return ramase
}

/** La deconectare, ca următorul om să nu moștenească drepturile celui dinainte. */
export const uitaModulele = () => {
  cerere = null

  try {
    window.localStorage.removeItem(CHEIE)
  } catch (e) {
    // nimic de făcut
  }
}

export default moduleleMele
