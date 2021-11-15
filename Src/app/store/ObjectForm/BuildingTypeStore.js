Ext.define('crm.store.ObjectForm.BuildingTypeStore', {
    extend  : 'Ext.data.ArrayStore',
    storeId : 'BuildingTypeStore',
    fields  : ['id', 'Text'],
    data    : [['0', ''], ['45', 'монолитный'], ['46', 'панельный'], ['47', 'кирпичный'], ['48', 'блочный'], ['49', 'сталинский'], ['50', 'элитный'], ['51', 'монолитно-кирпичный'], ['52', 'блочно-панельный']]
});