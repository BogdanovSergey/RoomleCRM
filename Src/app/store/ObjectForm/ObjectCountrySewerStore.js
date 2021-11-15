Ext.define('crm.store.ObjectForm.ObjectCountrySewerStore', {
    extend  : 'Ext.data.ArrayStore',
    storeId : 'ObjectCountrySewerStore',
    fields  : ['id', 'Text'],
    data    : [
        ['0', ''],
        ['94', 'нет'],
        ['95', 'есть'],
        ['96', 'вне дома'],
        ['97', 'септик'],
        ['98', 'центральная'],
        ['99', 'иное']
    ]
});
