Ext.define('crm.store.ObjectForm.GarbageStore', {
    extend  : 'Ext.data.ArrayStore',
    storeId : 'GarbageStore',
    fields  : ['id', 'Text'],
    data    : [['0', ''], ['23', 'нет мусоропровода'], ['24', 'есть мусоропровод']]
});