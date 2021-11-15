Ext.define('crm.view.UsersListWindow' ,{
    extend  : 'Ext.window.Window',
    alias   : 'widget.UsersListWindow',
    id      : "UsersListWindow",
    title   : 'Список сотрудников',
    autoShow: true,
    autoScroll: true,
    height  : 550,
    width   : 1100,
    layout  : 'fit',
    modal       : true,
    constrain   : true,
    closeAction : 'destroy',
    beforedestroy: function (window) {
        //Ext.getCmp('UsersGrid').close();
    },

    initComponent: function() {
        this.items = [
            Ext.create('crm.view.UsersGrid')
        ];

        Ext.data.StoreManager.lookup('UsersGridStore').load();  // предварительно загрузить store, чтобы сработало событие onLoad в контроллере для показа счетчика

        this.callParent(arguments);
    }



});