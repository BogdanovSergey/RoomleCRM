Ext.define('crm.store.ObjectForm.CommerceEnterTypeStore', {
    extend  : 'Ext.data.ArrayStore',
    storeId : 'CommerceEnterTypeStore',
    fields  : ['id', 'Text'],
    data    : [['0', ''], ['151', 'свободный/нет охраны'], ['152', 'пропускная система/контроль доступа'],['192','кпп при въезде'],['193','охрана на 1м этаже'],['194','охрана за счет арендатора']]
});