Ext.define('crm.model.MailGridModel', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'checkbox',      type: 'bool'},
        {name: 'id',            type: 'int'},
        {name: 'AddedDate',     type: 'string'},
        {name: 'ArchivedDate',  type: 'string'},
        {name: 'Subject',       type: 'string'},
        {name: 'MailFrom',      type: 'string'}
    ],
    proxy       : ActiveMailGridProxy
});