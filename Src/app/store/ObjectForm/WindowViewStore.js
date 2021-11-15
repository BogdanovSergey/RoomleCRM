Ext.define('crm.store.ObjectForm.WindowViewStore', {
    extend  : 'Ext.data.ArrayStore',
    storeId : 'WindowViewStore',
    fields  : ['id', 'Text'],
    data    : [['0', ''], ['16', 'улица'], ['17', 'двор'], ['18', 'двор и улица'], ['19', 'панорамный вид']]
});