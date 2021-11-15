Ext.define('crm.store.ObjectForm.ObjectConditionStore', {
    extend  : 'Ext.data.ArrayStore',
    storeId : 'ObjectConditionStore',
    fields  : ['id', 'Text'],
    data    : [['0', ''], ['3', 'эксклюзивный проект'], ['4', 'евроремонт'], ['5', 'отличное'], ['6', 'хорошее'], ['7', 'среднее'], ['8', 'нужен ремонт'], ['9', 'без отделки']]
});