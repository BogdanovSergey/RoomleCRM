Ext.define('crm.store.ObjectsCommerceGridStore', {
    extend: 'Ext.data.Store',
    requires    : 'crm.model.ObjectsCommerceGridModel',
    model       : 'crm.model.ObjectsCommerceGridModel',
    storeId     : 'ObjectsCommerceGridStore',//'GridStore',
    pageSize    : 100, // идет в связке с LIMIT 0,100
    leadingBufferZone: 100,
    buffered    : true,
    remoteSort  : true,   // удаленная сортировка
    autoLoad    : true
});
