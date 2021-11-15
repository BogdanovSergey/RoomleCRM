Ext.define('crm.store.ObjectForm.LiftStore', {
    extend  : 'Ext.data.ArrayStore',
    storeId : 'LiftStore',
    fields  : ['id', 'Text'],
    data    : [['0', ''], ['35', 'без лифта'], ['36', '1 пассажирский'], ['37', '1 грузовой'], ['38', 'пассажирский и грузовой']]
});