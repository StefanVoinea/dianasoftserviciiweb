import axios from '@axios'
import {globalHelpers} from '../../../plugins/globalHelpers.js'
import store from '@/store'
export const useInputImageRenderer = (inputEl, callback) => {
  const inputImageRenderer = () => {
    const file = inputEl.value.files[0]
    const reader = new FileReader()

    reader.addEventListener(
      'load',
      () => {
       // callback(reader.result)
      },
      false,
    )

    if (file) {

      reader.readAsDataURL(file)
      const config = {
                            headers: { 'content-type': 'multipart/form-data' }
                       }
     let formData = new FormData();
     formData.append('file', file);
     axios.post('/utilizatori/importAvatar', formData, config)
          .then(response => {
                              globalHelpers.updateCookiesLocal("user")
                              
                             // this.$bvToast.toast("Upload efectuat cu succes!", {
                             //                                            title: "Upload efectuat cu succes!",
                             //                                            variant:"success",
                             //                                            solid: false,
                             //                                            appendToast: true,
                             //                                            autoHideDelay: 3000,
                             //                                            toaster: "b-toaster-bottom-right",
                             //                                          }) 
                            
                              })
                        
    }
  }
  return {
    inputImageRenderer,
  }
}

export const _ = null
