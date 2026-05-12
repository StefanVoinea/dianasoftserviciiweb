import userpermissions from '../acl/userpermissions';

export default {
  install(Vue) {
    Vue.prototype.$userpermitt = userpermissions;

  }
};