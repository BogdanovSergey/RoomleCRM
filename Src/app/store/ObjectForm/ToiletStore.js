Ext.define('crm.store.ObjectForm.ToiletStore', {
    extend  : 'Ext.data.ArrayStore',
    storeId : 'ToiletStore',
    fields  : ['id', 'Text'],
    data    : [['0', ''], ['10', 'раздельный'], ['11', 'совместный'], ['12', 'два санузла'], ['13', 'три санузла'], ['14', 'больше трех'], ['15', 'на улице']]
});