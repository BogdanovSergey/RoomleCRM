Ext.define('crm.store.ObjectForm.OwnerPhoneStore', {
    extend      : 'Ext.data.Store',
    storeId     : 'OwnerPhoneStore',
    requires    : 'crm.model.SimpleModel',
    model       : 'crm.model.SimpleModel',
    autoLoad    : true,
    proxy: {
        type    : 'ajax',
        url     : GetObjectOwnerPhonesUrl,
        reader  : 'json',
        extraParams   : {
            ObjectOwnerId : null,
            GetObjectOwnerPhones : true
        }
    }
});