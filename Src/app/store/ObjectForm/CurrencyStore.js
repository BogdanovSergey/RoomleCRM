Ext.define('crm.store.ObjectForm.CurrencyStore', {
    extend  : 'Ext.data.ArrayStore',
    storeId : 'CurrencyStore',
    fields  : ['id', 'Text'],
    data    : [['70', 'RUB'], ['71', 'USD'], ['72', 'EUR']]
    //['60', 'не определен'],
});