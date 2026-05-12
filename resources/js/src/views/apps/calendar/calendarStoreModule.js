import axios from '@axios'
import store from '@/store'
export default {
  namespaced: true,
  state: {

    calendarOptions: [
      {
        color: 'primary',
        label: 'Personal',
      },
      
      {
        color: 'info',
       label:'Administrativ'
     },
     {
       color: 'warning',
       label:'Contabil'
     },
     
    {
       color: 'success',
       label:'Financiar'
     },
     {
       color: 'secondary',
       label:'Juridic'
     },
     {
       color: 'light',
       label:'Marketing'
     },
     {
       color: 'danger',
       label:'Tehnic'
     },
      
    ],
    selectedCalendars: ['Personal','Administrativ','Contabil','Financiar','Juridic','Marketing','Tehnic'],
  },
  getters: {},
  mutations: {
    SET_SELECTED_EVENTS(state, val) {
      state.selectedCalendars = val
    },
  },
  actions: {
    fetchEvents(ctx, { calendars }) {
    /*
     return new Promise((resolve, reject) => {
        axios
          .get('/apps/calendar/events', {
            params: {
              calendars: calendars.join(','),
            },
          })
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
  */
    },
    addEvent(ctx, { event }) {
      return new Promise((resolve, reject) => {
        axios
          .post('/apps/calendar/events', { event })
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    updateEvent(ctx, { event }) {
      return new Promise((resolve, reject) => {
        axios
          .post(`/apps/calendar/events/${event.id}`, { event })
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
    removeEvent(ctx, { id }) {
      return new Promise((resolve, reject) => {
        axios
          .delete(`/apps/calendar/events/${id}`)
          .then(response => resolve(response))
          .catch(error => reject(error))
      })
    },
  },
}
