    Ext.define('crm.model.ClientsGridModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id',            type: 'int'},
            {name: 'AddedDate',     type: 'string'},
            {name: 'ArchivedDate',  type: 'string'},
            {name: 'FirstName',     type: 'string'},
            {name: 'LastName',      type: 'string'},
            {name: 'ClientType',    type: 'string'},
            {name: 'MobilePhone',   type: 'string'},

            {name: 'Birthday',      type: 'string'},
            {name: 'Email',         type: 'string'},
            {name: 'ObjectLocation',type: 'string'},
            {name: 'Source',        type: 'string'}
        ],
        proxy       : ActiveClientsGridProxy
    });
