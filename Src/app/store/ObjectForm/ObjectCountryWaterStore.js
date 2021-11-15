Ext.define('crm.store.ObjectForm.ObjectCountryWaterStore', {
    extend  : 'Ext.data.ArrayStore',
    storeId : 'ObjectCountryWaterStore',
    fields  : ['id', 'Text'],
    data    : [
        ['0', ''],
        ['77', 'нет'],
        ['83', 'есть'],
        ['84', 'летний'],
        ['80', 'колодец'],
        ['79', 'скважина'],
        ['78', 'центральный'],
        ['81', 'магистральный'],
        ['82', 'иное']
    ]
});
