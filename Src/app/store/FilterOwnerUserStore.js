Ext.define('crm.store.FilterOwnerUserStore', {
    extend      : 'Ext.data.Store',
    storeId     : 'FilterOwnerUserStore',
    fields: [
        {name: 'id'},
        {name: 'VarName'}
    ],
    autoLoad: true,
    proxy: {
        type: 'ajax',
        url: BuildFilterOwnerUserSelectUrlString(MainAjaxDriver, FilterOwnerUserSelect_Action, FilterOwnerUserSelect_ActiveObjects, FilterOwnerUserSelect_GetAgents, FilterOwnerUserSelect_OnlyFio, FilterOwnerUserSelect_WithSumm, FilterOwnerUserSelect_RealtyType),
        reader: {
            type: 'json'
        }
    }
})