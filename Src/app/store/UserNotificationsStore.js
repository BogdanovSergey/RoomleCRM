Ext.define('crm.store.UserNotificationsStore', {
    extend  : 'Ext.data.ArrayStore',
    storeId : 'UserNotificationsStore',
    fields  : ['id', 'Text'],
    data    : [[1, 'Отправить приглашение по email']]//, ['2', 'Оповестить об изменениях в анкете по email']
});