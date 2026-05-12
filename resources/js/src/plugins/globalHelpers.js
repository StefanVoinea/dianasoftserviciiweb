import Vue from 'vue';
// import vuex from 'vuex';
import store from '../store/index'

// Vue.use(vuex);
export const globalHelpers = {
diffInDays(dateFrom, dateTo) {
    // Difference in milliseconds
  dateTo=new Date(dateTo)
  dateFrom=new Date(dateFrom)
    const diffInMs = dateTo - dateFrom;
    // Convert milliseconds to days
    const diffInDays = Math.floor(diffInMs / (1000 * 60 * 60 * 24));
    return diffInDays;
},
addMonths(date, months) {
  const d = new Date(date);           // nu modificăm originalul
  const day = d.getDate();
  d.setDate(1);                       // evităm “overflow”-ul
  d.setMonth(d.getMonth() + months);  // mutăm luna
  const lastDay = new Date(d.getFullYear(), d.getMonth() + 1, 0).getDate();
  d.setDate(Math.min(day, lastDay));  // păstrăm ziua sau ultimul din lună
  return d;
},
lunainlitere(luna){
  let lunaInt=Number(luna)
  let rasp=""; 
  switch (lunaInt){
       case 1:rasp="Ianuarie" 
              break;
       case 2:rasp="Februarie" 
              break;
       case 3:rasp="Martie" 
              break;
       case 4:rasp="Aprilie" 
              break;
       case 5:rasp="Mai" 
              break;
       case 6:rasp="Iunie" 
              break;
       case 7:rasp="Iulie" 
              break;
       case 8:rasp="August" 
              break;
       case 9:rasp="Septembrie" 
              break;
       case 10:rasp="Octombrie" 
              break;
       case 11:rasp="Noiembrie" 
              break;
       case 12:rasp="Decembrie" 
              break;
        default:
            rasp=""
  }
  return rasp;
},

 diffInMonths(dateFrom, dateTo) {
    // Calculate difference in total months
  dateTo=new Date(dateTo)
  dateFrom=new Date(dateFrom)

    let months = (dateTo.getFullYear() - dateFrom.getFullYear()) * 12;
    months -= dateFrom.getMonth();
    months += dateTo.getMonth();
  
    // Consider the days to adjust the calculation accurately
    return months - (dateTo.getDate() < dateFrom.getDate() ? 1 : 0);
},
      
  PMT(procDob, nr_luni, valCredit)
        {
          procDob = procDob / 100;
         var  amount = procDob * -valCredit * Math.pow((1 + procDob), nr_luni) / (1 - Math.pow((1 + procDob), nr_luni));
          return globalHelpers.roundFormat(amount,2);
        },
  parseLocaleDate:(data)=>{
    if(data.toString().indexOf("-")!=-1){
      return new Date(data)
    }
    if(data.toString().indexOf(".")==-1){
      return data
    }else{

     var b = data.split(".");
     return new Date(b[2], b[1]-1, b[0]);
    }

  },
  perioadablocata:(data,tip)=>{
      
      let perioada=JSON.parse(store.state.app.perioadablocata)
      let dt=new Date(globalHelpers.parseLocaleDate(data))
      let blocat=false
     
      switch (tip){
        case "Nota contabila":
                  if(dt<=new Date(perioada.data_note)){ return blocat=true }
                  break;
        case "Note contabile":
                   
                  if(dt<=new Date(perioada.data_note)){ return blocat=true }
                  break;
        case "Contracte":
                  
                  if(dt<=new Date(perioada.data_contracte)){ return blocat=true }
                  break; 
        case "Acte aditionale":
                  if(dt<=new Date(perioada.data_acte_aditionale)){ return blocat=true }
                  break;                 
        case "Vanzari":
                if(dt<=new Date(perioada.data_vanzari)){ return blocat=true }
                  break;        
        case "Incasari":
                if(dt<=new Date(perioada.data_incasari)){ return blocat=true }
                  break;        
        case "Documente primite":
                if(dt<=new Date(perioada.data_documenteprimite)){ return blocat=true }
                  break;        
        case "Plati":
                if(dt<=new Date(perioada.data_plati)){ return blocat=true }
                  break;        
        case "Bonuri de consum":
                if(dt<=new Date(perioada.data_bonuriconsum)){ return blocat=true }
                  break;        
        case "Transferuri":
                if(dt<=new Date(perioada.data_transferuri)){ return blocat=true }
                  break;        
        case "Rapoarte de productie":
                if(dt<=new Date(perioada.data_rapoarteproductie)){ return blocat=true }
                  break;        
         default:
            blocat=true
          
      }
      return blocat
  },
  selectezarticol:(cod,denumire)=>{
    let articole=[]
    articole=JSON.parse(store.state.app.articol)
    let articol=articole.filter((art)=>{
       if (cod!=""&&art.cod==cod)
       {
        return art
       }else{

       if (denumire!=""&&art.denumire==denumire)
       {
        return art
       }
       }
    })
    return articol[0]
  },
   roundFormat(value, decimals) {
       let rez= Number(Math.round(value+'e'+decimals)+'e-'+decimals).toFixed(decimals);
       if (isNaN(rez)){
        return 0
       }
       return rez
      },
  
   updateCookiesLocal: (data) => {
   	return new Promise((resolve, reject) => {
      
   	        // this.$vs.loading({color:"success"})
            let payLoad={}   //Extrag cookieLocal
            payLoad.requestType="post"
            payLoad.cookieLocal=data
            payLoad.requestUrl="/utilizatori/cookiesLocal"
            store.dispatch("app/api_Request",payLoad)
                .then(response=>{
                Object.keys(response).reduce(function (obj, key) {
						    store.dispatch('app/'+key,response[key])
                           
						    if (key=="gestiuniPermise"){
								store.dispatch('app/gestiuneaCurenta',JSON.stringify(JSON.parse(response["gestiuniPermise"])[0]))
						    }
						     // console.log(key)
						 }, {})
               		    // this.$vs.loading.close()		
						resolve(response)
                        

            })
            .catch(error => {
		            reject(error)
		          })

    })
      
     
    }
}