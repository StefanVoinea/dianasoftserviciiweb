class UserPermissions {
        
    can(permission) {
                        let allPermissions=[]
                        let userInfo={}
                         userInfo = JSON.parse(localStorage.getItem("user"))
                        
                        if(userInfo){
                         userInfo.permissions.forEach((permission) => {
                         allPermissions.push(permission.name);
                         })
                         }
                        
                         if (allPermissions.includes(permission)){
                            return true
                         }else
                         {
                            return false
                         }
                    }
  
}

export default new UserPermissions();