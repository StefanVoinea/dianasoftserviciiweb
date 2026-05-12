<template>
  <b-nav-item-dropdown
    class="dropdown-notification mr-25"
    menu-class="dropdown-menu-media"
    right
  >
    <template #button-content>
      <feather-icon
        :badge="newMessages"
        badge-classes="bg-danger"
        class="text-body"
        icon="BellIcon"
        size="21"

      />
    </template>

    <!-- Header -->
    <li class="dropdown-menu-header">
      <div v-if="!showAlerte" class="dropdown-header d-flex">
        <h4 class="notification-title mb-0 mr-auto">
          Notificări
        </h4>

      
      </div>
      <div v-else  @click="vizualizeazaGrup" class="dropdown-header d-flex">
        <h4 class="notification-title mb-0 mr-auto">
          {{this.titluGrup}}
        </h4>

         <b-badge
          pill
          variant="light-primary"
        >
          {{this.nrMesajeGrup}} New
        </b-badge> 
           <feather-icon icon="ChevronUpIcon"></feather-icon>
      </div>
    </li>

    <!-- Notifications -->
    <vue-perfect-scrollbar
     
      :settings="perfectScrollbarSettings"
      class="scrollable-container media-list scroll-area"
      tagname="li"
    >

      <b-link
        v-show="!showAlerte"
        v-for="notification in notificationGrup"
        :key="notification.notificationtype_id"
       
      >
        <b-media @click="afisezAlerteGrup(notification)">
        
          <p class="media-heading d-flex justify-content-between">
            <span class="font-weight-bolder">
              {{ notification.notificationtype?notification.notificationtype.denumire:"" }} 
            </span> 
             <b-badge
                    pill
                    variant="light-primary"
                  >
                    {{notification.nrMesaje}} New
                   <feather-icon icon="ChevronDownIcon"></feather-icon>
                  </b-badge> 
          </p>
          
        </b-media>
      </b-link>
      <!-- Account Notification -->
      <div  class="d-flex d-flex-row" v-for="notification in notificariFiltrate" :key="notification.id">
      <b-link
        v-show="showAlerte"
        
        :href="notification.link"
      >
        <b-media >
          <template  #aside>
           <b-avatar
              v-if="notification.icon"
              size="32"
              :variant="notification.type"
            >
              <feather-icon :icon="notification.icon" />
            </b-avatar>
           
            
            
          </template>
          <p class="media-heading d-flex justify-content-between">
            <span class="font-weight-bolder">
              {{ notification.title }} 
            </span> 
             <span v-show="notification.created_at" class="notification-text">
              {{ new Date(notification.created_at).toLocaleString('ro-RO') }}
            </span>
          
          </p>
          <small class="notification-text">{{ notification.subtitle }}</small>
        </b-media>
      </b-link>
         <b-button       v-show="showAlerte" 
                        size="sm"
                        variant="flat-danger"
                        @click="markAsRead(notification.id)"
                      >
                       <feather-icon icon="XIcon" />
                      </b-button>
    </div>
      <!-- 
      <div class="media d-flex align-items-center">
        <h6 class="font-weight-bolder mr-auto mb-0">
          System Notifications
        </h6>
        <b-form-checkbox
          :checked="true"
          switch
        />
      </div>

     
      <b-link
        v-for="notification in systemNotifications"
        :key="notification.subtitle"
      >
        <b-media>
          <template #aside>
            <b-avatar
              size="32"
              :variant="notification.type"
            >
              <feather-icon :icon="notification.icon" />
            </b-avatar>
          </template>
          <p class="media-heading">
            <span class="font-weight-bolder">
              {{ notification.title }}
            </span>
          </p>
          <small class="notification-text">{{ notification.subtitle }}</small>
        </b-media>
      </b-link>
      -->
    </vue-perfect-scrollbar>

    <!-- Cart Footer -->
    <li class="dropdown-menu-footer">
      <b-button
      v-show="this.newMessages>1"
      v-ripple.400="'rgba(255, 255, 255, 0.15)'"
      variant="flat-danger"
      block
      @click="markAsRead()"
    >Sterge toate
      <feather-icon icon="XIcon" />
    </b-button>
     <small v-show="this.newMessages==0" class="notification-text">Nu exista notificari</small>
    </li>
  </b-nav-item-dropdown>
</template>

<script>
import {
  BNavItemDropdown, BBadge, BMedia, BLink, BAvatar, BButton, BFormCheckbox,
} from 'bootstrap-vue'
import VuePerfectScrollbar from 'vue-perfect-scrollbar'
import Ripple from 'vue-ripple-directive'

