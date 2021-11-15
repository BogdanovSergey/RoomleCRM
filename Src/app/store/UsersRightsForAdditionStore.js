    Ext.define('crm.store.UsersRightsForAdditionStore', {
        extend      : 'Ext.data.Store',
        requires    : 'crm.model.UserFormRightsModel',
        model       : 'crm.model.UserFormRightsModel',
        storeId     : 'UsersRightsForAdditionStore',
        /*autoLoad  : false,
        pageSize    : 100, // идет в связке с LIMIT 0,100
        leadingBufferZone: 100,
        buffered    : true,
        remoteSort  : true,*/
        proxy : {
            type    : 'ajax',
            url     : MainAjaxDriver + '?Action=GetAccessRulesForAddition',
            reader  : { type: 'json', root: 'data', totalProperty: 'total' },
            extraParams   : {
                UserId     : '',
                PositionId : '',
                GroupId    : ''
            }
        }
    });
