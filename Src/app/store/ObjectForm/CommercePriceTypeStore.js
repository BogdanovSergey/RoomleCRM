Ext.define('crm.store.ObjectForm.CommercePriceTypeStore', {
    extend  : 'Ext.data.ArrayStore',
    storeId : 'CommercePriceTypeStore',
    fields  : ['id', 'Text'],
    data    : [
        ['164', 'за всю площадь'], ['165', 'за кв. м.']
    ]
});
