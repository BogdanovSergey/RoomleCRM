Ext.define('crm.store.ObjectForm.ObjectCountryMaterialStore', {
    extend  : 'Ext.data.ArrayStore',
    storeId : 'ObjectCountryMaterialStore',
    fields  : ['id', 'Text'],
    data    : [
        ['0', ''],
        ['137', 'кирпичный'],
        ['138', 'монолитный'],
        ['139', 'блочный'],
        ['140', 'деревянный'],
        ['141', 'щитовой']
    ]
});
