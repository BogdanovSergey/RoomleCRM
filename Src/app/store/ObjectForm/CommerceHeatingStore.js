Ext.define('crm.store.ObjectForm.CommerceHeatingStore', {
    extend  : 'Ext.data.ArrayStore',
    storeId : 'CommerceHeatingStore',
    fields  : ['id', 'Text'],
    data    : [['0', ''], ['209', 'Автономное отопление'], ['210', 'Собственная бойлерная'], ['211', 'Центральное отопление']]
});