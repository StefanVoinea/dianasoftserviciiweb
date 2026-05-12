<template>
  <div class="chats">
    <div
      v-for="(msgGrp, index) in formattedChatData.formattedChatLog"
      :key="msgGrp.senderId+String(index)"
      class="chat"
      :class="{'chat-left': (msgGrp.senderId === formattedChatData.contact.id)||formattedChatData.activeUserId.id!==msgGrp.senderId}"
    >
      <div class="chat-avatar">
        <b-avatar
          size="36"
          class="avatar-border-2 box-shadow-1"
          variant="transparent"
          :src="msgGrp.senderId === formattedChatData.contact.id ? formattedChatData.contact.link_poza : profileUserAvatar"
        />
      </div>
      <div class="chat-body">
        <div
          v-for="(msgData , index) in msgGrp.messages"
          :key="msgData.time"
          class="chat-content"
        >
           <span style="font-size:xx-small;">{{ msgGrp.senderId === formattedChatData.contact.id && formattedChatData.contact.grup ?formattedChatData.contact.name:null}} </span> 
           <span style="font-size:xx-small;">{{ formattedChatData.activeUserId.id!==msgGrp.senderId && formattedChatData.contact.grup ?msgGrp.senderName:null}} </span>
          <p>{{ msgData.msg }} </p>
          <span style="font-size:xx-small;">{{ toDate(msgData.time)}} </span>
         
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { computed } from '@vue/composition-api'
import { BAvatar } from 'bootstrap-vue'
import store from '@/store'
export default {
  components: {
    BAvatar,
  },
  props: {
    chatData: {
      type: Object,
      required: true,
    },
    profileUserAvatar: {
      type: String,
      required: true,
    },
  },
  setup(props) {
    
    const formattedChatData = computed(() => {
      
      const contact = {
        id: props.chatData.contact.id,
        avatar: props.chatData.contact.link_poza,
        name: props.chatData.contact.name,
        grup: props.chatData.contact.grup?true:false,
      }

      let chatLog = []
      if (props.chatData.chat) {
        chatLog = props.chatData.chat
      }
      const  activeUserId= JSON.parse(store.state.app.user);
       
      const formattedChatLog = []
      let chatMessageSenderId = chatLog[0] ? chatLog[0].senderId : undefined
      let msgGroup = {
        sender: chatMessageSenderId,
        messages: [],
      }

      chatLog.forEach((msg, index) => {

        //if (chatMessageSenderId === msg.senderId) {
        //  msgGroup.messages.push({
        //    msg: msg.message,
        //    time: msg.time,
        //  })
        //} else {
        //  chatMessageSenderId = msg.senderId
        
          msgGroup = {
            senderId: msg.senderId,
            senderName:msg.sender_name,
            messages: [{
              msg: msg.message,
              time: msg.time,
            }],
          }
          formattedChatLog.push(msgGroup)
          //}
        
      //  if (index === chatLog.length - 1) formattedChatLog.push(msgGroup)
      })

      return {
        formattedChatLog,
        contact,
        activeUserId,
        profileUserAvatar: props.profileUserAvatar,
      }
    })

    return {
      formattedChatData,
    }
  },
  
  methods: {
        isSameDay(time_to, time_from) {
            const date_time_to = new Date(Date.parse(time_to))
            const date_time_from = new Date(Date.parse(time_from));
            return date_time_to.getFullYear() === date_time_from.getFullYear() &&
                date_time_to.getMonth() === date_time_from.getMonth() &&
                date_time_to.getDate() === date_time_from.getDate();
        },
        toDate(time) {
            const locale = "ro-ro";
            const date_obj = new Date(Date.parse(time));

            const monthName= date_obj.toLocaleString(locale, {
                month: 'short'
            });
            return date_obj.toLocaleString(locale)
            //return date_obj.getDate() + ' '  + monthName+' '+date_obj.getYear()+'  '+ date_obj.getHours()+":"+date_obj.getMinutes()+":"+date_obj.getSeconds();
        },
       } 
}
</script>

<style>

</style>
