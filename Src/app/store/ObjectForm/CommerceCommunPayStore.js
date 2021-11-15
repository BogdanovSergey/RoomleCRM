Ext.define('crm.store.ObjectForm.CommerceCommunPayStore', {
    extend  : 'Ext.data.ArrayStore',
    storeId : 'CommerceCommunPayStore',
    fields  : ['id', 'Text'],
    data    : [['0', ''], ['178', 'включены в аренду'], ['177', 'оплачиваются отдельно'] ]
});