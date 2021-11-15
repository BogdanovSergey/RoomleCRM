Ext.define('crm.store.ObjectForm.BalconStore', {
    extend  : 'Ext.data.ArrayStore',
    storeId : 'BalconStore',
    fields  : ['id', 'Text'],
    data    : [['0', ''], ['25', 'балкон/лоджия отсутствует'], ['26', '1 балкон'], ['27', '2 балкона'], ['28', '3 балкона'],['29', '1 лоджия'], ['30', '2 лоджии'], ['31', '3 лоджии'], ['55', '4 и более лоджии'], ['32', 'балкон и лоджия'], ['33', 'балкон и 2 лоджии'], ['34', '2 балкона и 2 лоджии']]
});