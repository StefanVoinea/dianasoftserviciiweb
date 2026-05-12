<template>
  <!-- Need to add height inherit because Vue 2 don't support multiple root ele -->
  <div style="height: inherit" >
    <div
      class="body-content-overlay "
      :class="{'show': mqShallShowLeftSidebar}"
      @click="mqShallShowLeftSidebar = false"
    />
    <div class="todo-app-list">
       <div class="add-task">
            <b-button
              v-ripple.400="'rgba(255, 255, 255, 0.15)'"
              variant="primary"
              block
              @click="isTaskHandlerSidebarActive=true"
            >
              Adaugă Task
            </b-button>
          </div>
      <!-- App Searchbar Header 
      <div class="app-fixed-search d-flex align-items-center">

       
        <div class="sidebar-toggle d-block d-lg-none ml-1">
          <feather-icon
            icon="MenuIcon"
            size="21"
            class="cursor-pointer"
            @click="mqShallShowLeftSidebar = true"
          />
        </div>

        <div class="d-flex align-content-center justify-content-between w-100">
          <b-input-group class="input-group-merge">
            <b-input-group-prepend is-text>
              <feather-icon
                icon="SearchIcon"
                class="text-muted"
              />
            </b-input-group-prepend>
            <b-form-input
              :value="searchQuery"
              placeholder="Search task"
              @input="updateRouteQuery"
            />
          </b-input-group>
        </div>

      
        <div class="dropdown">
          <b-dropdown
            variant="link"
            no-caret
            toggle-class="p-0 mr-1"
            right
          >
            <template #button-content>
              <feather-icon
                icon="MoreVerticalIcon"
                size="16"
                class="align-middle text-body"
              />
            </template>
            <b-dropdown-item @click="resetSortAndNavigate">
              Reset Sort
            </b-dropdown-item>
            <b-dropdown-item :to="{ name: $route.name, query: { ...$route.query, sort: 'title-asc' } }">
              Sort A-Z
            </b-dropdown-item>
            <b-dropdown-item :to="{ name: $route.name, query: { ...$route.query, sort: 'title-desc' } }">
              Sort Z-A
            </b-dropdown-item>
            <b-dropdown-item :to="{ name: $route.name, query: { ...$route.query, sort: 'assignee' } }">
              Sort Assignee
            </b-dropdown-item>
            <b-dropdown-item :to="{ name: $route.name, query: { ...$route.query, sort: 'due-date' } }">
              Sort Due Date
            </b-dropdown-item>
          </b-dropdown>
        </div>
      </div>
