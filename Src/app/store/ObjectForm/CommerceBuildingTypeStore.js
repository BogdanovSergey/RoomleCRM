Ext.define('crm.store.ObjectForm.CommerceBuildingTypeStore', {
    extend  : 'Ext.data.ArrayStore',
    storeId : 'CommerceBuildingTypeStore',
    fields  : ['id', 'Text'],
    data    : [['0', ''], ['149', 'нежилое'], ['150', 'жилое']]
});