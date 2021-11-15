Ext.define('crm.store.ObjectForm.MetroWayTypeStore', {
    extend  : 'Ext.data.ArrayStore',
    storeId : 'MetroWayTypeStore',
    fields  : ['id', 'Text'],
    data    : [['1', 'пешком'], ['2', 'транспортом']]
});