Ext.define('crm.store.ObjectForm.ObjectCountryHeatStore', {
    extend  : 'Ext.data.ArrayStore',
    storeId : 'ObjectCountryHeatStore',
    fields  : ['id', 'Text'],
    data    : [
        ['0', ''],
        ['100', 'нет'],
        ['101', 'центральное'],
        ['102', 'электрокотел'],
        ['103', 'газовый котел'],
        ['104', 'жидкотопливный котел'],
        ['105', 'АГВ'],
        ['106', 'печь'],
        ['107', 'есть'],
        ['108', 'иное']
    ]
});
