
    Ext.define('crm.store.Mail.MailGridStore', {
        extend      : 'Ext.data.Store',
        requires    : 'crm.model.MailGridModel',
        model       : 'crm.model.MailGridModel',
        autoLoad    : true
    });
