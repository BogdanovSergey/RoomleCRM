Ext.define('crm.model.SimpleModel', {
    extend: 'Ext.data.Model',
    fields: [
        { name: 'id',    type: 'int'},
        { name: 'VarName', type: 'string' },
        { name: 'VarData', type: 'string' }
    ]
});