Ext.define('crm.store.ObjectForm.TerritoryStore', {
    extend  : 'Ext.data.ArrayStore',
    storeId : 'TerritoryStore',
    fields  : ['id', 'Text'],
    data    : [['0', ''], ['39', 'территория не огорожена'], ['40', 'территория огорожена']]
});
