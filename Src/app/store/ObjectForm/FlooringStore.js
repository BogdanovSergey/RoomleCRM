Ext.define('crm.store.ObjectForm.FlooringStore', {
    extend  : 'Ext.data.ArrayStore',
    storeId : 'FlooringStore',
    fields  : ['id', 'Text'],
    data    : [['0', ''], ['61', 'дерево'], ['62', 'паркетная доска'], ['63', 'ламинат'], ['64', 'ковролин'], ['65', 'паркет'], ['66', 'линолеум'], ['67', 'стяжка пола']]
        //['60', 'не определен'],
});