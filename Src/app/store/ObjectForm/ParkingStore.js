Ext.define('crm.store.ObjectForm.ParkingStore', {
    extend  : 'Ext.data.ArrayStore',
    storeId : 'ParkingStore',
    fields  : ['id', 'Text'],
    data    : [['0', ''], ['41', 'свободная парковка'], ['42', 'стояночное место'], ['43', 'подземный паркинг'], ['44', 'гараж']]
});
