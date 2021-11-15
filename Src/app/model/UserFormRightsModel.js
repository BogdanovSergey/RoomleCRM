Ext.define('crm.model.UserFormRightsModel', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'id',           type: 'string'},
        {name: 'TargetType',   type: 'string'},
        {name: 'Description',  type: 'string'},
        {name: 'Наследование', type: 'string'},
        {name: 'RemoveColumn', type: 'string' }
    ]
});
