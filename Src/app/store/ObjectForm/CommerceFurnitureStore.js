Ext.define('crm.store.ObjectForm.CommerceFurnitureStore', {
    extend  : 'Ext.data.ArrayStore',
    storeId : 'CommerceFurnitureStore',
    fields  : ['id', 'Text'],
    data    : [['0', ''], ['155', 'нет'], ['156', 'есть'], ['157', 'по желанию клиента']]
});