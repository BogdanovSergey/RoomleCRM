Ext.define('crm.store.ObjectForm.KladrStore', {
    extend      : 'Ext.data.Store',
    storeId     : 'KladrStore',
    requires    : 'crm.model.KladrModel',
    model       : 'crm.model.KladrModel',
    proxy: {
        type    : 'ajax',
        url     : MainSiteUrl + 'Super.php?Action=KladrQuery',
        reader  : 'json'
    },
    autoLoad: true
});