Ext.define('crm.store.ObjectForm.CountryWallsTypeStore', {
    extend  : 'Ext.data.ArrayStore',
    storeId : 'CountryWallsTypeStore',
    fields  : ['id', 'Text'],
    data    : [['0', ''], ['223', 'Кирпич'], ['224', 'Брус'], ['225', 'Бревно'], ['226', 'Металл'],
                ['227', 'Пеноблоки'], ['228', 'Сэндвич-панели'], ['229', 'Ж/б панели'], ['230', 'Экспериментальные материалы']]
});