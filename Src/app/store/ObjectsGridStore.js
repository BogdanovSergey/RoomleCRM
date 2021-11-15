Ext.define('crm.store.ObjectsGridStore', {
    extend: 'Ext.data.Store',
    requires    : 'crm.model.ObjectsGridModel',
    model       : 'crm.model.ObjectsGridModel',
    storeId     : 'ObjectsGridStore',//'GridStore',
    pageSize    : 100, // идет в связке с LIMIT 0,100
    leadingBufferZone: 100, // сколько сохранять строк в дополнение к текущим загруженном строкам, при load/reload они не обновляются
    buffered    : true,
    remoteSort  : true,   // удаленная сортировка
    autoLoad    : true
});
