    Ext.define('crm.store.UserFormRightsStore', {
        extend      : 'Ext.data.Store',
        requires    : 'crm.model.UserFormRightsModel',
        model       : 'crm.model.UserFormRightsModel',
        storeId     : 'UserFormRightsStore',
        autoLoad    : false,
        groupField  : 'Наследование',
        proxy : {
            type    : 'ajax',
            url     : GetAccessRulesStructureUrl,//MainAjaxDriver + '?Action=GetAccessRulesStructure',
            reader  : { type: 'json', root: 'data', totalProperty: 'total' },
            extraParams   : {
                UserId     : '',
                PositionId : '',
                GroupId    : ''
            }
        }
    });