--!>
      <!-- Todo List -->
      <vue-perfect-scrollbar
        :settings="perfectScrollbarSettings"
        class="todo-task-list-wrapper list-group scroll-area"
      >
        <draggable
          v-model="tasks"
          handle=".draggable-task-handle"
          tag="ul"
          class="todo-task-list media-list"
        >
          <li
            v-for="task in tasks"
            :key="task.id"
            class="todo-item"
            :class="{ 'completed': task.iscompleted }"
            @click="handleTaskClick(task)"
          >
            <feather-icon
              icon="MoreVerticalIcon"
              class="draggable-task-handle d-inline"
            />

                  
            <div class="todo-title-wrapper">
              <div class="todo-title-area">
                <div class="title-wrapper">
                  <b-form-checkbox
                    :checked="task.iscompleted"
                    @click.native.stop
                    @change="updateTaskIscompleted(task)"
                  />
                  
                 <feather-icon
                      v-show="task.isdeleted"
                      icon="TrashIcon"
                     :class="{ 'text-danger': task.isdeleted }"
                     
                    />
                   
                  <feather-icon
                    class="ml-1 cursor-pointer"
                    v-show="task.isimportant"
                    icon="StarIcon"
                    size="16"
                    :class="{ 'text-warning': task.isimportant }"
                    
                  />
                   <div class="d-flex flex-column justify-content-center ml-1">
                  <span class="todo-title font-weight-bolder">{{ task.title }}</span>
                  <div>
                   <small  class="text-nowrap text-muted mr-1">Creat la: {{ formatDate(task.created_at, { month: 'short', day: 'numeric',hour:'numeric',minute:'numeric'}) }}</small>
                    <small v-show="task.duedate" class="text-nowrap text-muted mr-1">Termen executare:  {{ formatDate(task.duedate, { month: 'short', day: 'numeric',hour:'numeric',minute:'numeric'}) }}</small>
                     <small   v-show="task.iscompleted" class="text-nowrap text-muted mr-1">Executat la: {{ formatDate(task.completed_at, { month: 'short', day: 'numeric',hour:'numeric',minute:'numeric'}) }}</small>
                </div>
                   </div>
                </div>
              
              </div>
              <div class="todo-item-action">
                <div class="badge-wrapper mr-1">
                  <b-badge
                    v-for="tag in task.tags"
                    :key="tag"
                    pill
                    :variant="`light-${resolveTagVariant(tag)}`"
                    class="text-capitalize"
                  >
                    {{ tag }}
                  </b-badge>
                </div>
                 <div  v-if="task.assignedby" class="d-flex flex-column justify-content-center">
                <small  class="text-nowrap text-muted mr-1">Creat de</small>
                <b-avatar
                  size="32"
                  :src="task.assignedby.link_poza"
                  :variant="`light-${resolveAvatarVariant(task.tags)}`"
                  :text="avatarText(task.assignedby.name)"
                />
                
                 <small  class="text-nowrap text-muted mr-1">{{ task.assignedby.name }}</small>
                 <small  class="text-nowrap text-muted mr-1">{{ task.assignedby.departament }}</small>
                 </div>
                  <div  v-if="task.assignedto" class="d-flex flex-column justify-content-center">
                <small  class="text-nowrap text-muted mr-1">Responsabil</small>
                <b-avatar
                   size="32"
                  :src="task.assignedto.link_poza"
                  :variant="`light-${resolveAvatarVariant(task.tags)}`"
                  :text="avatarText(task.assignedto.name)"
                />
               
                 <small v-show="task.assignedto" class="text-nowrap text-muted mr-1">{{ task.assignedto.name }}</small>
                 <small v-show="task.assignedto" class="text-nowrap text-muted mr-1">{{ task.assignedto.departament }}</small>
                 </div>
               
               

                <div v-if="task.completedby" class="d-flex flex-column justify-content-center">
                <small  class="text-nowrap text-muted mr-1">Executat de</small>
                <b-avatar
                  
                  size="32"
                  :src="task.completedby.link_poza"
                  :variant="`light-${resolveAvatarVariant(task.tags)}`"
                  :text="avatarText(task.completedby.name)"
                />
                
                 <small  class="text-nowrap text-muted mr-1">{{ task.completedby.name }}</small>
                 <small  class="text-nowrap text-muted mr-1">{{ task.completedby.departament }}</small>
                 </div>
               
                
               
              </div>
            </div>

          </li>
        </draggable>
        <div
          class="no-results"
          :class="{'show': !tasks.length}"
        >
          <h5>No Items Found</h5>
        </div>
      </vue-perfect-scrollbar>
    </div>

    <!-- Task Handler -->
    <todo-task-handler-sidebar
      v-model="isTaskHandlerSidebarActive"
      :task="task"
      :clear-task-data="clearTaskData"
      @remove-task="removeTask"
      @add-task="addTask"
      @update-task="updateTask"
    />

    <!-- Sidebar -->
    <portal to="content-renderer-sidebar-left">
      <!--  <todo-left-sidebar
        :task-tags="taskTags"
        :is-task-handler-sidebar-active.sync="isTaskHandlerSidebarActive"
        :class="{'show': mqShallShowLeftSidebar}"
        @close-left-sidebar="mqShallShowLeftSidebar = false"
      />  -->
    </portal> 
  </div>
</template>

