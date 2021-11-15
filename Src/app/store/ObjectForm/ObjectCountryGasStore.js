Ext.define('crm.store.ObjectForm.ObjectCountryGasStore', {
    extend  : 'Ext.data.ArrayStore',
    storeId : 'ObjectCountryGasStore',
    fields  : ['id', 'Text'],
    data    : [
        ['0', ''],
        ['85', 'нет'],
        ['89', 'перспектива'],
        ['88', 'по границе'],
        ['90', 'рядом'],
        ['91', 'баллоны'],
        ['86', 'есть'],
        ['87', 'магистральный'],
        ['92', 'центральный'],
        ['93', 'иное']
    ]
});
