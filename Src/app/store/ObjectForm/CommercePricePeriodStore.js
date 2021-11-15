Ext.define('crm.store.ObjectForm.CommercePricePeriodStore', {
    extend  : 'Ext.data.ArrayStore',
    storeId : 'CommercePricePeriodStore',
    fields  : ['id', 'Text'],
    data    : [
        ['160', 'в сутки'],['161', 'в месяц'],['162', 'в квартал'],['163', 'в год']
    ]
});
