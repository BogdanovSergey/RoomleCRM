Ext.define('crm.store.ObjectForm.CommerceParkingStore', {
    extend  : 'Ext.data.ArrayStore',
    storeId : 'CommerceParkingStore',
    fields  : ['id', 'Text'],
    data    : [['0', ''],
        ['159', 'Нет/парковка затруднена'], ['158', 'Есть/свободная парковка'],['212','Гостевая парковка'],
        ['218','Парковка во дворе'],['220','Парковка перед фасадом'],
        ['214','Пoдземная парковка'], ['215','Нaземная парковка'],['216','Многоуровневая наземная парковка'],
        ['217','Нaземная охраняемая парковка'],['219','Парковка на крыше']
        ]
});