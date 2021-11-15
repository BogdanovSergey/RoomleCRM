Ext.define('crm.store.ObjectForm.CommerceExplutPayStore', {
    extend  : 'Ext.data.ArrayStore',
    storeId : 'CommerceExplutPayStore',
    fields  : ['id', 'Text'],
    data    : [['0', ''], ['182', 'включены в аренду'], ['183', 'оплачиваются отдельно'] ]
});