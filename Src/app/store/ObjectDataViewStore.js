Ext.define('crm.store.ObjectDataViewStore', {
//Ext.create('Ext.data.Store', {
    extend      : 'Ext.data.Store',
    storeId     : 'ObjectDataViewStore',
    fields: [
        {name: 'id',    type: 'int'},
        {name: 'name'},
        {name: 'url'},
        {name: 'width', type: 'int'},
        {name: 'height',type: 'int'},
        {name: 'size',  type: 'float'},
        {name: 'lastmod', type:'date', dateFormat:'timestamp'}
    ],
    autoLoad: false,
    proxy: {
        type    : 'ajax',
        url     : GetObjectImagesUrl,
        reader  : {
            type: 'json',
            root: 'images'
        }
    }
})
