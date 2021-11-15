Ext.define('crm.store.ObjectForm.ObjectCommerceDealTypeStore', {
    extend  : 'Ext.data.ArrayStore',
    storeId : 'ObjectCommerceDealTypeStore',
    fields  : ['id', 'Text'],
    data    : [
        ['144', 'Прямая аренда'],
        ['145', 'Субаренда'],
        ['146', 'Продажа права аренды (ППА)'],
        ['147', 'Продажа объекта'],
        ['148', 'Договор совместной деятельности']
    ]
});
