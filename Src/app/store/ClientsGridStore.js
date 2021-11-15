    Ext.define('crm.store.ClientsGridStore', {
        extend      : 'Ext.data.Store',
        requires    : 'crm.model.ClientsGridModel',
        model       : 'crm.model.ClientsGridModel',
        storeId     : 'ClientsGridStore',
        autoLoad    : false,
        pageSize    : 100, // идет в связке с LIMIT 0,100
        leadingBufferZone: 100,
        buffered    : true,
        remoteSort  : true
    });