<script>
import store from '@/store'
import {
  ref, watch, computed, onUnmounted,
} from '@vue/composition-api'
import {
  BFormInput, BInputGroup, BInputGroupPrepend, BDropdown, BDropdownItem,
  BFormCheckbox, BBadge, BAvatar,
} from 'bootstrap-vue'
import VuePerfectScrollbar from 'vue-perfect-scrollbar'
import draggable from 'vuedraggable'
import { formatDate, avatarText } from '@core/utils/filter'
import { useRouter } from '@core/utils/utils'
import { useResponsiveAppLeftSidebarVisibility } from '@core/comp-functions/ui/app'
import TodoLeftSidebar from './TodoLeftSidebar.vue'
import todoStoreModule from './todoStoreModule'
import TodoTaskHandlerSidebar from './TodoTaskHandlerSidebar.vue'
import Ripple from 'vue-ripple-directive'

export default {
  directives: {
    Ripple,
  },
  components: {
    BFormInput,
    BInputGroup,
    BInputGroupPrepend,
    BDropdown,
    BDropdownItem,
    BFormCheckbox,
    BBadge,
    BAvatar,
    draggable,
    VuePerfectScrollbar,

    // App SFC
    TodoLeftSidebar,
    TodoTaskHandlerSidebar,
  },
  setup() {
    const TODO_APP_STORE_MODULE_NAME = 'app-todo'

    // Register module
    if (!store.hasModule(TODO_APP_STORE_MODULE_NAME)) store.registerModule(TODO_APP_STORE_MODULE_NAME, todoStoreModule)

    // UnRegister on leave
    onUnmounted(() => {
      if (store.hasModule(TODO_APP_STORE_MODULE_NAME)) store.unregisterModule(TODO_APP_STORE_MODULE_NAME)
    })

    const { route, router } = useRouter()
    const routeSortBy = computed(() => route.value.query.sort)
    const routeQuery = computed(() => route.value.query.q)
    const routeParams = computed(() => route.value.params)
    watch(routeParams, () => {
      // eslint-disable-next-line no-use-before-define
      fetchTasks()
    })

    const tasks = ref([])

    const sortOptions = [
      'latest',
      'title-asc',
      'title-desc',
      'assignee',
      'due-date',
    ]
    const sortBy = ref(routeSortBy.value)
    watch(routeSortBy, val => {
      if (sortOptions.includes(val)) sortBy.value = val
      else sortBy.value = val
    })
    const resetSortAndNavigate = () => {
      const currentRouteQuery = JSON.parse(JSON.stringify(route.value.query))

      delete currentRouteQuery.sort

      router.replace({ name: route.name, query: currentRouteQuery }).catch(() => {})
    }

    const blankTask = {
      id: null,
      title: '',
      dueDate: new Date(),
      description: '',
      assignee: null,
      tags: [],
      iscompleted: false,
      isdeleted: false,
      isimportant: false,
    }
    const task = ref(JSON.parse(JSON.stringify(blankTask)))
    const clearTaskData = () => {
      task.value = JSON.parse(JSON.stringify(blankTask))
    }

    const addTask = val => {
      /*store.dispatch('app-todo/addTask', val)
        .then(() => {
          // eslint-disable-next-line no-use-before-define
          fetchTasks()
        })
        */
      const payLoad=val 
      payLoad.requestType="post"
      payLoad.requestUrl="/task/store"
      store.dispatch("app/api_Request",payLoad)
           .then(response=>{
                      tasks.value=response
              
            })
    }
    const removeTask = () => {
      /*store.dispatch('app-todo/removeTask', { id: task.value.id })
        .then(() => {
          // eslint-disable-next-line no-use-before-define
          fetchTasks()
        })*/
         const payLoad=task.value
            payLoad.requestType="post"
            payLoad.isdeleted=true
            payLoad.requestUrl="/task/edit/"+task.value.id
            store.dispatch("app/api_Request",payLoad)
                 .then(response=>{
                            tasks.value=response
                           
                  })
    }
    const updateTask = taskData => {
      /*store.dispatch('app-todo/updateTask', { task: taskData })
        .then(() => {
          // eslint-disable-next-line no-use-before-define
          fetchTasks()
        })*/
        const payLoad=taskData 
            payLoad.requestType="post"
            payLoad.requestUrl="/task/edit/"+taskData.id
            store.dispatch("app/api_Request",payLoad)
                 .then(response=>{
                            tasks.value=response
                           
                  })
    }

    const perfectScrollbarSettings = {
      maxScrollbarLength: 150,
    }

    const isTaskHandlerSidebarActive = ref(false)

    const taskTags = [
      { title: 'Team', color: 'primary', route: { name: 'apps-todo-tag', params: { tag: 'team' } } },
      { title: 'Low', color: 'success', route: { name: 'apps-todo-tag', params: { tag: 'low' } } },
      { title: 'Medium', color: 'warning', route: { name: 'apps-todo-tag', params: { tag: 'medium' } } },
      { title: 'High', color: 'danger', route: { name: 'apps-todo-tag', params: { tag: 'high' } } },
      { title: 'Update', color: 'info', route: { name: 'apps-todo-tag', params: { tag: 'update' } } },
    ]

    const resolveTagVariant = tag => {
      if (tag === 'team') return 'primary'
      if (tag === 'low') return 'success'
      if (tag === 'medium') return 'warning'
      if (tag === 'high') return 'danger'
      if (tag === 'update') return 'info'
      return 'primary'
    }

    const resolveAvatarVariant = tags => {
      if(tags){
      if (tags.includes('high')) return 'primary'
      if (tags.includes('medium')) return 'warning'
      if (tags.includes('low')) return 'success'
      if (tags.includes('update')) return 'danger'
      if (tags.includes('team')) return 'info'
    }
      return 'primary'
    }

    // Search Query
    const searchQuery = ref(routeQuery.value)
    watch(routeQuery, val => {
      searchQuery.value = val
    })
    // eslint-disable-next-line no-use-before-define
    watch([searchQuery, sortBy], () => fetchTasks())
    const updateRouteQuery = val => {
      const currentRouteQuery = JSON.parse(JSON.stringify(route.value.query))

      if (val) currentRouteQuery.q = val
      else delete currentRouteQuery.q

      router.replace({ name: route.name, query: currentRouteQuery })
    }

    const fetchTasks = () => {
     /* store.dispatch('app-todo/fetchTasks', {
        q: searchQuery.value,
        filter: router.currentRoute.params.filter,
        tag: router.currentRoute.params.tag,
        sortBy: sortBy.value,
      })
        .then(response => {
          tasks.value = response.data
        })*/
      const payLoad={} 
      payLoad.requestType="get"
      payLoad.requestUrl="/taskuri"
      store.dispatch("app/api_Request",payLoad)
           .then(response=>{
                      tasks.value=response
                      
            })
    }

    fetchTasks()

    const handleTaskClick = taskData => {
      
      task.value = taskData

     
      isTaskHandlerSidebarActive.value = true
    }

    // Single Task iscompleted update
    const updateTaskIscompleted = taskData => {
      // eslint-disable-next-line no-param-reassign
      taskData.iscompleted = !taskData.iscompleted
      updateTask(taskData)
    }

    const { mqShallShowLeftSidebar } = useResponsiveAppLeftSidebarVisibility()

    return {
      task,
      tasks,
      removeTask,
      addTask,
      updateTask,
      
      clearTaskData,
      taskTags,
      searchQuery,
      fetchTasks,
      perfectScrollbarSettings,
      updateRouteQuery,
      resetSortAndNavigate,

      // UI
      resolveTagVariant,
      resolveAvatarVariant,
      isTaskHandlerSidebarActive,

      // Click Handler
      handleTaskClick,

      // Filters
      formatDate,
      avatarText,

      // Single Task iscompleted update
      updateTaskIscompleted,

      // Left Sidebar Responsive
      mqShallShowLeftSidebar,
    }
  },
}
</script>

<style lang="scss" scoped>
.draggable-task-handle {
position: absolute;
    left: 8px;
    top: 50%;
    transform: translateY(-50%);
    visibility: hidden;
    cursor: move;

    .todo-task-list .todo-item:hover & {
      visibility: visible;
    }
}
</style>

<style lang="scss">
@import "~@core/scss/base/pages/app-todo.scss";
</style>
