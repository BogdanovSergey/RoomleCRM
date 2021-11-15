Ext.define('crm.store.ObjectForm.ObjectCountrySecureStore', {
    extend  : 'Ext.data.ArrayStore',
    storeId : 'ObjectCountrySecureStore',
    fields  : ['id', 'Text'],
    data    : [
        ['0', ''],
        ['121', 'нет'],
        ['120', 'есть']
    ]
});
