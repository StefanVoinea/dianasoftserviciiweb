<template>
   <div  >
          <div class="d-flex justify-content-end">
       <b-button 
              variant="primary"
               size="lg"
               @click="inapoi">
              Inapoi
          </b-button>
           </div>       
    <div  class="container">                         
          
  <iframe  :src="urlPdf"  type='application/pdf' class="responsive-iframe"

   ></iframe>
 
</div>

</div>  
</template>

<script>


export default {
props: {
        numefis:String,
        blobPDF:Blob,
        rutainapoi:String,
        query:Object
      }, 
  name:"PDFViewer",
  data() {
    return {
        urlPdf:"",
        mailTo:"",
        showLoading:false,
    }
    },
    methods:{
      inapoi() {
        
        this.$router.push({ name:this.rutainapoi,query:this.query });
     
    },
  
    },
    created() {
      
      if (this.blobPDF!=null && this.blobPDF!="")
      {
        this.urlPdf = URL.createObjectURL(this.blobPDF)
      }else
      {
          this.showLoading=true
          const payLoad={} 
          payLoad.requestType="post"
          payLoad.requestUrl="/downloadPDF"
          payLoad.numefis=this.numefis
          this.$store.dispatch("api_blob_Request",payLoad)
          .then(response=>{
                       
                      this.urlPdf = URL.createObjectURL(response)
                   // this.urlPdf = window.URL.createObjectURL(new Blob([response]));
                   // var fileLink = document.createElement('a');
                   // fileLink.href = this.urlPdf;
                   // fileLink.setAttribute('download', 'file.pdf');
                   // document.body.appendChild(fileLink);
                   // fileLink.click();
                      this.showLoading=false
                    
          })
        
        }
     
    }
}
</script>
<style>
.container {
  position: abolute;
  overflow: hidden;
  width: 100%;
  padding-top: 56.25%; /* 16:9 Aspect Ratio (divide 9 by 16 = 0.5625) */
}

/* Then style the iframe to fit in the container div with full height and width */
.responsive-iframe {
  position: absolute;
  top: 5;
  left: 0;
  bottom: 0;
  right: 0;
  width: 100%;
  height: 80%; 
}
</style>
