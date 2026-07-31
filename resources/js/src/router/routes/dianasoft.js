export default  [

               
           
      { //Ordine de blocare ANAF
                    path: '/ordinedeblocareanaf',
                    name: 'ordinedeblocareanaf',
                    props:true,
                    props(route) {
                      return  route.query || {}
                    },
                    component: () => import('@/views/app_pages/Ordinedeblocareanaf.vue'),
                    meta: {
                            rule: 'editor',
                          }
                },       
                      
    { //Litigii
                    path: '/litigii',
                    name: 'litigii',
                    props:true,
                    props(route) {
                      return  route.query || {}
                    },
                    component: () => import('@/views/app_pages/Litigiu.vue'),
                    meta: {
                            rule: 'editor',
                          }
                },                              
   
 { //Curs BNR
                    path: '/cursbnr',
                    name: 'cursbnr',
                    props:true,
                    props(route) {
                      return  route.query || {}
                    },
                    component: () => import('@/views/app_pages/Cursbnr.vue'),
                    meta: {
                            rule: 'editor',
                          }
                },
 { //Jurnal SMSM
                    path: '/jurnalsms',
                    name: 'jurnalsms',
                    props:true,
                    props(route) {
                      return  route.query || {}
                    },
                    component: () => import('@/views/app_pages/Jurnalsms.vue'),
                    meta: {
                            rule: 'editor',
                          }
                },
 { //IPautorizat
                    path: '/ipautorizat',
                    name: 'ipautorizat',
                    props:true,
                    props(route) {
                      return  route.query || {}
                    },
                    component: () => import('@/views/app_pages/Ipautorizat.vue'),
                    meta: {
                            rule: 'editor',
                          }
                },
      { //Home 
                         path: '/',
                         name: 'home',
                         component: () => import('@/views/app_pages/Home.vue'),
                         meta: {
                                     rule: 'admin',
                                     requiresAuth: true 
                                 }
                 },
               { //DianaSoftMenuOption    
                        path: '/dianasoftmenuoption',
                        name: 'dianasoftmenuoption',
                        component: () => import('@/views/app_pages/DianaSoftMenuOption.vue'),
                        meta: {
                                    rule: 'editor',
                                }
                },
                
                { //Permission
                    path: '/permission',
                    name: 'permission',
                    component: () => import('@/views/app_pages/Permission.vue'),
                    meta: {
                                       rule: 'editor',
                                        }
                },
                { //DianaSoftModel
                    path: '/dianasoftmodel',
                    name: 'dianasoftmodel',
                    component: () => import('@/views/app_pages/dianasoft/Dianasoftmodel.vue'),
                    meta: {
                                       rule: 'editor',
                                        }
                }, 
                { //SPV
                    path: '/spv',
                    name: 'spv',
                    component: () => import('@/views/app_pages/Spv.vue'),
                    meta: {
                                       rule: 'editor',
                                        }
                },
                // Paginile de mai jos au fost unite in /spv (taburi); linkurile vechi raman valide
                { path: '/anaf-societati', redirect: { name: 'spv', query: { tab: 'entitati' } } },
                { path: '/declaratii', redirect: { name: 'spv', query: { tab: 'declaratii' } } },
                { path: '/spv-solicitari', redirect: { name: 'spv', query: { tab: 'solicitari' } } },
                { //E-transport
                    path: '/etransport',
                    name: 'etransport',
                    component: () => import('@/views/app_pages/Etransport.vue'),
                    meta: {
                                       rule: 'editor',
                                        }
                },
                { //Administrarea clientilor — doar contul din config('app.super_admin')
                    path: '/administrare',
                    name: 'administrare',
                    component: () => import('@/views/app_pages/Administrare.vue'),
                    meta: {
                                       rule: 'editor',
                                        }
                },
                { //Portal Just
                    path: '/portal-just',
                    name: 'portal-just',
                    component: () => import('@/views/app_pages/PortalJust.vue'),
                    meta: {
                                       rule: 'editor',
                                        }
                },
                { //Vector fiscal
                    path: '/vector-fiscal',
                    name: 'vector-fiscal',
                    component: () => import('@/views/app_pages/VectorFiscal.vue'),
                    meta: {
                                       rule: 'editor',
                                        }
                },
               
                 { //Notificari
                    path: '/notificari',
                    name: 'notificari',
                    component: () => import('@/views/app_pages/Notificationtype.vue'),
                    meta: {
                                       rule: 'editor',
                                        }
                },
                { //Jurnal notificari
                    path: '/notificationlog',
                    name: 'notificationlog',
                    component: () => import('@/views/app_pages/Notificationlog.vue'),
                    meta: {
                                       rule: 'editor',
                                        }
                },
                { //Jurnal notificari
                    path: '/jurnaltask',
                    name: 'jurnaltask',
                    component: () => import('@/views/app_pages/Task.vue'),
                    meta: {
                                       rule: 'editor',
                                        }
                },
                { //Company
                    path: '/companies',
                    name: 'companies',
                    component: () => import('@/views/app_pages/Company.vue'),
                    meta: {
                                       rule: 'editor',
                                        }
                },
                { //Datefirmeregcom
                    path: '/datefirmeregcom',
                    name: 'datefirmeregcom',
                    component: () => import('@/views/app_pages/Datefirmeregcom.vue'),
                    meta: {
                                       rule: 'editor',
                                        }
                },
                { //Judete
                    path: '/judete',
                    name: 'judete',
                    component: () => import('@/views/app_pages/Judet.vue'),
                    meta: {
                                       rule: 'editor',
                                        }
                },
                { //Tari
                    path: '/tari',
                    name: 'tari',
                    component: () => import('@/views/app_pages/Tari.vue'),
                    meta: {
                                       rule: 'editor',
                                        }
                },
                { //Optiuni dropdown
                    path: '/optiunidropdown',
                    name: 'optiunidropdown',
                    component: () => import('@/views/app_pages/Optiunidropdown.vue'),
                    meta: {
                                       rule: 'editor',
                                        }
                }, 
               
              
                { //Sarbatori legale
                    path: '/sarbatorilegale',
                    name: 'sarbatorilegale',
                    component: () => import('@/views/app_pages/SarbatoriLegale.vue'),
                    meta: {
                                       rule: 'editor',
                                        }
                }, 
            
                { //Localitati
                    path: '/localitati',
                    name: 'localitati',
                    component: () => import('@/views/app_pages/Localitati.vue'),
                    meta: {
                                       rule: 'editor',
                                        }
                },
             
             
              
                {  //Nombanci
                    path: '/nombanci',
                    name: 'nombanci',
                    component: () => import('@/views/app_pages/Nombanci.vue'),
                    meta: {
                                       rule: 'editor',
                                        }
                },
              
                 {  //Documente PDF
                    path: '/documentepdf',
                    name: 'documentepdf',
                    component: () => import('@/views/app_pages/Documentepdf.vue'),
                    meta: {
                                       rule: 'editor',
                                        }
                },
                
              
                { //pdfviewer
                    path: '/pdfviewer',
                    name: 'pdfviewer',
                    props:true,
                    //  props(route) {
                    //   return  route.query || {}
                    // },
                    component: () => import('@/views/app_pages/my-components/PDFViewer.vue'),
                    meta: {
                            rule: 'editor',
                          }
                },
                { //pdfprinter
                    path: '/pdfprinter',
                    name: 'pdfprinter',
                    props:true,
                    //  props(route) {
                    //   return  route.query || {}
                    // },
                    component: () => import('@/views/app_pages/my-components/PDFPrinter.vue'),
                    meta: {
                            rule: 'editor',
                          }
                },
                //COMERCIAL
                //TEHNIC
              
              
                {  //Cine ma suna ?
                    path: '/cinemasuna',
                    name: 'cinemasuna',
                     props:true,
                    props(route) {
                      return  route.query || {}
                    },
                    component: () => import('@/views/app_pages/my-components/Cinemasuna.vue'),
                    meta: {
                            rule: 'editor',
                          }
                },
                 {  //Documente PDF arhiva
                    path: '/documentepdfarhiva',
                    name: 'documentepdfarhiva',
                     props:true,
                    props(route) {
                      return  route.query || {}
                    },
                    component: () => import('@/views/app_pages/Documentepdfarhiva.vue'),
                    meta: {
                            rule: 'editor',
                          }
                },
               
                //TEHNIC
                //SECRETARIAT

         
                //SECRETARIAT
                //JURIDIC

                //JURIDIC
                //CONTABILITATE
            
              
                //CONTABILITATE
                //FINANCIAR
            
               
            
             
                { //Utilizatori
                        path: '/utilizatori',
                        name: 'utilizatori',
                        component: () => import('@/views/app_pages/Utilizatori.vue'),
                        meta: {
                                           rule: 'editor',
                                            }
                },
                { //Jurnal de operare
                        path: '/activity',
                        name: 'activity',
                        component: () => import('@/views/app_pages/Activity.vue'),
                        meta: {
                               rule: 'editor',
                               }
                },
              
                
               
    ]