Ext.define('crm.store.ObjectForm.ObjectCountryElectroStore', {
    extend  : 'Ext.data.ArrayStore',
    storeId : 'ObjectCountryElectroStore',
    fields  : ['id', 'Text'],
    data    : [
        ['0', ''],
        ['109', 'нет'],
        ['110', 'есть'],
        ['112', '220 В'],
        ['113', '380 В'],
        ['114', 'перспектива'],
        ['115', 'по границе'],
        ['116', '10 КВт'],
        ['117', 'иное']
    ]
});
