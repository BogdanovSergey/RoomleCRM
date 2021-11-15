Ext.define('crm.store.ObjectsCountryGridStore', {
    extend: 'Ext.data.Store',
    requires    : 'crm.model.ObjectsCountryGridModel',
    model       : 'crm.model.ObjectsCountryGridModel',
    storeId     : 'ObjectsCountryGridStore',
    pageSize    : 100, // идет в связке с LIMIT 0,100
    leadingBufferZone: 100,
    buffered    : true,
    remoteSort  : true,
    autoLoad    : false
});