export default {
  components: {
    BNavItemDropdown,
    BBadge,
    BMedia,
    BLink,
    BAvatar,
    VuePerfectScrollbar,
    BButton,
    BFormCheckbox,
  },
  directives: {
    Ripple,
  },
  data(){
      return {
          notifications:[{}],
          notificariFiltrate:[{}],
          notificationGrup:[{}],
          idGrup:"",
          newMessages:0,
          titluGrup:"",
          nrMesajeGrup:0,
          showAlerte:false,
          perfectScrollbarSettings :{
                                        maxScrollbarLength: 60,
                                        wheelPropagation: false,
                                      }
      }
    },
    methods:{
      vizualizeazaGrup(){
          this.showAlerte=false
          this.idGrup=""
        },
      afisezAlerteGrup(grupNotificare){
          
          this.notificariFiltrate=this.notifications.filter((t)=>{
               return t.notificationtype_id==grupNotificare.notificationtype_id})
          this.nrMesajeGrup=grupNotificare.nrMesaje
          this.titluGrup=grupNotificare.notificationtype.denumire
          this.idGrup=grupNotificare.notificationtype_id
          this.showAlerte=true
        },
      listen(){
      /*  Echo.channel("bancomatic_dianasoft")
                    .listen("NotificationEvents", (e) => {
                       this.notifications.unshift(e.notificare)
                       this.newMessages++;
                       console.log(e)
                       console.log("AM PRIMIT NOTIFICARE")
                       if(e.eveniment=="Notificare noua"){
                        this.contacts.map((t)=>{
                          if(t.id==e.user_id){
                            t.status=e.status
                            this.$store.dispatch('updateUserInfo', {id:t.id,status: e.status})
                          }
                        })
                       }

                       if(e.eveniment=="Transmite mesaj"){
                        let payload=e
                        // payload.id=e.user_id
                        
                        this.$store.dispatch('chat/receiveChatMessage', payload)
                        // this.contacts.map((t)=>{
                        //   if(t.id==e.user_id){
                        //     t.status_chat=e.status_chat
                        //     this.$store.dispatch('updateUserInfo', {id:t.id,status_chat: e.status_chat})
                        //   }
                        // })
                       }

                     });
               */      
    },

           markAsRead(id){
                        const payLoad={} 
                        payLoad.requestType="post"
                        payLoad.requestUrl="/markasread"
                        payLoad.id=id
                        payLoad.notificationtype_id=this.idGrup
                        this.$store.dispatch("app/api_Request",payLoad)
                        .then(response=>{
                                   /*

                                     if(id!=null){
                                        this.newMessages=this.newMessages-1
                                      }else{
                                        this.newMessages=0

                                      } */

                                     this.notifications=response.notificari
                                     this.notificationGrup=response.notificariGrup
                                     this.newMessages=response.new
                                     if(this.showAlerte){
                                        let grupCurent=this.notificationGrup.filter((t)=>{
                                            return t.notificationtype_id==this.idGrup
                                          })
                                        
                                        if(grupCurent.length>0){
                                        this.nrMesajeGrup=grupCurent[0].nrMesaje

                                        this.notificariFiltrate=this.notifications.filter((t)=>{
                                                         return t.notificationtype_id==this.idGrup
          
                                             })
                                        }else{
                                          this.showAlerte=false
                                          this.idGrup=""
                                        }
                                
                                    }
                                    })
          
              },
           getNotificationList(){
                        const payLoad={} 
                        payLoad.requestType="get"
                        payLoad.requestUrl="/notificariusercurent"
                        this.$store.dispatch("app/api_Request",payLoad)
                        .then(response=>{
                                    
                                    // this.notifications=response.data
                                    this.notifications=response.notificari
                                    this.notificationGrup=response.notificariGrup  
                                     this.newMessages=response.new
                                   
          
                        })
          
              },
      },
   beforeMount(){
          this.getNotificationList()
    },
    created(){
     
          this.getNotificationList()
          
          this.listen()
      },
  /*setup() {
    
    const notifications = [
      {
        title: 'Congratulation Sam 🎉',
        avatar: require('@/assets/images/avatars/6-small.png'),
        subtitle: 'Won the monthly best seller badge',
        type: 'light-success',
      },
      {
        title: 'New message received',
        avatar: require('@/assets/images/avatars/9-small.png'),
        subtitle: 'You have 10 unread messages',
        type: 'light-info',
      },
      {
        title: 'Revised Order 👋',
        avatar: 'MD',
        subtitle: 'MD Inc. order updated',
        type: 'light-danger',
      },
    ]
    

    const systemNotifications = [
      {
        title: 'Server down',
        subtitle: 'USA Server is down due to hight CPU usage',
        type: 'light-danger',
        icon: 'XIcon',
      },
      {
        title: 'Sales report generated',
        subtitle: 'Last month sales report generated',
        type: 'light-success',
        icon: 'CheckIcon',
      },
      {
        title: 'High memory usage',
        subtitle: 'BLR Server using high memory',
        type: 'light-warning',
        icon: 'AlertTriangleIcon',
      },
    ]
    */

   
}
</script>

<style>

</style>
