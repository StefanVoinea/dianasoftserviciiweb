<template>
 
 
    <div class="flex justify-center">
   

 
  </div>

</template>

<script>

export default {
  
  name:"home",
  
  data() {
          return {
                  tblRecords:[],
                  showLoading:false,
                }
             
          },
  computed:{
       asset_path(){
            return window.asset_path;
        },
  },
  
  created(){  
           
           document.title=window.app_name+"->Home"
           
           if(!this.$store.getters["app/loggedIn"]){
             this.$store.dispatch('app/destroyToken')
             this.$router.push({name:'auth-login'})
         }
          
           
         
        // this.showLoading=false
    },
    methods:{
    
      handleErrors(error)
      {
        
            if(error.status==401)
            {
             // this.showLoading=false 
           
              this.$store.dispatch('app/destroyToken')
              .then(response=>{
                    this.$router.push({name:'auth-login'})
                  })  
                  .catch(error => {
                      this.$router.push({name:'auth-login'})
                  })  
              
            }else
            {
            // this.showLoading=false  
            
            // this.$vs.notify({
            //     title: "Eroare la conectarea la server!",
            //     text: error.data.error,
            //     iconPack: "feather",
            //     icon: "icon-alert-circle",
            //     color: "danger"
            // })
          }
      },  
     
    }
    
    
}

</script>

