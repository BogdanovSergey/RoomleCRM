Ext.define('crm.view.ClientsListWindow' ,{
    extend  : 'Ext.window.Window',
    alias   : 'widget.ClientsListWindow',
    id      : "ClientsListWindow",
    title   : 'Список клиентов',
    autoShow: true,
    autoScroll: true,
    height  : 550,
    width   : 700,
    layout  : 'fit',
    modal       : true,
    constrain   : true,
    closeAction : 'destroy',
    beforedestroy: function (window) {
        //Ext.getCmp('UsersGrid').close();
    },

    initComponent: function() {
        this.items = [
            Ext.create('crm.view.ClientsGrid')
        ];

        Ext.data.StoreManager.lookup('ClientsGridStore').load();  // предварительно загрузить store, чтобы сработало событие onLoad в контроллере для показа счетчика

        this.callParent(arguments);
    }



});