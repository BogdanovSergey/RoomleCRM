Ext.define('crm.store.ObjectForm.TelephoneStore', {
    extend  : 'Ext.data.ArrayStore',
    storeId : 'TelephoneStore',
    fields  : ['id', 'Text'],
    data    : [['0', ''], ['21', 'есть телефонная линия'],  ['22', 'две и более телефонные линии'], ['20', 'нет телефонной линии']]
});