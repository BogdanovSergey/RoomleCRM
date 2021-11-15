Ext.define('crm.store.Owners.OwnersGridStore', {
    extend: 'Ext.data.Store',
    requires    : 'crm.model.OwnersGridModel',
    model       : 'crm.model.OwnersGridModel',
    storeId     : 'OwnersGridStore',
    pageSize    : 100, // идет в связке с LIMIT 0,100
    leadingBufferZone: 100,
    buffered    : true,
    remoteSort  : true,   // удаленная сортировка
    autoLoad    : false
});
