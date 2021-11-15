Ext.define('crm.model.OwnersGridModel', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'id',            type: 'int'},
        {name: 'AddedDate',     type: 'string'},
        {name: 'Color',         type: 'string'},
        //{name: 'ObjectTypeName',type: 'string'},
        {name: 'FlatType',    type: 'string'},
        {name: 'Metro',         type: 'string'},
        {name: 'Address',        type: 'string'},
        {name: 'Floors',        type: 'string'},
        {name: 'Square',       type: 'string'},
        {name: 'Price',         type: 'string'},
        {name: 'Phone',         type: 'string'}
    ],
    proxy       : OwnersGridProxy
});