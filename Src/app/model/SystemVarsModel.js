Ext.define('crm.model.SystemVarsModel', {
    extend: 'Ext.data.Model',
    fields: [
        { name: 'id',       type: 'int'},
        { name: 'VarKey',   type: 'string' },
        { name: 'VarValue', type: 'string' }
    ],
    proxy: {
    type    : 'ajax',
        url     : DataRequestUrl,
        reader : {
        type: 'json',
            root: 'data',
            totalProperty: 'total' },
    extraParams   : {
        DataType : 'GetSystemVars'
    }
}
});