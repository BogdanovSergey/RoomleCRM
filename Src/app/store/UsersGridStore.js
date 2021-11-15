    Ext.define('crm.store.UsersGridStore', {
        extend      : 'Ext.data.Store',
        requires    : 'crm.model.UsersGridModel',
        model       : 'crm.model.UsersGridModel',
        storeId     : 'UsersGridStore',
        autoLoad    : false,
        pageSize    : 100, // идет в связке с LIMIT 0,100
        leadingBufferZone: 100,
        buffered    : true,
        remoteSort  : true
    });
