Ext.define('crm.store.ObjectForm.ObjectTypeStore', {
    extend  : 'Ext.data.ArrayStore',
    storeId : 'ObjectTypeStore',
    fields  : ['id', 'Text'],
    data    : [['1', 'квартира'], ['3', 'комната'], ['2', 'доля']] //
});
