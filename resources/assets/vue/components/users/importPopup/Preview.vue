<template>
  <div>
    <div
      class="alert alert-info"
      v-if="reading"
    >
      <i class="fa fa-spinner fa-pulse"></i>
      {{ _ix('Reading file...','Users') }}
    </div>
    <template v-if="!reading">
      <div
        class="import-preview"
        v-if="users.length>0"
      >
        <table class="table table-layout-auto">
          <thead>
          <tr>
            <th>{{ _ix('Row','Users') }}</th>
            <th>{{ _ix('Email','Users') }}</th>
            <th>{{ _ix('Name','Users') }}</th>
            <th>{{ _ix('QQ','Users') }}</th>
            <th>{{ _ix('Wechat ID','Users') }}</th>
            <th>{{ _ix('Phone','Users') }}</th>
            <th>{{ _ix('Company name','Users') }}</th>
            <th>{{ _ix('Company URL','Users') }}</th>
            <th>{{ _ix('Alipay ID','Users') }}</th>
            <th>{{ _ix('Notes','Users') }}</th>
            <th>{{ _ix('Register date','Users') }}</th>
          </tr>
          </thead>
          <tbody>
          <template v-for="importedUser in users">
            <tr :class="importedUserClass(importedUser.imported,importedUser.error)">
              <td>{{ importedUser.row }}</td>
              <td>{{ importedUser.user.email }}</td>
              <td>{{ importedUser.user.name }}</td>
              <td>{{ importedUser.user.qq }}</td>
              <td>{{ importedUser.user.wechatId }}</td>
              <td>{{ importedUser.user.phone }}</td>
              <td>{{ importedUser.user.companyName }}</td>
              <td>{{ importedUser.user.companyUrl }}</td>
              <td>{{ importedUser.user.alipayId }}</td>
              <td>{{ importedUser.user.notes }}</td>
              <td>{{ importedUser.user.registerDate }}</td>
            </tr>
            <tr
              v-if="importedUser.error||!importedUser.imported"
              :class="importedUserClass(importedUser.imported,importedUser.error)"
            >
              <td>&nbsp;</td>
              <td colspan="18">
                <i class="fa fa-exclamation-triangle"></i>
                <template v-if="importedUser.error">{{ importedUser.error }}</template>
                <template v-if="!importedUser.error">{{ _ix('This user already exists','Users') }}</template>
              </td>
            </tr>
          </template>
          </tbody>
        </table>
      </div>
      <div
        class="alert alert-warning"
        v-if="!users.length"
      >
        {{ _ix('No users have been found in this file','Users') }}
      </div>
    </template>
  </div>
</template>
<script>
  export default {
    props:{
      reading:{
        type:Boolean,
        required:true
      },
      users:{
        type:Array,
        required:true
      }
    },
    methods:{
      importedUserClass(imported,error){
        if(error)
          return 'imported-user imported-user-error'
        else if(imported)
          return 'imported-user imported-user-ok'
        else
          return 'imported-user imported-user-existing'
      }
    }
  }
</script>
