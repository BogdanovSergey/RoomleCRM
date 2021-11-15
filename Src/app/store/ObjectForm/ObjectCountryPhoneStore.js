Ext.define('crm.store.ObjectForm.ObjectCountryPhoneStore', {
    extend  : 'Ext.data.ArrayStore',
    storeId : 'ObjectCountryPhoneStore',
    fields  : ['id', 'Text'],
    data    : [
        ['0', ''],
        ['143', 'нет'],
        ['142', 'есть']
    ]
});
