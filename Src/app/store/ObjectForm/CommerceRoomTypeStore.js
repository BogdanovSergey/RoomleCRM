Ext.define('crm.store.ObjectForm.CommerceRoomTypeStore', {
    extend  : 'Ext.data.ArrayStore',
    storeId : 'CommerceRoomTypeStore',
    fields  : ['id', 'Text'],
    data    : [['0', ''], ['175', 'помещения для офисов'], ['176', 'торговые помещения'],['195','производственно-складские помещения'],['196','разное, другой тип помещений']]
});