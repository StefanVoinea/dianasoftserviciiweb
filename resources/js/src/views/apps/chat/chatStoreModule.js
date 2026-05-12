import axios from '@axios'
import Vue from 'vue'
axios.defaults.baseURL=window.api_url
export default {
  namespaced: true,
  state: {// Chat Search Query
            chatSearchQuery: "",

            // Stores All Contacts
            contacts: [],

            // Stores Chat Contacts
            chatContacts: [],

            // Stores Chat data(log)
            chats: {},
          },
  getters: {
            chatDataOfUser: state => id => {
                return state.chats[Object.keys(state.chats).find(key => key == id)]
            },
            chatContacts: (state, getters) => {
              let chatContacts = state.chatContacts.filter((contact) => contact.name.toLowerCase().includes(state.chatSearchQuery.toLowerCase()))

              chatContacts.sort((x,y) => {
                let timeX = getters.chatLastMessaged(x.id).time
                let timeY = getters.chatLastMessaged(y.id).time
                return (new Date(timeY) - new Date(timeX))
              })

              return chatContacts.sort((x,y) => {
                const chatDataX = getters.chatDataOfUser(x.id)
                const chatDataY = getters.chatDataOfUser(y.id)
                if(chatDataX && chatDataY) return (chatDataY.isPinned - chatDataX.isPinned)
                else return 0
              })
            },
            contacts: (state) => state.contacts.filter((contact) => contact.name.toLowerCase().includes(state.chatSearchQuery.toLowerCase())),
            contact: (state) => contactId => state.contacts.find((contact) => contact.id == contactId),
            chats: (state) => state.chats,
            chatUser: (state, getters, rootState) => id => state.contacts.find((contact) => contact.id == id) || rootState.AppActiveUser,

            chatLastMessaged: (state, getters) => id => {
                if(getters.chatDataOfUser(id)) return getters.chatDataOfUser(id).msg.slice(-1)[0];
                else return false
            },
            chatUnseenMessages: (state, getters) => id => {
                let unseenMsgs = 0;
                const chatData = getters.chatDataOfUser(id);
                if(chatData) {
                    chatData.msg.map((msg) => {
                        if(!msg.isSeen && !msg.isSent) unseenMsgs++;
                    })
                }
                return unseenMsgs;
            },
  },
  mutations: {
              UPDATE_ABOUT_CHAT(state, obj) {
              obj.rootState.AppActiveUser.about = obj.value
            },
            UPDATE_STATUS_CHAT(state, obj) {
              obj.rootState.AppActiveUser.status = obj.value
            },
            RECEIVE_CHAT_MESSAGE(state, payload) {
              
              if (payload.chatData) {
                // If there's already chat. Push msg to existing chat
                state.chats[Object.keys(state.chats).find(key => (key == payload.user_id))].msg.push(JSON.parse(payload.mesaj))
              } else {
                // Create New chat and add msg
                const chatId = payload.user_id
                Vue.set(state.chats, [chatId], { isPinned: false, msg: [JSON.parse(payload.mesaj)] })
              }
              
            },
            // API AFTER
            SEND_CHAT_MESSAGE(state, payload) {
              if (payload.chatData) {
                // If there's already chat. Push msg to existing chat
                state.chats[Object.keys(state.chats).find(key => key == payload.id)].msg.push(payload.msg)
              } else {
                // Create New chat and add msg
                const chatId = payload.id
                Vue.set(state.chats, [chatId], { isPinned: payload.isPinned, msg: [payload.msg] })
              }
            },
            SELECTEAZA_CONTACT(state, payload) {
              // state.contacts[Object.keys(state.contacts).find(key => key == contact.id)].selectat = contact.selectat
              
              state.contacts.map(t => { if (t.id==payload.contact.id){
                                        // console.log(t)
                                         t.selectat=payload.contactSelectat
                                          // console.log(t)
                                         }
                                       })
            },
            DESELECTEAZA_CONTACTE(state) {
              state.contacts.map(t => {return t.selectat=false})
            },
            UPDATE_CONTACTS(state, contacts) {
              state.contacts = contacts
            },

            UPDATE_CHAT_CONTACTS(state, chatContacts) {
              state.chatContacts = chatContacts
            },
            UPDATE_CHATS(state, chats) {
              state.chats = chats
            },
            SET_CHAT_SEARCH_QUERY(state, query) {
              state.chatSearchQuery = query
            },
            MARK_SEEN_ALL_MESSAGES(state, payload) {
              payload.chatData.msg.forEach((msg) => {
                msg.isSeen = true
              })
            },
            TOGGLE_IS_PINNED(state, payload) {
              state.chats[Object.keys(state.chats).find(key => key == payload.id)].isPinned = payload.value
            },
  },
  actions: {
     setChatSearchQuery({ commit }, query){
        commit('SET_CHAT_SEARCH_QUERY', query)
    },
    updateAboutChat({ commit, rootState }, value) {
        commit('UPDATE_ABOUT_CHAT', {rootState: rootState, value: value})
    },
    updateStatusChat({ commit, rootState }, value) {
        commit('UPDATE_STATUS_CHAT', {rootState: rootState, value: value})
    },
    // API CALLS
    addGroup({ getters, commit, dispatch,rootState }, payload) {
       axios.defaults.headers.common['Authorization'] = 'Bearer ' + rootState.token
       axios.defaults.headers.common['AuthorizationHeader'] = JSON.parse(rootState.societateaCurenta).id
      return new Promise((resolve, reject) => {
        axios.post("/utilizatori/storegroup", {payload: payload})
          .then((response) => {
                        resolve(response)
          })
          .catch((error) => { reject(error) })
      })
    },
    receiveChatMessage({ getters, commit, dispatch,rootState }, payload) {
            payload.chatData = getters.chatDataOfUser(payload.user_id)
            commit('RECEIVE_CHAT_MESSAGE', payload)
          
    },

    selecteazaContact({ getters, commit, dispatch,rootState }, payload) {
           
            commit('SELECTEAZA_CONTACT', payload)
          
    },
    deselecteazaContacte({ getters, commit, dispatch,rootState }) {
            commit('DESELECTEAZA_CONTACTE')
          
    },
   /* fetchChatsAndContacts() {
      return new Promise((resolve, reject) => {
        axios
          .get('/apps/chat/chats-and-contacts')
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
   */
        fetchChatsAndContacts({ commit,rootState }) {
          
      axios.defaults.headers.common['Authorization'] = 'Bearer ' + rootState.app.token
       axios.defaults.headers.common['AuthorizationHeader'] = JSON.parse(rootState.app.societateaCurenta).id
      return new Promise((resolve, reject) => {
        axios.get("/chat/contacteChat", {params: {q: ""}})
          .then((response) => {
            
           // commit('UPDATE_CONTACTS', response.data.contacts)
          //  commit('UPDATE_CHAT_CONTACTS', response.data.chatsContacts)
          //  commit('UPDATE_CHATS', response.data.chatsContacts)

            resolve(response)
          })
          .catch((error) => { reject(error) })
      })
    },
   // Get contacts from server. Also change in store
    fetchContacts({ commit,rootState }) {
     
       axios.defaults.headers.common['Authorization'] = 'Bearer ' + rootState.app.token
       axios.defaults.headers.common['AuthorizationHeader'] = JSON.parse(rootState.app.societateaCurenta).id
      return new Promise((resolve, reject) => {
        axios.get("/chat/contacte", {params: {q: ""}})
          .then((response) => {
            
            commit('UPDATE_CONTACTS', response.data.contacts)
            commit('UPDATE_CHAT_CONTACTS', response.data.chatsContacts)
            resolve(response)
          })
          .catch((error) => { reject(error) })
      })
    },

    getProfileUser() {
      return new Promise((resolve, reject) => {
        axios
          .get('/apps/chat/users/profile-user')
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    /*
    getChat(ctx, { userId }) {
      return new Promise((resolve, reject) => {
        axios
          .get(`/apps/chat/chats/${userId}`)
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },*/
    // Get chats from server. Also change in store
    getChat( {commit,rootState} ,   userId ) {
       
       axios.defaults.headers.common['Authorization'] = 'Bearer ' + rootState.app.token
       axios.defaults.headers.common['AuthorizationHeader'] = JSON.parse(rootState.app.societateaCurenta).id
       
      return new Promise((resolve, reject) => {
        axios.post('/chat/chats',{userId:userId})
          .then((response) => {
           
            commit('UPDATE_CHATS', response.data)
            resolve(response)
          })
          .catch((error) => { reject(error) })
      })
    },
    /*
    sendMessage(ctx, { contactId, message, senderId }) {
      return new Promise((resolve, reject) => {
        axios
          .post(`/apps/chat/chats/${contactId}`, { message, senderId })
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    */
     // API CALLS
    sendMessage({ getters, commit, dispatch,rootState }, payload) {
       axios.defaults.headers.common['Authorization'] = 'Bearer ' + rootState.app.token
       axios.defaults.headers.common['AuthorizationHeader'] = JSON.parse(rootState.app.societateaCurenta).id
      return new Promise((resolve, reject) => {
        axios.post("/chat/sendChatMessage", {payload: payload})
          .then((response) => {
            payload.chatData = getters.chatDataOfUser(payload.id)
           // if(!payload.chatData) { dispatch("fetchChatContacts") }
            commit('SEND_CHAT_MESSAGE', payload)
            resolve(response)
          })
          .catch((error) => { reject(error) })
      })
    },
     // API CALLS
      changeAvatar({ getters, commit,rootState }, formData, config) {
      axios.defaults.headers.common['Authorization'] = 'Bearer ' + rootState.token
       axios.defaults.headers.common['AuthorizationHeader'] = JSON.parse(rootState.societateaCurenta).id
      return new Promise((resolve, reject) => {
        axios.post("/utilizatori/modificaAvatar", formData, config)
          .then((response) => {
            
            resolve(response)
          })
          .catch((error) => { reject(error) })
      })
    },
    markSeenAllMessages({ getters, commit,rootState }, id) {
      axios.defaults.headers.common['Authorization'] = 'Bearer ' + rootState.token
       axios.defaults.headers.common['AuthorizationHeader'] = JSON.parse(rootState.societateaCurenta).id
      return new Promise((resolve, reject) => {
        axios.post("/chat/mark-all-seen", {id: id})
          .then((response) => {
            commit('MARK_SEEN_ALL_MESSAGES', {
              id: id,
              chatData: getters.chatDataOfUser(id)
            })
            resolve(response)
          })
          .catch((error) => { reject(error) })
      })
    },

    toggleIsPinned({ commit,rootState }, payload) {
      axios.defaults.headers.common['Authorization'] = 'Bearer ' + rootState.token
       axios.defaults.headers.common['AuthorizationHeader'] = JSON.parse(rootState.societateaCurenta).id
      return new Promise((resolve, reject) => {
        axios.post("/chat/set-pinned/", {contactId: payload.id, value: payload.value})
          .then((response) => {
            commit('TOGGLE_IS_PINNED', payload)
            resolve(response)
          })
          .catch((error) => { reject(error) })
      })
    },
  },
}
