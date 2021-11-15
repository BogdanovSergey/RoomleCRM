Ext.define('crm.store.ObjectForm.ObjectCountryLandTypeStore', {
    extend  : 'Ext.data.ArrayStore',
    storeId : 'ObjectCountryLandTypeStore',
    fields  : ['id', 'Text'],
    data    : [['74', 'СНТ'], ['75', 'ИЖС'], ['76', 'промназначение'],['125','фермерское хозяйство'],['126','ДНП']] // ,['127','инвестпроект']
});
