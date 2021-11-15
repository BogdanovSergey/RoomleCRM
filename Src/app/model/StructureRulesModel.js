Ext.define('crm.model.StructureRulesModel', {
    extend: 'Ext.data.Model',
    fields: [
        { name: 'id',    type: 'int'},
        { name: 'VarName', type: 'string' } // { name: 'id', type: 'int' },
    ]
});