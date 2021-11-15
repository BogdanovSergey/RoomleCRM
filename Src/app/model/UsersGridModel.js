    Ext.define('crm.model.UsersGridModel', {
        extend: 'Ext.data.Model',
        fields: [
            {name: 'id',            type: 'int'},
            {name: 'AddedDate',     type: 'string'},
            {name: 'ArchivedDate',  type: 'string'},
            {name: 'FirstName',     type: 'string'},
            {name: 'LastName',      type: 'string'},
            {name: 'Position',      type: 'string'},
            {name: 'Group',         type: 'string'},
            {name: 'PositionId',    type: 'string'},
            {name: 'GroupId',       type: 'string'},
            {name: 'Status',        type: 'string'},
            {name: 'Email',         type: 'string'},
            {name: 'Birthday',      type: 'string'},
            {name: 'LastEnter',     type: 'string'},
            {name: 'MobilePhone',   type: 'string'},
            {name: 'MobilePhone1',  type: 'string'},
            {name: 'MobilePhone2',  type: 'string'},
            {name: 'CurrentSumm',   type: 'string'}
        ],
        proxy       : ActiveUsersGridProxy
    });
