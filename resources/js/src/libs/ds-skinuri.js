/*
 * Skinurile DianaSoft, aceleași ca în aplicația DV Auto.
 *
 * Acolo interfața e Vue 3 cu Vuetify și fiecare skin își are tema lui; aici e
 * Vue 2 cu Bootstrap, deci tot ce ține de culori, fonturi și forme stă în CSS
 * (vezi resources/scss/ds-skinuri.scss), iar de aici se pune doar clasa pe
 * <html>.
 *
 * Cele două skinuri închise nu-și refac singure toată interfața: se așază peste
 * tema întunecată pe care Vuexy o are deja („dark-layout" pe body). Altfel ar fi
 * trebuit rescrisă fiecare componentă, de două ori, pentru ceva ce aplicația
 * știe să facă.
 */

export const skinuri = [
  {
    id: 'standard',
    nume: 'Standard',
    descriere: 'Interfața de până acum, cu light și dark',
    clasa: null,
    intunecat: false,
    icon: 'GridIcon',
  },
  {
    id: 'dsgarage',
    nume: 'DS Garage',
    descriere: 'Crem cald, teracotă, forme rotunjite',
    clasa: 'skin-dsgarage',
    intunecat: false,
    icon: 'ToolIcon',
  },
  {
    id: 'industrial',
    nume: 'Industrial',
    descriere: 'Închis și colțuros, portocaliu de siguranță',
    clasa: 'skin-industrial',
    intunecat: true,
    icon: 'SquareIcon',
  },
  {
    id: 'pastel',
    nume: 'Pastel editorial',
    descriere: 'Roz prăfuit, literă cu picioare, carduri moi',
    clasa: 'skin-pastel',
    intunecat: false,
    icon: 'FeatherIcon',
  },
  {
    id: 'futurist',
    nume: 'Futurist',
    descriere: 'Panouri de sticlă, teal și mov, pe întuneric',
    clasa: 'skin-futurist',
    intunecat: true,
    icon: 'CpuIcon',
  },
]

/** Skinul cerut, sau cel standard când numele nu spune nimic. */
export function gasesteSkinul(id) {
  return skinuri.find(s => s.id === id) || skinuri[0]
}

/**
 * Pune skinul pe pagină.
 *
 * Se curăță întâi toate clasele de skin: altfel două puse una peste alta s-ar
 * bate pe aceleași reguli, iar care iese deasupra ar ține de ordinea din fișier,
 * nu de ce a ales omul.
 */
export function aplicaSkinul(id) {
  const skin = gasesteSkinul(id)
  const radacina = document.documentElement

  skinuri.forEach(s => {
    if (s.clasa) radacina.classList.remove(s.clasa)
  })

  if (skin.clasa) radacina.classList.add(skin.clasa)

  /*
   * Temelia întunecată: o dă Vuexy, prin „dark-layout". Când se pleacă de pe un
   * skin întunecat, ea se ridică numai dacă omul n-a ales el însuși tema
   * întunecată din setările obișnuite — aceea rămâne a lui.
   */
  const alesIntuneric = localStorage.getItem('vuexy-skin') === 'dark'

  if (skin.intunecat) document.body.classList.add('dark-layout')
  else if (!alesIntuneric) document.body.classList.remove('dark-layout')
}

/** La pornirea aplicației: se pune ce era ales data trecută. */
export function pornesteSkinul() {
  aplicaSkinul(localStorage.getItem('ds_skin') || 'standard')
}
