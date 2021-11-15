Ext.define('crm.store.ObjectForm.DealTypeStore', {
    extend  : 'Ext.data.ArrayStore',
    storeId : 'DealTypeStore',
    fields  : ['id', 'Text'],
    data    : [['58', 'свободная продажа'], ['59', 'альтернатива']]
});
