Ext.define('crm.store.ObjectForm.MortgageStore', {
    extend  : 'Ext.data.ArrayStore',
    storeId : 'MortgageStore',
    fields  : ['id', 'Text'],
    data    : [['0', ''], ['68', 'ипотека не возможна'], ['69', 'ипотека возможна']]
});