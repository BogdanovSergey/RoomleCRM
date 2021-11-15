Ext.define('crm.store.StructureRulesStore', {  //#AvitoAltAddr
    extend      : 'Ext.data.Store',
    storeId     : 'StructureRulesStore',
    requires    : 'crm.model.StructureRulesModel',
    model       : 'crm.model.StructureRulesModel',
    autoLoad: true,
    proxy: {
        type    : 'ajax',
        url     : MainAjaxDriver + '?Action=GetAccessRulesArr',
        reader  : 'json',
        extraParams   : {
            ChosenItemId : '',
            ChosenItemType : ''
        }
    }/*,
    fields: [
        {name: 'id'},
        {name: 'VarName'}
    ]*/
    //
});