Ext.define('crm.model.RightsModel', {
    extend: 'Ext.data.Model',
    fields: [
        { name: 'id',           type: 'string' },// Id должностей мешаются с id прав -> добавлен префикс типа rightX
        { name: 'ItemName',     type: 'string' },
        { name: 'ItemType',     type: 'string' },
        { name: 'RightDescr',   type: 'string' },
        { name: 'DeleteColumn', type: 'string' },
        { name: 'BindToItemId', type: 'string' },
        { name: 'BindToItemName', type: 'string' }

    ]
});