Ext.define('crm.store.SystemVarsStore', {
    extend      : 'Ext.data.Store',
    storeId     : 'SystemVarsStore',
    requires    : 'crm.model.SystemVarsModel',
    model       : 'crm.model.SystemVarsModel',
    autoLoad: true
});