Ext.define('crm.store.ObjectForm.CommerceBuildingStatusStore', {
    extend  : 'Ext.data.ArrayStore',
    storeId : 'CommerceBuildingStatusStore',
    fields  : ['id', 'Text'],
    data    : [['0', ''], ['188', 'сдано в эксплуатацию'], ['189', 'выполнена отделка общих зон'],['190', 'идет реконструкция'],['191', 'идет строительство']]
});